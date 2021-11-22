<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addKind('genre+'.$i));
        }
        $manager->flush();
    }

    private function addKind(string $tilte): Genre
    {
        $genre = new Genre();
        $genre->setTitle($tilte);

        $this->addReference($tilte, $genre);

        return $genre;
    }
}
