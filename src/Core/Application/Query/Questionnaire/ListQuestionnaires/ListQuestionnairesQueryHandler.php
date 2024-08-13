<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\ListQuestionnaires;

use App\Core\Domain\Model\Questionnaire\Questionnaire;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListQuestionnairesQueryHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    /**
     * @return list<Questionnaire>
     */
    public function __invoke(ListQuestionnairesQuery $query): array
    {
        return $this->questionnaireRepository->findAll();
    }
}