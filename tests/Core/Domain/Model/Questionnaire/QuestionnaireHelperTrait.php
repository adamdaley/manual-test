<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Answer;
use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\Questionnaire;
use Symfony\Component\Uid\UuidV7;

trait QuestionnaireHelperTrait
{
    public function generateQuestionnaire(?UuidV7 $productCategoryId = null): Questionnaire
    {
        if (!$productCategoryId instanceof UuidV7) {
            $productCategoryId = UuidV7::v7();
        }
        return new Questionnaire($productCategoryId, 'Test questionnaire');
    }

    public function generateQuestion(?Questionnaire $questionnaire = null): Question
    {
        if (!$questionnaire instanceof Questionnaire) {
            $questionnaire = $this->generateQuestionnaire();
        }

        return new Question($questionnaire, 'Test question');
    }

    public function generateAnswer(?Question $question = null): Answer
    {
        if (!$question instanceof Question) {
            $question = $this->generateQuestion();
        }

        return new Answer($question, 'Test answer');
    }
}