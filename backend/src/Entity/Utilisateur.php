<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\State\HasheurMotDePasse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'utilisateur:read', 'enable_max_depth'=>true]),
        new GetCollection(normalizationContext: ['groups' => 'utilisateur:read', 'enable_max_depth'=>true])
    ],
    paginationEnabled: false,
)]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $password = null;

    private ?string $passwordClair = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $codePostal = null;

    #[ORM\Column]
    #[Groups(['ressource:read', 'utilisateur:read'])]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\ManyToMany(targetEntity: Ressource::class, inversedBy: 'utilisateursFavoris')]
    #[Groups(['utilisateur:read'])]
    #[ApiSubresource()]
    private Collection $favoris;

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Ressource::class)]
    #[Groups(['utilisateur:read'])]
    private Collection $ressources;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?Exploiter $exploiter = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commentaire::class)]
    #[Groups(['utilisateur:read'])]
    #[ApiSubresource()]
    private Collection $commentaires;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }


    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // return an array of roles that the user has
        return ['CONNECTE', 'Citoyen'];
    }

    public function eraseCredentials(): void
    {
        // erase any sensitive data stored on the user (if any)
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Ressource $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
        }

        return $this;
    }

    public function removeFavori(Ressource $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
            $ressource->setCreateur($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getCreateur() === $this) {
                $ressource->setCreateur(null);
            }
        }

        return $this;
    }

    public function getExploiter(): ?Exploiter
    {
        return $this->exploiter;
    }

    public function setExploiter(Exploiter $exploiter): self
    {
        // set the owning side of the relation if necessary
        if ($exploiter->getUtilisateur() !== $this) {
            $exploiter->setUtilisateur($this);
        }

        $this->exploiter = $exploiter;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getPasswordClair(): ?string
    {
        return $this->passwordClair;
    }

    public function setPasswordClair(string $passwordClair): self
    {
        $this->passwordClair = $passwordClair;

        return $this;
    }

    private function isRole(string $role): bool
    {
        if($this->getRole()){
            if($this->getRole()->getLibelle() == $role){
                return true;
            }
        }
        return false;
    }

    public function isModerator(): bool
    {
        return $this->isRole('Modérateur') || $this->isRole('Administrateur') || $this->isRole('Super Administrateur');
    }

    public function isAdministrator(): bool
    {
        return $this->isRole('Administrateur') || $this->isRole('Super Administrateur');
    }

    public function isSuperAdministrator(): bool
    {
        return $this->isRole('Super Administrateur');
    }

    public function __toString(): string
    {
        return $this->getId().' | '.$this->prenom . ' ' . $this->nom;
    }
}
