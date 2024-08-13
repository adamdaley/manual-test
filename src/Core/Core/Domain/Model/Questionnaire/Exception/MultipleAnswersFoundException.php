<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;

class MultipleAnswersFoundException extends Exception
{
    /**
     * @param UuidV7[] $answerIds
     */
    public function __construct(UuidV7 $questionId, array $answerIds)
    {
        parent::__construct(sprintf(
            'Multiple answers found for question %s given answer ids %s',
            $questionId,
            implode(', ', $answerIds),
        ));
    }
}