<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{

    private ?Review $testReview = null;

    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testShouldNotShowVideoGame(): void
    {
        $this->get('/jeu-video-blablablabla');
        self::assertResponseStatusCodeSame(404);
    }

    public function testThatAddingAReviewWorks(): void
    {
        $this->login('testuser@gmail.com');
        $this->get('/jeu-video-1');
        self::assertResponseIsSuccessful();

        $this->client->submitForm(
            'Poster',
            [
                'review[comment]' => "Clair obscur c'est pas mal",
                'review[rating]' => 5,
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'testuser');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '5');

        $this->testReview = $this->getEntityManager()
            ->getRepository(Review::class)
            ->findOneBy([
                'comment' => "Clair obscur c'est pas mal",
                'rating' => 5
            ]);

    }

    public function testThatFormIsHiddenWhenUserHasAlreadyReviewed(): void
    {
        $videogames = $this->getEntityManager()->getRepository(VideoGame::class)->findAll();
        $videoGame = $videogames[1];
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'testuser@gmail.com']);
        $review = (new Review())
            ->setUser($user)
            ->setVideoGame($videoGame)
            ->setRating(5)
            ->setComment('Han shot first');

        $videoGame->addReview($review);

        $this->login('testuser@gmail.com');
        $this->get(sprintf('/%s', $videoGame->getSlug()));

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[name="review"]');
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'testuser');
    }

    public function testThatUnloggedUserCannotPostAReview(): void
    {
        $this->get('/jeu-video-2');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[name="review"]');

        $response = $this->client->request('POST', '/jeu-video-2', [
            'review' => [
                'comment' => "Clair obscur c'est pas mal",
                'rating' => 5,
            ],
        ])->html();

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertStringContainsString('<h1 class="mb-4 text-center text-uppercase">Jeu vidéo 2</h1>', $response);
        self::assertStringNotContainsString("Clair obscur c'est pas mal", $response);
    }

    protected function tearDown(): void
    {
        if ($this->testReview) {
            $em = $this->getEntityManager();
            $em->remove($this->testReview);
            $em->flush();
        }

        parent::tearDown();
    }
}
