<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Exception\AnswerNotFoundException;
use App\Core\Domain\Model\Questionnaire\Exception\MultipleAnswersFoundException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionAnsweredMultipleTimesException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionNotAnsweredException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionNotFoundException;
use App\Shared\Domain\Model\Aggregate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV7;

#[Mapping\Entity]
class Questionnaire extends Aggregate implements JsonSerializable
{
    /** @var Collection<int, Question> */
    #[Mapping\OneToMany(targetEntity: Question::class, mappedBy: 'questionnaire', cascade: ['persist', 'remove'])]
    private Collection $questions;

    public function __construct(
        #[Mapping\Column(type: UuidType::NAME)]
        public readonly UuidV7 $productCategoryId, // todo then load in app layer and also query for in controller
        #[Mapping\Column(type: Types::STRING)]
        public readonly string $title,
    ) {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Questionnaire title cannot be empty.');
        }

        parent::__construct();

        $this->questions = new ArrayCollection();

        // todo record event
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @throws QuestionNotFoundException
     */
    public function getQuestionById(UuidV7 $questionId): Question
    {
        return $this->questions->findFirst(fn(int $key, Question $question) => $question->getId()->equals($questionId))
            ?? throw new QuestionNotFoundException($questionId);
    }

    public function addQuestion(string $title): Question
    {
        $question = $this->questions[] = new Question($this, $title);

        // todo record event

        return $question;
    }

    /**
     * @throws QuestionNotFoundException
     */
    public function removeQuestion(UuidV7 $questionId): void
    {
        $question = $this->getQuestionById($questionId);

        $this->questions->removeElement($question);

        // todo record event
    }

    /**
     * @param list<UuidV7> $productIdRestrictions
     *
     * @throws QuestionNotFoundException
     */
    public function addAnswerToQuestion(
        UuidV7 $questionId,
        string $title,
        ?UuidV7 $nextQuestionId = null,
        array $productIdRestrictions = [],
    ): Answer {
        $question = $this->getQuestionById($questionId);

        $answer = $question->addAnswer($title, $nextQuestionId, $productIdRestrictions);

        // todo record event

        return $answer;
    }

    /**
     * @throws AnswerNotFoundException
     * @throws QuestionNotFoundException
     */
    public function removeAnswerFromQuestion(UuidV7 $questionId, UuidV7 $answerId): void
    {
        $question = $this->getQuestionById($questionId);

        $question->removeAnswer($answerId);

        // todo record event
    }

    /**
     * @param list<UuidV7> $answerIds
     *
     * @return list<UuidV7>
     *
     * @throws QuestionAnsweredMultipleTimesException
     * @throws QuestionNotAnsweredException
     * @throws QuestionNotFoundException
     */
    public function getProductIdRestrictionsFromAnswerIds(array $answerIds): array
    {
        // v4
        $productIdRestrictions = [];

        $currentQuestion = $this->questions->first();

        while ($currentQuestion instanceof Question) {
            try {
                $answer = $currentQuestion->getAnswerByIds($answerIds);
            } catch (AnswerNotFoundException $e) {
                throw new QuestionNotAnsweredException($currentQuestion->getId(), $currentQuestion->title, $e);
            } catch (MultipleAnswersFoundException $e) {
                throw new QuestionAnsweredMultipleTimesException($currentQuestion->getId(), $currentQuestion->title, $e);
            }

            $productIdRestrictions = array_merge($productIdRestrictions, $answer->getProductIdRestrictions());

            $nextQuestionId = $answer->getNextQuestionId();
            if (!$nextQuestionId instanceof UuidV7) {
                break;
            }

            $currentQuestion = $this->getQuestionById($nextQuestionId);
        }

        return array_values(array_unique($productIdRestrictions));
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'productCategoryId' => $this->productCategoryId, // todo need to make sure controller is returning product category as well
            'questions' => $this->questions->map(fn(Question $question) => $question->jsonSerialize())->toArray(),
        ];
    }
}