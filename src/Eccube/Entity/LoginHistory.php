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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Repository\LoginHistoryRepository;

if (!class_exists(LoginHistory::class)) {
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
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(type: 'text', nullable: true)]
        private $user_name;

        /**
         * @var string|null
         */
        #[ORM\Column(type: 'text', nullable: true)]
        private $client_ip;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var LoginHistoryStatus
         */
        #[ORM\ManyToOne(targetEntity: LoginHistoryStatus::class)]
        #[ORM\JoinColumn(name: 'login_history_status_id', referencedColumnName: 'id', nullable: false)]
        private $Status;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
        private $LoginUser;

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
         *
         * @param string $userName
         *
         * @return LoginHistory
         */
        public function setUserName($userName): LoginHistory
        {
            $this->user_name = $userName;

            return $this;
        }

        /**
         * Get user_name
         *
         * @return string
         */
        public function getUserName(): string
        {
            return $this->user_name;
        }

        /**
         * @param LoginHistoryStatus $Status
         *
         * @return LoginHistory
         */
        public function setStatus($Status): LoginHistory
        {
            $this->Status = $Status;

            return $this;
        }

        /**
         * @return LoginHistoryStatus
         */
        public function getStatus(): LoginHistoryStatus
        {
            return $this->Status;
        }

        /**
         * Set client_ip
         *
         * @param string $clientIp
         *
         * @return LoginHistory
         */
        public function setClientIp($clientIp): LoginHistory
        {
            $this->client_ip = $clientIp;

            return $this;
        }

        /**
         * Get client_ip
         *
         * @return string
         */
        public function getClientIp(): string
        {
            return $this->client_ip;
        }

        /**
         * Set create_date
         *
         * @param \DateTime $createDate
         *
         * @return LoginHistory
         */
        public function setCreateDate($createDate): LoginHistory
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
         *
         * @param \DateTime $updateDate
         *
         * @return LoginHistory
         */
        public function setUpdateDate($updateDate): LoginHistory
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
         *
         * @return LoginHistory
         */
        public function setLoginUser(?Member $loginUser = null): LoginHistory
        {
            $this->LoginUser = $loginUser;

            return $this;
        }

        /**
         * Get LoginUser
         *
         * @return Member
         */
        public function getLoginUser(): Member
        {
            return $this->LoginUser;
        }
    }
}
