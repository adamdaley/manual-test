<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;
use Throwable;

class QuestionAnsweredMultipleTimesException extends Exception
{
    public function __construct(UuidV7 $questionId, string $title, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Question %s - "%s" answered multiple times.', $questionId, $title), previous: $previous);
    }
}