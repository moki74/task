<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
/**
 * TODO: Review Message class
 */

#[HasLifecycleCallbacks]
class Message
{
    const STATUS_SENT = 'sent';
    const STATUS_READ = 'read';

    // We don't need two primary keys, we should use one and make it not nullable

    #[Groups(['list'])]
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    private string $uuid;

    #[Groups(['list'])]
    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[Groups(['list'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    // make createdAt property automatically set on insert
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }
}
