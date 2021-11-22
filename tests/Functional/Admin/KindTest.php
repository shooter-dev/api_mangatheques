<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\KindCrudController;
use App\Entity\Administrator;
use App\Entity\Kind;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class KindTest extends WebTestCase
{
    public function testIfKindsAreListed(): void
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
                ->setController(KindCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );

        $this->assertResponseIsSuccessful();

        $this->assertCount(9, $crawler->filter('article.content table tbody tr'));
    }

    public function testIfKindIsShown(): void
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
                ->setController(KindCrudController::class)
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
            'kind+1',
            $crawler->filter('dl.datalist div:nth-child(2) dd')->text()
        );
    }

    public function testIfKindIsUpdated(): void
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
                ->setController(KindCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId(1)
                ->generateUrl()
        );

        $client->submitForm('Sauvegarder les modifications', [
            'Kind[title]' => 'Modifié',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $kind = $entityManager->find(Kind::class, 1);

        $this->assertEquals('Modifié', $kind->getTitle());
    }

    public function testIfKindIsCreated(): void
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
                ->setController(KindCrudController::class)
                ->setAction(Action::NEW)
                ->generateUrl()
        );

        $client->submitForm('Créer', [
            'Kind[title]' => 'kind+10',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        /** @var Kind $kind */
        $kind = $entityManager->getRepository(Kind::class)->findBy([], ['id' => 'desc'])[0];
        $this->assertEquals('kind+10', $kind->getTitle());
    }
}
