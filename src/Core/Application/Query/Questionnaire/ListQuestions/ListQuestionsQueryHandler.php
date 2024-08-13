<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\ListQuestions;

use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListQuestionsQueryHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    /**
     * @return list<Question>
     */
    public function __invoke(ListQuestionsQuery $query): array
    {
        $questionnaire = $this->questionnaireRepository->find($query->questionnaireId);

        return $questionnaire->getQuestions()->toArray();
    }
}