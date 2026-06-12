<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reviewSummary = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Review::class)]
    private Collection $review;

    public function __construct()
    {
        $this->review = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getReviewSummary(): ?string
    {
        return $this->reviewSummary;
    }

    public function setReviewSummary(?string $reviewSummary): static
    {
        $this->reviewSummary = $reviewSummary;

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->review;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
