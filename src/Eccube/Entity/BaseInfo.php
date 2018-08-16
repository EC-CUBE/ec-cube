<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\BaseInfo')) {
    /**
     * BaseInfo
     *
     * @ORM\Table(name="dtb_base_info")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\BaseInfoRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class BaseInfo extends \Eccube\Entity\AbstractEntity
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
         * @var string|null
         *
         * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
         */
        private $company_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="company_kana", type="string", length=255, nullable=true)
         */
        private $company_kana;

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
         * @var string|null
         *
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phone_number;

        /**
         * @var string|null
         *
         * @ORM\Column(name="business_hour", type="string", length=255, nullable=true)
         */
        private $business_hour;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email01", type="string", length=255, nullable=true)
         */
        private $email01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email02", type="string", length=255, nullable=true)
         */
        private $email02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email03", type="string", length=255, nullable=true)
         */
        private $email03;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email04", type="string", length=255, nullable=true)
         */
        private $email04;

        /**
         * @var string|null
         *
         * @ORM\Column(name="shop_name", type="string", length=255, nullable=true)
         */
        private $shop_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="shop_kana", type="string", length=255, nullable=true)
         */
        private $shop_kana;

        /**
         * @var string|null
         *
         * @ORM\Column(name="shop_name_eng", type="string", length=255, nullable=true)
         */
        private $shop_name_eng;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="good_traded", type="string", length=4000, nullable=true)
         */
        private $good_traded;

        /**
         * @var string|null
         *
         * @ORM\Column(name="message", type="string", length=4000, nullable=true)
         */
        private $message;

        /**
         * @var string|null
         *
         * @ORM\Column(name="delivery_free_amount", type="decimal", precision=12, scale=2, nullable=true)
         */
        private $delivery_free_amount;

        /**
         * @var int|null
         *
         * @ORM\Column(name="delivery_free_quantity", type="integer", nullable=true, options={"unsigned":true})
         */
        private $delivery_free_quantity;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_mypage_order_status_display", type="boolean", options={"default":true})
         */
        private $option_mypage_order_status_display = true;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_nostock_hidden", type="boolean", options={"default":false})
         */
        private $option_nostock_hidden = false;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_favorite_product", type="boolean", options={"default":true})
         */
        private $option_favorite_product = true;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_product_delivery_fee", type="boolean", options={"default":false})
         */
        private $option_product_delivery_fee = false;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_product_tax_rule", type="boolean", options={"default":false})
         */
        private $option_product_tax_rule = false;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_customer_activate", type="boolean", options={"default":true})
         */
        private $option_customer_activate = true;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_remember_me", type="boolean", options={"default":true})
         */
        private $option_remember_me = true;

        /**
         * @var string|null
         *
         * @ORM\Column(name="authentication_key", type="string", length=255, nullable=true)
         */
        private $authentication_key;

        /**
         * @var string|null
         *
         * @ORM\Column(name="php_path", type="string", length=255, nullable=true)
         */
        private $php_path;

        /**
         * @var boolean
         *
         * @ORM\Column(name="option_point", type="boolean", options={"default":true})
         */
        private $option_point = true;

        /**
         * @var string
         *
         * @ORM\Column(name="basic_point_rate", type="decimal", precision=10, scale=0, options={"unsigned":true, "default":1}, nullable=true)
         */
        private $basic_point_rate = '1';

        /**
         * @var string
         *
         * @ORM\Column(name="point_conversion_rate", type="decimal", precision=10, scale=0, options={"unsigned":true, "default":1}, nullable=true)
         */
        private $point_conversion_rate = '1';

        /**
         * @var \Eccube\Entity\Master\Country
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
         * })
         * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
         */
        private $Country;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
         */
        private $Pref;

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
         * Set companyName.
         *
         * @param string|null $companyName
         *
         * @return BaseInfo
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
         * Set companyKana.
         *
         * @param string|null $companyKana
         *
         * @return BaseInfo
         */
        public function setCompanyKana($companyKana = null)
        {
            $this->company_kana = $companyKana;

            return $this;
        }

        /**
         * Get companyKana.
         *
         * @return string|null
         */
        public function getCompanyKana()
        {
            return $this->company_kana;
        }

        /**
         * Set postal_code.
         *
         * @param string|null $postal_code
         *
         * @return BaseInfo
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
         * @return BaseInfo
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
         * @return BaseInfo
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
         * Set phone_number.
         *
         * @param string|null $phone_number
         *
         * @return BaseInfo
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
         * Set businessHour.
         *
         * @param string|null $businessHour
         *
         * @return BaseInfo
         */
        public function setBusinessHour($businessHour = null)
        {
            $this->business_hour = $businessHour;

            return $this;
        }

        /**
         * Get businessHour.
         *
         * @return string|null
         */
        public function getBusinessHour()
        {
            return $this->business_hour;
        }

        /**
         * Set email01.
         *
         * @param string|null $email01
         *
         * @return BaseInfo
         */
        public function setEmail01($email01 = null)
        {
            $this->email01 = $email01;

            return $this;
        }

        /**
         * Get email01.
         *
         * @return string|null
         */
        public function getEmail01()
        {
            return $this->email01;
        }

        /**
         * Set email02.
         *
         * @param string|null $email02
         *
         * @return BaseInfo
         */
        public function setEmail02($email02 = null)
        {
            $this->email02 = $email02;

            return $this;
        }

        /**
         * Get email02.
         *
         * @return string|null
         */
        public function getEmail02()
        {
            return $this->email02;
        }

        /**
         * Set email03.
         *
         * @param string|null $email03
         *
         * @return BaseInfo
         */
        public function setEmail03($email03 = null)
        {
            $this->email03 = $email03;

            return $this;
        }

        /**
         * Get email03.
         *
         * @return string|null
         */
        public function getEmail03()
        {
            return $this->email03;
        }

        /**
         * Set email04.
         *
         * @param string|null $email04
         *
         * @return BaseInfo
         */
        public function setEmail04($email04 = null)
        {
            $this->email04 = $email04;

            return $this;
        }

        /**
         * Get email04.
         *
         * @return string|null
         */
        public function getEmail04()
        {
            return $this->email04;
        }

        /**
         * Set shopName.
         *
         * @param string|null $shopName
         *
         * @return BaseInfo
         */
        public function setShopName($shopName = null)
        {
            $this->shop_name = $shopName;

            return $this;
        }

        /**
         * Get shopName.
         *
         * @return string|null
         */
        public function getShopName()
        {
            return $this->shop_name;
        }

        /**
         * Set shopKana.
         *
         * @param string|null $shopKana
         *
         * @return BaseInfo
         */
        public function setShopKana($shopKana = null)
        {
            $this->shop_kana = $shopKana;

            return $this;
        }

        /**
         * Get shopKana.
         *
         * @return string|null
         */
        public function getShopKana()
        {
            return $this->shop_kana;
        }

        /**
         * Set shopNameEng.
         *
         * @param string|null $shopNameEng
         *
         * @return BaseInfo
         */
        public function setShopNameEng($shopNameEng = null)
        {
            $this->shop_name_eng = $shopNameEng;

            return $this;
        }

        /**
         * Get shopNameEng.
         *
         * @return string|null
         */
        public function getShopNameEng()
        {
            return $this->shop_name_eng;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return BaseInfo
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
         * Set goodTraded.
         *
         * @param string|null $goodTraded
         *
         * @return BaseInfo
         */
        public function setGoodTraded($goodTraded = null)
        {
            $this->good_traded = $goodTraded;

            return $this;
        }

        /**
         * Get goodTraded.
         *
         * @return string|null
         */
        public function getGoodTraded()
        {
            return $this->good_traded;
        }

        /**
         * Set message.
         *
         * @param string|null $message
         *
         * @return BaseInfo
         */
        public function setMessage($message = null)
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         *
         * @return string|null
         */
        public function getMessage()
        {
            return $this->message;
        }

        /**
         * Set deliveryFreeAmount.
         *
         * @param string|null $deliveryFreeAmount
         *
         * @return BaseInfo
         */
        public function setDeliveryFreeAmount($deliveryFreeAmount = null)
        {
            $this->delivery_free_amount = $deliveryFreeAmount;

            return $this;
        }

        /**
         * Get deliveryFreeAmount.
         *
         * @return string|null
         */
        public function getDeliveryFreeAmount()
        {
            return $this->delivery_free_amount;
        }

        /**
         * Set deliveryFreeQuantity.
         *
         * @param int|null $deliveryFreeQuantity
         *
         * @return BaseInfo
         */
        public function setDeliveryFreeQuantity($deliveryFreeQuantity = null)
        {
            $this->delivery_free_quantity = $deliveryFreeQuantity;

            return $this;
        }

        /**
         * Get deliveryFreeQuantity.
         *
         * @return int|null
         */
        public function getDeliveryFreeQuantity()
        {
            return $this->delivery_free_quantity;
        }

        /**
         * Set optionMypageOrderStatusDisplay.
         *
         * @param boolean $optionMypageOrderStatusDisplay
         *
         * @return BaseInfo
         */
        public function setOptionMypageOrderStatusDisplay($optionMypageOrderStatusDisplay)
        {
            $this->option_mypage_order_status_display = $optionMypageOrderStatusDisplay;

            return $this;
        }

        /**
         * Get optionMypageOrderStatusDisplay.
         *
         * @return boolean
         */
        public function isOptionMypageOrderStatusDisplay()
        {
            return $this->option_mypage_order_status_display;
        }

        /**
         * Set optionNostockHidden.
         *
         * @param integer $optionNostockHidden
         *
         * @return BaseInfo
         */
        public function setOptionNostockHidden($optionNostockHidden)
        {
            $this->option_nostock_hidden = $optionNostockHidden;

            return $this;
        }

        /**
         * Get optionNostockHidden.
         *
         * @return boolean
         */
        public function isOptionNostockHidden()
        {
            return $this->option_nostock_hidden;
        }

        /**
         * Set optionFavoriteProduct.
         *
         * @param boolean $optionFavoriteProduct
         *
         * @return BaseInfo
         */
        public function setOptionFavoriteProduct($optionFavoriteProduct)
        {
            $this->option_favorite_product = $optionFavoriteProduct;

            return $this;
        }

        /**
         * Get optionFavoriteProduct.
         *
         * @return boolean
         */
        public function isOptionFavoriteProduct()
        {
            return $this->option_favorite_product;
        }

        /**
         * Set optionProductDeliveryFee.
         *
         * @param boolean $optionProductDeliveryFee
         *
         * @return BaseInfo
         */
        public function setOptionProductDeliveryFee($optionProductDeliveryFee)
        {
            $this->option_product_delivery_fee = $optionProductDeliveryFee;

            return $this;
        }

        /**
         * Get optionProductDeliveryFee.
         *
         * @return boolean
         */
        public function isOptionProductDeliveryFee()
        {
            return $this->option_product_delivery_fee;
        }

        /**
         * Set optionProductTaxRule.
         *
         * @param boolean $optionProductTaxRule
         *
         * @return BaseInfo
         */
        public function setOptionProductTaxRule($optionProductTaxRule)
        {
            $this->option_product_tax_rule = $optionProductTaxRule;

            return $this;
        }

        /**
         * Get optionProductTaxRule.
         *
         * @return boolean
         */
        public function isOptionProductTaxRule()
        {
            return $this->option_product_tax_rule;
        }

        /**
         * Set optionCustomerActivate.
         *
         * @param boolean $optionCustomerActivate
         *
         * @return BaseInfo
         */
        public function setOptionCustomerActivate($optionCustomerActivate)
        {
            $this->option_customer_activate = $optionCustomerActivate;

            return $this;
        }

        /**
         * Get optionCustomerActivate.
         *
         * @return boolean
         */
        public function isOptionCustomerActivate()
        {
            return $this->option_customer_activate;
        }

        /**
         * Set optionRememberMe.
         *
         * @param boolean $optionRememberMe
         *
         * @return BaseInfo
         */
        public function setOptionRememberMe($optionRememberMe)
        {
            $this->option_remember_me = $optionRememberMe;

            return $this;
        }

        /**
         * Get optionRememberMe.
         *
         * @return boolean
         */
        public function isOptionRememberMe()
        {
            return $this->option_remember_me;
        }

        /**
         * Set authenticationKey.
         *
         * @param string|null $authenticationKey
         *
         * @return BaseInfo
         */
        public function setAuthenticationKey($authenticationKey = null)
        {
            $this->authentication_key = $authenticationKey;

            return $this;
        }

        /**
         * Get authenticationKey.
         *
         * @return string|null
         */
        public function getAuthenticationKey()
        {
            return $this->authentication_key;
        }

        /**
         * Set country.
         *
         * @param \Eccube\Entity\Master\Country|null $country
         *
         * @return BaseInfo
         */
        public function setCountry(\Eccube\Entity\Master\Country $country = null)
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
         * @return BaseInfo
         */
        public function setPref(\Eccube\Entity\Master\Pref $pref = null)
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
         * Set optionPoint
         *
         * @param boolean $optionPoint
         *
         * @return BaseInfo
         */
        public function setOptionPoint($optionPoint)
        {
            $this->option_point = $optionPoint;

            return $this;
        }

        /**
         * Get optionPoint
         *
         * @return boolean
         */
        public function isOptionPoint()
        {
            return $this->option_point;
        }

        /**
         * Set pointConversionRate
         *
         * @param string $pointConversionRate
         *
         * @return BaseInfo
         */
        public function setPointConversionRate($pointConversionRate)
        {
            $this->point_conversion_rate = $pointConversionRate;

            return $this;
        }

        /**
         * Get pointConversionRate
         *
         * @return string
         */
        public function getPointConversionRate()
        {
            return $this->point_conversion_rate;
        }

        /**
         * Set basicPointRate
         *
         * @param string $basicPointRate
         *
         * @return BaseInfo
         */
        public function setBasicPointRate($basicPointRate)
        {
            $this->basic_point_rate = $basicPointRate;

            return $this;
        }

        /**
         * Get basicPointRate
         *
         * @return string
         */
        public function getBasicPointRate()
        {
            return $this->basic_point_rate;
        }

        /**
         * @return null|string
         */
        public function getPhpPath()
        {
            return $this->php_path;
        }

        /**
         * @param null|string $php_path
         * @return $this
         */
        public function setPhpPath($php_path)
        {
            $this->php_path = $php_path;

            return $this;
        }
    }
}
