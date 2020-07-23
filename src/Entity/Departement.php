<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DepartementRepository::class)
 */
class Departement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"region:read_all"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"region:read_all"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"region:read_all"})
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="departements")
     */
    private $region;

    /**
     * @Groups({"region:read_all"})
     * @ORM\OneToMany(targetEntity=Comune::class, mappedBy="departement")
     */
    private $comunes;

    public function __construct()
    {
        $this->comunes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Comune[]
     */
    public function getComunes(): Collection
    {
        return $this->comunes;
    }

    public function addComune(Comune $comune): self
    {
        if (!$this->comunes->contains($comune)) {
            $this->comunes[] = $comune;
            $comune->setDepartement($this);
        }

        return $this;
    }

    public function removeComune(Comune $comune): self
    {
        if ($this->comunes->contains($comune)) {
            $this->comunes->removeElement($comune);
            // set the owning side to null (unless already changed)
            if ($comune->getDepartement() === $this) {
                $comune->setDepartement(null);
            }
        }

        return $this;
    }
}
