<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest;

use App\Core\Application\Query\ProductCategory\GetProductCategory\GetProductCategoryQuery;
use App\Shared\Infrastructure\MessageBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

#[Route('/product-category')]
class ProductCategoryController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
//        private readonly CommandBus $commandBus,
    ) {
    }

    #[Route('/{id}', name: 'product_category_show', methods: [Request::METHOD_GET])]
    public function show(UuidV7 $id): JsonResponse
    {
        $productCategory = $this->queryBus->query(new GetProductCategoryQuery($id));

        return $this->json($productCategory);
    }
}