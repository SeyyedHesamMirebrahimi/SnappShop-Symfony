<?php

namespace App\Entity;

use App\Repository\AccountNumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountNumberRepository::class)]
class AccountNumber extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $account_number = null;

    #[ORM\ManyToOne(inversedBy: 'accountNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'accountNumber', targetEntity: CardNumber::class)]
    private Collection $cardNumbers;

    #[ORM\Column]
    private ?int $balance = null;

    public function __construct()
    {
        parent::__construct();
        $this->cardNumbers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    public function setAccountNumber(string $account_number): self
    {
        $this->account_number = $account_number;

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

    /**
     * @return Collection<int, CardNumber>
     */
    public function getCardNumbers(): Collection
    {
        return $this->cardNumbers;
    }

    public function addCardNumber(CardNumber $cardNumber): self
    {
        if (!$this->cardNumbers->contains($cardNumber)) {
            $this->cardNumbers->add($cardNumber);
            $cardNumber->setAccountNumber($this);
        }

        return $this;
    }

    public function removeCardNumber(CardNumber $cardNumber): self
    {
        if ($this->cardNumbers->removeElement($cardNumber)) {
            // set the owning side to null (unless already changed)
            if ($cardNumber->getAccountNumber() === $this) {
                $cardNumber->setAccountNumber(null);
            }
        }

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
