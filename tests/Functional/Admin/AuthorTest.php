<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\AuthorCrudController;
use App\Entity\Administrator;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthorTest extends WebTestCase
{
    public function testIfAuthorsAreListed(): void
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
                ->setController(AuthorCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
        $this->assertResponseIsSuccessful();

        $this->assertCount(9, $crawler->filter('article.content table tbody tr'));
    }

    public function testIfAuthorIsShown(): void
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
                ->setController(AuthorCrudController::class)
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
            'last_name+1',
            $crawler->filter('dl.datalist div:nth-child(2) dd')->text()
        );
        $this->assertStringContainsString(
            'first_name+1',
            $crawler->filter('dl.datalist div:nth-child(3) dd')->text()
        );
    }

    public function testIfAuthorIsUpdated(): void
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
                ->setController(AuthorCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId(1)
                ->generateUrl()
        );

        $client->submitForm('Sauvegarder les modifications', [
            'Author[lastName]' => 'Modifié',
            'Author[firstName]' => 'Modifié',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $author = $entityManager->find(Author::class, 1);

        $this->assertEquals('Modifié', $author->getLastName());
        $this->assertEquals('Modifié', $author->getFirstName());
    }

    public function testIfAuthorIsCreated(): void
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
                ->setController(AuthorCrudController::class)
                ->setAction(Action::NEW)
                ->generateUrl()
        );

        $client->submitForm('Créer', [
            'Author[firstName]' => 'first_name+10',
            'Author[lastName]' => 'last_name+10',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        /** @var Author $author */
        $author = $entityManager->getRepository(Author::class)->findBy([], ['id' => 'desc'])[0];
        $this->assertEquals('last_name+10', $author->getLastName());
        $this->assertEquals('first_name+10', $author->getFirstName());
    }

    public function testIfAuthorIsDeleted(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $author = new Author();
        $author->setFirstName('first_name+100');
        $author->setLastName('last_name+100');
        $entityManager->persist($author);
        $entityManager->flush();

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->loginUser($entityManager->find(Administrator::class, 1), 'admin');

        $crawler = $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(AuthorCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($author->getId())
                ->generateUrl()
        );

        $client->request(
            'POST',
            $adminUrlGenerator
                ->setController(AuthorCrudController::class)
                ->setAction(Action::DELETE)
                ->setEntityId($author->getId())
                ->generateUrl(),
            ['token' => $crawler->filter('form#delete-form input')->attr('value')]
        );

        $this->assertResponseRedirects();

        $client->followRedirect();

        $this->assertNull($entityManager->find(Author::class, $author->getId()));
    }
}
