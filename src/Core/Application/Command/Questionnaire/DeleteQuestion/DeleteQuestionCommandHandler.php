<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteQuestion;

use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteQuestionCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(DeleteQuestionCommand $command): void
    {
        $questionnaire = $this->questionnaireRepository->find($command->questionnaireId);

        $questionnaire->removeQuestion($command->questionId);
    }
}