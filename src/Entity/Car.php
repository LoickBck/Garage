<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Image;
use DateTimeInterface;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarRepository;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields:['model'], message:"La page de présentation de ce modèle existe déjà")]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, minMessage:'Votre Marque doit faire plus de 1 caractères')]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, minMessage:'Votre modèle doit faire plus de 1 caractères')]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: "Il faut une URL valide")]
    private ?string $coverImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $mileage = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $n_owner = null;

    #[ORM\Column]
    private ?string $displacement = null; // cylindrée de la voiture 

    #[ORM\Column]
    private ?int $power = null; // puissance de la voiture

    #[ORM\Column(length: 255)]
    private ?string $fuel = null; // Carburant de la voiture

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $manufacturingYear = null; // Date de mise en circulation

    #[ORM\Column(length: 255)]
    private ?string $transmission = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 20, max: 255, minMessage:"La description doit faire plus de 20 caractères", maxMessage: "La description ne doit pas faire plus de 255 caractères")]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 20, max: 4096, minMessage:"Les optionnages doivent faire plus de 20 caractères", maxMessage: "Les optionnages ne doivent pas faire plus de 4096 caractères")]
    private ?string $options = null;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Image::class, orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
    
    /**
     * Permet d'intialiser le slug automatiquement si on ne le donne pas
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->model);
        }
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): static
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getNOwner(): ?int
    {
        return $this->n_owner;
    }

    public function setNOwner(int $nOwner): static
    {
        $this->n_owner = $nOwner;

        return $this;
    }

    public function getDisplacement(): ?float
    {
        return $this->displacement;
    }

    public function setDisplacement(float $displacement): static
    {
        $this->displacement = $displacement;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getManufacturingYear(): ?\DateTimeInterface
    {
        return $this->manufacturingYear;
    }

    public function setManufacturingYear(\DateTimeInterface $manufacturingYear): static
    {
        $this->manufacturingYear = $manufacturingYear;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
    * @return Collection<int, Image>
    */
    public function getImages(): Collection
    {
       return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }
}

