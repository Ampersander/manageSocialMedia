<?php

namespace App\Entity;

use App\Repository\TwitterAccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TwitterAccountRepository::class)
 */
class TwitterAccount
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
    private $consumerKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consumerSecret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessTokenSecret;

    /**
     * @ORM\OneToOne(targetEntity=SocialMediaAccount::class, inversedBy="twitterAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $socialMediaAccount;

  

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getConsumerKey(): ?string
    {
        return $this->consumerKey;
    }

    public function setConsumerKey(?string $consumerKey): self
    {
        $this->consumerKey = $consumerKey;

        return $this;
    }

    public function getConsumerSecret(): ?string
    {
        return $this->consumerSecret;
    }

    public function setConsumerSecret(string $consumerSecret): self
    {
        $this->consumerSecret = $consumerSecret;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessTokenSecret(): ?string
    {
        return $this->accessTokenSecret;
    }

    public function setAccessTokenSecret(?string $accessTokenSecret): self
    {
        $this->accessTokenSecret = $accessTokenSecret;

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
