<?php

namespace App\Entity;

use App\Repository\DetailCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCommandeRepository::class)]
class DetailCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prix_unitaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creer_le = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $supprimer_le = null;

    #[ORM\ManyToOne(inversedBy: 'detailCommandes')]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'detailCommandes')]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(float $prix_unitaire): static
    {
        $this->prix_unitaire = $prix_unitaire;

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

    public function getCommande(): ?commande
    {
        return $this->commande;
    }

    public function setCommande(?commande $commande): static
    {
        $this->commande = $commande;

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
}
