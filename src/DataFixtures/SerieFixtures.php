<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SerieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addSerie('serie+'.$i));
        }
        $manager->flush();
    }

    private function addSerie(string $tilte): Serie
    {
        $serie = new Serie();
        $serie->setTitle($tilte);
        $serie->setAdulteContent(boolval(rand(0, 1)));

        $this->addReference($tilte, $serie);

        return $serie;
    }
}
