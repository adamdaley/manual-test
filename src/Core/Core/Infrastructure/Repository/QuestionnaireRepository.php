<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\Model\Questionnaire\Questionnaire;
use App\Core\Domain\Model\Questionnaire\QuestionnaireRepositoryInterface;
use App\Shared\Infrastructure\Repository\AbstractAggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractAggregateRepository<Questionnaire>
 */
class QuestionnaireRepository extends AbstractAggregateRepository implements QuestionnaireRepositoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Questionnaire::class);
    }
}