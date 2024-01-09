<?php

namespace App\Entity;

use App\Repository\FdsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FdsRepository::class)]
class Fds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chemin_pdf = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creer_le = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $supprimer_le = null;

    #[ORM\OneToMany(mappedBy: 'fds', targetEntity: produit::class)]
    private Collection $produit;

    public function __construct()
    {
        $this->produit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheminPdf(): ?string
    {
        return $this->chemin_pdf;
    }

    public function setCheminPdf(string $chemin_pdf): static
    {
        $this->chemin_pdf = $chemin_pdf;

        return $this;
    }

    public function getCreerLe(): ?\DateTimeInterface
    {
        return $this->creer_le;
    }

    public function setCreerLe(\DateTimeInterface $creer_le): static
    {
        $this->creer_le = $creer_le;

        return $this;
    }

    public function getSupprimerLe(): ?\DateTimeInterface
    {
        return $this->supprimer_le;
    }

    public function setSupprimerLe(?\DateTimeInterface $supprimer_le): static
    {
        $this->supprimer_le = $supprimer_le;

        return $this;
    }

    /**
     * @return Collection<int, produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(produit $produit): static
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setFds($this);
        }

        return $this;
    }

    public function removeProduit(produit $produit): static
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getFds() === $this) {
                $produit->setFds(null);
            }
        }

        return $this;
    }
}
