<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $mobile = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $verified = null;

    #[ORM\Column(nullable: true)]
    private ?int $verifyCode = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccountNumber::class)]
    private Collection $accountNumbers;

    public function __construct()
    {
        parent::__construct();
        $this->token = uniqid('token-' , false);
        $this->accountNumbers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->mobile;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getVerifyCode(): ?int
    {
        return $this->verifyCode;
    }

    public function setVerifyCode(?int $verifyCode): self
    {
        $this->verifyCode = $verifyCode;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, AccountNumber>
     */
    public function getAccountNumbers(): Collection
    {
        return $this->accountNumbers;
    }

    public function addAccountNumber(AccountNumber $accountNumber): self
    {
        if (!$this->accountNumbers->contains($accountNumber)) {
            $this->accountNumbers->add($accountNumber);
            $accountNumber->setUser($this);
        }

        return $this;
    }

    public function removeAccountNumber(AccountNumber $accountNumber): self
    {
        if ($this->accountNumbers->removeElement($accountNumber)) {
            // set the owning side to null (unless already changed)
            if ($accountNumber->getUser() === $this) {
                $accountNumber->setUser(null);
            }
        }

        return $this;
    }
}
