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
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Repository\LoginHistoryRepository;

/**
 * LoginHistory
 */
#[ORM\Table(name: 'dtb_login_history')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: LoginHistoryRepository::class)]
class LoginHistory extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $user_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $client_ip = null;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private $create_date;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private $update_date;

    #[ORM\ManyToOne(targetEntity: LoginHistoryStatus::class)]
    #[ORM\JoinColumn(name: 'login_history_status_id', referencedColumnName: 'id', nullable: false)]
    // @phpstan-ignore doctrine.associationType (Formでの初期化時にnullが必要なためnullableとしている)
    private ?LoginHistoryStatus $Status = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Member $LoginUser = null;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set user_name
     */
    public function setUserName(string $userName): LoginHistory
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function setStatus(LoginHistoryStatus $Status): LoginHistory
    {
        $this->Status = $Status;

        return $this;
    }

    public function getStatus(): LoginHistoryStatus
    {
        return $this->Status;
    }

    /**
     * Set client_ip
     */
    public function setClientIp(string $clientIp): LoginHistory
    {
        $this->client_ip = $clientIp;

        return $this;
    }

    /**
     * Get client_ip
     */
    public function getClientIp(): string
    {
        return $this->client_ip;
    }

    /**
     * Set create_date
     */
    public function setCreateDate(\DateTime $createDate): LoginHistory
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     */
    public function setUpdateDate(\DateTime $updateDate): LoginHistory
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Set LoginUser
     *
     * @param Member $loginUser
     */
    public function setLoginUser(?Member $loginUser = null): LoginHistory
    {
        $this->LoginUser = $loginUser;

        return $this;
    }

    /**
     * Get LoginUser
     */
    public function getLoginUser(): Member
    {
        return $this->LoginUser;
    }
}
