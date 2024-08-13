<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteQuestionnaire;

use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;

final readonly class DeleteQuestionnaireCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(DeleteQuestionnaireCommand $command): void
    {
        $questionnaire = $this->questionnaireRepository->find($command->id);

        $this->questionnaireRepository->remove($questionnaire);
    }
}