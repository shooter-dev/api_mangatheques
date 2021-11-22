<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'path' => '/authors',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
    itemOperations: [
        'get' => [
            'path' => '/author/{id}',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
)]
#[Entity]
#[Table(name: 'author')]
class Author
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    protected ?int $id = null;

    #[Column()]
    protected string $firstName;

    #[Column()]
    protected string $lastName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
