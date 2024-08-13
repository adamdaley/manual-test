<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Admin;

use App\Core\Application\Command\ProductCategory\CreateProductCategory\CreateProductCategoryCommand;
use App\Core\Application\Command\ProductCategory\DeleteProductCategory\DeleteProductCategoryCommand;
use App\Core\Application\Query\ProductCategory\GetProductCategory\GetProductCategoryQuery;
use App\Core\Application\Query\ProductCategory\ListProductCategories\ListProductCategoriesQuery;
use App\Entity\User;
use App\Shared\Infrastructure\MessageBus\CommandBus;
use App\Shared\Infrastructure\MessageBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV7;

#[Route('/admin/product-category')]
#[IsGranted(User::ROLE_ADMIN)]
class ProductCategoryController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {
    }

    #[Route('/', name: 'admin_product_category_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        $productCategories = $this->queryBus->query(new ListProductCategoriesQuery());

        return $this->json($productCategories);
    }

    #[Route('/', name: 'admin_product_category_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $name = $request->get('name', '');

        $productCategory = $this->commandBus->command(new CreateProductCategoryCommand($name));

        return $this->json($productCategory);
    }

    #[Route('/{id}', name: 'admin_product_category_show', methods: [Request::METHOD_GET])]
    public function show(UuidV7 $id): JsonResponse
    {
        $productCategory = $this->queryBus->query(new GetProductCategoryQuery($id));

        return $this->json($productCategory);
    }

//    #[Route('/{id}', name: 'admin_product_category_delete', methods: [Request::METHOD_DELETE])]
    #[Route('/{id}/delete', name: 'admin_product_category_delete', methods: [Request::METHOD_GET])]
    public function delete(UuidV7 $id): JsonResponse
    {
        $this->commandBus->command(new DeleteProductCategoryCommand($id));

        return new JsonResponse([]);
    }
}