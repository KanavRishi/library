<?php
// src/Entity/Borrow.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\BorrowRepository")]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Book")]
    #[ORM\JoinColumn(nullable: false)]
    private $book;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\DateTime]
    private $borrowDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\DateTime]
    private $returnDate;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBorrowDate(): ?\DateTimeInterface
    {
        return $this->borrowDate;
    }

    public function setBorrowDate(\DateTimeInterface $borrowDate): self
    {
        $this->borrowDate = $borrowDate;
        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->returnDate;
    }

    public function setReturnDate(?\DateTimeInterface $returnDate): self
    {
        $this->returnDate = $returnDate;
        return $this;
    }
}
