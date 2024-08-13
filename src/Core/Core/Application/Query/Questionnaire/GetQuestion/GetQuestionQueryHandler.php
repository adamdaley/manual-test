<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetQuestion;

use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetQuestionQueryHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(GetQuestionQuery $query): Question
    {
        $questionnaire = $this->questionnaireRepository->find($query->questionnaireId);

        return $questionnaire->getQuestionById($query->questionId);
    }
}