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

    public $date;


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
     * @ORM\ManyToMany(targetEntity=Artiste::class, mappedBy="post")
     */
    private $artistes;


    /**
     * @ORM\ManyToOne(targetEntity=TemplatePost::class, inversedBy="post")
     */
    private $templatePost;


    public function __construct()
    {
        $this->socialMediaAccounts = new ArrayCollection();
        $this->artiste = new ArrayCollection();
        $this->artistes = new ArrayCollection();
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
    public function getArtistes(): Collection
    {
        return $this->artistes;
    }

    public function addArtiste(Artiste $artiste): self
    {
        if (!$this->artistes->contains($artiste)) {
            $this->artistes[] = $artiste;
            $artiste->addPost($this);
        }

        return $this;
    }

    public function removeArtiste(Artiste $artiste): self
    {
        if ($this->artistes->removeElement($artiste)) {
            $artiste->removePost($this);
        }

        return $this;
    }


    public function getTemplatePost(): ?TemplatePost
    {
        return $this->templatePost;
    }

    public function setTemplatePost(?TemplatePost $templatePost): self
    {
        $this->templatePost = $templatePost;

        return $this;
    }


   

   
}
