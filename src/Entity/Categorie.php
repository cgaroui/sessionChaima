<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    /**
     * @var Collection<int, ModuleSession>
     */
    #[ORM\OneToMany(targetEntity: ModuleSession::class, mappedBy: 'categorie')]
    private Collection $moduleSessions;

    public function __construct()
    {
        $this->moduleSessions = new ArrayCollection();
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

    /**
     * @return Collection<int, ModuleSession>
     */
    public function getModuleSessions(): Collection
    {
        return $this->moduleSessions;
    }

    public function addModuleSession(ModuleSession $moduleSession): static
    {
        if (!$this->moduleSessions->contains($moduleSession)) {
            $this->moduleSessions->add($moduleSession);
            $moduleSession->setCategorie($this);
        }

        return $this;
    }

    public function removeModuleSession(ModuleSession $moduleSession): static
    {
        if ($this->moduleSessions->removeElement($moduleSession)) {
            // set the owning side to null (unless already changed)
            if ($moduleSession->getCategorie() === $this) {
                $moduleSession->setCategorie(null);
            }
        }

        return $this;
    }
}
