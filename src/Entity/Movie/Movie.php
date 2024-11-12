<?php

namespace App\Entity\Movie;

use App\Entity\Authentication\User;
use App\Repository\Movie\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'movie')]
    private Collection $ratings;

    private ?float $rating = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file_path = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'movies')]
    private ?Category $category = null;

    // Construct

    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {
        $this->ratings = new ArrayCollection();
    }

    // Getter - Setter

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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }
    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }
    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $rating->setMovie($this);
            $this->ratings->add($rating);
        }

        return $this;
    }
    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            if ($rating->getMovie() === $this) {
                $rating->setMovie(null);
            }
        }

        return $this;
    }

    public function getRating(): ?float
    {
        $sum = 0;
        foreach ($this->getRatings() as $rating) {
            /** @var $rating Rating */
            $sum += $rating->getRating();
        }
        if (count($this->getRatings()) == 0) {
            return null;
        }
        return ($sum / count($this->getRatings())) * 100;
    }
    public function getRatingsNumber(): ?int
    {
        return count($this->getRatings());
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }
    public function setFilePath(?string $file_path): void
    {
        $this->file_path = $file_path;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }
}
