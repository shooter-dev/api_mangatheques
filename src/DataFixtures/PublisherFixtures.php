<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class PublisherFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addPublisher('publisher+'.$i));
        }
        $manager->flush();
    }

    private function addPublisher(string $tilte): Publisher
    {
        $publisher = new Publisher();
        $publisher->setTitle($tilte);
        $publisher->setClosed(false);
        $publisher->setNoAmazone(false);

        $this->addReference($tilte, $publisher);

        return $publisher;
    }
}
