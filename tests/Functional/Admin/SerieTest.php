<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\SerieCrudController;
use App\Entity\Administrator;
use App\Entity\Serie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SerieTest extends WebTestCase
{
    public function testIfSeriesAreListed(): void
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
                ->setController(SerieCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );

        $this->assertResponseIsSuccessful();

        $this->assertCount(9, $crawler->filter('article.content table tbody tr'));
    }

    public function testIfSerieIsShown(): void
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
                ->setController(SerieCrudController::class)
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
            'serie+1',
            $crawler->filter('dl.datalist div:nth-child(2) dd')->text()
        );
        'Oui' === $crawler->filter('dl.datalist div:nth-child(3) dd')->text()
            ? $this->assertStringContainsString(
            'oui', $crawler->filter(
            'dl.datalist div:nth-child(3) dd')->text()
        )
            : $this->assertStringContainsString(
            'Non', $crawler->filter(
            'dl.datalist div:nth-child(3) dd')->text()
        );
    }

    public function testIfSerieIsUpdated(): void
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
                ->setController(SerieCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId(1)
                ->generateUrl()
        );

        $client->submitForm('Sauvegarder les modifications', [
            'Serie[title]' => 'Modifié',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $serie = $entityManager->find(Serie::class, 1);

        $this->assertEquals('Modifié', $serie->getTitle());
    }

    public function testIfSerieIsCreated(): void
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
                ->setController(SerieCrudController::class)
                ->setAction(Action::NEW)
                ->generateUrl()
        );
        /** @var bool $valBool */
        $valBool = boolval(rand(0, 1));
        $client->submitForm('Créer', [
            'Serie[title]' => 'serie+10',
            'Serie[adulteContent]' => $valBool,
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        /** @var Serie $serie */
        $serie = $entityManager->getRepository(Serie::class)->findBy([], ['id' => 'desc'])[0];
        $this->assertEquals('serie+10', $serie->getTitle());
        $this->assertEquals($valBool, $serie->isAdulteContent());
    }

    public function testIfSerieIsDeleted(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var bool $valBool */
        $valBool = boolval(rand(0, 1));

        $serie = new Serie();
        $serie->setTitle('serie+100');
        $serie->setAdulteContent($valBool);
        $entityManager->persist($serie);
        $entityManager->flush();

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $crawler = $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(SerieCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($serie->getId())
                ->generateUrl()
        );

        $client->request(
            'POST',
            $adminUrlGenerator
                ->setController(SerieCrudController::class)
                ->setAction(Action::DELETE)
                ->setEntityId($serie->getId())
                ->generateUrl(),
            ['token' => $crawler->filter('form#delete-form input')->attr('value')]
        );

        $this->assertResponseRedirects();

        $client->followRedirect();

        $this->assertNull($entityManager->find(Serie::class, $serie->getId()));
    }
}
