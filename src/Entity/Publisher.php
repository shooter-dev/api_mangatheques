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
            'path' => '/publishers',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
    itemOperations: [
        'get' => [
            'path' => '/publisher/{id}',
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
    ],
)]
#[Entity]
#[Table(name: 'publisher')]
class Publisher
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    protected ?int $id = null;

    #[Column(unique: true)]
    protected string $title;

    #[Column()]
    protected bool $closed;

    #[Column()]
    protected bool $noAmazone;

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

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): void
    {
        $this->closed = $closed;
    }

    public function isNoAmazone(): bool
    {
        return $this->noAmazone;
    }

    public function setNoAmazone(bool $noAmazone): void
    {
        $this->noAmazone = $noAmazone;
    }
}
