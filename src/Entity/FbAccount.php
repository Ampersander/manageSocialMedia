<?php

namespace App\Entity;

use App\Repository\FbAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FbAccountRepository::class)
 */
class FbAccount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $shortlivedtoken;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $longlivedtoken;

    /**
     * @ORM\OneToMany(targetEntity=FbPage::class, mappedBy="fbAccount", orphanRemoval=true)
     */
    private $FbPage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accountId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientSecret;

    /**
     * @ORM\OneToOne(targetEntity=SocialMediaAccount::class, inversedBy="fbAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $socialMediaAccount;

   
    public function __construct()
    {
        $this->FbPage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

  

    public function getShortlivedtoken(): ?string
    {
        return $this->shortlivedtoken;
    }

    public function setShortlivedtoken(?string $shortlivedtoken): self
    {
        $this->shortlivedtoken = $shortlivedtoken;

        return $this;
    }

    public function getLonglivedtoken(): ?string
    {
        return $this->longlivedtoken;
    }

    public function setLonglivedtoken(?string $longlivedtoken): self
    {
        $this->longlivedtoken = $longlivedtoken;

        return $this;
    }

    /**
     * @return Collection|FbPage[]
     */
    public function getFbPage(): Collection
    {
        return $this->FbPage;
    }

    public function addFbPage(FbPage $FbPage): self
    {
        if (!$this->FbPage->contains($FbPage)) {
            $this->FbPage[] = $FbPage;
            $FbPage->setFbAccount($this);
        }

        return $this;
    }

    public function removeFbPage(FbPage $FbPage): self
    {
        if ($this->FbPage->removeElement($FbPage)) {
            // set the owning side to null (unless already changed)
            if ($FbPage->getFbAccount() === $this) {
                $FbPage->setFbAccount(null);
            }
        }

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getSocialMediaAccount(): ?SocialMediaAccount
    {
        return $this->socialMediaAccount;
    }

    public function setSocialMediaAccount(SocialMediaAccount $socialMediaAccount): self
    {
        $this->socialMediaAccount = $socialMediaAccount;

        return $this;
    }

   
}
