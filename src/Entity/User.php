<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="bidders")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="lastBidd")
     */
    private $winningAuctions;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="author")
     */
    private $addedAuctions;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->winningAuctions = new ArrayCollection();
        $this->addedAuctions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if ($roles === null) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addBidder($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeBidder($this);
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getWinningAuctions(): Collection
    {
        return $this->winningAuctions;
    }

    public function addWinningAuction(Product $winningAuction): self
    {
        if (!$this->winningAuctions->contains($winningAuction)) {
            $this->winningAuctions[] = $winningAuction;
            $winningAuction->setLastBidd($this);
        }

        return $this;
    }

    public function removeWinningAuction(Product $winningAuction): self
    {
        if ($this->winningAuctions->removeElement($winningAuction)) {
            // set the owning side to null (unless already changed)
            if ($winningAuction->getLastBidd() === $this) {
                $winningAuction->setLastBidd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getAddedAuctions(): Collection
    {
        return $this->addedAuctions;
    }

    public function addAddedAuction(Product $addedAuction): self
    {
        if (!$this->addedAuctions->contains($addedAuction)) {
            $this->addedAuctions[] = $addedAuction;
            $addedAuction->setAuthor($this);
        }

        return $this;
    }

    public function removeAddedAuction(Product $addedAuction): self
    {
        if ($this->addedAuctions->removeElement($addedAuction)) {
            // set the owning side to null (unless already changed)
            if ($addedAuction->getAuthor() === $this) {
                $addedAuction->setAuthor(null);
            }
        }

        return $this;
    }
}
