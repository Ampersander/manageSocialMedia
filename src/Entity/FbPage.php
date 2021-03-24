<?php

namespace App\Entity;

use App\Repository\FbPageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FbPageRepository::class)
 */
class FbPage
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
    private $pageID;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $pageAccessToken;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $namePage;

    /**
     * @ORM\ManyToOne(targetEntity=FbAccount::class, inversedBy="FbPage")
     * @ORM\JoinColumn(nullable=false)
     */

    private $fbAccount;

    /**
     * @ORM\OneToOne(targetEntity=SocialMediaAccount::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $socialMediaAccount;

    /**
     * @ORM\OneToOne(targetEntity=InstaAccount::class, mappedBy="fbPage", cascade={"persist", "remove"})
     */
    private $instaAccount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageID(): ?int
    {
        return $this->pageID;
    }

    public function setPageID(int $pageID): self
    {
        $this->pageID = $pageID;

        return $this;
    }

  

    public function getNamePage(): ?string
    {
        return $this->namePage;
    }

    public function setNamePage(string $namePage): self
    {
        $this->namePage = $namePage;

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


    public function getPageAccessToken(): ?string
    {
        return $this->pageAccessToken;
    }

    public function setPageAccessToken(?string $pageAccessToken): self
    {
        $this->pageAccessToken = $pageAccessToken;

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

    public function getInstaAccount(): ?InstaAccount
    {
        return $this->instaAccount;
    }

    public function setInstaAccount(InstaAccount $instaAccount): self
    {
        // set the owning side of the relation if necessary
        if ($instaAccount->getFbPage() !== $this) {
            $instaAccount->setFbPage($this);
        }

        $this->instaAccount = $instaAccount;

        return $this;
    }
}
