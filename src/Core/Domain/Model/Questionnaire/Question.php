<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Exception\AnswerNotFoundException;
use App\Core\Domain\Model\Questionnaire\Exception\MultipleAnswersFoundException;
use App\Shared\Domain\Model\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[Mapping\Entity]
class Question implements EntityInterface, JsonSerializable
{
    #[Mapping\Id]
    #[Mapping\Column(type: UuidType::NAME, unique: true)]
    private UuidV7 $id;

    /** @var Collection<int, Answer> */
    #[Mapping\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist', 'remove'])]
    private Collection $answers;

    public function __construct(
        #[Mapping\ManyToOne(targetEntity: Questionnaire::class)]
        #[Mapping\JoinColumn(nullable: false)]
        public readonly Questionnaire $questionnaire,
        #[Mapping\Column(type: Types::STRING)]
        public readonly string $title,
    ) {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Question title cannot be empty.');
        }

        $this->id = Uuid::v7();
        $this->answers = new ArrayCollection();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @throws AnswerNotFoundException
     */
    public function getAnswerById(UuidV7 $answerId): Answer
    {
        return $this->answers->findFirst(fn(int $key, Answer $answer) => $answer->getId()->equals($answerId))
            ?? throw new AnswerNotFoundException(sprintf('Answer %s not found.', $answerId));
    }

    /**
     * @param UuidV7[] $answerIds
     *
     * @throws AnswerNotFoundException
     * @throws MultipleAnswersFoundException
     */
    public function getAnswerByIds(array $answerIds): Answer
    {
        $answers = $this->answers->filter(fn(Answer $answer) => in_array($answer->getId(), $answerIds));

        if ($answers->isEmpty()) {
            throw new AnswerNotFoundException(
                sprintf(
                    'Answer not found for question %s given answer ids %s',
                    $this->id,
                    implode(', ', $answerIds),
                ),
            );
        }

        if ($answers->count() > 1) {
            throw new MultipleAnswersFoundException($this->id, $answerIds);
        }

        return $answers->first();
    }

    /**
     * @param list<UuidV7> $productIdRestrictions
     */
    public function addAnswer(
        string $title,
        ?UuidV7 $nextQuestionId = null,
        array $productIdRestrictions = [],
    ): Answer {
        return $this->answers[] = new Answer(
            $this,
            $title,
            $nextQuestionId,
            $productIdRestrictions,
        );
    }

    /**
     * @throws AnswerNotFoundException
     */
    public function removeAnswer(UuidV7 $answerId): void
    {
        $answer = $this->getAnswerById($answerId);

        $this->answers->removeElement($answer);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'answers' => $this->answers->map(fn(Answer $answer) => $answer->jsonSerialize())->toArray(),
        ];
    }
}