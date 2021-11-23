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
            'path' => '/series',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
    itemOperations: [
        'get' => [
            'path' => '/serie/{id}',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
)]
#[Entity]
#[Table(name: 'serie')]
class Serie
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    protected ?int $id = null;

    #[Column(unique: true)]
    protected string $title;

    #[Column()]
    protected bool $adulteContent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isAdulteContent(): bool
    {
        return $this->adulteContent;
    }

    public function setAdulteContent(bool $adulteContent): void
    {
        $this->adulteContent = $adulteContent;
    }
}
