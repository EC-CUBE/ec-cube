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

if (!class_exists('\Eccube\Entity\LoginHistory')) {
    /**
     * LoginHistory
     *
     * @ORM\Table(name="dtb_login_history")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\LoginHistoryRepository")
     */
    class LoginHistory extends AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         * @ORM\Column(type="text",nullable=true)
         */
        private $user_name;

        /**
         * @var string
         * @ORM\Column(type="text",nullable=true)
         */
        private $client_ip;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var LoginHistoryStatus
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\LoginHistoryStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="login_history_status_id", referencedColumnName="id", nullable=false)
         * })
         */
        private $Status;

        /**
         * @var Member
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="SET NULL")
         * })
         */
        private $LoginUser;

        /**
         * Get id
         *
         * @return integer
         */
        public function getId()
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
        public function setUserName($userName)
        {
            $this->user_name = $userName;

            return $this;
        }

        /**
         * Get user_name
         *
         * @return string
         */
        public function getUserName()
        {
            return $this->user_name;
        }

        /**
         * @param LoginHistoryStatus $Status
         *
         * @return LoginHistory
         */
        public function setStatus($Status)
        {
            $this->Status = $Status;

            return $this;
        }

        /**
         * @return LoginHistoryStatus
         */
        public function getStatus()
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
        public function setClientIp($clientIp)
        {
            $this->client_ip = $clientIp;

            return $this;
        }

        /**
         * Get client_ip
         *
         * @return string
         */
        public function getClientIp()
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
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get create_date
         *
         * @return \DateTime
         */
        public function getCreateDate()
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
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get update_date
         *
         * @return \DateTime
         */
        public function getUpdateDate()
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
        public function setLoginUser(Member $loginUser = null)
        {
            $this->LoginUser = $loginUser;

            return $this;
        }

        /**
         * Get LoginUser
         *
         * @return Member
         */
        public function getLoginUser()
        {
            return $this->LoginUser;
        }
    }
}
