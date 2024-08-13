<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetQuestionnaire;

use App\Core\Domain\Model\Questionnaire\Questionnaire;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetQuestionnaireQueryHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(GetQuestionnaireQuery $query): Questionnaire
    {
        return $this->questionnaireRepository->find($query->id);
    }
}