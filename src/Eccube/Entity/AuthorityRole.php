<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\Authority;
use Eccube\Repository\AuthorityRoleRepository;

/**
 * AuthorityRole
 */
#[ORM\Table(name: 'dtb_authority_role')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: AuthorityRoleRepository::class)]
class AuthorityRole extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'deny_url', type: Types::STRING, length: 4000)]
    private ?string $deny_url = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\ManyToOne(targetEntity: Authority::class)]
    #[ORM\JoinColumn(name: 'authority_id', referencedColumnName: 'id')]
    private ?Authority $Authority = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
    private ?Member $Creator = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set denyUrl.
     */
    public function setDenyUrl(?string $denyUrl): AuthorityRole
    {
        $this->deny_url = $denyUrl;

        return $this;
    }

    /**
     * Get denyUrl.
     */
    public function getDenyUrl(): ?string
    {
        return $this->deny_url;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): AuthorityRole
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     */
    public function setUpdateDate(\DateTime $updateDate): AuthorityRole
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Set authority.
     */
    public function setAuthority(?Authority $authority = null): AuthorityRole
    {
        $this->Authority = $authority;

        return $this;
    }

    /**
     * Get authority.
     */
    public function getAuthority(): ?Authority
    {
        return $this->Authority;
    }

    /**
     * Set creator.
     */
    public function setCreator(?Member $creator = null): AuthorityRole
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     */
    public function getCreator(): ?Member
    {
        return $this->Creator;
    }
}
