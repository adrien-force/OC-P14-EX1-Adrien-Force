<?php

namespace App\Tests\Unit\Rating;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-type Ratings array<int>
 * @phpstan-type Counts array<string, int>
 */
final class RatingHandlerTest extends TestCase
{
    protected RatingHandler $ratingHandler;
    protected MockObject&VideoGame $videoGame;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ratingHandler = new RatingHandler();
        $this->videoGame = $this->createMock(VideoGame::class);
    }

    /**
     * @dataProvider provideAverageCalculationCases
     *
     * @param array<int> $ratings
     */
    public function testThatCalculateAverageReturnsCorrectAverage(array $ratings, ?int $expectedAverage): void
    {
        $reviews = new ArrayCollection();
        foreach ($ratings as $rating) {
            $review = $this->createMock(Review::class);
            $review->method('getRating')->willReturn($rating);
            $reviews->add($review);
        }

        $videoGame = new VideoGame();
        foreach ($reviews as $review) {
            $videoGame->addReview($review);
        }

        $this->ratingHandler->calculateAverage($videoGame);

        self::assertSame($expectedAverage, $videoGame->getAverageRating());
    }

    public static function provideAverageCalculationCases(): \Generator
    {
        yield 'no reviews' => [
            [],
            null,
        ];
        yield 'single review' => [
            [4],
            4,
        ];
        yield 'multiple reviews, average rounds up' => [
            [3, 4, 4],
            4,
        ];
        yield 'multiple reviews, average is integer' => [
            [2, 4],
            3,
        ];
        yield 'all minimum ratings' => [
            [1, 1, 1],
            1,
        ];
        yield 'all maximum ratings' => [
            [5, 5, 5, 5],
            5,
        ];
        yield 'large number of reviews' => [
            array_merge(...array_fill(0, 999, range(1, 5))),
            3,
        ];
    }

    /**
     * @dataProvider provideRatingsCountCases
     *
     * @param Ratings $ratings
     * @param Counts  $expectedCounts
     */
    public function testThatCountRatingsSetsCorrectCounts(array $ratings, array $expectedCounts): void
    {
        $reviews = new ArrayCollection();
        foreach ($ratings as $rating) {
            $review = $this->createMock(Review::class);
            $review->method('getRating')->willReturn($rating);
            $reviews->add($review);
        }

        $this->videoGame->method('getReviews')->willReturn($reviews);

        $numberOfRatingsPerValue = new NumberOfRatingPerValue();
        $this->videoGame
            ->method('getNumberOfRatingsPerValue')
            ->willReturn($numberOfRatingsPerValue);

        $this->ratingHandler->countRatingsPerValue($this->videoGame);

        self::assertSame($expectedCounts['One'] ?? 0, $numberOfRatingsPerValue->getNumberOfOne());
        self::assertSame($expectedCounts['Two'] ?? 0, $numberOfRatingsPerValue->getNumberOfTwo());
        self::assertSame($expectedCounts['Three'] ?? 0, $numberOfRatingsPerValue->getNumberOfThree());
        self::assertSame($expectedCounts['Four'] ?? 0, $numberOfRatingsPerValue->getNumberOfFour());
        self::assertSame($expectedCounts['Five'] ?? 0, $numberOfRatingsPerValue->getNumberOfFive());
    }

    public static function provideRatingsCountCases(): \Generator
    {
        yield 'no reviews' => [
            [],
            [],
        ];
        yield 'single review' => [
            [4],
            ['Four' => 1],
        ];
        yield 'multiple reviews' => [
            [1, 2, 3, 4, 5],
            ['One' => 1, 'Two' => 1, 'Three' => 1, 'Four' => 1, 'Five' => 1],
        ];
        yield 'multiple reviews with duplicates' => [
            [1, 1, 2, 3, 4, 5, 5],
            ['One' => 2, 'Two' => 1, 'Three' => 1, 'Four' => 1, 'Five' => 2],
        ];
        yield 'all minimum ratings' => [
            [1, 1, 1],
            ['One' => 3],
        ];
        yield 'all maximum ratings' => [
            [5, 5, 5, 5],
            ['Five' => 4],
        ];
        yield 'large number of reviews' => [
            array_merge(...array_fill(0, 999, range(1, 5))),
            ['One' => 999, 'Two' => 999, 'Three' => 999, 'Four' => 999, 'Five' => 999],
        ];
    }

    /**
     * In this test, we will not mock VideoGame because there's some issues
     * with the mock method GetReviews after the first call, where it still returns the first instruction.
     * Mock WillReturnOnConsecutiveCalls is risky to use as it's called multiple times inside the same method.
     *
     * @dataProvider provideRatingsCountCasesTwoTimes
     *
     * @param array<Ratings>     $ratings
     * @param array<int, Counts> $expectedCounts
     */
    public function testThatCountRatingClearsPreviousCounts(array $ratings, array $expectedCounts): void
    {
        $videoGame = new VideoGame();
        $reviews = new ArrayCollection();
        foreach ($ratings[0] as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $reviews->add($review);
            $videoGame->addReview($review);
        }

        $this->ratingHandler->countRatingsPerValue($videoGame);
        $numberOfRatingsPerValue = $videoGame->getNumberOfRatingsPerValue();

        // Assert first set of counts
        self::assertSame($expectedCounts[0]['One'] ?? 0, $numberOfRatingsPerValue->getNumberOfOne());
        self::assertSame($expectedCounts[0]['Two'] ?? 0, $numberOfRatingsPerValue->getNumberOfTwo());
        self::assertSame($expectedCounts[0]['Three'] ?? 0, $numberOfRatingsPerValue->getNumberOfThree());
        self::assertSame($expectedCounts[0]['Four'] ?? 0, $numberOfRatingsPerValue->getNumberOfFour());
        self::assertSame($expectedCounts[0]['Five'] ?? 0, $numberOfRatingsPerValue->getNumberOfFive());

        // Second set of ratings
        $reviews2 = new ArrayCollection();
        foreach ($ratings[1] as $rating2) {
            $review = new Review();
            $review->setRating($rating2);
            $reviews2->add($review);
            $videoGame->addReview($review);
        }

        $this->ratingHandler->countRatingsPerValue($videoGame);

        self::assertSame($expectedCounts[1]['One'] ?? 0, $numberOfRatingsPerValue->getNumberOfOne());
        self::assertSame($expectedCounts[1]['Two'] ?? 0, $numberOfRatingsPerValue->getNumberOfTwo());
        self::assertSame($expectedCounts[1]['Three'] ?? 0, $numberOfRatingsPerValue->getNumberOfThree());
        self::assertSame($expectedCounts[1]['Four'] ?? 0, $numberOfRatingsPerValue->getNumberOfFour());
        self::assertSame($expectedCounts[1]['Five'] ?? 0, $numberOfRatingsPerValue->getNumberOfFive());
    }

    public function provideRatingsCountCasesTwoTimes(): \Generator
    {
        yield 'no reviews' => [
            [[], []],
            [[], []],
        ];
        yield 'single review' => [
            [[4], [2]],
            [['Four' => 1], ['Two' => 1, 'Four' => 1]],
        ];
        yield 'multiple reviews' => [
            [[1, 1, 1, 1, 1], [2, 2, 2, 2, 2]],
            [['One' => 5, 'Two' => 0, 'Three' => 0, 'Four' => 0, 'Five' => 0], ['One' => 5, 'Two' => 5, 'Three' => 0, 'Four' => 0, 'Five' => 0]],
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->ratingHandler, $this->videoGame);
    }
}
