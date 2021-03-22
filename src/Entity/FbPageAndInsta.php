<?php

namespace App\Entity;

use App\Repository\FbPageAndInstaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FbPageAndInstaRepository::class)
 */
class FbPageAndInsta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $pageID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountIdInst;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $namePage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameInst;

    /**
     * @ORM\ManyToOne(targetEntity=FbAccount::class, inversedBy="FbPageAndInsta")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fbAccount;

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

    public function getAccountIdInst(): ?string
    {
        return $this->accountIdInst;
    }

    public function setAccountIdInst(?string $accountIdInst): self
    {
        $this->accountIdInst = $accountIdInst;

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

    public function getNameInst(): ?string
    {
        return $this->nameInst;
    }

    public function setNameInst(?string $nameInst): self
    {
        $this->nameInst = $nameInst;

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
}
