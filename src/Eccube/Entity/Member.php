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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

if (!class_exists('\Eccube\Entity\Member')) {
    /**
     * Member
     *
     * @ORM\Table(name="dtb_member")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\MemberRepository")
     */
    class Member extends \Eccube\Entity\AbstractEntity implements UserInterface, \Serializable
    {
        public static function loadValidatorMetadata(ClassMetadata $metadata)
        {
            $metadata->addConstraint(new UniqueEntity([
                'fields' => 'login_id',
                'message' => 'form_error.member_already_exists',
            ]));
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getName();
        }

        /**
         * {@inheritdoc}
         */
        public function getRoles()
        {
            return ['ROLE_ADMIN'];
        }

        /**
         * {@inheritdoc}
         */
        public function getUsername()
        {
            return $this->login_id;
        }

        /**
         * {@inheritdoc}
         */
        public function eraseCredentials()
        {
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="name", type="string", length=255, nullable=true)
         */
        private $name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="department", type="string", length=255, nullable=true)
         */
        private $department;

        /**
         * @var string
         *
         * @ORM\Column(name="login_id", type="string", length=255)
         */
        private $login_id;

        /**
         * @Assert\NotBlank()
         * @Assert\Length(max=4096)
         */
        private $plainPassword;

        /**
         * @var string
         *
         * @ORM\Column(name="password", type="string", length=255)
         */
        private $password;

        /**
         * @var string
         *
         * @ORM\Column(name="salt", type="string", length=255, nullable=true)
         */
        private $salt;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
         */
        private $sort_no;

        /**
         * @var string
         *
         * @ORM\Column(name="two_factor_auth_key",type="string",length=255,nullable=true,options={"fixed":false})
         */
        private $two_factor_auth_key;

        /**
         * @ORM\Column(name="two_factor_auth_enabled",type="boolean",nullable=false,options={"default":false})
         *
         * @var integer
         */
        private $two_factor_auth_enabled = false;

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
         * @var \DateTime|null
         *
         * @ORM\Column(name="login_date", type="datetimetz", nullable=true)
         */
        private $login_date;

        /**
         * @var \Eccube\Entity\Master\Work
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Work")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="work_id", referencedColumnName="id")
         * })
         */
        private $Work;

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
         * Set name.
         *
         * @param string|null $name
         *
         * @return Member
         */
        public function setName($name = null)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string|null
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set department.
         *
         * @param string|null $department
         *
         * @return Member
         */
        public function setDepartment($department = null)
        {
            $this->department = $department;

            return $this;
        }

        /**
         * Get department.
         *
         * @return string|null
         */
        public function getDepartment()
        {
            return $this->department;
        }

        /**
         * Set loginId.
         *
         * @param string $loginId
         *
         * @return Member
         */
        public function setLoginId($loginId)
        {
            $this->login_id = $loginId;

            return $this;
        }

        /**
         * Get loginId.
         *
         * @return string
         */
        public function getLoginId()
        {
            return $this->login_id;
        }

        /**
         * @return string|null
         */
        public function getPlainPassword(): ?string
        {
            return $this->plainPassword;
        }

        /**
         * @param string $password
         *
         * @return $this
         */
        public function setPlainPassword(?string $password): self
        {
            $this->plainPassword = $password;

            return $this;
        }

        /**
         * Set password.
         *
         * @param string $password
         *
         * @return Member
         */
        public function setPassword($password)
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         *
         * @return string
         */
        public function getPassword()
        {
            return $this->password;
        }

        /**
         * Set salt.
         *
         * @param string $salt
         *
         * @return Member
         */
        public function setSalt($salt)
        {
            $this->salt = $salt;

            return $this;
        }

        /**
         * Get salt.
         *
         * @return string
         */
        public function getSalt()
        {
            return $this->salt;
        }

        /**
         * Set sortNo.
         *
         * @param int $sortNo
         *
         * @return Member
         */
        public function setSortNo($sortNo)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Set twoFactorAuthKey.
         *
         * @param string $two_factor_auth_key
         *
         * @return Member
         */
        public function setTwoFactorAuthKey($two_factor_auth_key)
        {
            $this->two_factor_auth_key = $two_factor_auth_key;

            return $this;
        }

        /**
         * Get twoFactorAuthKey.
         *
         * @return string
         */
        public function getTwoFactorAuthKey()
        {
            return $this->two_factor_auth_key;
        }

        /**
         * Set twoFactorAuthEnabled.
         *
         * @param boolean $two_factor_auth_enabled
         *
         * @return Member
         */
        public function setTwoFactorAuthEnabled($two_factor_auth_enabled)
        {
            $this->two_factor_auth_enabled = $two_factor_auth_enabled;

            return $this;
        }

        /**
         * Get twoFactorAuthEnabled.
         *
         * @return boolean
         */
        public function isTwoFactorAuthEnabled()
        {
            return $this->two_factor_auth_enabled;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Member
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
         * @return Member
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
         * Set loginDate.
         *
         * @param \DateTime|null $loginDate
         *
         * @return Member
         */
        public function setLoginDate($loginDate = null)
        {
            $this->login_date = $loginDate;

            return $this;
        }

        /**
         * Get loginDate.
         *
         * @return \DateTime|null
         */
        public function getLoginDate()
        {
            return $this->login_date;
        }

        /**
         * Set Work
         *
         * @param \Eccube\Entity\Master\Work
         *
         * @return Member
         */
        public function setWork(Master\Work $work = null)
        {
            $this->Work = $work;

            return $this;
        }

        /**
         * Get work.
         *
         * @return \Eccube\Entity\Master\Work|null
         */
        public function getWork()
        {
            return $this->Work;
        }

        /**
         * Set authority.
         *
         * @param \Eccube\Entity\Master\Authority|null $authority
         *
         * @return Member
         */
        public function setAuthority(Master\Authority $authority = null)
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
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return Member
         */
        public function setCreator(Member $creator = null)
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

        /**
         * String representation of object
         *
         * @see http://php.net/manual/en/serializable.serialize.php
         *
         * @return string the string representation of the object or null
         *
         * @since 5.1.0
         */
        public function serialize()
        {
            // see https://symfony.com/doc/2.7/security/entity_provider.html#create-your-user-entity
            // MemberRepository::loadUserByUsername() で Work をチェックしているため、ここでは不要
            return serialize([
                $this->id,
                $this->login_id,
                $this->password,
                $this->salt,
            ]);
        }

        /**
         * Constructs the object
         *
         * @see http://php.net/manual/en/serializable.unserialize.php
         *
         * @param string $serialized <p>
         * The string representation of the object.
         * </p>
         *
         * @return void
         *
         * @since 5.1.0
         */
        public function unserialize($serialized)
        {
            list(
                $this->id,
                $this->login_id,
                $this->password,
                $this->salt) = unserialize($serialized);
        }
    }
}
