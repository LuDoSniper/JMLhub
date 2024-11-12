<?php

namespace App\Entity\Movie;

use App\Repository\Movie\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: 'category')]
    private ?Collection $movies;

    // Construct

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    // Getter - Setter

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

    public function getMovies(): ?Collection
    {
        return $this->movies;
    }
    public function setMovies(?Collection $movies): void
    {
        $this->movies = $movies;
    }
}
