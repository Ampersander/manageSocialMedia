<?php

namespace App\Entity;

use App\Repository\SocialMediaAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocialMediaAccountRepository::class)
 */
class SocialMediaAccount
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
    private $name;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $socialMedia;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="socialMediaAccounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="socialMediaAccounts")
     */
    private $posts;

    /**
     * @ORM\OneToOne(targetEntity=FbAccount::class, cascade={"persist", "remove"})
     */
    private $fbAccount;

    /**
     * @ORM\OneToOne(targetEntity=TwitterAccount::class, cascade={"persist", "remove"})
     */
    private $twitterAccount;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSocialMedia(): ?string
    {
        return $this->socialMedia;
    }

    public function setSocialMedia(string $socialMedia): self
    {
        $this->socialMedia = $socialMedia;

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
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addSocialMediaAccount($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeSocialMediaAccount($this);
        }

        return $this;
    }

    public function getFbAccount(): ?FbAccount
    {
        return $this->fbAccount;
    }

    public function setFbAccount(?FbAccount $fbAccount): self
    {
        $this->fbAccount = $fbAccount;

        return $this;
    }

    public function getTwitterAccount(): ?TwitterAccount
    {
        return $this->twitterAccount;
    }

    public function setTwitterAccount(?TwitterAccount $twitterAccount): self
    {
        $this->twitterAccount = $twitterAccount;

        return $this;
    }
}
