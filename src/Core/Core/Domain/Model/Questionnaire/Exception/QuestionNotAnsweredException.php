<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;
use Throwable;

class QuestionNotAnsweredException extends Exception
{
    public function __construct(UuidV7 $questionId, string $title, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Question %s - "%s" not answered.', $questionId, $title), previous: $previous);
    }
}