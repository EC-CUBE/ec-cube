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
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\BaseInfoRepository;

#[ORM\Table(name: 'dtb_base_info')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: BaseInfoRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class BaseInfo extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'company_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $company_name = null;

    #[ORM\Column(name: 'company_kana', type: Types::STRING, length: 255, nullable: true)]
    private ?string $company_kana = null;

    #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 8, nullable: true)]
    private ?string $postal_code = null;

    #[ORM\Column(name: 'addr01', type: Types::STRING, length: 255, nullable: true)]
    private ?string $addr01 = null;

    #[ORM\Column(name: 'addr02', type: Types::STRING, length: 255, nullable: true)]
    private ?string $addr02 = null;

    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 14, nullable: true)]
    private ?string $phone_number = null;

    #[ORM\Column(name: 'business_hour', type: Types::STRING, length: 255, nullable: true)]
    private ?string $business_hour = null;

    #[ORM\Column(name: 'email01', type: Types::STRING, length: 255, nullable: true)]
    private ?string $email01 = null;

    #[ORM\Column(name: 'email02', type: Types::STRING, length: 255, nullable: true)]
    private ?string $email02 = null;

    #[ORM\Column(name: 'email03', type: Types::STRING, length: 255, nullable: true)]
    private ?string $email03 = null;

    #[ORM\Column(name: 'email04', type: Types::STRING, length: 255, nullable: true)]
    private ?string $email04 = null;

    #[ORM\Column(name: 'shop_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $shop_name = null;

    #[ORM\Column(name: 'shop_kana', type: Types::STRING, length: 255, nullable: true)]
    private ?string $shop_kana = null;

    #[ORM\Column(name: 'shop_name_eng', type: Types::STRING, length: 255, nullable: true)]
    private ?string $shop_name_eng = null;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private $update_date;

    #[ORM\Column(name: 'good_traded', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $good_traded = null;

    #[ORM\Column(name: 'message', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(name: 'delivery_free_amount', type: Types::DECIMAL, precision: 12, scale: 2, nullable: true, options: ['unsigned' => true])]
    private ?string $delivery_free_amount = null;

    #[ORM\Column(name: 'delivery_free_quantity', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $delivery_free_quantity = null;

    #[ORM\Column(name: 'option_mypage_order_status_display', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_mypage_order_status_display = true;

    #[ORM\Column(name: 'option_nostock_hidden', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $option_nostock_hidden = false;

    #[ORM\Column(name: 'option_favorite_product', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_favorite_product = true;

    #[ORM\Column(name: 'option_product_delivery_fee', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $option_product_delivery_fee = false;

    // クッキーポリシー同意機能はオプトイン（既定 OFF）。新規・アップグレードとも OFF で、
    // 店舗が管理画面「店舗基本情報」で ON にするまでバナー・同意連動は一切動作しない（後方互換）。
    #[ORM\Column(name: 'option_cookie_consent', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $option_cookie_consent = false;

    #[ORM\Column(name: 'invoice_registration_number', type: Types::STRING, length: 255, nullable: true)]
    private ?string $invoice_registration_number = null;

    #[ORM\Column(name: 'option_product_tax_rule', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $option_product_tax_rule = false;

    #[ORM\Column(name: 'option_customer_activate', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_customer_activate = true;

    #[ORM\Column(name: 'option_remember_me', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_remember_me = true;

    #[ORM\Column(name: 'option_mail_notifier', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $option_mail_notifier = false;

    #[ORM\Column(name: 'option_sanitize_csv_formulas', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_sanitize_csv_formulas = true;

    #[ORM\Column(name: 'authentication_key', type: Types::STRING, length: 255, nullable: true)]
    private ?string $authentication_key = null;

    #[ORM\Column(name: 'option_guest_purchase', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_guest_purchase = true;

    /**
     * @deprecated 使用していないため、削除予定
     */
    #[ORM\Column(name: 'php_path', type: Types::STRING, length: 255, nullable: true)]
    private ?string $php_path = null;

    #[ORM\Column(name: 'option_point', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $option_point = true;

    #[ORM\Column(name: 'basic_point_rate', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 1], nullable: true)]
    private ?string $basic_point_rate = '1';

    #[ORM\Column(name: 'point_conversion_rate', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 1], nullable: true)]
    private ?string $point_conversion_rate = '1';

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private ?Country $Country = null;

    #[ORM\ManyToOne(targetEntity: Pref::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
    private ?Pref $Pref = null;

    #[ORM\Column(name: 'ga_id', type: Types::STRING, length: 255, nullable: true)]
    private ?string $gaId = null;

    #[ORM\Column(name: 'acp_checkout_enabled', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $acp_checkout_enabled = false;

    #[ORM\Column(name: 'ucp_checkout_enabled', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $ucp_checkout_enabled = false;

    #[ORM\Column(name: 'ucp_catalog_requires_auth', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $ucp_catalog_requires_auth = false;

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
     * Set optionCookieConsent.
     */
    public function setOptionCookieConsent(bool $optionCookieConsent): BaseInfo
    {
        $this->option_cookie_consent = $optionCookieConsent;

        return $this;
    }

    /**
     * Get optionCookieConsent.
     */
    public function isOptionCookieConsent(): bool
    {
        return $this->option_cookie_consent;
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
     * Set optionGuestPurchase.
     */
    public function setOptionGuestPurchase(bool $optionGuestPurchase): BaseInfo
    {
        $this->option_guest_purchase = $optionGuestPurchase;

        return $this;
    }

    /**
     * Get optionGuestPurchase.
     */
    public function isOptionGuestPurchase(): bool
    {
        return $this->option_guest_purchase;
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
     * Set optionSanitizeCsvFormulas.
     */
    public function setOptionSanitizeCsvFormulas(bool $optionSanitizeCsvFormulas): BaseInfo
    {
        $this->option_sanitize_csv_formulas = $optionSanitizeCsvFormulas;

        return $this;
    }

    /**
     * Get optionSanitizeCsvFormulas.
     */
    public function isOptionSanitizeCsvFormulas(): bool
    {
        return $this->option_sanitize_csv_formulas;
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

    /**
     * Set acpCheckoutEnabled.
     */
    public function setAcpCheckoutEnabled(bool $acpCheckoutEnabled): BaseInfo
    {
        $this->acp_checkout_enabled = $acpCheckoutEnabled;

        return $this;
    }

    /**
     * Get acpCheckoutEnabled.
     */
    public function isAcpCheckoutEnabled(): bool
    {
        return $this->acp_checkout_enabled;
    }

    /**
     * Set ucpCheckoutEnabled.
     */
    public function setUcpCheckoutEnabled(bool $ucpCheckoutEnabled): BaseInfo
    {
        $this->ucp_checkout_enabled = $ucpCheckoutEnabled;

        return $this;
    }

    /**
     * Get ucpCheckoutEnabled.
     */
    public function isUcpCheckoutEnabled(): bool
    {
        return $this->ucp_checkout_enabled;
    }

    /**
     * Set ucpCatalogRequiresAuth.
     */
    public function setUcpCatalogRequiresAuth(bool $ucpCatalogRequiresAuth): BaseInfo
    {
        $this->ucp_catalog_requires_auth = $ucpCatalogRequiresAuth;

        return $this;
    }

    /**
     * Get ucpCatalogRequiresAuth.
     */
    public function isUcpCatalogRequiresAuth(): bool
    {
        return $this->ucp_catalog_requires_auth;
    }
}
