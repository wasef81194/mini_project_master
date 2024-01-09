<?php

namespace App\Entity;

use App\Repository\ProduitPanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitPanierRepository::class)]
class ProduitPanier
{
    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'produitPaniers')]
    private ?produit $produit = null;
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'produitPaniers')]
    private ?panier $panier = null;


    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getProduit(): ?produit
    {
        return $this->produit;
    }

    public function setProduit(?produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getPanier(): ?panier
    {
        return $this->panier;
    }

    public function setPanier(?panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }
}
