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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\Job;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\Sex;
use Eccube\Repository\CustomerRepository;
use Eccube\Util\PasswordNormalizer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[UniqueEntity(fields: 'email', message: 'form_error.customer_already_exists', repositoryMethod: 'getNonWithdrawingCustomers')]
    class Customer extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface, \Serializable, \Stringable
    {
        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'name01', type: Types::STRING, length: 255)]
        private ?string $name01 = null;

        #[ORM\Column(name: 'name02', type: Types::STRING, length: 255)]
        private ?string $name02 = null;

        #[ORM\Column(name: 'kana01', type: Types::STRING, length: 255, nullable: true)]
        private ?string $kana01 = null;

        #[ORM\Column(name: 'kana02', type: Types::STRING, length: 255, nullable: true)]
        private ?string $kana02 = null;

        #[ORM\Column(name: 'company_name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $company_name = null;

        #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 8, nullable: true)]
        private ?string $postal_code = null;

        #[ORM\Column(name: 'addr01', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr01 = null;

        #[ORM\Column(name: 'addr02', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr02 = null;

        #[ORM\Column(name: 'email', type: Types::STRING, length: 255)]
        private ?string $email = null;

        #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 14, nullable: true)]
        private ?string $phone_number = null;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'birth', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $birth;

        /**
         * @var string|null
         */
        #[Assert\NotBlank]
        #[Assert\Length(max: 4096)]
        private $plain_password;

        #[ORM\Column(name: 'password', type: Types::STRING, length: 255)]
        private ?string $password = null;

        #[ORM\Column(name: 'salt', type: Types::STRING, length: 255, nullable: true)]
        private ?string $salt = null;

        #[ORM\Column(name: 'secret_key', type: Types::STRING, length: 255)]
        private ?string $secret_key = null;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'first_buy_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $first_buy_date;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'last_buy_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $last_buy_date;

        #[ORM\Column(name: 'buy_times', type: Types::DECIMAL, precision: 10, scale: 0, nullable: true, options: ['unsigned' => true, 'default' => 0])]
        private ?string $buy_times = '0';

        #[ORM\Column(name: 'buy_total', type: Types::DECIMAL, precision: 12, scale: 2, nullable: true, options: ['unsigned' => true, 'default' => 0])]
        private ?string $buy_total = '0';

        #[ORM\Column(name: 'note', type: Types::STRING, length: 4000, nullable: true)]
        private ?string $note = null;

        #[ORM\Column(name: 'reset_key', type: Types::STRING, length: 255, nullable: true)]
        private ?string $reset_key = null;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'reset_expire', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $reset_expire;

        #[ORM\Column(name: 'point', type: Types::DECIMAL, precision: 12, scale: 0, options: ['unsigned' => false, 'default' => 0])]
        private ?string $point = '0';

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
         * @var Collection<int, CustomerFavoriteProduct>
         */
        #[ORM\OneToMany(mappedBy: 'Customer', targetEntity: CustomerFavoriteProduct::class, cascade: ['remove'])]
        private $CustomerFavoriteProducts;

        /**
         * @var Collection<int, CustomerAddress>
         */
        #[ORM\OneToMany(targetEntity: CustomerAddress::class, mappedBy: 'Customer', cascade: ['remove'])]
        #[ORM\OrderBy(['id' => 'ASC'])]
        private $CustomerAddresses;

        /**
         * @var Collection<int, Order>
         */
        #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'Customer')]
        private $Orders;

        #[ORM\ManyToOne(targetEntity: CustomerStatus::class)]
        #[ORM\JoinColumn(name: 'customer_status_id', referencedColumnName: 'id')]
        private ?CustomerStatus $Status = null;

        #[ORM\ManyToOne(targetEntity: Sex::class)]
        #[ORM\JoinColumn(name: 'sex_id', referencedColumnName: 'id')]
        private ?Sex $Sex = null;

        #[ORM\ManyToOne(targetEntity: Job::class)]
        #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'id')]
        private ?Job $Job = null;

        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private ?Country $Country = null;

        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private ?Pref $Pref = null;

        #[ORM\ManyToOne(targetEntity: Payment::class)]
        #[ORM\JoinColumn(name: 'preferred_payment_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
        private ?Payment $PreferredPayment = null;

        #[ORM\ManyToOne(targetEntity: Delivery::class)]
        #[ORM\JoinColumn(name: 'preferred_delivery_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
        private ?Delivery $PreferredDelivery = null;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->CustomerFavoriteProducts = new ArrayCollection();
            $this->CustomerAddresses = new ArrayCollection();
            $this->Orders = new ArrayCollection();

            $this->setBuyTimes('0');
            $this->setBuyTotal('0');
        }

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

        public function getUsername(): string
        {
            return $this->email;
        }

        /**
         * {@inheritdoc}
         */
        #[\Override]
        public function eraseCredentials(): void
        {
        }

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set name01.
         */
        public function setName01(?string $name01): Customer
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         */
        public function getName01(): ?string
        {
            return $this->name01;
        }

        /**
         * Set name02.
         */
        public function setName02(?string $name02): Customer
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         */
        public function getName02(): ?string
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         */
        public function setKana01(?string $kana01 = null): Customer
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         */
        public function getKana01(): ?string
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         */
        public function setKana02(?string $kana02 = null): Customer
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         */
        public function getKana02(): ?string
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         */
        public function setCompanyName(?string $companyName = null): Customer
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         */
        public function getCompanyName(): ?string
        {
            return $this->company_name;
        }

        /**
         * Set postal_code.
         */
        public function setPostalCode(?string $postal_code = null): Customer
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         */
        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         */
        public function setAddr01(?string $addr01 = null): Customer
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         */
        public function getAddr01(): ?string
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         */
        public function setAddr02(?string $addr02 = null): Customer
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         */
        public function getAddr02(): ?string
        {
            return $this->addr02;
        }

        /**
         * Set email.
         */
        public function setEmail(?string $email): Customer
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }

        /**
         * Set phone_number.
         */
        public function setPhoneNumber(?string $phone_number = null): Customer
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         */
        public function getPhoneNumber(): ?string
        {
            return $this->phone_number;
        }

        /**
         * Set birth.
         */
        public function setBirth(?\DateTime $birth = null): Customer
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         */
        public function getBirth(): ?\DateTime
        {
            return $this->birth;
        }

        /**
         * @return $this
         */
        public function setPlainPassword(?string $password): static
        {
            // NIST SP 800-63B-4 に従い, 保存時とログイン照合時で表記ゆれを統一するため NFKC 正規化する.
            $this->plain_password = PasswordNormalizer::normalize($password);

            return $this;
        }

        public function getPlainPassword(): ?string
        {
            return $this->plain_password;
        }

        /**
         * Set password.
         */
        public function setPassword(?string $password = null): Customer
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         */
        #[\Override]
        public function getPassword(): ?string
        {
            return $this->password;
        }

        /**
         * Set salt.
         */
        public function setSalt(?string $salt = null): Customer
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
         * Set secretKey.
         */
        public function setSecretKey(string $secretKey): Customer
        {
            $this->secret_key = $secretKey;

            return $this;
        }

        /**
         * Get secretKey.
         */
        public function getSecretKey(): string
        {
            return $this->secret_key;
        }

        /**
         * Set firstBuyDate.
         */
        public function setFirstBuyDate(?\DateTime $firstBuyDate = null): Customer
        {
            $this->first_buy_date = $firstBuyDate;

            return $this;
        }

        /**
         * Get firstBuyDate.
         */
        public function getFirstBuyDate(): ?\DateTime
        {
            return $this->first_buy_date;
        }

        /**
         * Set lastBuyDate.
         */
        public function setLastBuyDate(?\DateTime $lastBuyDate = null): Customer
        {
            $this->last_buy_date = $lastBuyDate;

            return $this;
        }

        /**
         * Get lastBuyDate.
         */
        public function getLastBuyDate(): ?\DateTime
        {
            return $this->last_buy_date;
        }

        /**
         * Set buyTimes.
         */
        public function setBuyTimes(?string $buyTimes = null): Customer
        {
            $this->buy_times = $buyTimes;

            return $this;
        }

        /**
         * Get buyTimes.
         */
        public function getBuyTimes(): ?string
        {
            return $this->buy_times;
        }

        /**
         * Set buyTotal.
         */
        public function setBuyTotal(?string $buyTotal = null): Customer
        {
            $this->buy_total = $buyTotal;

            return $this;
        }

        /**
         * Get buyTotal.
         */
        public function getBuyTotal(): ?string
        {
            return $this->buy_total;
        }

        /**
         * Set note.
         */
        public function setNote(?string $note = null): Customer
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         */
        public function getNote(): ?string
        {
            return $this->note;
        }

        /**
         * Set resetKey.
         */
        public function setResetKey(?string $resetKey = null): Customer
        {
            $this->reset_key = $resetKey;

            return $this;
        }

        /**
         * Get resetKey.
         */
        public function getResetKey(): ?string
        {
            return $this->reset_key;
        }

        /**
         * Set resetExpire.
         */
        public function setResetExpire(?\DateTime $resetExpire = null): Customer
        {
            $this->reset_expire = $resetExpire;

            return $this;
        }

        /**
         * Get resetExpire.
         */
        public function getResetExpire(): ?\DateTime
        {
            return $this->reset_expire;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Customer
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
        public function setUpdateDate(\DateTime $updateDate): Customer
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
         * Add customerFavoriteProduct.
         */
        public function addCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct): Customer
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
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
         * @return Collection<int, CustomerFavoriteProduct>
         */
        public function getCustomerFavoriteProducts(): Collection
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Add customerAddress.
         */
        public function addCustomerAddress(CustomerAddress $customerAddress): Customer
        {
            $this->CustomerAddresses[] = $customerAddress;

            return $this;
        }

        /**
         * Remove customerAddress.
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
         * @return Collection<int, CustomerAddress>
         */
        public function getCustomerAddresses(): Collection
        {
            return $this->CustomerAddresses;
        }

        /**
         * Add order.
         */
        public function addOrder(Order $order): Customer
        {
            $this->Orders[] = $order;

            return $this;
        }

        /**
         * Remove order.
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
         * @return Collection<int, Order>
         */
        public function getOrders(): Collection
        {
            return $this->Orders;
        }

        /**
         * Set status.
         */
        public function setStatus(?CustomerStatus $status = null): Customer
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         */
        public function getStatus(): ?CustomerStatus
        {
            return $this->Status;
        }

        /**
         * Set sex.
         */
        public function setSex(?Sex $sex = null): Customer
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         */
        public function getSex(): ?Sex
        {
            return $this->Sex;
        }

        /**
         * Set job.
         */
        public function setJob(?Job $job = null): Customer
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         */
        public function getJob(): ?Job
        {
            return $this->Job;
        }

        /**
         * Set country.
         */
        public function setCountry(?Country $country = null): Customer
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         */
        public function setPref(?Pref $pref = null): Customer
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }

        /**
         * Set preferredPayment.
         */
        public function setPreferredPayment(?Payment $preferredPayment = null): Customer
        {
            $this->PreferredPayment = $preferredPayment;

            return $this;
        }

        /**
         * Get preferredPayment.
         */
        public function getPreferredPayment(): ?Payment
        {
            return $this->PreferredPayment;
        }

        /**
         * Set preferredDelivery.
         */
        public function setPreferredDelivery(?Delivery $preferredDelivery = null): Customer
        {
            $this->PreferredDelivery = $preferredDelivery;

            return $this;
        }

        /**
         * Get preferredDelivery.
         */
        public function getPreferredDelivery(): ?Delivery
        {
            return $this->PreferredDelivery;
        }

        /**
         * Set point
         */
        public function setPoint(?string $point): Customer
        {
            $this->point = $point;

            return $this;
        }

        /**
         * Get point
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
