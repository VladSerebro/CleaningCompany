<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cleaner", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $cleaner;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quality_score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCleaner(): ?Cleaner
    {
        return $this->cleaner;
    }

    public function setCleaner(?Cleaner $cleaner): self
    {
        $this->cleaner = $cleaner;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration($duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getQualityScore(): ?int
    {
        return $this->quality_score;
    }

    public function setQualityScore(?int $quality_score): self
    {
        $this->quality_score = $quality_score;

        return $this;
    }
}
