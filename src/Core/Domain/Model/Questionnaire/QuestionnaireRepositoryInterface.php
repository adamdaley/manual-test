<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire;

use App\Shared\Infrastructure\Repository\AggregateRepositoryInterface;

/**
 * @extends AggregateRepositoryInterface<Questionnaire>
 */
interface QuestionnaireRepositoryInterface extends AggregateRepositoryInterface
{
}