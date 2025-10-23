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
         */
        public function setCompanyName(?string $companyName = null): BaseInfo
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
         * Set companyKana.
         */
        public function setCompanyKana(?string $companyKana = null): BaseInfo
        {
            $this->company_kana = $companyKana;

            return $this;
        }

        /**
         * Get companyKana.
         */
        public function getCompanyKana(): ?string
        {
            return $this->company_kana;
        }

        /**
         * Set postal_code.
         */
        public function setPostalCode(?string $postal_code = null): BaseInfo
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
        public function setAddr01(?string $addr01 = null): BaseInfo
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
        public function setAddr02(?string $addr02 = null): BaseInfo
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
         * Set phone_number.
         */
        public function setPhoneNumber(?string $phone_number = null): BaseInfo
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
         * Set businessHour.
         */
        public function setBusinessHour(?string $businessHour = null): BaseInfo
        {
            $this->business_hour = $businessHour;

            return $this;
        }

        /**
         * Get businessHour.
         */
        public function getBusinessHour(): ?string
        {
            return $this->business_hour;
        }

        /**
         * Set email01.
         */
        public function setEmail01(?string $email01 = null): BaseInfo
        {
            $this->email01 = $email01;

            return $this;
        }

        /**
         * Get email01.
         */
        public function getEmail01(): ?string
        {
            return $this->email01;
        }

        /**
         * Set email02.
         */
        public function setEmail02(?string $email02 = null): BaseInfo
        {
            $this->email02 = $email02;

            return $this;
        }

        /**
         * Get email02.
         */
        public function getEmail02(): ?string
        {
            return $this->email02;
        }

        /**
         * Set email03.
         */
        public function setEmail03(?string $email03 = null): BaseInfo
        {
            $this->email03 = $email03;

            return $this;
        }

        /**
         * Get email03.
         */
        public function getEmail03(): ?string
        {
            return $this->email03;
        }

        /**
         * Set email04.
         */
        public function setEmail04(?string $email04 = null): BaseInfo
        {
            $this->email04 = $email04;

            return $this;
        }

        /**
         * Get email04.
         */
        public function getEmail04(): ?string
        {
            return $this->email04;
        }

        /**
         * Set shopName.
         */
        public function setShopName(?string $shopName = null): BaseInfo
        {
            $this->shop_name = $shopName;

            return $this;
        }

        /**
         * Get shopName.
         */
        public function getShopName(): ?string
        {
            return $this->shop_name;
        }

        /**
         * Set shopKana.
         */
        public function setShopKana(?string $shopKana = null): BaseInfo
        {
            $this->shop_kana = $shopKana;

            return $this;
        }

        /**
         * Get shopKana.
         */
        public function getShopKana(): ?string
        {
            return $this->shop_kana;
        }

        /**
         * Set shopNameEng.
         */
        public function setShopNameEng(?string $shopNameEng = null): BaseInfo
        {
            $this->shop_name_eng = $shopNameEng;

            return $this;
        }

        /**
         * Get shopNameEng.
         */
        public function getShopNameEng(): ?string
        {
            return $this->shop_name_eng;
        }

        /**
         * Set updateDate.
         */
        public function setUpdateDate(\DateTime $updateDate): BaseInfo
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
         * Set goodTraded.
         */
        public function setGoodTraded(?string $goodTraded = null): BaseInfo
        {
            $this->good_traded = $goodTraded;

            return $this;
        }

        /**
         * Get goodTraded.
         */
        public function getGoodTraded(): ?string
        {
            return $this->good_traded;
        }

        /**
         * Set message.
         */
        public function setMessage(?string $message = null): BaseInfo
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         */
        public function getMessage(): ?string
        {
            return $this->message;
        }

        /**
         * Set deliveryFreeAmount.
         */
        public function setDeliveryFreeAmount(?string $deliveryFreeAmount = null): BaseInfo
        {
            $this->delivery_free_amount = $deliveryFreeAmount;

            return $this;
        }

        /**
         * Get deliveryFreeAmount.
         */
        public function getDeliveryFreeAmount(): ?string
        {
            return $this->delivery_free_amount;
        }

        /**
         * Set deliveryFreeQuantity.
         */
        public function setDeliveryFreeQuantity(?int $deliveryFreeQuantity = null): BaseInfo
        {
            $this->delivery_free_quantity = $deliveryFreeQuantity;

            return $this;
        }

        /**
         * Get deliveryFreeQuantity.
         */
        public function getDeliveryFreeQuantity(): ?int
        {
            return $this->delivery_free_quantity;
        }

        /**
         * Set optionMypageOrderStatusDisplay.
         */
        public function setOptionMypageOrderStatusDisplay(bool $optionMypageOrderStatusDisplay): BaseInfo
        {
            $this->option_mypage_order_status_display = $optionMypageOrderStatusDisplay;

            return $this;
        }

        /**
         * Get optionMypageOrderStatusDisplay.
         */
        public function isOptionMypageOrderStatusDisplay(): bool
        {
            return $this->option_mypage_order_status_display;
        }

        /**
         * Set optionNostockHidden.
         */
        public function setOptionNostockHidden(bool $optionNostockHidden): BaseInfo
        {
            $this->option_nostock_hidden = $optionNostockHidden;

            return $this;
        }

        /**
         * Get optionNostockHidden.
         */
        public function isOptionNostockHidden(): bool
        {
            return $this->option_nostock_hidden;
        }

        /**
         * Set optionFavoriteProduct.
         */
        public function setOptionFavoriteProduct(bool $optionFavoriteProduct): BaseInfo
        {
            $this->option_favorite_product = $optionFavoriteProduct;

            return $this;
        }

        /**
         * Get optionFavoriteProduct.
         */
        public function isOptionFavoriteProduct(): bool
        {
            return $this->option_favorite_product;
        }

        /**
         * Set optionProductDeliveryFee.
         */
        public function setOptionProductDeliveryFee(bool $optionProductDeliveryFee): BaseInfo
        {
            $this->option_product_delivery_fee = $optionProductDeliveryFee;

            return $this;
        }

        /**
         * Get optionProductDeliveryFee.
         */
        public function isOptionProductDeliveryFee(): bool
        {
            return $this->option_product_delivery_fee;
        }

        /**
         * Set invoiceRegistrationNumber.
         */
        public function setInvoiceRegistrationNumber(string $invoiceRegistrationNumber): BaseInfo
        {
            $this->invoice_registration_number = $invoiceRegistrationNumber;

            return $this;
        }

        /**
         * Get invoiceRegistrationNumber.
         */
        public function getInvoiceRegistrationNumber(): ?string
        {
            return $this->invoice_registration_number;
        }

        /**
         * Set optionProductTaxRule.
         */
        public function setOptionProductTaxRule(bool $optionProductTaxRule): BaseInfo
        {
            $this->option_product_tax_rule = $optionProductTaxRule;

            return $this;
        }

        /**
         * Get optionProductTaxRule.
         */
        public function isOptionProductTaxRule(): bool
        {
            return $this->option_product_tax_rule;
        }

        /**
         * Set optionCustomerActivate.
         */
        public function setOptionCustomerActivate(bool $optionCustomerActivate): BaseInfo
        {
            $this->option_customer_activate = $optionCustomerActivate;

            return $this;
        }

        /**
         * Get optionCustomerActivate.
         */
        public function isOptionCustomerActivate(): bool
        {
            return $this->option_customer_activate;
        }

        /**
         * Set optionRememberMe.
         */
        public function setOptionRememberMe(bool $optionRememberMe): BaseInfo
        {
            $this->option_remember_me = $optionRememberMe;

            return $this;
        }

        /**
         * Get optionRememberMe.
         */
        public function isOptionRememberMe(): bool
        {
            return $this->option_remember_me;
        }

        /**
         * Set optionMailNotifier.
         */
        public function setOptionMailNotifier(bool $optionRememberMe): BaseInfo
        {
            $this->option_mail_notifier = $optionRememberMe;

            return $this;
        }

        /**
         * Get optionRememberMe.
         */
        public function isOptionMailNotifier(): bool
        {
            return $this->option_mail_notifier;
        }

        /**
         * Set authenticationKey.
         */
        public function setAuthenticationKey(?string $authenticationKey = null): BaseInfo
        {
            $this->authentication_key = $authenticationKey;

            return $this;
        }

        /**
         * Get authenticationKey.
         */
        public function getAuthenticationKey(): ?string
        {
            return $this->authentication_key;
        }

        /**
         * Set country.
         */
        public function setCountry(?Country $country = null): BaseInfo
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
        public function setPref(?Pref $pref = null): BaseInfo
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
         * Set optionPoint
         */
        public function setOptionPoint(bool $optionPoint): BaseInfo
        {
            $this->option_point = $optionPoint;

            return $this;
        }

        /**
         * Get optionPoint
         */
        public function isOptionPoint(): bool
        {
            return $this->option_point;
        }

        /**
         * Set pointConversionRate
         */
        public function setPointConversionRate(?string $pointConversionRate): BaseInfo
        {
            $this->point_conversion_rate = $pointConversionRate;

            return $this;
        }

        /**
         * Get pointConversionRate
         */
        public function getPointConversionRate(): ?string
        {
            return $this->point_conversion_rate;
        }

        /**
         * Set basicPointRate
         */
        public function setBasicPointRate(?string $basicPointRate): BaseInfo
        {
            $this->basic_point_rate = $basicPointRate;

            return $this;
        }

        /**
         * Get basicPointRate
         */
        public function getBasicPointRate(): string
        {
            return $this->basic_point_rate;
        }

        /**
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
         */
        public function setGaId(?string $gaId = null): BaseInfo
        {
            $this->gaId = $gaId;

            return $this;
        }

        /**
         * Get gaId.
         */
        public function getGaId(): ?string
        {
            return $this->gaId;
        }
    }
}
