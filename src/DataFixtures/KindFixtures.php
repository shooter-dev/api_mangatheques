<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Kind;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class KindFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addKind('kind+'.$i));
        }
        $manager->flush();
    }

    private function addKind(string $tilte): Kind
    {
        $kind = new Kind();
        $kind->setTitle($tilte);

        $this->addReference($tilte, $kind);

        return $kind;
    }
}
