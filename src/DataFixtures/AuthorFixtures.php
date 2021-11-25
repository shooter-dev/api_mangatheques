<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addPublisher('last_name+'.$i, 'first_name+'.$i, $i));
        }
        $manager->flush();
    }

    private function addPublisher(string $lastName, string $firstName, int $i): Author
    {
        $author = new Author();
        $author->setLastName($lastName);
        $author->setFirstName($firstName);

        $this->addReference('author+'.$i, $author);

        return $author;
    }
}
