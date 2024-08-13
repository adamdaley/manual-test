<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Answer;
use App\Core\Domain\Model\Questionnaire\Exception\AnswerNotFoundException;
use App\Core\Domain\Model\Questionnaire\Exception\MultipleAnswersFoundException;
use App\Core\Domain\Model\Questionnaire\Question;
use App\Core\Domain\Model\Questionnaire\Questionnaire;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class QuestionTest extends TestCase
{
    use QuestionnaireHelperTrait;

    private Questionnaire $questionnaire;
    private Question $subject;

    public function setUp(): void
    {
        $this->questionnaire = $this->generateQuestionnaire();
        $this->subject = new Question($this->questionnaire, 'What is your name?');
    }

    public function testConstruct_WithEmptyTitle_ThrowsInvalidArgumentException(): void
    {
        // ARRANGE
        $questionnaire = $this->generateQuestionnaire();

        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ACT
        new Question($questionnaire, '');
    }

    public function testConstruct_WithValidData_ReturnsQuestionInstance(): void
    {
        // ARRANGE, ACT & ASSERT
        $this->assertInstanceOf(Question::class, $this->subject);
        $this->assertInstanceOf(UuidV7::class, $this->subject->getId());
        $this->assertSame($this->questionnaire, $this->subject->questionnaire);
        $this->assertSame('What is your name?', $this->subject->title);
        $this->assertInstanceOf(Collection::class, $this->subject->getAnswers());
        $this->assertEmpty($this->subject->getAnswers());
    }

    public function testGetId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testGetAnswers_WithValidData_ReturnsCollection(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getAnswers();

        // ASSERT
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetAnswerById_WithAnUnknownId_ThrowsAnswerNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addAnswer('Adam');
        $this->subject->addAnswer('Tony');

        $unknownAnswerId = Uuid::v7();

        // ASSERT
        $this->expectException(AnswerNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Answer %s not found.', $unknownAnswerId));

        // ACT
        $this->subject->getAnswerById($unknownAnswerId);
    }

    public function testGetAnswerById_WithValidData_ReturnsAnswerInstance(): void
    {
        // ARRANGE
        $answer1 = $this->subject->addAnswer('Adam');
        $this->subject->addAnswer('Tony');

        // ACT
        $result = $this->subject->getAnswerById($answer1->getId());

        // ASSERT
        $this->assertSame($answer1, $result);
    }

    public function testGetAnswerByIds_WithNoMatchingAnswerIds_ThrowsAnswerNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addAnswer('Adam');
        $this->subject->addAnswer('Tony');

        $invalidAnswerId = Uuid::v7();

        // ASSERT
        $this->expectException(AnswerNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Answer not found for question %s given answer ids %s',
                $this->subject->getId(),
                $invalidAnswerId,
            ),
        );

        // ACT
        $this->subject->getAnswerByIds([$invalidAnswerId]);
    }

    public function testGetAnswerByIds_WithMultipleMatchingAnswerIds_ThrowsMultipleAnswersFoundException(): void
    {
        // ARRANGE
        $answer1 = $this->subject->addAnswer('Adam');
        $answer2 = $this->subject->addAnswer('Tony');

        // ASSERT
        $this->expectException(MultipleAnswersFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Multiple answers found for question %s given answer ids %s',
                $this->subject->getId(),
                implode(', ', [$answer1->getId(), $answer2->getId()]),
            ),
        );

        // ACT
        $this->subject->getAnswerByIds([$answer1->getId(), $answer2->getId()]);
    }

    public function testGetAnswerByIds_WithValidData_ReturnsAnswerInstance(): void
    {
        // ARRANGE
        $answer1 = $this->subject->addAnswer('Adam');
        $this->subject->addAnswer('Tony');

        // ACT
        $result = $this->subject->getAnswerByIds([$answer1->getId()]);

        // ASSERT
        $this->assertSame($answer1, $result);
    }

    public function testAddAnswer_WithValidData_ReturnsAnswerInstance(): void
    {
        // ARRANGE
        $answerTitle = 'Adam';
        $nextQuestionId = Uuid::v7();
        $productIdRestrictions = [Uuid::v7()];

        // ACT
        $result = $this->subject->addAnswer($answerTitle, $nextQuestionId, $productIdRestrictions);

        // ASSERT
        $this->assertInstanceOf(Answer::class, $result);
        $this->assertInstanceOf(UuidV7::class, $result->getId());
        $this->assertSame($this->subject, $result->question);
        $this->assertSame($answerTitle, $result->title);
        $this->assertSame($nextQuestionId, $result->getNextQuestionId());
        $this->assertEquals($productIdRestrictions, $result->getProductIdRestrictions());
    }

    public function testRemoveAnswer_WithUnknownAnswerId_ThrowsAnswerNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addAnswer('Adam');

        // ASSERT
        $this->expectException(AnswerNotFoundException::class);

        // ACT
        $this->subject->removeAnswer(Uuid::v7());
    }

    public function testRemoveAnswer_WithValidData_RemovesAnswerInstance(): void
    {
        // ARRANGE
        $answer = $this->subject->addAnswer('Adam');

        // ACT
        $this->subject->removeAnswer($answer->getId());

        // ASSERT
        $this->assertEmpty($this->subject->getAnswers());
    }

    public function testJsonSerialize_WithValidData_ReturnsArray(): void
    {
        // ARRANGE
        $answer1 = $this->subject->addAnswer('Adam');
        $answer2 = $this->subject->addAnswer('Tony');

        // ACT
        $result = $this->subject->jsonSerialize();

        // ASSERT
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($this->subject->getId(), $result['id']);
        $this->assertArrayHasKey('title', $result);
        $this->assertSame('What is your name?', $result['title']);
        $this->assertArrayHasKey('answers', $result);

        $this->assertIsArray($result['answers']);
        $this->assertCount(2, $result['answers']);

        $this->assertArrayHasKey('id', $result['answers'][0]);
        $this->assertSame($answer1->getId(), $result['answers'][0]['id']);
        $this->assertArrayHasKey('title', $result['answers'][0]);
        $this->assertSame($answer1->title, $result['answers'][0]['title']);
        $this->assertArrayHasKey('nextQuestionId', $result['answers'][0]);
        $this->assertSame($answer1->getNextQuestionId(), $result['answers'][0]['nextQuestionId']);
        $this->assertArrayHasKey('productIdRestrictions', $result['answers'][0]);
        $this->assertSame($answer1->getProductIdRestrictions(), $result['answers'][0]['productIdRestrictions']);

        $this->assertArrayHasKey('id', $result['answers'][1]);
        $this->assertSame($answer2->getId(), $result['answers'][1]['id']);
        $this->assertArrayHasKey('title', $result['answers'][1]);
        $this->assertSame($answer2->title, $result['answers'][1]['title']);
        $this->assertArrayHasKey('nextQuestionId', $result['answers'][1]);
        $this->assertSame($answer2->getNextQuestionId(), $result['answers'][1]['nextQuestionId']);
        $this->assertArrayHasKey('productIdRestrictions', $result['answers'][1]);
        $this->assertSame($answer2->getProductIdRestrictions(), $result['answers'][1]['productIdRestrictions']);
    }
}