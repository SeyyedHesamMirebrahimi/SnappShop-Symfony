<?php

namespace App\Entity;

use App\Repository\CardNumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardNumberRepository::class)]
class CardNumber extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cardtNumber = null;

    #[ORM\ManyToOne(inversedBy: 'cardNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountNumber $accountNumber = null;

    #[ORM\OneToMany(mappedBy: 'cardNumber', targetEntity: Transaction::class)]
    private Collection $transactions;

    public function __construct()
    {
        parent::__construct();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardtNumber(): ?string
    {
        return $this->cardtNumber;
    }

    public function setCardtNumber(string $cardtNumber): self
    {
        $this->cardtNumber = $cardtNumber;

        return $this;
    }

    public function getAccountNumber(): ?AccountNumber
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?AccountNumber $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCardNumber($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCardNumber() === $this) {
                $transaction->setCardNumber(null);
            }
        }

        return $this;
    }
}
