<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @phpstan-type TagInfo array{value: int, label: string}
 */
final class FilterTest extends FunctionalTestCase
{
    public function testThatHomePageDefaultPaginationIsTen(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');
        self::assertSelectorCount(10, 'article.game-card', 'Pagination is not set to 10');
    }

    public function testThatHomePagePaginationCanBeChanged(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');
        $this->client->submitForm('Filtrer', ['limit' => 25], 'GET');
        self::assertResponseIsSuccessful('Summit form failed on pagination 25');
        self::assertSelectorCount(25, 'article.game-card', 'Pagination is not set to 25');
    }

    public function testThatFilterOrderingOrdersValuesForReleaseDatesAscending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'ReleaseDate',
            'direction' => 'Ascending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by ReleaseDate ascending');

        $dates = $this->client->getCrawler()
            ->filter('article.game-card small.text-muted')
            ->each(function (Crawler $node) {
                $value = $node->nodeValue ?? '';
                assert(is_string($value));

                return \DateTime::createFromFormat(
                    'd/m/Y',
                    substr($value, 8) // Skips "Sortie: " to get only the date
                );
            });

        $sortedDates = $dates;
        sort($sortedDates);
        self::assertEquals($sortedDates, $dates, 'Release dates are not in ascending order');
    }

    public function testThatFilterOrderingOrdersValuesForReleaseDatesDescending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by ReleaseDate descending');

        $dates = $this->client->getCrawler()
            ->filter('article.game-card small.text-muted')
            ->each(function (Crawler $node) {
                $value = $node->nodeValue ?? '';
                assert(is_string($value));

                return \DateTime::createFromFormat(
                    'd/m/Y',
                    substr($value, 8) // Skips "Sortie: " to get only the date
                );
            });

        $sortedDates = $dates;
        rsort($sortedDates);
        self::assertEquals($sortedDates, $dates, 'Release dates are not in descending order');
    }

    public function testThatFilterOrderingOrdersValuesForNameAscending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'Name',
            'direction' => 'Ascending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by Name ascending');

        $names = $this->client->getCrawler()
            ->filter('article.game-card h2')
            ->each(function (Crawler $node) {
                return $node->text();
            });

        $sortedNames = $names;
        sort($sortedNames);
        self::assertEquals($sortedNames, $names, 'Names are not in ascending order');
    }

    public function testThatFilterOrderingOrdersValuesForNameDescending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'Name',
            'direction' => 'Descending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by Name descending');

        $names = $this->client->getCrawler()
            ->filter('article.game-card h2')
            ->each(function (Crawler $node) {
                return $node->text();
            });

        $sortedNames = $names;
        rsort($sortedNames);
        self::assertEquals($sortedNames, $names, 'Names are not in descending order');
    }

    public function testThatFilterOrderingOrdersValuesForRatingAscending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'Rating',
            'direction' => 'Ascending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by Rating ascending');

        $ratings = $this->client->getCrawler()
            ->filter('div.rating-square div.rating-1 span.value')
            ->each(function (Crawler $node) {
                return (int) $node->text();
            });

        $sortedRatings = $ratings;
        sort($sortedRatings);
        self::assertEquals($sortedRatings, $ratings, 'Ratings are not in ascending order');
    }

    public function testThatFilterOrderingOrdersValuesForRatingDescending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'Rating',
            'direction' => 'Descending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by Rating descending');

        $ratings = $this->client->getCrawler()
            ->filter('div.rating-square div.rating-1 span.value')
            ->each(function (Crawler $node) {
                return (int) $node->text();
            });

        $sortedRatings = $ratings;
        rsort($sortedRatings);
        self::assertEquals($sortedRatings, $ratings, 'Ratings are not in descending order');
    }

    public function testThatFilterOrderingOrdersValuesForAverageRatingAscending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'AverageRating',
            'direction' => 'Ascending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by AverageRating ascending');

        $ratings = $this->client->getCrawler()
            ->filter('div.rating-square div.rating-2 span.value')
            ->each(function (Crawler $node) {
                return (int) $node->text();
            });

        $sortedRatings = $ratings;
        sort($sortedRatings);
        self::assertEquals($sortedRatings, $ratings, 'Average ratings are not in ascending order');
    }

    public function testThatFilterOrderingOrdersValuesForAverageRatingDescending(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        $this->client->submitForm('Filtrer', [
            'sorting' => 'AverageRating',
            'direction' => 'Descending',
        ], 'GET');

        self::assertResponseIsSuccessful('Summit form failed on sorting by AverageRating descending');

        $ratings = $this->client->getCrawler()
            ->filter('div.rating-square div.rating-2 span.value')
            ->each(function (Crawler $node) {
                return (int) $node->text();
            });

        $sortedRatings = $ratings;
        rsort($sortedRatings);
        self::assertEquals($sortedRatings, $ratings, 'Average ratings are not in descending order');
    }

    public function testThatFilteringByNameReturnsCorrectEntity(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');
        $videogameName = 'Jeu vidéo 5';
        $this->client->submitForm('Filtrer', ['filter[search]' => $videogameName], 'GET');
        self::assertResponseIsSuccessful('Summit form failed on filtering by name');
        self::assertSelectorCount(1, 'article.game-card', 'Filter by name does not return one result');
        self::assertSelectorTextContains('article.game-card', $videogameName, 'Name is not found in the filtered result');
    }

    // uri 	http://127.0.0.1:8000/?page=1&limit=25&sorting=ReleaseDate&direction=Descending&filter[search]=&filter[tags][]=175&filter[tags][]=177
    public function testThatFilteringByOneTagReturnsCorrectEntity(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        // Get all available tags

        /**
         * @var TagInfo[] $selectedTags
         */
        $selectedTags = $this->client->getCrawler()
            ->filterXPath('//div[@id="filter_tags"]/div')
            ->each(function (Crawler $node) {
                /**
                 * @var TagInfo $tags
                 */
                $tags = [
                    'value' => (int) $node->filter('input')->attr('value'),
                    'label' => trim($node->filter('label')->text()),
                ];

                return $tags;
            });

        $this->client->submitForm('Filtrer', ['filter[tags]' => [$selectedTags[0]['value']]], 'GET');
        self::assertResponseIsSuccessful('Summit form failed on filtering by tag');

        // Assert that each videogame found has at least the targeted tags
        $this->client->getCrawler()
            ->filter('div.card-body')
            ->each(function (Crawler $node) use ($selectedTags) {
                $tags = $node->filter('div.game-card-tags');
                self::assertStringContainsString($selectedTags[0]['label'], $tags->text(), 'Tag is not found in the filtered result');
            });
    }

    public function testThatFilteringByMultipleTagsReturnsCorrectEntity(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful('Home page is not reachable');

        /**
         * @var TagInfo[] $selectedTags
         */
        $selectedTags = $this->client->getCrawler()
            ->filterXPath('//div[@id="filter_tags"]/div')
            ->each(function (Crawler $node) {
                return [
                    'value' => (int) $node->filter('input')->attr('value'),
                    'label' => trim($node->filter('label')->text()),
                ];
            });
        $selectedTags = array_slice($selectedTags, 0, 2);

        $this->client->submitForm('Filtrer', ['filter[tags]' => [$selectedTags[0]['value'], $selectedTags[1]['value']]], 'GET');
        self::assertResponseIsSuccessful('Summit form failed on filtering by multiple tags');

        $this->client->getCrawler()
            ->filter('div.card-body')
            ->each(function (Crawler $node) use ($selectedTags) {
                $tags = $node->filter('div.game-card-tags');
                foreach ($selectedTags as $tag) {
                    self::assertStringContainsString($tag['label'], $tags->text(), 'Tag is not found in the filtered result');
                }
            });
    }
}
