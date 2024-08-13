<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteAnswer;

use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteAnswerCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(DeleteAnswerCommand $command): void
    {
        $questionnaire = $this->questionnaireRepository->find($command->questionnaireId);

        $questionnaire->removeAnswerFromQuestion($command->questionId, $command->answerId);
    }
}