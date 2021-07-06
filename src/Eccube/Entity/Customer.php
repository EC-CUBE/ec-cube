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
use Symfony\Component\Validator\Mapping\ClassMetadata;

if (!class_exists('\Eccube\Entity\Customer')) {
    /**
     * Customer
     *
     * @ORM\Table(name="dtb_customer", uniqueConstraints={@ORM\UniqueConstraint(name="secret_key", columns={"secret_key"})}, indexes={@ORM\Index(name="dtb_customer_buy_times_idx", columns={"buy_times"}), @ORM\Index(name="dtb_customer_buy_total_idx", columns={"buy_total"}), @ORM\Index(name="dtb_customer_create_date_idx", columns={"create_date"}), @ORM\Index(name="dtb_customer_update_date_idx", columns={"update_date"}), @ORM\Index(name="dtb_customer_last_buy_date_idx", columns={"last_buy_date"}), @ORM\Index(name="dtb_customer_email_idx", columns={"email"})})
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\CustomerRepository")
     */
    class Customer extends \Eccube\Entity\AbstractEntity implements UserInterface, \Serializable
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
         * @ORM\Column(name="name01", type="string", length=255)
         */
        private $name01;

        /**
         * @var string
         *
         * @ORM\Column(name="name02", type="string", length=255)
         */
        private $name02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana01", type="string", length=255, nullable=true)
         */
        private $kana01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana02", type="string", length=255, nullable=true)
         */
        private $kana02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
         */
        private $company_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr01", type="string", length=255, nullable=true)
         */
        private $addr01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr02", type="string", length=255, nullable=true)
         */
        private $addr02;

        /**
         * @var string
         *
         * @ORM\Column(name="email", type="string", length=255)
         */
        private $email;

        /**
         * @var string|null
         *
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phone_number;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="birth", type="datetimetz", nullable=true)
         */
        private $birth;

        /**
         * @var string|null
         *
         * @ORM\Column(name="password", type="string", length=255)
         */
        private $password;

        /**
         * @var string|null
         *
         * @ORM\Column(name="salt", type="string", length=255, nullable=true)
         */
        private $salt;

        /**
         * @var string
         *
         * @ORM\Column(name="secret_key", type="string", length=255)
         */
        private $secret_key;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="first_buy_date", type="datetimetz", nullable=true)
         */
        private $first_buy_date;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="last_buy_date", type="datetimetz", nullable=true)
         */
        private $last_buy_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="buy_times", type="decimal", precision=10, scale=0, nullable=true, options={"unsigned":true,"default":0})
         */
        private $buy_times = 0;

        /**
         * @var string|null
         *
         * @ORM\Column(name="buy_total", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true,"default":0})
         */
        private $buy_total = 0;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

        /**
         * @var string|null
         *
         * @ORM\Column(name="reset_key", type="string", length=255, nullable=true)
         */
        private $reset_key;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="reset_expire", type="datetimetz", nullable=true)
         */
        private $reset_expire;

        /**
         * @var string
         *
         * @ORM\Column(name="point", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $point = '0';

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
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerFavoriteProduct", mappedBy="Customer", cascade={"remove"})
         */
        private $CustomerFavoriteProducts;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerAddress", mappedBy="Customer", cascade={"remove"})
         * @ORM\OrderBy({
         *     "id"="ASC"
         * })
         */
        private $CustomerAddresses;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\Order", mappedBy="Customer")
         */
        private $Orders;

        /**
         * @var \Eccube\Entity\Master\CustomerStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\CustomerStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="customer_status_id", referencedColumnName="id")
         * })
         */
        private $Status;

        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="sex_id", referencedColumnName="id")
         * })
         */
        private $Sex;

        /**
         * @var \Eccube\Entity\Master\Job
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Job")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="job_id", referencedColumnName="id")
         * })
         */
        private $Job;

        /**
         * @var \Eccube\Entity\Master\Country
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
         * })
         */
        private $Country;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
            $this->CustomerAddresses = new \Doctrine\Common\Collections\ArrayCollection();
            $this->Orders = new \Doctrine\Common\Collections\ArrayCollection();

            $this->setBuyTimes(0);
            $this->setBuyTotal(0);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) ($this->getName01().' '.$this->getName02());
        }

        /**
         * {@inheritdoc}
         */
        public function getRoles()
        {
            return ['ROLE_USER'];
        }

        /**
         * {@inheritdoc}
         */
        public function getUsername()
        {
            return $this->email;
        }

        /**
         * {@inheritdoc}
         */
        public function eraseCredentials()
        {
        }

        // TODO: できればFormTypeで行いたい
        public static function loadValidatorMetadata(ClassMetadata $metadata)
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
         * @return int
         */
        public function getId()
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
        public function setName01($name01)
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string
         */
        public function getName01()
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
        public function setName02($name02)
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string
         */
        public function getName02()
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
        public function setKana01($kana01 = null)
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01()
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
        public function setKana02($kana02 = null)
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02()
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
        public function setCompanyName($companyName = null)
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName()
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
        public function setPostalCode($postal_code = null)
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode()
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
        public function setAddr01($addr01 = null)
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01()
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
        public function setAddr02($addr02 = null)
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02()
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
        public function setEmail($email)
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         *
         * @return string
         */
        public function getEmail()
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
        public function setPhoneNumber($phone_number = null)
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber()
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
        public function setBirth($birth = null)
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         *
         * @return \DateTime|null
         */
        public function getBirth()
        {
            return $this->birth;
        }

        /**
         * Set password.
         *
         * @param string|null $password
         *
         * @return Customer
         */
        public function setPassword($password = null)
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         *
         * @return string|null
         */
        public function getPassword()
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
        public function setSalt($salt = null)
        {
            $this->salt = $salt;

            return $this;
        }

        /**
         * Get salt.
         *
         * @return string|null
         */
        public function getSalt()
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
        public function setSecretKey($secretKey)
        {
            $this->secret_key = $secretKey;

            return $this;
        }

        /**
         * Get secretKey.
         *
         * @return string
         */
        public function getSecretKey()
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
        public function setFirstBuyDate($firstBuyDate = null)
        {
            $this->first_buy_date = $firstBuyDate;

            return $this;
        }

        /**
         * Get firstBuyDate.
         *
         * @return \DateTime|null
         */
        public function getFirstBuyDate()
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
        public function setLastBuyDate($lastBuyDate = null)
        {
            $this->last_buy_date = $lastBuyDate;

            return $this;
        }

        /**
         * Get lastBuyDate.
         *
         * @return \DateTime|null
         */
        public function getLastBuyDate()
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
        public function setBuyTimes($buyTimes = null)
        {
            $this->buy_times = $buyTimes;

            return $this;
        }

        /**
         * Get buyTimes.
         *
         * @return string|null
         */
        public function getBuyTimes()
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
        public function setBuyTotal($buyTotal = null)
        {
            $this->buy_total = $buyTotal;

            return $this;
        }

        /**
         * Get buyTotal.
         *
         * @return string|null
         */
        public function getBuyTotal()
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
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
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
        public function setResetKey($resetKey = null)
        {
            $this->reset_key = $resetKey;

            return $this;
        }

        /**
         * Get resetKey.
         *
         * @return string|null
         */
        public function getResetKey()
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
        public function setResetExpire($resetExpire = null)
        {
            $this->reset_expire = $resetExpire;

            return $this;
        }

        /**
         * Get resetExpire.
         *
         * @return \DateTime|null
         */
        public function getResetExpire()
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
         * @return Customer
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
         * Add customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return Customer
         */
        public function addCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct)
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct)
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerFavoriteProducts()
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Add customerAddress.
         *
         * @param \Eccube\Entity\CustomerAddress $customerAddress
         *
         * @return Customer
         */
        public function addCustomerAddress(CustomerAddress $customerAddress)
        {
            $this->CustomerAddresses[] = $customerAddress;

            return $this;
        }

        /**
         * Remove customerAddress.
         *
         * @param \Eccube\Entity\CustomerAddress $customerAddress
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerAddress(CustomerAddress $customerAddress)
        {
            return $this->CustomerAddresses->removeElement($customerAddress);
        }

        /**
         * Get customerAddresses.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerAddresses()
        {
            return $this->CustomerAddresses;
        }

        /**
         * Add order.
         *
         * @param \Eccube\Entity\Order $order
         *
         * @return Customer
         */
        public function addOrder(Order $order)
        {
            $this->Orders[] = $order;

            return $this;
        }

        /**
         * Remove order.
         *
         * @param \Eccube\Entity\Order $order
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrder(Order $order)
        {
            return $this->Orders->removeElement($order);
        }

        /**
         * Get orders.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getOrders()
        {
            return $this->Orders;
        }

        /**
         * Set status.
         *
         * @param \Eccube\Entity\Master\CustomerStatus|null $status
         *
         * @return Customer
         */
        public function setStatus(Master\CustomerStatus $status = null)
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         *
         * @return \Eccube\Entity\Master\CustomerStatus|null
         */
        public function getStatus()
        {
            return $this->Status;
        }

        /**
         * Set sex.
         *
         * @param \Eccube\Entity\Master\Sex|null $sex
         *
         * @return Customer
         */
        public function setSex(Master\Sex $sex = null)
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getSex()
        {
            return $this->Sex;
        }

        /**
         * Set job.
         *
         * @param \Eccube\Entity\Master\Job|null $job
         *
         * @return Customer
         */
        public function setJob(Master\Job $job = null)
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         *
         * @return \Eccube\Entity\Master\Job|null
         */
        public function getJob()
        {
            return $this->Job;
        }

        /**
         * Set country.
         *
         * @param \Eccube\Entity\Master\Country|null $country
         *
         * @return Customer
         */
        public function setCountry(Master\Country $country = null)
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return \Eccube\Entity\Master\Country|null
         */
        public function getCountry()
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param \Eccube\Entity\Master\Pref|null $pref
         *
         * @return Customer
         */
        public function setPref(Master\Pref $pref = null)
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return \Eccube\Entity\Master\Pref|null
         */
        public function getPref()
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
        public function setPoint($point)
        {
            $this->point = $point;

            return $this;
        }

        /**
         * Get point
         *
         * @return string
         */
        public function getPoint()
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
        public function serialize()
        {
            // see https://symfony.com/doc/2.7/security/entity_provider.html#create-your-user-entity
            // CustomerRepository::loadUserByUsername() で Status をチェックしているため、ここでは不要
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
        public function unserialize($serialized)
        {
            list(
                $this->id,
                $this->email,
                $this->password,
                $this->salt) = unserialize($serialized);
        }
    }
}
