<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; ++$i) {
            $manager->persist($this->addJob('job+'.$i));
        }
        $manager->flush();
    }

    private function addJob(string $tilte): Job
    {
        $job = new Job();
        $job->setTitle($tilte);

        $this->addReference($tilte, $job);

        return $job;
    }
}
