<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetProductRestrictionsFromAnswers;

use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\UuidV7;

#[AsMessageHandler]
final readonly class GetProductRestrictionsFromAnswersQueryHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    /**
     * @return list<UuidV7>
     */
    public function __invoke(GetProductRestrictionsFromAnswersQuery $query): array
    {
        $questionnaire = $this->questionnaireRepository->find($query->questionnaireId);

        return $questionnaire->getProductIdRestrictionsFromAnswerIds($query->answerIds);
    }
}