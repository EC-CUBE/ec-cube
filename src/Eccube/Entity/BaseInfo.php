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
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\BaseInfoRepository;

if (!class_exists(BaseInfo::class)) {
    #[ORM\Table(name: 'dtb_base_info')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: BaseInfoRepository::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    class BaseInfo extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 **/
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'company_name', type: 'string', length: 255, nullable: true)]
        private $company_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'company_kana', type: 'string', length: 255, nullable: true)]
        private $company_kana;

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
         * @var string|null
         */
        #[ORM\Column(name: 'phone_number', type: 'string', length: 14, nullable: true)]
        private $phone_number;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'business_hour', type: 'string', length: 255, nullable: true)]
        private $business_hour;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'email01', type: 'string', length: 255, nullable: true)]
        private $email01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'email02', type: 'string', length: 255, nullable: true)]
        private $email02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'email03', type: 'string', length: 255, nullable: true)]
        private $email03;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'email04', type: 'string', length: 255, nullable: true)]
        private $email04;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'shop_name', type: 'string', length: 255, nullable: true)]
        private $shop_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'shop_kana', type: 'string', length: 255, nullable: true)]
        private $shop_kana;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'shop_name_eng', type: 'string', length: 255, nullable: true)]
        private $shop_name_eng;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'good_traded', type: 'string', length: 4000, nullable: true)]
        private $good_traded;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'message', type: 'string', length: 4000, nullable: true)]
        private $message;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'delivery_free_amount', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true])]
        private $delivery_free_amount;

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'delivery_free_quantity', type: 'integer', nullable: true, options: ['unsigned' => true])]
        private $delivery_free_quantity;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_mypage_order_status_display', type: 'boolean', options: ['default' => true])]
        private $option_mypage_order_status_display = true;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_nostock_hidden', type: 'boolean', options: ['default' => false])]
        private $option_nostock_hidden = false;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_favorite_product', type: 'boolean', options: ['default' => true])]
        private $option_favorite_product = true;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_product_delivery_fee', type: 'boolean', options: ['default' => false])]
        private $option_product_delivery_fee = false;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'invoice_registration_number', type: 'string', length: 255, nullable: true)]
        private $invoice_registration_number;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_product_tax_rule', type: 'boolean', options: ['default' => false])]
        private $option_product_tax_rule = false;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_customer_activate', type: 'boolean', options: ['default' => true])]
        private $option_customer_activate = true;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_remember_me', type: 'boolean', options: ['default' => true])]
        private $option_remember_me = true;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_mail_notifier', type: 'boolean', options: ['default' => false])]
        private $option_mail_notifier = false;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'authentication_key', type: 'string', length: 255, nullable: true)]
        private $authentication_key;

        /**
         * @var string|null
         *
         * @deprecated 使用していないため、削除予定
         */
        #[ORM\Column(name: 'php_path', type: 'string', length: 255, nullable: true)]
        private $php_path;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'option_point', type: 'boolean', options: ['default' => true])]
        private $option_point = true;

        /**
         * @var string
         */
        #[ORM\Column(name: 'basic_point_rate', type: 'decimal', precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 1], nullable: true)]
        private $basic_point_rate = '1';

        /**
         * @var string
         */
        #[ORM\Column(name: 'point_conversion_rate', type: 'decimal', precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 1], nullable: true)]
        private $point_conversion_rate = '1';

        /**
         * @var Country|null
         */
        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private $Country;

        /**
         * @var Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'ga_id', type: 'string', length: 255, nullable: true)]
        private $gaId;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set companyName.
         *
         * @return BaseInfo
         */
        public function setCompanyName(?string $companyName = null): BaseInfo
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
         * Set companyKana.
         *
         * @return BaseInfo
         */
        public function setCompanyKana(?string $companyKana = null): BaseInfo
        {
            $this->company_kana = $companyKana;

            return $this;
        }

        /**
         * Get companyKana.
         *
         * @return string|null
         */
        public function getCompanyKana(): ?string
        {
            return $this->company_kana;
        }

        /**
         * Set postal_code.
         *
         * @return BaseInfo
         */
        public function setPostalCode(?string $postal_code = null): BaseInfo
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
         * @return BaseInfo
         */
        public function setAddr01(?string $addr01 = null): BaseInfo
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
         * @return BaseInfo
         */
        public function setAddr02(?string $addr02 = null): BaseInfo
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
         * Set phone_number.
         *
         * @return BaseInfo
         */
        public function setPhoneNumber(?string $phone_number = null): BaseInfo
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
         * Set businessHour.
         *
         * @return BaseInfo
         */
        public function setBusinessHour(?string $businessHour = null): BaseInfo
        {
            $this->business_hour = $businessHour;

            return $this;
        }

        /**
         * Get businessHour.
         *
         * @return string|null
         */
        public function getBusinessHour(): ?string
        {
            return $this->business_hour;
        }

        /**
         * Set email01.
         *
         * @return BaseInfo
         */
        public function setEmail01(?string $email01 = null): BaseInfo
        {
            $this->email01 = $email01;

            return $this;
        }

        /**
         * Get email01.
         *
         * @return string|null
         */
        public function getEmail01(): ?string
        {
            return $this->email01;
        }

        /**
         * Set email02.
         *
         * @return BaseInfo
         */
        public function setEmail02(?string $email02 = null): BaseInfo
        {
            $this->email02 = $email02;

            return $this;
        }

        /**
         * Get email02.
         *
         * @return string|null
         */
        public function getEmail02(): ?string
        {
            return $this->email02;
        }

        /**
         * Set email03.
         *
         * @return BaseInfo
         */
        public function setEmail03(?string $email03 = null): BaseInfo
        {
            $this->email03 = $email03;

            return $this;
        }

        /**
         * Get email03.
         *
         * @return string|null
         */
        public function getEmail03(): ?string
        {
            return $this->email03;
        }

        /**
         * Set email04.
         *
         * @return BaseInfo
         */
        public function setEmail04(?string $email04 = null): BaseInfo
        {
            $this->email04 = $email04;

            return $this;
        }

        /**
         * Get email04.
         *
         * @return string|null
         */
        public function getEmail04(): ?string
        {
            return $this->email04;
        }

        /**
         * Set shopName.
         *
         * @return BaseInfo
         */
        public function setShopName(?string $shopName = null): BaseInfo
        {
            $this->shop_name = $shopName;

            return $this;
        }

        /**
         * Get shopName.
         *
         * @return string|null
         */
        public function getShopName(): ?string
        {
            return $this->shop_name;
        }

        /**
         * Set shopKana.
         *
         * @return BaseInfo
         */
        public function setShopKana(?string $shopKana = null): BaseInfo
        {
            $this->shop_kana = $shopKana;

            return $this;
        }

        /**
         * Get shopKana.
         *
         * @return string|null
         */
        public function getShopKana(): ?string
        {
            return $this->shop_kana;
        }

        /**
         * Set shopNameEng.
         *
         * @return BaseInfo
         */
        public function setShopNameEng(?string $shopNameEng = null): BaseInfo
        {
            $this->shop_name_eng = $shopNameEng;

            return $this;
        }

        /**
         * Get shopNameEng.
         *
         * @return string|null
         */
        public function getShopNameEng(): ?string
        {
            return $this->shop_name_eng;
        }

        /**
         * Set updateDate.
         *
         * @return BaseInfo
         */
        public function setUpdateDate(\DateTime $updateDate): BaseInfo
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
         * Set goodTraded.
         *
         * @return BaseInfo
         */
        public function setGoodTraded(?string $goodTraded = null): BaseInfo
        {
            $this->good_traded = $goodTraded;

            return $this;
        }

        /**
         * Get goodTraded.
         *
         * @return string|null
         */
        public function getGoodTraded(): ?string
        {
            return $this->good_traded;
        }

        /**
         * Set message.
         *
         * @return BaseInfo
         */
        public function setMessage(?string $message = null): BaseInfo
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         *
         * @return string|null
         */
        public function getMessage(): ?string
        {
            return $this->message;
        }

        /**
         * Set deliveryFreeAmount.
         *
         * @return BaseInfo
         */
        public function setDeliveryFreeAmount(?string $deliveryFreeAmount = null): BaseInfo
        {
            $this->delivery_free_amount = $deliveryFreeAmount;

            return $this;
        }

        /**
         * Get deliveryFreeAmount.
         *
         * @return string|null
         */
        public function getDeliveryFreeAmount(): ?string
        {
            return $this->delivery_free_amount;
        }

        /**
         * Set deliveryFreeQuantity.
         *
         * @return BaseInfo
         */
        public function setDeliveryFreeQuantity(?int $deliveryFreeQuantity = null): BaseInfo
        {
            $this->delivery_free_quantity = $deliveryFreeQuantity;

            return $this;
        }

        /**
         * Get deliveryFreeQuantity.
         *
         * @return int|null
         */
        public function getDeliveryFreeQuantity(): ?int
        {
            return $this->delivery_free_quantity;
        }

        /**
         * Set optionMypageOrderStatusDisplay.
         *
         * @return BaseInfo
         */
        public function setOptionMypageOrderStatusDisplay(bool $optionMypageOrderStatusDisplay): BaseInfo
        {
            $this->option_mypage_order_status_display = $optionMypageOrderStatusDisplay;

            return $this;
        }

        /**
         * Get optionMypageOrderStatusDisplay.
         *
         * @return bool
         */
        public function isOptionMypageOrderStatusDisplay(): bool
        {
            return $this->option_mypage_order_status_display;
        }

        /**
         * Set optionNostockHidden.
         *
         * @return BaseInfo
         */
        public function setOptionNostockHidden(bool $optionNostockHidden): BaseInfo
        {
            $this->option_nostock_hidden = $optionNostockHidden;

            return $this;
        }

        /**
         * Get optionNostockHidden.
         *
         * @return bool
         */
        public function isOptionNostockHidden(): bool
        {
            return $this->option_nostock_hidden;
        }

        /**
         * Set optionFavoriteProduct.
         *
         * @return BaseInfo
         */
        public function setOptionFavoriteProduct(bool $optionFavoriteProduct): BaseInfo
        {
            $this->option_favorite_product = $optionFavoriteProduct;

            return $this;
        }

        /**
         * Get optionFavoriteProduct.
         *
         * @return bool
         */
        public function isOptionFavoriteProduct(): bool
        {
            return $this->option_favorite_product;
        }

        /**
         * Set optionProductDeliveryFee.
         *
         * @return BaseInfo
         */
        public function setOptionProductDeliveryFee(bool $optionProductDeliveryFee): BaseInfo
        {
            $this->option_product_delivery_fee = $optionProductDeliveryFee;

            return $this;
        }

        /**
         * Get optionProductDeliveryFee.
         *
         * @return bool
         */
        public function isOptionProductDeliveryFee(): bool
        {
            return $this->option_product_delivery_fee;
        }

        /**
         * Set invoiceRegistrationNumber.
         *
         * @return BaseInfo
         */
        public function setInvoiceRegistrationNumber(string $invoiceRegistrationNumber): BaseInfo
        {
            $this->invoice_registration_number = $invoiceRegistrationNumber;

            return $this;
        }

        /**
         * Get invoiceRegistrationNumber.
         *
         * @return string|null
         */
        public function getInvoiceRegistrationNumber(): ?string
        {
            return $this->invoice_registration_number;
        }

        /**
         * Set optionProductTaxRule.
         *
         * @return BaseInfo
         */
        public function setOptionProductTaxRule(bool $optionProductTaxRule): BaseInfo
        {
            $this->option_product_tax_rule = $optionProductTaxRule;

            return $this;
        }

        /**
         * Get optionProductTaxRule.
         *
         * @return bool
         */
        public function isOptionProductTaxRule(): bool
        {
            return $this->option_product_tax_rule;
        }

        /**
         * Set optionCustomerActivate.
         *
         * @return BaseInfo
         */
        public function setOptionCustomerActivate(bool $optionCustomerActivate): BaseInfo
        {
            $this->option_customer_activate = $optionCustomerActivate;

            return $this;
        }

        /**
         * Get optionCustomerActivate.
         *
         * @return bool
         */
        public function isOptionCustomerActivate(): bool
        {
            return $this->option_customer_activate;
        }

        /**
         * Set optionRememberMe.
         *
         * @return BaseInfo
         */
        public function setOptionRememberMe(bool $optionRememberMe): BaseInfo
        {
            $this->option_remember_me = $optionRememberMe;

            return $this;
        }

        /**
         * Get optionRememberMe.
         *
         * @return bool
         */
        public function isOptionRememberMe(): bool
        {
            return $this->option_remember_me;
        }

        /**
         * Set optionMailNotifier.
         *
         * @return BaseInfo
         */
        public function setOptionMailNotifier(bool $optionRememberMe): BaseInfo
        {
            $this->option_mail_notifier = $optionRememberMe;

            return $this;
        }

        /**
         * Get optionRememberMe.
         *
         * @return bool
         */
        public function isOptionMailNotifier(): bool
        {
            return $this->option_mail_notifier;
        }

        /**
         * Set authenticationKey.
         *
         * @return BaseInfo
         */
        public function setAuthenticationKey(?string $authenticationKey = null): BaseInfo
        {
            $this->authentication_key = $authenticationKey;

            return $this;
        }

        /**
         * Get authenticationKey.
         *
         * @return string|null
         */
        public function getAuthenticationKey(): ?string
        {
            return $this->authentication_key;
        }

        /**
         * Set country.
         *
         * @return BaseInfo
         */
        public function setCountry(?Country $country = null): BaseInfo
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return Country|null
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @return BaseInfo
         */
        public function setPref(?Pref $pref = null): BaseInfo
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Pref|null
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }

        /**
         * Set optionPoint
         *
         * @return BaseInfo
         */
        public function setOptionPoint(bool $optionPoint): BaseInfo
        {
            $this->option_point = $optionPoint;

            return $this;
        }

        /**
         * Get optionPoint
         *
         * @return bool
         */
        public function isOptionPoint(): bool
        {
            return $this->option_point;
        }

        /**
         * Set pointConversionRate
         *
         * @return BaseInfo
         */
        public function setPointConversionRate(?string $pointConversionRate): BaseInfo
        {
            $this->point_conversion_rate = $pointConversionRate;

            return $this;
        }

        /**
         * Get pointConversionRate
         *
         * @return string|null
         */
        public function getPointConversionRate(): ?string
        {
            return $this->point_conversion_rate;
        }

        /**
         * Set basicPointRate
         *
         * @return BaseInfo
         */
        public function setBasicPointRate(?string $basicPointRate): BaseInfo
        {
            $this->basic_point_rate = $basicPointRate;

            return $this;
        }

        /**
         * Get basicPointRate
         *
         * @return string
         */
        public function getBasicPointRate(): string
        {
            return $this->basic_point_rate;
        }

        /**
         * @return string|null
         *
         * @deprecated 使用していないため、削除予定
         */
        public function getPhpPath(): ?string
        {
            return $this->php_path;
        }

        /**
         * @deprecated 使用していないため、削除予定
         *
         * @return $this
         */
        public function setPhpPath(?string $php_path): static
        {
            $this->php_path = $php_path;

            return $this;
        }

        /**
         * Set gaId.
         *
         * @return BaseInfo
         */
        public function setGaId(?string $gaId = null): BaseInfo
        {
            $this->gaId = $gaId;

            return $this;
        }

        /**
         * Get gaId.
         *
         * @return string|null
         */
        public function getGaId(): ?string
        {
            return $this->gaId;
        }
    }
}
