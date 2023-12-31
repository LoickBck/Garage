<?php

namespace App\Entity;

use App\Entity\Car;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: "veuillez donner une URL valide")]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10, max:255, minMessage:"Le titre de l'image doit faire plus de 10 caractères", maxMessage:"Le titre de l'image ne doit pas faire plus de 255 caractères")]
    private ?string $caption = null;


    #[ORM\ManyToOne(inversedBy: 'images', targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }
    
    public function setCar(?Car $car): static
    {
        $this->car = $car;
    
        return $this;
    }
}
