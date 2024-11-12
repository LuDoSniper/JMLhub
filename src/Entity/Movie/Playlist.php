<?php

namespace App\Entity\Movie;

use App\Entity\Authentication\User;
use App\Repository\Movie\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Movie::class)]
    private Collection $movies;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'playlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: false)]
    private ?bool $native = false;

    // Construct

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    // Getters - Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMovies(): Collection
    {
        return $this->movies;
    }
    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }

        return $this;
    }
    public function removeMovie(Movie $movie): static
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }

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

    public function getNative(): ?bool
    {
        return $this->native;
    }
    public function setNative(?bool $native): void
    {
        $this->native = $native;
    }
}
