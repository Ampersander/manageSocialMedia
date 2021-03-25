<?php

namespace App\Entity;

use App\Repository\InstaAccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InstaAccountRepository::class)
 */
class InstaAccount
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
     * @ORM\Column(type="string", length=255)
     */
    private $id_account;


    /**
     * @ORM\OneToOne(targetEntity=SocialMediaAccount::class, inversedBy="instaAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $socialMediaAccount;

    /**
     * @ORM\OneToOne(targetEntity=FbPage::class, inversedBy="instaAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $fbPage;


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

    public function getIdAccount(): ?string
    {
        return $this->id_account;
    }

    public function setIdAccount(string $id_account): self
    {
        $this->id_account = $id_account;

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

    public function getFbPage(): ?FbPage
    {
        return $this->fbPage;
    }

    public function setFbPage(FbPage $fbPage): self
    {
        $this->fbPage = $fbPage;

        return $this;
    }
}
