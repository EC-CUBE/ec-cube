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

if (!class_exists('\Eccube\Entity\AuthorityRole')) {
    /**
     * AuthorityRole
     *
     * @ORM\Table(name="dtb_authority_role")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\AuthorityRoleRepository")
     */
    class AuthorityRole extends \Eccube\Entity\AbstractEntity
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
         *
         * @ORM\Column(name="deny_url", type="string", length=4000, nullable=true)
         */
        private $deny_url;

        /**
         * @var string
         *
         * @ORM\Column(name="read_only_url", type="string", length=4000, nullable=true)
         */
        private $read_only_url;

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
         * @var \Eccube\Entity\Master\Authority
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Authority")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="authority_id", referencedColumnName="id")
         * })
         */
        private $Authority;

        /**
         * @var \Eccube\Entity\Master\Role
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Role")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
         * })
         */
        private $Role;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set denyUrl.
         *
         * @param string $denyUrl
         *
         * @return AuthorityRole
         */
        public function setDenyUrl($denyUrl)
        {
            $this->deny_url = $denyUrl;

            return $this;
        }

        /**
         * Get denyUrl.
         *
         * @return string
         */
        public function getDenyUrl()
        {
            return $this->deny_url;
        }

        /**
         * Set readOnlyUrl.
         *
         * @param string $readOnlyUrl
         *
         * @return AuthorityRole
         */
        public function setReadOnlyUrl($readOnlyUrl)
        {
            $this->read_only_url = $readOnlyUrl;

            return $this;
        }

        /**
         * Get readOnlyUrl.
         *
         * @return string
         */
        public function getReadOnlyUrl()
        {
            return $this->read_only_url;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return AuthorityRole
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return AuthorityRole
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Set authority.
         *
         * @param \Eccube\Entity\Master\Authority|null $authority
         *
         * @return AuthorityRole
         */
        public function setAuthority(\Eccube\Entity\Master\Authority $authority = null)
        {
            $this->Authority = $authority;

            return $this;
        }

        /**
         * Get authority.
         *
         * @return \Eccube\Entity\Master\Authority|null
         */
        public function getAuthority()
        {
            return $this->Authority;
        }

        /**
         * Set role.
         *
         * @param \Eccube\Entity\Master\Role|null $role
         *
         * @return AuthorityRole
         */
        public function setRole(\Eccube\Entity\Master\Role $role = null)
        {
            $this->Role = $role;

            return $this;
        }

        /**
         * Get role.
         *
         * @return \Eccube\Entity\Master\Role|null
         */
        public function getRole()
        {
            return $this->Role;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return AuthorityRole
         */
        public function setCreator(\Eccube\Entity\Member $creator = null)
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }
    }
}
