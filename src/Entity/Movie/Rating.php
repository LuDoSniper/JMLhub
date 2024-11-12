<?php

namespace App\Entity\Movie;

use App\Entity\Authentication\User;
use App\Repository\Movie\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ratings')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'ratings')]
    private ?Movie $movie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?bool
    {
        return $this->rating;
    }
    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }
    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;
        return $this;
    }
}
