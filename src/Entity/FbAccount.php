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
    private $pageAccessToken;

    /**
     * @ORM\OneToMany(targetEntity=FbPageAndInsta::class, mappedBy="fbAccount", orphanRemoval=true)
     */
    private $FbPageAndInsta;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $account_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientSecret;

    public function __construct()
    {
        $this->FbPageAndInsta = new ArrayCollection();
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

    public function getPageAccessToken(): ?string
    {
        return $this->pageAccessToken;
    }

    public function setPageAccessToken(?string $pageAccessToken): self
    {
        $this->pageAccessToken = $pageAccessToken;

        return $this;
    }

    /**
     * @return Collection|FbPageAndInsta[]
     */
    public function getFbPageAndInsta(): Collection
    {
        return $this->FbPageAndInsta;
    }

    public function addFbPageAndInstum(FbPageAndInsta $fbPageAndInstum): self
    {
        if (!$this->FbPageAndInsta->contains($fbPageAndInstum)) {
            $this->FbPageAndInsta[] = $fbPageAndInstum;
            $fbPageAndInstum->setFbAccount($this);
        }

        return $this;
    }

    public function removeFbPageAndInstum(FbPageAndInsta $fbPageAndInstum): self
    {
        if ($this->FbPageAndInsta->removeElement($fbPageAndInstum)) {
            // set the owning side to null (unless already changed)
            if ($fbPageAndInstum->getFbAccount() === $this) {
                $fbPageAndInstum->setFbAccount(null);
            }
        }

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->account_id;
    }

    public function setAccountId(string $account_id): self
    {
        $this->account_id = $account_id;

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
}
