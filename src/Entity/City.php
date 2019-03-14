<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cleaner", mappedBy="city")
     */
    private $cleaners;

    //===========================

    public function __construct()
    {
        $this->cleaners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection|Cleaner[]
     */
    public function getCleaners(): Collection
    {
        return $this->cleaners;
    }

    public function addCleaner(Cleaner $cleaner): self
    {
        if (!$this->cleaners->contains($cleaner)) {
            $this->cleaners[] = $cleaner;
            $cleaner->setCity($this);
        }

        return $this;
    }

    public function removeCleaner(Cleaner $cleaner): self
    {
        if ($this->cleaners->contains($cleaner)) {
            $this->cleaners->removeElement($cleaner);
            // set the owning side to null (unless already changed)
            if ($cleaner->getCity() === $this) {
                $cleaner->setCity(null);
            }
        }

        return $this;
    }

}
