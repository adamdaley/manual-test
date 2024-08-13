<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateAnswer;

use App\Core\Domain\Model\Questionnaire\Answer;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateAnswerCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(CreateAnswerCommand $command): Answer
    {
        $questionnaire = $this->questionnaireRepository->find($command->questionnaireId);

        return $questionnaire->addAnswerToQuestion(
            $command->questionId,
            $command->title,
            $command->nextQuestionId,
            $command->productIdRestrictions,
        );
    }
}