<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 * @UniqueEntity("email")
 */
class Admin implements UserInterface {
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pin_terminal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_number;

    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="admin")
     */
    private $transactions;

    public function __construct() {
        $this->transactions = new ArrayCollection();
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function getPinTerminal(): ?string {
        return $this->pin_terminal;
    }

    public function setPinTerminal(?string $pin_terminal): self {
        $this->pin_terminal = $pin_terminal;

        return $this;
    }

    public function getPhoneNumber(): ?string {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): self {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user): self {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAdmin($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getAdmin() === $this) {
                $transaction->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return ['ROLE_ADMIN'];
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        // Pas nécessaire avec bcrypt
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
    }

    public function __toString() {
        return (string)$this->getUser()->getId();
    }
}
