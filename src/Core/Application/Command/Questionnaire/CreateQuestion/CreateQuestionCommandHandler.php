<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateQuestion;

use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateQuestionCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(CreateQuestionCommand $command): Question
    {
        $questionnaire = $this->questionnaireRepository->find($command->questionnaireId);

        return $questionnaire->addQuestion($command->title);
    }
}