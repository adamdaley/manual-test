<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Exception\AnswerNotFoundException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionAnsweredMultipleTimesException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionNotAnsweredException;
use App\Core\Domain\Model\Questionnaire\Exception\QuestionNotFoundException;
use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\Questionnaire;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final class QuestionnaireTest extends TestCase
{
    use QuestionnaireHelperTrait;

    private UuidV7 $productCategoryId;
    private Questionnaire $subject;

    public function setUp(): void
    {
        $this->productCategoryId = Uuid::v7();

        $this->subject = new Questionnaire($this->productCategoryId, 'Test questionnaire');
    }

    public function testConstruct_WithEmptyTitle_ThrowsInvalidArgumentException(): void
    {
        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ARRANGE & ACT
        new Questionnaire($this->productCategoryId, '');
    }

    public function testConstruct_WithValidData_ReturnsQuestionnaireInstance(): void
    {
        // ARRANGE, ACT & ASSERT
        $this->assertInstanceOf(Questionnaire::class, $this->subject);
        $this->assertSame($this->productCategoryId, $this->subject->productCategoryId);
        $this->assertSame('Test questionnaire', $this->subject->title);
    }

    public function testGetId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testGetQuestions_WithValidData_ReturnsCollection(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getQuestions();

        // ASSERT
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetQuestionById_WithAnUnknownId_ThrowsQuestionNotFoundException(): void
    {
        // ARRANGE
        $unknownQuestionId = Uuid::v7();

        // ASSERT
        $this->expectException(QuestionNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Question %s not found.', $unknownQuestionId));

        // ARRANGE & ACT
        $this->subject->getQuestionById($unknownQuestionId);
    }

    public function testGetQuestionById_WithValidData_ReturnsQuestionInstance(): void
    {
        // ARRANGE
        $question = $this->subject->addQuestion('Test question');

        // ACT
        $result = $this->subject->getQuestionById($question->getId());

        // ASSERT
        $this->assertSame($question, $result);
    }

    public function testAddQuestion_WithValidData_ReturnsQuestionInstance(): void
    {
        // ARRANGE
        $questionTitle = 'Test question';

        // ACT
        $result = $this->subject->addQuestion($questionTitle);

        // ASSERT
        $this->assertInstanceOf(Question::class, $result);
        $this->assertInstanceOf(UuidV7::class, $result->getId());
        $this->assertSame($this->subject, $result->questionnaire);
        $this->assertSame($questionTitle, $result->title);
    }

    public function testRemoveQuestion_WithAnUnknownId_ThrowsQuestionNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addQuestion('Test question');

        $unknownQuestionId = Uuid::v7();

        // ASSERT
        $this->expectException(QuestionNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Question %s not found.', $unknownQuestionId));

        // ARRANGE & ACT
        $this->subject->removeQuestion($unknownQuestionId);
    }

    public function testRemoveQuestion_WithValidData_RemovesQuestion(): void
    {
        // ARRANGE
        $question = $this->subject->addQuestion('Test question');

        // ACT
        $this->subject->removeQuestion($question->getId());

        // ASSERT
        $this->assertEmpty($this->subject->getQuestions());
    }

    public function testAddAnswerToQuestion_WithUnknownQuestionId_ThrowsQuestionNotFoundException(): void
    {
        // ARRANGE
        $unknownQuestionId = Uuid::v7();

        // ASSERT
        $this->expectException(QuestionNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Question %s not found.', $unknownQuestionId));

        // ARRANGE & ACT
        $this->subject->addAnswerToQuestion($unknownQuestionId, 'Yes');
    }

    public function testAddAnswerToQuestion_WithValidData_ReturnsAnswerInstance(): void
    {
        // ARRANGE
        $question = $this->subject->addQuestion('Test question');

        $answerTitle = 'Yes';
        $nextQuestionId = Uuid::v7();
        $productIdRestrictions = [Uuid::v7()];

        // ACT
        $result = $this->subject->addAnswerToQuestion($question->getId(), $answerTitle, $nextQuestionId, $productIdRestrictions);

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result->getId());
        $this->assertSame($question, $result->question);
        $this->assertSame($answerTitle, $result->title);
        $this->assertSame($nextQuestionId, $result->getNextQuestionId());
        $this->assertEquals($productIdRestrictions, $result->getProductIdRestrictions());
    }

    public function testRemoveAnswerFromQuestion_WithUnknownQuestionId_ThrowsQuestionNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addQuestion('Test question');

        $unknownQuestionId = Uuid::v7();

        // ASSERT
        $this->expectException(QuestionNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Question %s not found.', $unknownQuestionId));

        // ARRANGE & ACT
        $this->subject->removeAnswerFromQuestion($unknownQuestionId, Uuid::v7());
    }

    public function testRemoveAnswerFromQuestion_WithUnknownAnswerId_ThrowsAnswerNotFoundException(): void
    {
        // ARRANGE
        $question = $this->subject->addQuestion('Test question');

        $unknownAnswerId = Uuid::v7();

        // ASSERT
        $this->expectException(AnswerNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Answer %s not found.', $unknownAnswerId));

        // ARRANGE & ACT
        $this->subject->removeAnswerFromQuestion($question->getId(), $unknownAnswerId);
    }

    public function testRemoveAnswerFromQuestion_WithValidData_RemovesAnswer(): void
    {
        // ARRANGE
        $question = $this->subject->addQuestion('Test question');
        $answer = $this->subject->addAnswerToQuestion($question->getId(), 'Yes');

        // ACT
        $this->subject->removeAnswerFromQuestion($question->getId(), $answer->getId());

        // ASSERT
        $this->assertEmpty($question->getAnswers());
    }

    public function testGetProductIdRestrictionsFromAnswerIds_WithNoAnswerIds_ReturnsEmptyArray(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getProductIdRestrictionsFromAnswerIds([]);

        // ASSERT
        $this->assertEmpty($result);
    }

    public function testGetProductIdRestrictionsFromAnswerIds_WithUnknownAnswerId_ThrowsQuestionNotAnsweredException(): void
    {
        // ARRANGE
        $questionTitle = 'Test question';
        $question = $this->subject->addQuestion($questionTitle);

        // ASSERT
        $this->expectException(QuestionNotAnsweredException::class);
        $this->expectExceptionMessage(sprintf('Question %s - "%s" not answered.', $question->getId(), $questionTitle));

        // ACT
        $this->subject->getProductIdRestrictionsFromAnswerIds([Uuid::v7()]);
    }

    public function testGetProductIdRestrictionsFromAnswerIds_WithMultipleAnswerIds_ThrowsQuestionAnsweredMultipleTimesException(): void
    {
        // ARRANGE
        $questionTitle = 'Test question';
        $question = $this->subject->addQuestion($questionTitle);
        $answer1 = $this->subject->addAnswerToQuestion($question->getId(), 'Yes');
        $answer2 = $this->subject->addAnswerToQuestion($question->getId(), 'No');

        // ASSERT
        $this->expectException(QuestionAnsweredMultipleTimesException::class);
        $this->expectExceptionMessage(sprintf('Question %s - "%s" answered multiple times.', $question->getId(), $questionTitle));

        // ACT
        $this->subject->getProductIdRestrictionsFromAnswerIds([$answer1->getId(), $answer2->getId()]);
    }

    public function testGetProductIdRestrictionsFromAnswerIds_WithMissingAnswerId_ThrowsQuestionNotAnsweredException(): void
    {
        // Erectile dysfunction
        $productCategoryId = Uuid::v7();

        $sildenafil50Id = Uuid::v7();
        $sildenafil100Id = Uuid::v7();
        $tadalafil10Id = Uuid::v7();
        $tadalafil20Id = Uuid::v7();

        $allProductIds = [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id, $tadalafil20Id];

        $subject = new Questionnaire($productCategoryId, 'Medical history');
        $question1 = $subject->addQuestion('1. Do you have difficulty getting or maintaining an erection?');
        $question2 = $subject->addQuestion('2. Have you tried any of the following treatments before?');
        $question2a = $subject->addQuestion('2a. Was the Viagra or Sildenafil product you tried before effective?');
        $question2b = $subject->addQuestion('2b. Was the Cialis or Tadalafil product you tried before effective?');
        $question2c = $subject->addQuestion('2c. Which is your preferred treatment?');
        $question3 = $subject->addQuestion('3. Do you have, or have you ever had, any heart or neurological conditions?');
        $question4 = $subject->addQuestion('4. Do any of the listed medical conditions apply to you?');
        $question5 = $subject->addQuestion('5. Are you taking any of the following drugs?');

        $question1Answer1 = $subject->addAnswerToQuestion($question1->getId(), 'Yes', $question2->getId());
        $question1Answer2 = $subject->addAnswerToQuestion($question1->getId(), 'No', productIdRestrictions: $allProductIds);

        $question2Answer1 = $subject->addAnswerToQuestion($question2->getId(), 'Viagra or Sildenafil', $question2a->getId());
        $question2Answer2 = $subject->addAnswerToQuestion($question2->getId(), 'Cialis or Tadalafil', $question2b->getId());
        $question2Answer3 = $subject->addAnswerToQuestion($question2->getId(), 'Both', $question2c->getId());
        // todo not sure about this
        $question2Answer4 = $subject->addAnswerToQuestion($question2->getId(), 'None of the above', $question3->getId(), [$sildenafil100Id, $tadalafil20Id]);

        // todo not sure about this
        $question2aAnswer1 = $subject->addAnswerToQuestion($question2a->getId(), 'Yes', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil100Id]);
        $question2aAnswer2 = $subject->addAnswerToQuestion($question2a->getId(), 'No', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id]);

        // todo not sure about this
        $question2bAnswer1 = $subject->addAnswerToQuestion($question2b->getId(), 'Yes', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil20Id]);
        $question2bAnswer2 = $subject->addAnswerToQuestion($question2b->getId(), 'No', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil50Id]);

        // todo not sure about this
        $question2cAnswer1 = $subject->addAnswerToQuestion($question2c->getId(), 'Viagra or Sildenafil', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil50Id]);
        $question2cAnswer2 = $subject->addAnswerToQuestion($question2c->getId(), 'Cialis or Tadalafil', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id]);
        $question2cAnswer3 = $subject->addAnswerToQuestion($question2c->getId(), 'None of the above', $question3->getId(), [$sildenafil50Id, $tadalafil10Id]);

        $question3Answer1 = $subject->addAnswerToQuestion($question3->getId(), 'Yes', productIdRestrictions: $allProductIds);
        $question3Answer2 = $subject->addAnswerToQuestion($question3->getId(), 'No', $question4->getId());

        // ASSERT
        $this->expectException(QuestionNotAnsweredException::class);
        $this->expectExceptionMessage(sprintf('Question %s - "%s" not answered.', $question2->getId(), '2. Have you tried any of the following treatments before?'));

        // ACT
        $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2cAnswer3->getId(), $question3Answer2->getId()]);
    }

    public function testGetProductIdRestrictionsFromAnswerIds_WithValidData_ReturnsArray(): void
    {
        // ARRANGE

        // Erectile dysfunction
        $productCategoryId = Uuid::v7();

        $sildenafil50Id = Uuid::v7();
        $sildenafil100Id = Uuid::v7();
        $tadalafil10Id = Uuid::v7();
        $tadalafil20Id = Uuid::v7();

        $allProductIds = [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id, $tadalafil20Id];

        $subject = new Questionnaire($productCategoryId, 'Medical history');
        $question1 = $subject->addQuestion('1. Do you have difficulty getting or maintaining an erection?');
        $question2 = $subject->addQuestion('2. Have you tried any of the following treatments before?');
        $question2a = $subject->addQuestion('2a. Was the Viagra or Sildenafil product you tried before effective?');
        $question2b = $subject->addQuestion('2b. Was the Cialis or Tadalafil product you tried before effective?');
        $question2c = $subject->addQuestion('2c. Which is your preferred treatment?');
        $question3 = $subject->addQuestion('3. Do you have, or have you ever had, any heart or neurological conditions?');
        $question4 = $subject->addQuestion('4. Do any of the listed medical conditions apply to you?');
        $question5 = $subject->addQuestion('5. Are you taking any of the following drugs?');

        $question1Answer1 = $subject->addAnswerToQuestion($question1->getId(), 'Yes', $question2->getId());
        $question1Answer2 = $subject->addAnswerToQuestion($question1->getId(), 'No', productIdRestrictions: $allProductIds);

        $question2Answer1 = $subject->addAnswerToQuestion($question2->getId(), 'Viagra or Sildenafil', $question2a->getId());
        $question2Answer2 = $subject->addAnswerToQuestion($question2->getId(), 'Cialis or Tadalafil', $question2b->getId());
        $question2Answer3 = $subject->addAnswerToQuestion($question2->getId(), 'Both', $question2c->getId());
        // todo not sure about this
        $question2Answer4 = $subject->addAnswerToQuestion($question2->getId(), 'None of the above', $question3->getId(), [$sildenafil100Id, $tadalafil20Id]);

        // todo not sure about this
        $question2aAnswer1 = $subject->addAnswerToQuestion($question2a->getId(), 'Yes', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil100Id]);
        $question2aAnswer2 = $subject->addAnswerToQuestion($question2a->getId(), 'No', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id]);

        // todo not sure about this
        $question2bAnswer1 = $subject->addAnswerToQuestion($question2b->getId(), 'Yes', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil20Id]);
        $question2bAnswer2 = $subject->addAnswerToQuestion($question2b->getId(), 'No', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil50Id]);

        // todo not sure about this
        $question2cAnswer1 = $subject->addAnswerToQuestion($question2c->getId(), 'Viagra or Sildenafil', $question3->getId(), [$tadalafil10Id, $tadalafil20Id, $sildenafil50Id]);
        $question2cAnswer2 = $subject->addAnswerToQuestion($question2c->getId(), 'Cialis or Tadalafil', $question3->getId(), [$sildenafil50Id, $sildenafil100Id, $tadalafil10Id]);
        $question2cAnswer3 = $subject->addAnswerToQuestion($question2c->getId(), 'None of the above', $question3->getId(), [$sildenafil50Id, $tadalafil10Id]);

        $question3Answer1 = $subject->addAnswerToQuestion($question3->getId(), 'Yes', productIdRestrictions: $allProductIds);
        $question3Answer2 = $subject->addAnswerToQuestion($question3->getId(), 'No', $question4->getId());

        $question4Answer1 = $subject->addAnswerToQuestion($question4->getId(), 'Significant liver problems (such as cirrhosis of the liver) or kidney problems', productIdRestrictions: $allProductIds);
        $question4Answer2 = $subject->addAnswerToQuestion($question4->getId(), 'Currently prescribed GTN, Isosorbide mononitrate, Isosorbide dinitrate , Nicorandil (nitrates) or Rectogesic ointment', productIdRestrictions: $allProductIds);
        $question4Answer3 = $subject->addAnswerToQuestion($question4->getId(), 'Abnormal blood pressure (lower than 90/50 mmHg or higher than 160/90 mmHg)', productIdRestrictions: $allProductIds);
        $question4Answer4 = $subject->addAnswerToQuestion($question4->getId(), "Condition affecting your penis (such as Peyronie's Disease, previous injuries or an inability to retract your foreskin)", productIdRestrictions: $allProductIds);
        $question4Answer5 = $subject->addAnswerToQuestion($question4->getId(), "I don't have any of these conditions", $question5->getId());

        $question5Answer1 = $subject->addAnswerToQuestion($question5->getId(), 'Alpha-blocker medication such as Alfuzosin, Doxazosin, Tamsulosin, Prazosin, Terazosin or over-the-counter Flomax', productIdRestrictions: $allProductIds);
        $question5Answer2 = $subject->addAnswerToQuestion($question5->getId(), 'Riociguat or other guanylate cyclase stimulators (for lung problems)', productIdRestrictions: $allProductIds);
        $question5Answer3 = $subject->addAnswerToQuestion($question5->getId(), 'Saquinavir, Ritonavir or Indinavir (for HIV)', productIdRestrictions: $allProductIds);
        $question5Answer4 = $subject->addAnswerToQuestion($question5->getId(), 'Cimetidine (for heartburn)', productIdRestrictions: $allProductIds);
        $question5Answer5 = $subject->addAnswerToQuestion($question5->getId(), "I don't take any of these drugs");

        // ACT & ASSERT

        // todo change this test to use a data provider
        // q1 - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer2->getId()]);
        $this->assertEquals([$sildenafil50Id, $sildenafil100Id, $tadalafil10Id, $tadalafil20Id], $result);

        // q2 - a1, q2a - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer1->getId(), $question2aAnswer1->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$tadalafil10Id, $tadalafil20Id, $sildenafil100Id], $result);

        // q2 - a1, q2a - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer1->getId(), $question2aAnswer2->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$sildenafil50Id, $sildenafil100Id, $tadalafil10Id], $result);

        // q2 - a2, q2b - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer2->getId(), $question2bAnswer1->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$sildenafil50Id, $sildenafil100Id, $tadalafil20Id], $result);

        // q2 - a2, q2b - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer2->getId(), $question2bAnswer2->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$tadalafil10Id, $tadalafil20Id, $sildenafil50Id], $result);

        // q2 - a3, q2c - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer3->getId(), $question2cAnswer1->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$tadalafil10Id, $tadalafil20Id, $sildenafil50Id], $result);

        // q2 - a3, q2c - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer3->getId(), $question2cAnswer2->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$sildenafil50Id, $sildenafil100Id, $tadalafil10Id], $result);

        // q2 - a3, q2c - a3
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer3->getId(), $question2cAnswer3->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$sildenafil50Id, $tadalafil10Id], $result);

        // q2 - a4
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer2->getId(), $question4Answer5->getId(), $question5Answer5->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id], $result);

        // q3 - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q4 - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer1->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q4 - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer2->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q4 - a3
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer3->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q4 - a4
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer4->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q5 - a1
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer5->getId(), $question5Answer1->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q5 - a2
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer5->getId(), $question5Answer2->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q5 - a3
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer5->getId(), $question5Answer3->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);

        // q5 - a4
        $result = $subject->getProductIdRestrictionsFromAnswerIds([$question1Answer1->getId(), $question2Answer4->getId(), $question3Answer1->getId(), $question4Answer5->getId(), $question5Answer4->getId()]);
        $this->assertEquals([$sildenafil100Id, $tadalafil20Id, $sildenafil50Id, $tadalafil10Id], $result);
    }
}