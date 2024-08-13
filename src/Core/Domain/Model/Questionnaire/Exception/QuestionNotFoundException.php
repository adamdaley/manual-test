<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;

class QuestionNotFoundException extends Exception
{
    public function __construct(UuidV7 $questionId)
    {
        parent::__construct(sprintf('Question %s not found.', $questionId));
    }
}