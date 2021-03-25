<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=SocialMediaAccount::class, inversedBy="posts")
     */
    private $socialMediaAccounts;


     /**
     * @ORM\ManyToMany(targetEntity=artiste::class, mappedBy="post")
     */
    private $artiste;

    

    public function __construct()
    {
        $this->socialMediaAccounts = new ArrayCollection();
        $this->artiste = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|SocialMediaAccount[]
     */
    public function getSocialMediaAccounts(): Collection
    {
        return $this->socialMediaAccounts;
    }

    public function addSocialMediaAccount(SocialMediaAccount $socialMediaAccount): self
    {
        if (!$this->socialMediaAccounts->contains($socialMediaAccount)) {
            $this->socialMediaAccounts[] = $socialMediaAccount;
        }

        return $this;
    }

    public function removeSocialMediaAccount(SocialMediaAccount $socialMediaAccount): self
    {
        $this->socialMediaAccounts->removeElement($socialMediaAccount);

        return $this;
    }

     /**
     * @return Collection|Artiste[]
     */
    public function getartiste(): Collection
    {
        return $this->artiste;
    }

    public function addArtiste(Artiste $artiste): self
    {
        if (!$this->artiste->contains($artiste)) {
            $this->artiste[] = $artiste;
            $artiste->addPost($this);
        }

        return $this;
    }

    public function removeArtiste(Artiste $artiste): self
    {
        if ($this->artiste->removeElement($artiste)) {
            $artiste->removePost($this);
        }

        return $this;
    }
}
