<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\Admin;

use App\Core\Application\Command\Questionnaire\CreateQuestionnaire\CreateQuestionnaireCommand;
use App\Core\Application\Command\Questionnaire\DeleteQuestionnaire\DeleteQuestionnaireCommand;
use App\Entity\User;
use App\Shared\Infrastructure\MessageBus\CommandBus;
use App\Shared\Infrastructure\MessageBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV7;

#[Route('/admin/questionnaire')]
#[IsGranted(User::ROLE_ADMIN)]
class QuestionnaireController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {
    }

    #[Route('/', name: 'admin_questionnaire_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        // todo request mapping
        $productCategoryId = UuidV7::fromString($request->get('productCategoryId', ''));
        $title = $request->get('title', '');

        $questionnaire = $this->commandBus->command(new CreateQuestionnaireCommand($productCategoryId, $title));

        return $this->json($questionnaire);
    }

    #[Route('/{id}', name: 'admin_questionnaire_delete', methods: [Request::METHOD_DELETE])]
    public function delete(UuidV7 $id): JsonResponse
    {
        $this->commandBus->command(new DeleteQuestionnaireCommand($id));

        return $this->json([]);
    }



//    #[Route('/', name: 'admin_questionnaire_index', methods: [Request::METHOD_GET])]
//    public function index(): JsonResponse
//    {
//        $questionnaires = $this->queryBus->query(new ListQuestionnairesQuery());
//
//        return $this->json($questionnaires);
//    }
//
//    #[Route('/{id}', name: 'admin_questionnaire_show', methods: ['GET'])]
//    public function show(
////        #[CurrentUser] User $user,
//        string $id,
//    ): JsonResponse {
//        $id = UuidV7::fromString($id);
//        $questionnaire = $this->queryBus->query(new GetQuestionnaireQuery($id));
//
//        return $this->json($questionnaire);
//    }
}