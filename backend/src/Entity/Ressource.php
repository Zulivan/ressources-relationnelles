<?php

namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;


#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'ressource:read']
        ),
        new GetCollection(normalizationContext: ['groups' => 'ressource:read'])
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    paginationMaximumItemsPerPage: 5,
)]

#[ApiFilter(SearchFilter::class, properties: ['categories.id', 'typeRessource.id'])]
#[ApiFilter(NumericFilter::class, properties: ['categories.id', 'typeRessource.id'])]
#[ApiFilter(OrderFilter::class, properties: ['dateCreation'])]
#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    //add groups to expose this field only when ressource:reading a resource
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?string $titre = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['ressource:read'])]
    private ?string $texte = null;

    #[ORM\Column]
    #[Groups(['ressource:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private ?bool $validee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ressource:read'])]
    #[ApiSubresource()]
    private ?TypeRessource $typeRessource = null;

    // eager fetch the categories when reading a ressource
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'ressources', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ressource:read'])]
    #[ApiSubresource()]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'favoris')]
    private Collection $utilisateursFavoris;

    #[Groups(['ressource:read'])]
    private ?int $nbFavoris = 0;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ressource:read'])]
    #[ApiSubresource()]
    private ?Utilisateur $createur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $validateur = null;

    #[ORM\OneToOne(mappedBy: 'ressource', cascade: ['persist', 'remove'])]
    private ?Exploiter $exploiter = null;

    #[Groups(['ressource:read'])]
    #[ORM\OneToMany(mappedBy: 'ressource', targetEntity: Commentaire::class, orphanRemoval: true)]
    #[ApiSubresource()]
    private Collection $commentaires;

    #[Groups(['ressource:read'])]
    private ?int $nbCommentaires = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['ressource:read'])]
    private ?string $lien = null;

    #[Groups(['ressource:read'])]
    private ?string $document = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->utilisateursFavoris = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->validee = false;

    }

    public function getDocument(): ?string
    {
        if($this->getLien()){
            if($this->getTypeRessource()->getLibelle() == 'Image'){
                $url = $this->getLien();
                return '<img src="'.$url.'" alt="Image de la ressource" style="width:100%;" />';
            }elseif($this->getTypeRessource()->getId() == 3){
                $id = explode('=', $this->getLien())[1];
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

            }

        }

        return null;
    }

    public function getNbCommentaires(): ?int
    {
        return $this->commentaires->count();
    }

    public function getNbFavoris(): ?int
    {
        return $this->utilisateursFavoris->count();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function isValidee(): ?bool
    {
        return $this->validee;
    }

    public function setValidee(bool $validee): self
    {
        $this->validee = $validee;

        return $this;
    }

    public function getDateCreation(): ?string
    {
        $date = $this->dateCreation;

        // Set the timezone to UTC
        $date->setTimezone(new \DateTimeZone('UTC'));

        // Convert the date to a French-readable format
        $formattedDate = $date->format('d/m/Y à H:i:s');
        return $formattedDate;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getTypeRessource(): ?TypeRessource
    {
        return $this->typeRessource;
    }

    public function setTypeRessource(?TypeRessource $typeRessource): self
    {
        $this->typeRessource = $typeRessource;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addRessource($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeRessource($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateursFavoris(): Collection
    {
        return $this->utilisateursFavoris;
    }

    public function addUtilisateursFavori(Utilisateur $utilisateursFavori): self
    {
        if (!$this->utilisateursFavoris->contains($utilisateursFavori)) {
            $this->utilisateursFavoris->add($utilisateursFavori);
            $utilisateursFavori->addFavori($this);
        }

        return $this;
    }

    public function removeUtilisateursFavori(Utilisateur $utilisateursFavori): self
    {
        if ($this->utilisateursFavoris->removeElement($utilisateursFavori)) {
            $utilisateursFavori->removeFavori($this);
        }

        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getValidateur(): ?Utilisateur
    {
        return $this->validateur;
    }

    public function setValidateur(?Utilisateur $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getExploiter(): ?Exploiter
    {
        return $this->exploiter;
    }

    public function setExploiter(Exploiter $exploiter): self
    {
        // set the owning side of the relation if necessary
        if ($exploiter->getRessource() !== $this) {
            $exploiter->setRessource($this);
        }

        $this->exploiter = $exploiter;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        // loop commentaires
        $cmts = new ArrayCollection();
        foreach ($this->commentaires as $commentaire) {
            if(!$commentaire->getReponse()) {
                $cmts->add($commentaire);
            }
        }
        return $cmts;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRessource($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless alressource:ready changed)
            if ($commentaire->getRessource() === $this) {
                $commentaire->setRessource(null);
            }
        }

        return $this;
    }

    public function getLien(): ?string
    {
        $url = null;
        if($this->lien)$url = 'http://localhost:8000' . $this->lien;
        return $url;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }
}
