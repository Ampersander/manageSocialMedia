<?php

namespace App\Entity;

use App\Repository\ArtisteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtisteRepository::class)
 */
class Artiste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nameFacebook;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nameTwitter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nameInsta;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, inversedBy="artistes")
     */
    public $post;

    public function __construct()
    {
        $this->post = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNameFacebook(): ?string
    {
        return $this->nameFacebook;
    }

    public function setNameFacebook(string $nameFacebook): self
    {
        $this->nameFacebook = $nameFacebook;

        return $this;
    }

    public function getNameTwitter(): ?string
    {
        return $this->nameTwitter;
    }

    public function setNameTwitter(string $nameTwitter): self
    {
        $this->nameTwitter = $nameTwitter;

        return $this;
    }

    public function getNameInsta(): ?string
    {
        return $this->nameInsta;
    }

    public function setNameInsta(string $nameInsta): self
    {
        $this->nameInsta = $nameInsta;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->post->removeElement($post);

        return $this;
    }
}
