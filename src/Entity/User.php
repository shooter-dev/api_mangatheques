<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    collectionOperations: [
        'post' => [
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
    itemOperations: ['get'],
)]
#[Entity]
#[Table(name: 'user')]
#[UniqueEntity(fields: 'email')]
class User extends BaseUser
{
    /**
     * @return array<array-key, string>
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
}
