<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decorated,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @param object                $data
     * @param array<string, string> $context
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User                  $data
     * @param array<string, string> $context
     */
    public function persist($data, array $context = []): User
    {
        $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPassword()));

        /** @var User $user */
        $user = $this->decorated->persist($data, $context);

        return $user;
    }

    /**
     * @param User                  $data
     * @param array<string, string> $context
     */
    public function remove($data, array $context = []): void
    {
        $this->decorated->remove($data, $context);
    }
}
