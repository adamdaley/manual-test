<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateQuestionnaire;

use App\Core\Domain\Model\Questionnaire\Questionnaire;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateQuestionnaireCommandHandler
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {
    }

    public function __invoke(CreateQuestionnaireCommand $command): Questionnaire
    {
        $questionnaire = new Questionnaire($command->productCategoryId, $command->title);

        $this->questionnaireRepository->add($questionnaire);

        return $questionnaire;
    }
}