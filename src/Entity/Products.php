<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $percentage = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description_0 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description_1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description_2 = null;

    #[ORM\OneToMany(targetEntity: ImgProducts::class, mappedBy: 'Name', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $imgProducts;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'products')]
    private ?Categories $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    public function __construct()
    {
        $this->imgProducts = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getDescription0(): ?string
    {
        return $this->description_0;
    }

    public function setDescription0(string $description_0): static
    {
        $this->description_0 = $description_0;

        return $this;
    }

    public function getDescription1(): ?string
    {
        return $this->description_1;
    }

    public function setDescription1(?string $description_1): static
    {
        $this->description_1 = $description_1;

        return $this;
    }

    public function getDescription2(): ?string
    {
        return $this->description_2;
    }

    public function setDescription2(?string $description_2): static
    {
        $this->description_2 = $description_2;

        return $this;
    }

    public function getImgProducts(): Collection
    {
        return $this->imgProducts;
    }

    public function addImgProduct(ImgProducts $imgProduct): static
    {
        if (!$this->imgProducts->contains($imgProduct)) {
            $this->imgProducts->add($imgProduct);
            $imgProduct->setName($this);
        }

        return $this;
    }

    public function removeImgProduct(ImgProducts $imgProduct): static
    {
        if ($this->imgProducts->removeElement($imgProduct)) {
            if ($imgProduct->getName() === $this) {
                $imgProduct->setName(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }
}