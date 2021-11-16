<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Administrator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdministratorFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $administrator = new Administrator();
        $administrator->setFirstName('John');
        $administrator->setLastName('Doe');
        $administrator->setEmail('admin+1@email.com');
        $administrator->setPassword($this->userPasswordHasher->hashPassword($administrator, 'password'));

        $manager->persist($administrator);
        $manager->flush();
    }
}
