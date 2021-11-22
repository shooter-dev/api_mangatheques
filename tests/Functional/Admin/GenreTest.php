<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\GenreCrudController;
use App\Entity\Administrator;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GenreTest extends WebTestCase
{
    public function testIfGenresAreListed(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $crawler = $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(GenreCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );

        $this->assertResponseIsSuccessful();

        $this->assertCount(9, $crawler->filter('article.content table tbody tr'));
    }

    public function testIfGenreIsShown(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $crawler = $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(GenreCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId(1)
                ->generateUrl()
        );

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString(
            '1',
            $crawler->filter('dl.datalist div:first-child dd')->text()
        );
        $this->assertStringContainsString(
            'genre+1',
            $crawler->filter('dl.datalist div:nth-child(2) dd')->text()
        );
    }

    public function testIfGenreIsUpdated(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(GenreCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId(1)
                ->generateUrl()
        );

        $client->submitForm('Sauvegarder les modifications', [
            'Genre[title]' => 'Modifié',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $genre = $entityManager->find(Genre::class, 1);

        $this->assertEquals('Modifié', $genre->getTitle());
    }
}
