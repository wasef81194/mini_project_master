<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Type(type: 'numeric', message: 'Entrer un nombre')]
    private ?float $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creer_le = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $supprimer_le = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitPanier::class)]
    private Collection $produitPaniers;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: DetailCommande::class)]
    private Collection $detailCommandes;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Fds::class)]
    private Collection $fds;

    public function __construct()
    {
        $this->produitPaniers = new ArrayCollection();
        $this->detailCommandes = new ArrayCollection();
        $this->fds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

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
     * @return Collection<int, ProduitPanier>
     */
    public function getProduitPaniers(): Collection
    {
        return $this->produitPaniers;
    }

    public function addProduitPanier(ProduitPanier $produitPanier): static
    {
        if (!$this->produitPaniers->contains($produitPanier)) {
            $this->produitPaniers->add($produitPanier);
            $produitPanier->setProduit($this);
        }

        return $this;
    }

    public function removeProduitPanier(ProduitPanier $produitPanier): static
    {
        if ($this->produitPaniers->removeElement($produitPanier)) {
            // set the owning side to null (unless already changed)
            if ($produitPanier->getProduit() === $this) {
                $produitPanier->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetailCommandes(): Collection
    {
        return $this->detailCommandes;
    }

    public function addDetailCommande(DetailCommande $detailCommande): static
    {
        if (!$this->detailCommandes->contains($detailCommande)) {
            $this->detailCommandes->add($detailCommande);
            $detailCommande->setProduit($this);
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): static
    {
        if ($this->detailCommandes->removeElement($detailCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailCommande->getProduit() === $this) {
                $detailCommande->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fds>
     */
    public function getFds(): Collection
    {
        return $this->fds;
    }

    public function addFd(Fds $fd): static
    {
        if (!$this->fds->contains($fd)) {
            $this->fds->add($fd);
            $fd->setProduit($this);
        }

        return $this;
    }

    public function removeFd(Fds $fd): static
    {
        if ($this->fds->removeElement($fd)) {
            // set the owning side to null (unless already changed)
            if ($fd->getProduit() === $this) {
                $fd->setProduit(null);
            }
        }

        return $this;
    }
}
