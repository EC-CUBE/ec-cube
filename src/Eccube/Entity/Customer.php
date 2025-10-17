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
use Eccube\Repository\CustomerRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

if (!class_exists(Customer::class)) {
    /**
     * Customer
     */
    #[ORM\Table(name: 'dtb_customer')]
    #[ORM\Index(columns: ['buy_times'], name: 'dtb_customer_buy_times_idx')]
    #[ORM\Index(columns: ['buy_total'], name: 'dtb_customer_buy_total_idx')]
    #[ORM\Index(columns: ['create_date'], name: 'dtb_customer_create_date_idx')]
    #[ORM\Index(columns: ['update_date'], name: 'dtb_customer_update_date_idx')]
    #[ORM\Index(name: 'dtb_customer_last_buy_date_idx', columns: ['last_buy_date'])]
    #[ORM\Index(columns: ['email'], name: 'dtb_customer_email_idx')]
    #[ORM\UniqueConstraint(name: 'secret_key', columns: ['secret_key'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CustomerRepository::class)]
    class Customer extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface, \Serializable, \Stringable
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name01', type: 'string', length: 255)]
        private $name01;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name02', type: 'string', length: 255)]
        private $name02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana01', type: 'string', length: 255, nullable: true)]
        private $kana01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana02', type: 'string', length: 255, nullable: true)]
        private $kana02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'company_name', type: 'string', length: 255, nullable: true)]
        private $company_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'postal_code', type: 'string', length: 8, nullable: true)]
        private $postal_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr01', type: 'string', length: 255, nullable: true)]
        private $addr01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr02', type: 'string', length: 255, nullable: true)]
        private $addr02;

        /**
         * @var string
         */
        #[ORM\Column(name: 'email', type: 'string', length: 255)]
        private $email;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'phone_number', type: 'string', length: 14, nullable: true)]
        private $phone_number;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'birth', type: 'datetimetz', nullable: true)]
        private $birth;

        /**
         * @var string|null
         */
        #[Assert\NotBlank]
        #[Assert\Length(max: 4096)]
        private $plain_password;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'password', type: 'string', length: 255)]
        private $password;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'salt', type: 'string', length: 255, nullable: true)]
        private $salt;

        /**
         * @var string
         */
        #[ORM\Column(name: 'secret_key', type: 'string', length: 255)]
        private $secret_key;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'first_buy_date', type: 'datetimetz', nullable: true)]
        private $first_buy_date;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'last_buy_date', type: 'datetimetz', nullable: true)]
        private $last_buy_date;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'buy_times', type: 'decimal', precision: 10, scale: 0, nullable: true, options: ['unsigned' => true, 'default' => 0])]
        private $buy_times = '0';

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'buy_total', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true, 'default' => 0])]
        private $buy_total = '0';

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note', type: 'string', length: 4000, nullable: true)]
        private $note;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'reset_key', type: 'string', length: 255, nullable: true)]
        private $reset_key;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'reset_expire', type: 'datetimetz', nullable: true)]
        private $reset_expire;

        /**
         * @var string
         */
        #[ORM\Column(name: 'point', type: 'decimal', precision: 12, scale: 0, options: ['unsigned' => false, 'default' => 0])]
        private $point = '0';

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
         * @var \Doctrine\Common\Collections\Collection<int,CustomerFavoriteProduct>
         */
        #[ORM\OneToMany(mappedBy: 'Customer', targetEntity: CustomerFavoriteProduct::class, cascade: ['remove'])]
        private $CustomerFavoriteProducts;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,CustomerAddress>
         */
        #[ORM\OneToMany(targetEntity: CustomerAddress::class, mappedBy: 'Customer', cascade: ['remove'])]
        #[ORM\OrderBy(['id' => 'ASC'])]
        private $CustomerAddresses;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,Order>
         */
        #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'Customer')]
        private $Orders;

        /**
         * @var Master\CustomerStatus|null
         */
        #[ORM\ManyToOne(targetEntity: Master\CustomerStatus::class)]
        #[ORM\JoinColumn(name: 'customer_status_id', referencedColumnName: 'id')]
        private $Status;

        /**
         * @var Master\Sex|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Sex::class)]
        #[ORM\JoinColumn(name: 'sex_id', referencedColumnName: 'id')]
        private $Sex;

        /**
         * @var Master\Job|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Job::class)]
        #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'id')]
        private $Job;

        /**
         * @var Master\Country|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private $Country;

        /**
         * @var Master\Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
            $this->CustomerAddresses = new \Doctrine\Common\Collections\ArrayCollection();
            $this->Orders = new \Doctrine\Common\Collections\ArrayCollection();

            $this->setBuyTimes('0');
            $this->setBuyTotal('0');
        }

        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName01().' '.$this->getName02();
        }

        /**
         * {@inheritdoc}
         */
        #[\Override]
        public function getRoles(): array
        {
            return ['ROLE_USER'];
        }

        /**
         * @return string
         */
        public function getUsername(): string
        {
            return $this->email;
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
         * @param ClassMetadata $metadata
         *
         * @return void
         */
        // TODO: できればFormTypeで行いたい
        public static function loadValidatorMetadata(ClassMetadata $metadata): void
        {
            $metadata->addConstraint(new UniqueEntity([
                'fields' => 'email',
                'message' => 'form_error.customer_already_exists',
                'repositoryMethod' => 'getNonWithdrawingCustomers',
            ]));
        }

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
         * Set name01.
         *
         * @param string $name01
         *
         * @return Customer
         */
        public function setName01($name01): Customer
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string|null
         */
        public function getName01(): ?string
        {
            return $this->name01;
        }

        /**
         * Set name02.
         *
         * @param string $name02
         *
         * @return Customer
         */
        public function setName02($name02): Customer
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string|null
         */
        public function getName02(): ?string
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         *
         * @param string|null $kana01
         *
         * @return Customer
         */
        public function setKana01($kana01 = null): Customer
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01(): ?string
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         *
         * @param string|null $kana02
         *
         * @return Customer
         */
        public function setKana02($kana02 = null): Customer
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02(): ?string
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         *
         * @param string|null $companyName
         *
         * @return Customer
         */
        public function setCompanyName($companyName = null): Customer
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName(): ?string
        {
            return $this->company_name;
        }

        /**
         * Set postal_code.
         *
         * @param string|null $postal_code
         *
         * @return Customer
         */
        public function setPostalCode($postal_code = null): Customer
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         *
         * @param string|null $addr01
         *
         * @return Customer
         */
        public function setAddr01($addr01 = null): Customer
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01(): ?string
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         *
         * @param string|null $addr02
         *
         * @return Customer
         */
        public function setAddr02($addr02 = null): Customer
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02(): ?string
        {
            return $this->addr02;
        }

        /**
         * Set email.
         *
         * @param string $email
         *
         * @return Customer
         */
        public function setEmail($email): Customer
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         *
         * @return string|null
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }

        /**
         * Set phone_number.
         *
         * @param string|null $phone_number
         *
         * @return Customer
         */
        public function setPhoneNumber($phone_number = null): Customer
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber(): ?string
        {
            return $this->phone_number;
        }

        /**
         * Set birth.
         *
         * @param \DateTime|null $birth
         *
         * @return Customer
         */
        public function setBirth($birth = null): Customer
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         *
         * @return \DateTime|null
         */
        public function getBirth(): ?\DateTime
        {
            return $this->birth;
        }

        /**
         * @param string|null $password
         *
         * @return $this
         */
        public function setPlainPassword(?string $password): static
        {
            $this->plain_password = $password;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getPlainPassword(): ?string
        {
            return $this->plain_password;
        }

        /**
         * Set password.
         *
         * @param string|null $password
         *
         * @return Customer
         */
        public function setPassword($password = null): Customer
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         *
         * @return string|null
         */
        #[\Override]
        public function getPassword(): ?string
        {
            return $this->password;
        }

        /**
         * Set salt.
         *
         * @param string|null $salt
         *
         * @return Customer
         */
        public function setSalt($salt = null): Customer
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
         * Set secretKey.
         *
         * @param string $secretKey
         *
         * @return Customer
         */
        public function setSecretKey($secretKey): Customer
        {
            $this->secret_key = $secretKey;

            return $this;
        }

        /**
         * Get secretKey.
         *
         * @return string
         */
        public function getSecretKey(): string
        {
            return $this->secret_key;
        }

        /**
         * Set firstBuyDate.
         *
         * @param \DateTime|null $firstBuyDate
         *
         * @return Customer
         */
        public function setFirstBuyDate($firstBuyDate = null): Customer
        {
            $this->first_buy_date = $firstBuyDate;

            return $this;
        }

        /**
         * Get firstBuyDate.
         *
         * @return \DateTime|null
         */
        public function getFirstBuyDate(): ?\DateTime
        {
            return $this->first_buy_date;
        }

        /**
         * Set lastBuyDate.
         *
         * @param \DateTime|null $lastBuyDate
         *
         * @return Customer
         */
        public function setLastBuyDate($lastBuyDate = null): Customer
        {
            $this->last_buy_date = $lastBuyDate;

            return $this;
        }

        /**
         * Get lastBuyDate.
         *
         * @return \DateTime|null
         */
        public function getLastBuyDate(): ?\DateTime
        {
            return $this->last_buy_date;
        }

        /**
         * Set buyTimes.
         *
         * @param string|null $buyTimes
         *
         * @return Customer
         */
        public function setBuyTimes($buyTimes = null): Customer
        {
            $this->buy_times = $buyTimes;

            return $this;
        }

        /**
         * Get buyTimes.
         *
         * @return string|null
         */
        public function getBuyTimes(): ?string
        {
            return $this->buy_times;
        }

        /**
         * Set buyTotal.
         *
         * @param string|null $buyTotal
         *
         * @return Customer
         */
        public function setBuyTotal($buyTotal = null): Customer
        {
            $this->buy_total = $buyTotal;

            return $this;
        }

        /**
         * Get buyTotal.
         *
         * @return string|null
         */
        public function getBuyTotal(): ?string
        {
            return $this->buy_total;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Customer
         */
        public function setNote($note = null): Customer
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote(): ?string
        {
            return $this->note;
        }

        /**
         * Set resetKey.
         *
         * @param string|null $resetKey
         *
         * @return Customer
         */
        public function setResetKey($resetKey = null): Customer
        {
            $this->reset_key = $resetKey;

            return $this;
        }

        /**
         * Get resetKey.
         *
         * @return string|null
         */
        public function getResetKey(): ?string
        {
            return $this->reset_key;
        }

        /**
         * Set resetExpire.
         *
         * @param \DateTime|null $resetExpire
         *
         * @return Customer
         */
        public function setResetExpire($resetExpire = null): Customer
        {
            $this->reset_expire = $resetExpire;

            return $this;
        }

        /**
         * Get resetExpire.
         *
         * @return \DateTime|null
         */
        public function getResetExpire(): ?\DateTime
        {
            return $this->reset_expire;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Customer
         */
        public function setCreateDate($createDate): Customer
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
         * @return Customer
         */
        public function setUpdateDate($updateDate): Customer
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
         * Add customerFavoriteProduct.
         *
         * @param CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return Customer
         */
        public function addCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct): Customer
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @param CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct): bool
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return \Doctrine\Common\Collections\Collection<int,CustomerFavoriteProduct>
         */
        public function getCustomerFavoriteProducts(): \Doctrine\Common\Collections\Collection
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Add customerAddress.
         *
         * @param CustomerAddress $customerAddress
         *
         * @return Customer
         */
        public function addCustomerAddress(CustomerAddress $customerAddress): Customer
        {
            $this->CustomerAddresses[] = $customerAddress;

            return $this;
        }

        /**
         * Remove customerAddress.
         *
         * @param CustomerAddress $customerAddress
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerAddress(CustomerAddress $customerAddress): bool
        {
            return $this->CustomerAddresses->removeElement($customerAddress);
        }

        /**
         * Get customerAddresses.
         *
         * @return \Doctrine\Common\Collections\Collection<int,CustomerAddress>
         */
        public function getCustomerAddresses(): \Doctrine\Common\Collections\Collection
        {
            return $this->CustomerAddresses;
        }

        /**
         * Add order.
         *
         * @param Order $order
         *
         * @return Customer
         */
        public function addOrder(Order $order): Customer
        {
            $this->Orders[] = $order;

            return $this;
        }

        /**
         * Remove order.
         *
         * @param Order $order
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrder(Order $order): bool
        {
            return $this->Orders->removeElement($order);
        }

        /**
         * Get orders.
         *
         * @return \Doctrine\Common\Collections\Collection<int,Order>
         */
        public function getOrders(): \Doctrine\Common\Collections\Collection
        {
            return $this->Orders;
        }

        /**
         * Set status.
         *
         * @param Master\CustomerStatus|null $status
         *
         * @return Customer
         */
        public function setStatus(?Master\CustomerStatus $status = null): Customer
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         *
         * @return Master\CustomerStatus|null
         */
        public function getStatus(): ?Master\CustomerStatus
        {
            return $this->Status;
        }

        /**
         * Set sex.
         *
         * @param Master\Sex|null $sex
         *
         * @return Customer
         */
        public function setSex(?Master\Sex $sex = null): Customer
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         *
         * @return Master\Sex|null
         */
        public function getSex(): ?Master\Sex
        {
            return $this->Sex;
        }

        /**
         * Set job.
         *
         * @param Master\Job|null $job
         *
         * @return Customer
         */
        public function setJob(?Master\Job $job = null): Customer
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         *
         * @return Master\Job|null
         */
        public function getJob(): ?Master\Job
        {
            return $this->Job;
        }

        /**
         * Set country.
         *
         * @param Master\Country|null $country
         *
         * @return Customer
         */
        public function setCountry(?Master\Country $country = null): Customer
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return Master\Country|null
         */
        public function getCountry(): ?Master\Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param Master\Pref|null $pref
         *
         * @return Customer
         */
        public function setPref(?Master\Pref $pref = null): Customer
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Master\Pref|null
         */
        public function getPref(): ?Master\Pref
        {
            return $this->Pref;
        }

        /**
         * Set point
         *
         * @param string $point
         *
         * @return Customer
         */
        public function setPoint($point): Customer
        {
            $this->point = $point;

            return $this;
        }

        /**
         * Get point
         *
         * @return string|null
         */
        public function getPoint(): ?string
        {
            return $this->point;
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
            // CustomerRepository::loadUserByIdentifier() で Status をチェックしているため、ここでは不要
            return serialize([
                $this->id,
                $this->email,
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
            [$this->id, $this->email, $this->password, $this->salt] = unserialize($serialized);
        }

        #[\Override]
        public function getUserIdentifier(): string
        {
            return $this->email;
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
