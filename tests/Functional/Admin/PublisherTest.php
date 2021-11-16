<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\PublisherCrudController;
use App\Entity\Administrator;
use App\Entity\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PublisherTest extends WebTestCase
{
    public function testIfPublishersAreListed(): void
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
                ->setController(PublisherCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );

        $this->assertResponseIsSuccessful();

        $this->assertCount(9, $crawler->filter('article.content table tbody tr'));
    }

    public function testIfPublisherIsShown(): void
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
                ->setController(PublisherCrudController::class)
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
            'publisher+1',
            $crawler->filter('dl.datalist div:nth-child(2) dd')->text()
        );
        $this->assertStringContainsString(
            'Non',
            $crawler->filter('dl.datalist div:nth-child(3) dd')->text()
        );
        $this->assertStringContainsString(
            'Non',
            $crawler->filter('dl.datalist div:nth-child(4) dd')->text()
        );
    }

    public function testIfPublisherIsUpdated(): void
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
                ->setController(PublisherCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId(1)
                ->generateUrl()
        );

        $client->submitForm('Sauvegarder les modifications', [
            'Publisher[title]' => 'Modifié',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $publisher = $entityManager->find(Publisher::class, 1);

        $this->assertEquals('Modifié', $publisher->getTitle());
    }

    public function testIfPublisherIsCreated(): void
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
                ->setController(PublisherCrudController::class)
                ->setAction(Action::NEW)
                ->generateUrl()
        );

        $client->submitForm('Créer', [
            'Publisher[title]' => 'publisher+10',
            'Publisher[closed]' => true,
            'Publisher[noAmazone]' => true,
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        /** @var Publisher $publisher */
        $publisher = $entityManager->getRepository(Publisher::class)->findBy([], ['id' => 'desc'])[0];
        $this->assertEquals('publisher+10', $publisher->getTitle());
        $this->assertEquals(true, $publisher->isClosed());
        $this->assertEquals(true, $publisher->isNoAmazone());
    }

    public function testIfPublisherIsDeleted(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $publisher = new Publisher();
        $publisher->setTitle('publisher+100');
        $publisher->setClosed(false);
        $publisher->setNoAmazone(false);
        $entityManager->persist($publisher);
        $entityManager->flush();

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $crawler = $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(PublisherCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($publisher->getId())
                ->generateUrl()
        );

        $client->request(
            'POST',
            $adminUrlGenerator
                ->setController(PublisherCrudController::class)
                ->setAction(Action::DELETE)
                ->setEntityId($publisher->getId())
                ->generateUrl(),
            ['token' => $crawler->filter('form#delete-form input')->attr('value')]
        );

        $this->assertResponseRedirects();

        $client->followRedirect();

        $this->assertNull($entityManager->find(Publisher::class, $publisher->getId()));
    }
}
