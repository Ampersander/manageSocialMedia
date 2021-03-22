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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consumer_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consumer_secret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token_secret;

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

    public function getConsumerKey(): ?string
    {
        return $this->consumer_key;
    }

    public function setConsumerKey(?string $consumer_key): self
    {
        $this->consumer_key = $consumer_key;

        return $this;
    }

    public function getConsumerSecret(): ?string
    {
        return $this->consumer_secret;
    }

    public function setConsumerSecret(string $consumer_secret): self
    {
        $this->consumer_secret = $consumer_secret;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token): self
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getAccessTokenSecret(): ?string
    {
        return $this->access_token_secret;
    }

    public function setAccessTokenSecret(?string $access_token_secret): self
    {
        $this->access_token_secret = $access_token_secret;

        return $this;
    }
}
