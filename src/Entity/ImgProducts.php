<?php

namespace App\Entity;

use App\Repository\ImgProductsRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Products;

use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ImgProductsRepository::class)]
#[Vich\Uploadable]

class ImgProducts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Vich\UploadableField(mapping: 'Products', fileNameProperty: 'Nomimage')]

    private ?File $imageFile = null;
    
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $Nomimage = null;

    #[ORM\ManyToOne(inversedBy: 'imgProducts')]
    #[ORM\JoinColumn(nullable: false)]

    private ?products $Name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?products
    {
        return $this->Name;
    }

    public function setName(?products $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    
        if (null !== $imageFile) {
    
    
    
        }
    
    }
    public function getNomimage(): ?string
    {
        return $this->Nomimage;
    }
    
    public function setNomimage(?string $Nomimage): self
    {
        $this->Nomimage = $Nomimage;
    
        return $this;
    }
  
    public function __toString(): string
    {
        return $this->getName() ? $this->getName()->getTitle() : '';
    }
    
    

}
