<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity]
#[UniqueEntity(fields: 'email')]
class Administrator extends BaseUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @return array<array-key, string>
     */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN', 'ROLE_USER'];
    }
}
