<?php

namespace App\Entity;

use App\Enum\SessionStatusEnum;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Program $program = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Coach $coach = null;

    /**
     * @var Collection<int, SessionHistory>
     */
    #[ORM\OneToMany(targetEntity: SessionHistory::class, mappedBy: 'session')]
    private Collection $sessionHistories;

    #[ORM\Column(enumType: SessionStatusEnum::class)]
    private ?SessionStatusEnum $status = null;

    public function __construct()
    {
        $this->sessionHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

    /**
     * @return Collection<int, SessionHistory>
     */
    public function getSessionHistories(): Collection
    {
        return $this->sessionHistories;
    }

    public function addSessionHistory(SessionHistory $sessionHistory): static
    {
        if (!$this->sessionHistories->contains($sessionHistory)) {
            $this->sessionHistories->add($sessionHistory);
            $sessionHistory->setSession($this);
        }

        return $this;
    }

    public function removeSessionHistory(SessionHistory $sessionHistory): static
    {
        if ($this->sessionHistories->removeElement($sessionHistory)) {
            // set the owning side to null (unless already changed)
            if ($sessionHistory->getSession() === $this) {
                $sessionHistory->setSession(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?SessionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(SessionStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
