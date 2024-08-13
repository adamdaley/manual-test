<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Questionnaire;

use App\Core\Domain\Model\Questionnaire\Answer;
use App\Core\Domain\Model\Questionnaire\Question;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class AnswerTest extends TestCase
{
    use QuestionnaireHelperTrait;

    private Question $question;

    public function setUp(): void
    {
        $this->question = $this->generateQuestion();
    }

    public function testConstruct_WithEmptyAnswer_ThrowsInvalidArgumentException(): void
    {
        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ARRANGE & ACT
        new Answer($this->question, '');
    }

    public function testConstruct_WithProductIdRestrictionsThatAreNotUuidV7_ThrowsInvalidArgumentException(): void
    {
        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ARRANGE & ACT
        new Answer($this->question, 'Yes', null, [Uuid::v6()]);
    }

    public function testConstruct_WithValidData_ReturnsAnswerInstance(): void
    {
        // ARRANGE
        $nextQuestionId = Uuid::v7();
        $productRestrictionId = Uuid::v7();

        // ACT
        $result = new Answer($this->question, 'Yes', $nextQuestionId, [$productRestrictionId]);

        // ASSERT
        $this->assertInstanceOf(Answer::class, $result);
        $this->assertInstanceOf(UuidV7::class, $result->getId());
        $this->assertSame($this->question, $result->question);
        $this->assertSame('Yes', $result->title);
        $this->assertSame($nextQuestionId, $result->getNextQuestionId());
        $this->assertEquals([$productRestrictionId], $result->getProductIdRestrictions());
    }

    public function testGetId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE
        $subject = new Answer($this->question, 'Yes');

        // ACT
        $result = $subject->getId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testGetNextQuestionId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE
        $subject = new Answer($this->question, 'Yes', Uuid::v7());

        // ACT
        $result = $subject->getNextQuestionId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testGetProductIdRestrictions_WithValidData_ReturnsArrayOfUuidV7(): void
    {
        // ARRANGE
        $subject = new Answer($this->question, 'Yes', null, [Uuid::v7()]);

        // ACT
        $result = $subject->getProductIdRestrictions();

        // ASSERT
        $this->assertContainsOnlyInstancesOf(UuidV7::class, $result);
    }

    public function testJsonSerialize_WithValidData_ReturnsArray(): void
    {
        // ARRANGE
        $nextQuestionId = Uuid::v7();
        $productRestrictionId = Uuid::v7();

        $subject = new Answer($this->question, 'Yes', $nextQuestionId, [$productRestrictionId]);

        // ACT
        $result = $subject->jsonSerialize();

        // ASSERT
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($subject->getId(), $result['id']);
        $this->assertArrayHasKey('title', $result);
        $this->assertSame('Yes', $result['title']);
        $this->assertArrayHasKey('nextQuestionId', $result);
        $this->assertSame($nextQuestionId, $result['nextQuestionId']);
        $this->assertArrayHasKey('productIdRestrictions', $result);
        $this->assertEquals([$productRestrictionId], $result['productIdRestrictions']);
    }
}