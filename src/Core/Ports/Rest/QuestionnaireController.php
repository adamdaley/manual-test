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

        /** @var list<UuidV7> $productRestrictionIds */
        $productRestrictionIds = $this->queryBus->query(new GetProductRestrictionsFromAnswersQuery($questionnaire->getId(), $answerIds));

        /** @var list<Product> $recommendedProducts */
        $recommendedProducts = $this->queryBus->query(new ListProductsBasedOnRestrictionsQuery($productCategory->id, $productRestrictionIds));

        return $this->json($recommendedProducts);
    }

//    #[Route('/init', name: 'questionnaire_init', methods: ['GET'])]
//    public function init(): JsonResponse
//    {
//        $questionnaire = $this->initQuestionnaire();
//
//        return $this->json($questionnaire);
//    }
//
//    #[Route('/test', name: 'questionnaire_test', methods: ['GET'])]
//    public function test(): JsonResponse
//    {
//        $questionnaire = $this->commandBus->command(new CreateQuestionnaireCommand(Uuid::v7(),'Test Questionnaire'));
//
//        return $this->json($questionnaire);
//    }
//
//    #[Route('/{id:questionnaire}', name: 'questionnaire_get', methods: ['GET'])]
//    public function get(
//        Questionnaire $questionnaire,
//        EntityManagerInterface $entityManager,
//    ): JsonResponse {
//        $questionnaire = $entityManager->find(Questionnaire::class, $questionnaire->getId());
//
//        return $this->json($questionnaire);
//    }

//    private function initQuestionnaire(): Questionnaire
//    {
//        $productCategory = new ProductCategory('Erectile dysfunction');
//        $sildenafil50 = $productCategory->addProduct('Sildenafil 50mg');
//        $sildenafil100 = $productCategory->addProduct('Sildenafil 100mg');
//        $tadalafil10 = $productCategory->addProduct('Tadalafil 10mg');
//        $tadalafil20 = $productCategory->addProduct('Tadalafil 20mg');
//
//        $this->entityManager->persist($productCategory);
//
//        $allProductIds = [$sildenafil50->getId(), $sildenafil100->getId(), $tadalafil10->getId(), $tadalafil20->getId()];
//
//        $questionnaire = new Questionnaire($productCategory->id, 'Medical history');
//        $question1 = $questionnaire->addQuestion('1. Do you have difficulty getting or maintaining an erection?');
//        $question2 = $questionnaire->addQuestion('2. Have you tried any of the following treatments before?');
//        $question2a = $questionnaire->addQuestion('2a. Was the Viagra or Sildenafil product you tried before effective?');
//        $question2b = $questionnaire->addQuestion('2b. Was the Cialis or Tadalafil product you tried before effective?');
//        $question2c = $questionnaire->addQuestion('2c. Which is your preferred treatment?');
//        $question3 = $questionnaire->addQuestion('3. Do you have, or have you ever had, any heart or neurological conditions?');
//        $question4 = $questionnaire->addQuestion('4. Do any of the listed medical conditions apply to you?');
//        $question5 = $questionnaire->addQuestion('5. Are you taking any of the following drugs?');
//
//        $question1Answer1 = $questionnaire->addAnswerToQuestion($question1->getId(), 'Yes', $question2->getId());
//        $question1Answer2 = $questionnaire->addAnswerToQuestion($question1->getId(), 'No', productIdRestrictions: $allProductIds);
//
//        $question2Answer1 = $questionnaire->addAnswerToQuestion($question2->getId(), 'Viagra or Sildenafil', $question2a->getId());
//        $question2Answer2 = $questionnaire->addAnswerToQuestion($question2->getId(), 'Cialis or Tadalafil', $question2b->getId());
//        $question2Answer3 = $questionnaire->addAnswerToQuestion($question2->getId(), 'Both', $question2c->getId());
//        // todo not sure about this
//        $question2Answer4 = $questionnaire->addAnswerToQuestion($question2->getId(), 'None of the above', $question3->getId(), [$sildenafil100->getId(), $tadalafil20->getId()]);
//
//        // todo not sure about this
//        $question2aAnswer1 = $questionnaire->addAnswerToQuestion($question2a->getId(), 'Yes', $question3->getId(), [$tadalafil10->getId(), $tadalafil20->getId(), $sildenafil100->getId()]);
//        $question2aAnswer2 = $questionnaire->addAnswerToQuestion($question2a->getId(), 'No', $question3->getId(), [$sildenafil50->getId(), $sildenafil100->getId(), $tadalafil10->getId()]);
//
//        // todo not sure about this
//        $question2bAnswer1 = $questionnaire->addAnswerToQuestion($question2b->getId(), 'Yes', $question3->getId(), [$sildenafil50->getId(), $sildenafil100->getId(), $tadalafil20->getId()]);
//        $question2bAnswer2 = $questionnaire->addAnswerToQuestion($question2b->getId(), 'No', $question3->getId(), [$tadalafil10->getId(), $tadalafil20->getId(), $sildenafil50->getId()]);
//
//        // todo not sure about this
//        $question2cAnswer1 = $questionnaire->addAnswerToQuestion($question2c->getId(), 'Viagra or Sildenafil', $question3->getId(), [$tadalafil10->getId(), $tadalafil20->getId(), $sildenafil50->getId()]);
//        $question2cAnswer2 = $questionnaire->addAnswerToQuestion($question2c->getId(), 'Cialis or Tadalafil', $question3->getId(), [$sildenafil50->getId(), $sildenafil100->getId(), $tadalafil10->getId()]);
//        $question2cAnswer3 = $questionnaire->addAnswerToQuestion($question2c->getId(), 'None of the above', $question3->getId(), [$sildenafil50->getId(), $tadalafil10->getId()]);
//
//        $question3Answer1 = $questionnaire->addAnswerToQuestion($question3->getId(), 'Yes', productIdRestrictions: $allProductIds);
//        $question3Answer2 = $questionnaire->addAnswerToQuestion($question3->getId(), 'No', $question4->getId());
//
//        $question4Answer1 = $questionnaire->addAnswerToQuestion($question4->getId(), 'Significant liver problems (such as cirrhosis of the liver) or kidney problems', productIdRestrictions: $allProductIds);
//        $question4Answer2 = $questionnaire->addAnswerToQuestion($question4->getId(), 'Currently prescribed GTN, Isosorbide mononitrate, Isosorbide dinitrate , Nicorandil (nitrates) or Rectogesic ointment', productIdRestrictions: $allProductIds);
//        $question4Answer3 = $questionnaire->addAnswerToQuestion($question4->getId(), 'Abnormal blood pressure (lower than 90/50 mmHg or higher than 160/90 mmHg)', productIdRestrictions: $allProductIds);
//        $question4Answer4 = $questionnaire->addAnswerToQuestion($question4->getId(), "Condition affecting your penis (such as Peyronie's Disease, previous injuries or an inability to retract your foreskin)", productIdRestrictions: $allProductIds);
//        $question4Answer5 = $questionnaire->addAnswerToQuestion($question4->getId(), "I don't have any of these conditions", $question5->getId());
//
//        $question5Answer1 = $questionnaire->addAnswerToQuestion($question5->getId(), 'Alpha-blocker medication such as Alfuzosin, Doxazosin, Tamsulosin, Prazosin, Terazosin or over-the-counter Flomax', productIdRestrictions: $allProductIds);
//        $question5Answer2 = $questionnaire->addAnswerToQuestion($question5->getId(), 'Riociguat or other guanylate cyclase stimulators (for lung problems)', productIdRestrictions: $allProductIds);
//        $question5Answer3 = $questionnaire->addAnswerToQuestion($question5->getId(), 'Saquinavir, Ritonavir or Indinavir (for HIV)', productIdRestrictions: $allProductIds);
//        $question5Answer4 = $questionnaire->addAnswerToQuestion($question5->getId(), 'Cimetidine (for heartburn)', productIdRestrictions: $allProductIds);
//        $question5Answer5 = $questionnaire->addAnswerToQuestion($question5->getId(), "I don't take any of these drugs");
//
//        $this->entityManager->persist($questionnaire);
//        $this->entityManager->flush();
//
//        return $questionnaire;
//    }
}