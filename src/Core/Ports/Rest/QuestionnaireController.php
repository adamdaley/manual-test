<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest;

use App\Core\Application\Query\ProductCategory\GetProductCategory\GetProductCategoryQuery;
use App\Core\Application\Query\ProductCategory\ListProductsBasedOnRestrictions\ListProductsBasedOnRestrictionsQuery;
use App\Core\Application\Query\Questionnaire\GetProductRestrictionsFromAnswers\GetProductRestrictionsFromAnswersQuery;
use App\Core\Application\Query\Questionnaire\GetQuestionnaire\GetQuestionnaireQuery;
use App\Core\Application\Query\Questionnaire\ListQuestionnaires\ListQuestionnairesQuery;
use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategory;
use App\Core\Domain\Model\Questionnaire\Questionnaire;
use App\Shared\Infrastructure\MessageBus\CommandBus;
use App\Shared\Infrastructure\MessageBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

#[Route('/questionnaire')]
class QuestionnaireController extends AbstractController
{
    public function __construct(
        private QueryBus $queryBus,
//        private CommandBus $commandBus,
    ) {
    }

    #[Route('/', name: 'questionnaire_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        $questionnaires = $this->queryBus->query(new ListQuestionnairesQuery());

        return $this->json($questionnaires);
    }

    #[Route('/{id}', name: 'questionnaire_show', methods: [Request::METHOD_GET])]
    public function show(
//        #[CurrentUser] User $user,
        UuidV7 $id,
    ): JsonResponse {
        $questionnaire = $this->queryBus->query(new GetQuestionnaireQuery($id));

        return $this->json($questionnaire);
    }

    #[Route('/{id}/recommended-products', name: 'questionnaire_recommended_products', methods: [Request::METHOD_GET])]
    public function recommendedProducts(
//        #[CurrentUser] User $user,
        UuidV7 $id,
        Request $request,
    ): JsonResponse {
        /** @var list<string> $answerIds */
        $answerIds = $request->get('answerIds', []);
        $answerIds = array_map(fn(string $answerId): UuidV7 => UuidV7::fromString($answerId), $answerIds);

        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->queryBus->query(new GetQuestionnaireQuery($id));

        /** @var ProductCategory $productCategory */
        $productCategory = $this->queryBus->query(new GetProductCategoryQuery($questionnaire->productCategoryId));

        /**
         * Ideally this endpoint would just return the product restrictions
         * and the frontend would make a separate request to get the recommended products filtered by the restrictions.
         */
        /** @var list<UuidV7> $productRestrictionIds */
        $productRestrictionIds = $this->queryBus->query(new GetProductRestrictionsFromAnswersQuery($questionnaire->getId(), $answerIds));

        /** @var list<Product> $recommendedProducts */
        $recommendedProducts = $this->queryBus->query(new ListProductsBasedOnRestrictionsQuery($productCategory->id, $productRestrictionIds));

        return $this->json($recommendedProducts);
    }
}