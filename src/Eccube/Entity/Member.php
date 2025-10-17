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
use Eccube\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

if (!class_exists(Member::class)) {
    /**
     * Member
     */
    #[ORM\Table(name: 'dtb_member')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: MemberRepository::class)]
    class Member extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface, \Serializable, \Stringable
    {
        /**
         * @param ClassMetadata $metadata
         *
         * @return void
         */
        public static function loadValidatorMetadata(ClassMetadata $metadata): void
        {
            $metadata->addConstraint(new UniqueEntity([
                'fields' => 'login_id',
                'message' => 'form_error.member_already_exists',
            ]));
        }

        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->getName();
        }

        /**
         * {@inheritdoc}
         */
        #[\Override]
        public function getRoles(): array
        {
            return ['ROLE_ADMIN'];
        }

        /**
         * @return string
         */
        public function getUsername(): string
        {
            return $this->login_id;
        }

        /**
         * {@inheritdoc}
         *
         * @return void
         */
        #[\Override]
        public function eraseCredentials(): void
        {
        }

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
        private $name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'department', type: 'string', length: 255, nullable: true)]
        private $department;

        /**
         * @var string
         */
        #[ORM\Column(name: 'login_id', type: 'string', length: 255)]
        private $login_id;

        /**
         * @var string|null
         */
        #[Assert\NotBlank]
        #[Assert\Length(max: 4096)]
        private $plainPassword;

        /**
         * @var string
         */
        #[ORM\Column(name: 'password', type: 'string', length: 255)]
        private $password;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'salt', type: 'string', length: 255, nullable: true)]
        private $salt;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', options: ['unsigned' => true])]
        private $sort_no;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'two_factor_auth_key', type: 'string', length: 255, nullable: true, options: ['fixed' => false])]
        private $two_factor_auth_key;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'two_factor_auth_enabled', type: 'boolean', nullable: false, options: ['default' => false])]
        private $two_factor_auth_enabled = false;

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
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'login_date', type: 'datetimetz', nullable: true)]
        private $login_date;

        /**
         * @var Master\Work|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Work::class)]
        #[ORM\JoinColumn(name: 'work_id', referencedColumnName: 'id')]
        private $Work;

        /**
         * @var Master\Authority|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Authority::class)]
        #[ORM\JoinColumn(name: 'authority_id', referencedColumnName: 'id')]
        private $Authority;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
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
        public function setName($name = null): Member
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string|null
         */
        public function getName(): ?string
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
        public function setDepartment($department = null): Member
        {
            $this->department = $department;

            return $this;
        }

        /**
         * Get department.
         *
         * @return string|null
         */
        public function getDepartment(): ?string
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
        public function setLoginId($loginId): Member
        {
            $this->login_id = $loginId;

            return $this;
        }

        /**
         * Get loginId.
         *
         * @return string
         */
        public function getLoginId(): string
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
        public function setPlainPassword(?string $password): static
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
        public function setPassword($password): Member
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         *
         * @return string
         */
        #[\Override]
        public function getPassword(): string
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
        public function setSalt($salt): Member
        {
            $this->salt = $salt;

            return $this;
        }

        /**
         * Get salt.
         *
         * @return string|null
         */
        #[\Override]
        public function getSalt(): ?string
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
        public function setSortNo($sortNo): Member
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo(): int
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
        public function setTwoFactorAuthKey($two_factor_auth_key): Member
        {
            $this->two_factor_auth_key = $two_factor_auth_key;

            return $this;
        }

        /**
         * Get twoFactorAuthKey.
         *
         * @return string
         */
        public function getTwoFactorAuthKey(): string
        {
            return $this->two_factor_auth_key;
        }

        /**
         * Set twoFactorAuthEnabled.
         *
         * @param bool $two_factor_auth_enabled
         *
         * @return Member
         */
        public function setTwoFactorAuthEnabled($two_factor_auth_enabled): Member
        {
            $this->two_factor_auth_enabled = $two_factor_auth_enabled;

            return $this;
        }

        /**
         * Get twoFactorAuthEnabled.
         *
         * @return bool
         */
        public function isTwoFactorAuthEnabled(): bool
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
        public function setCreateDate($createDate): Member
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
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
        public function setUpdateDate($updateDate): Member
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
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
        public function setLoginDate($loginDate = null): Member
        {
            $this->login_date = $loginDate;

            return $this;
        }

        /**
         * Get loginDate.
         *
         * @return \DateTime|null
         */
        public function getLoginDate(): ?\DateTime
        {
            return $this->login_date;
        }

        /**
         * Set Work
         *
         * @param Master\Work|null $work
         *
         * @return Member
         */
        public function setWork(?Master\Work $work = null): Member
        {
            $this->Work = $work;

            return $this;
        }

        /**
         * Get work.
         *
         * @return Master\Work|null
         */
        public function getWork(): ?Master\Work
        {
            return $this->Work;
        }

        /**
         * Set authority.
         *
         * @param Master\Authority|null $authority
         *
         * @return Member
         */
        public function setAuthority(?Master\Authority $authority = null): Member
        {
            $this->Authority = $authority;

            return $this;
        }

        /**
         * Get authority.
         *
         * @return Master\Authority|null
         */
        public function getAuthority(): ?Master\Authority
        {
            return $this->Authority;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return Member
         */
        public function setCreator(?Member $creator = null): Member
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
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
        #[\Override]
        public function serialize(): string
        {
            // see https://symfony.com/doc/2.7/security/entity_provider.html#create-your-user-entity
            // MemberRepository::loadUserByIdentifier() で Work をチェックしているため、ここでは不要
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
        #[\Override]
        public function unserialize($serialized): void
        {
            [$this->id, $this->login_id, $this->password, $this->salt] = unserialize($serialized);
        }

        #[\Override]
        public function getUserIdentifier(): string
        {
            return $this->login_id;
        }

        public function __serialize(): array
        {
            return ['p' => $this->serialize()];
        }

        /**
         * @param array<string, mixed> $data
         */
        public function __unserialize(array $data): void
        {
            if (isset($data['p']) && is_string($data['p'])) {
                $this->unserialize($data['p']);
            }
        }
    }
}
