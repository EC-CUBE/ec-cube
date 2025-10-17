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
use Eccube\Entity\Master\Authority;
use Eccube\Repository\AuthorityRoleRepository;

if (!class_exists(AuthorityRole::class)) {
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
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'deny_url', type: 'string', length: 4000)]
        private $deny_url;

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
         * @var Authority|null
         *
         * @ORM\JoinColumns({
         *
         *   @ORM\JoinColumn(name="authority_id", referencedColumnName="id")
         * })
         *
         * @var Authority|null
         */
        #[ORM\ManyToOne(targetEntity: Authority::class)]
        #[ORM\JoinColumn(name: 'authority_id', referencedColumnName: 'id')]
        /** @phpstan-ignore-next-line */
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
         * Set denyUrl.
         *
         * @param string $denyUrl
         *
         * @return AuthorityRole
         */
        public function setDenyUrl($denyUrl): AuthorityRole
        {
            $this->deny_url = $denyUrl;

            return $this;
        }

        /**
         * Get denyUrl.
         *
         * @return string|null
         */
        public function getDenyUrl(): ?string
        {
            return $this->deny_url;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return AuthorityRole
         */
        public function setCreateDate($createDate): AuthorityRole
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
         * @return AuthorityRole
         */
        public function setUpdateDate($updateDate): AuthorityRole
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
         * Set authority.
         *
         * @param Authority|null $authority
         *
         * @return AuthorityRole
         */
        public function setAuthority(?Authority $authority = null): AuthorityRole
        {
            $this->Authority = $authority;

            return $this;
        }

        /**
         * Get authority.
         *
         * @return Authority|null
         */
        public function getAuthority(): ?Authority
        {
            return $this->Authority;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return AuthorityRole
         */
        public function setCreator(?Member $creator = null): AuthorityRole
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
    }
}
