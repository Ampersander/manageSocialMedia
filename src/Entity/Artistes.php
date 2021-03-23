<?php

namespace App\Entity;

use App\Repository\ArtistesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtistesRepository::class)
 */
class Artistes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_facebook;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_twitter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_insta;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, inversedBy="artistes")
     */
    private $post;

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
        return $this->name_facebook;
    }

    public function setNameFacebook(string $name_facebook): self
    {
        $this->name_facebook = $name_facebook;

        return $this;
    }

    public function getNameTwitter(): ?string
    {
        return $this->name_twitter;
    }

    public function setNameTwitter(string $name_twitter): self
    {
        $this->name_twitter = $name_twitter;

        return $this;
    }

    public function getNameInsta(): ?string
    {
        return $this->name_insta;
    }

    public function setNameInsta(string $name_insta): self
    {
        $this->name_insta = $name_insta;

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
