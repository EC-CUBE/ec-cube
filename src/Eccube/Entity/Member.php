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
use Eccube\Entity\Master\Work;
use Eccube\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

if (!class_exists(Member::class)) {
    /**
     * Member
     */
    #[ORM\Table(name: 'dtb_member')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: MemberRepository::class)]
    #[UniqueEntity(fields: 'login_id', message: 'form_error.member_already_exists')]
    class Member extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface, \Serializable, \Stringable
    {
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

        public function getUsername(): string
        {
            return $this->login_id;
        }

        /**
         * {@inheritdoc}
         */
        #[\Override]
        #[\Deprecated(message: 'eraseCredentials() is empty as we moved the logic to __serialize()', since: 'eccube/eccube 7.3')]
        public function eraseCredentials(): void
        {
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $name = null;

        #[ORM\Column(name: 'department', type: Types::STRING, length: 255, nullable: true)]
        private ?string $department = null;

        #[ORM\Column(name: 'login_id', type: Types::STRING, length: 255)]
        private ?string $login_id = null;

        /**
         * @var string|null
         */
        #[Assert\NotBlank]
        #[Assert\Length(max: 4096)]
        private $plainPassword;

        #[ORM\Column(name: 'password', type: Types::STRING, length: 255)]
        private ?string $password = null;

        #[ORM\Column(name: 'salt', type: Types::STRING, length: 255, nullable: true)]
        private ?string $salt = null;

        #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, options: ['unsigned' => true])]
        private ?int $sort_no = null;

        #[ORM\Column(name: 'two_factor_auth_key', type: Types::STRING, length: 255, nullable: true, options: ['fixed' => false])]
        private ?string $two_factor_auth_key = null;

        #[ORM\Column(name: 'two_factor_auth_enabled', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
        private bool $two_factor_auth_enabled = false;

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

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'login_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $login_date;

        #[ORM\ManyToOne(targetEntity: Work::class)]
        #[ORM\JoinColumn(name: 'work_id', referencedColumnName: 'id')]
        private ?Work $Work = null;

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
         * Set name.
         */
        public function setName(?string $name = null): Member
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * Set department.
         */
        public function setDepartment(?string $department = null): Member
        {
            $this->department = $department;

            return $this;
        }

        /**
         * Get department.
         */
        public function getDepartment(): ?string
        {
            return $this->department;
        }

        /**
         * Set loginId.
         */
        public function setLoginId(string $loginId): Member
        {
            $this->login_id = $loginId;

            return $this;
        }

        /**
         * Get loginId.
         */
        public function getLoginId(): string
        {
            return $this->login_id;
        }

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
         */
        public function setPassword(string $password): Member
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         */
        #[\Override]
        public function getPassword(): string
        {
            return $this->password;
        }

        /**
         * Set salt.
         */
        public function setSalt(?string $salt): Member
        {
            $this->salt = $salt;

            return $this;
        }

        /**
         * Get salt.
         */
        #[\Override]
        public function getSalt(): ?string
        {
            return $this->salt;
        }

        /**
         * Set sortNo.
         */
        public function setSortNo(int $sortNo): Member
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * Set twoFactorAuthKey.
         */
        public function setTwoFactorAuthKey(string $two_factor_auth_key): Member
        {
            $this->two_factor_auth_key = $two_factor_auth_key;

            return $this;
        }

        /**
         * Get twoFactorAuthKey.
         */
        public function getTwoFactorAuthKey(): ?string
        {
            return $this->two_factor_auth_key;
        }

        /**
         * Set twoFactorAuthEnabled.
         */
        public function setTwoFactorAuthEnabled(bool $two_factor_auth_enabled): Member
        {
            $this->two_factor_auth_enabled = $two_factor_auth_enabled;

            return $this;
        }

        /**
         * Get twoFactorAuthEnabled.
         */
        public function isTwoFactorAuthEnabled(): bool
        {
            return $this->two_factor_auth_enabled;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Member
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
        public function setUpdateDate(\DateTime $updateDate): Member
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
         * Set loginDate.
         */
        public function setLoginDate(?\DateTime $loginDate = null): Member
        {
            $this->login_date = $loginDate;

            return $this;
        }

        /**
         * Get loginDate.
         */
        public function getLoginDate(): ?\DateTime
        {
            return $this->login_date;
        }

        /**
         * Set Work
         */
        public function setWork(?Work $work = null): Member
        {
            $this->Work = $work;

            return $this;
        }

        /**
         * Get work.
         */
        public function getWork(): ?Work
        {
            return $this->Work;
        }

        /**
         * Set authority.
         */
        public function setAuthority(?Authority $authority = null): Member
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
        public function setCreator(?Member $creator = null): Member
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
