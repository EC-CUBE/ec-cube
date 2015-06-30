<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

/**
 * BaseInfo
 */
class BaseInfo extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $company_name;

    /**
     * @var string
     */
    private $company_kana;

    /**
     * @var string
     */
    private $zip01;

    /**
     * @var string
     */
    private $zip02;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $addr01;

    /**
     * @var string
     */
    private $addr02;

    /**
     * @var string
     */
    private $tel01;

    /**
     * @var string
     */
    private $tel02;

    /**
     * @var string
     */
    private $tel03;

    /**
     * @var string
     */
    private $fax01;

    /**
     * @var string
     */
    private $fax02;

    /**
     * @var string
     */
    private $fax03;

    /**
     * @var string
     */
    private $business_hour;

    /**
     * @var string
     */
    private $email01;

    /**
     * @var string
     */
    private $email02;

    /**
     * @var string
     */
    private $email03;

    /**
     * @var string
     */
    private $email04;

    /**
     * @var string
     */
    private $shop_name;

    /**
     * @var string
     */
    private $shop_kana;

    /**
     * @var string
     */
    private $shop_name_eng;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $good_traded;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $latitude;

    /**
     * @var string
     */
    private $longitude;

    /**
     * @var string
     */
    private $delivery_free_amount;

    /**
     * @var integer
     */
    private $delivery_free_quantity;

    /**
     * @var integer
     */
    private $option_multiple_shipping;

    /**
     * @var integer
     */
    private $option_mypage_order_status_display;

    /**
     * @var integer
     */
    private $nostock_hidden;

    /**
     * @var integer
     */
    private $option_favorite_product;

    /**
     * @var integer
     */
    private $option_product_delivery_fee;

    /**
     * @var integer
     */
    private $option_product_tax_rule;

    /**
     * @var integer
     */
    private $option_delivery_fee;

    /**
     * @var integer
     */
    private $option_customer_activate;

    /**
     * @var \Eccube\Entity\Master\Country
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set company_name
     *
     * @param string $companyName
     * @return BaseInfo
     */
    public function setCompanyName($companyName)
    {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get company_name
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * Set company_kana
     *
     * @param string $companyKana
     * @return BaseInfo
     */
    public function setCompanyKana($companyKana)
    {
        $this->company_kana = $companyKana;

        return $this;
    }

    /**
     * Get company_kana
     *
     * @return string 
     */
    public function getCompanyKana()
    {
        return $this->company_kana;
    }

    /**
     * Set zip01
     *
     * @param string $zip01
     * @return BaseInfo
     */
    public function setZip01($zip01)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01
     *
     * @return string 
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02
     *
     * @param string $zip02
     * @return BaseInfo
     */
    public function setZip02($zip02)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02
     *
     * @return string 
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return BaseInfo
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set addr01
     *
     * @param string $addr01
     * @return BaseInfo
     */
    public function setAddr01($addr01)
    {
        $this->addr01 = $addr01;

        return $this;
    }

    /**
     * Get addr01
     *
     * @return string 
     */
    public function getAddr01()
    {
        return $this->addr01;
    }

    /**
     * Set addr02
     *
     * @param string $addr02
     * @return BaseInfo
     */
    public function setAddr02($addr02)
    {
        $this->addr02 = $addr02;

        return $this;
    }

    /**
     * Get addr02
     *
     * @return string 
     */
    public function getAddr02()
    {
        return $this->addr02;
    }

    /**
     * Set tel01
     *
     * @param string $tel01
     * @return BaseInfo
     */
    public function setTel01($tel01)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01
     *
     * @return string 
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02
     *
     * @param string $tel02
     * @return BaseInfo
     */
    public function setTel02($tel02)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02
     *
     * @return string 
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03
     *
     * @param string $tel03
     * @return BaseInfo
     */
    public function setTel03($tel03)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03
     *
     * @return string 
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01
     *
     * @param string $fax01
     * @return BaseInfo
     */
    public function setFax01($fax01)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01
     *
     * @return string 
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02
     *
     * @param string $fax02
     * @return BaseInfo
     */
    public function setFax02($fax02)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02
     *
     * @return string 
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03
     *
     * @param string $fax03
     * @return BaseInfo
     */
    public function setFax03($fax03)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03
     *
     * @return string 
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set business_hour
     *
     * @param string $businessHour
     * @return BaseInfo
     */
    public function setBusinessHour($businessHour)
    {
        $this->business_hour = $businessHour;

        return $this;
    }

    /**
     * Get business_hour
     *
     * @return string 
     */
    public function getBusinessHour()
    {
        return $this->business_hour;
    }

    /**
     * Set email01
     *
     * @param string $email01
     * @return BaseInfo
     */
    public function setEmail01($email01)
    {
        $this->email01 = $email01;

        return $this;
    }

    /**
     * Get email01
     *
     * @return string 
     */
    public function getEmail01()
    {
        return $this->email01;
    }

    /**
     * Set email02
     *
     * @param string $email02
     * @return BaseInfo
     */
    public function setEmail02($email02)
    {
        $this->email02 = $email02;

        return $this;
    }

    /**
     * Get email02
     *
     * @return string 
     */
    public function getEmail02()
    {
        return $this->email02;
    }

    /**
     * Set email03
     *
     * @param string $email03
     * @return BaseInfo
     */
    public function setEmail03($email03)
    {
        $this->email03 = $email03;

        return $this;
    }

    /**
     * Get email03
     *
     * @return string 
     */
    public function getEmail03()
    {
        return $this->email03;
    }

    /**
     * Set email04
     *
     * @param string $email04
     * @return BaseInfo
     */
    public function setEmail04($email04)
    {
        $this->email04 = $email04;

        return $this;
    }

    /**
     * Get email04
     *
     * @return string 
     */
    public function getEmail04()
    {
        return $this->email04;
    }

    /**
     * Set shop_name
     *
     * @param string $shopName
     * @return BaseInfo
     */
    public function setShopName($shopName)
    {
        $this->shop_name = $shopName;

        return $this;
    }

    /**
     * Get shop_name
     *
     * @return string 
     */
    public function getShopName()
    {
        return $this->shop_name;
    }

    /**
     * Set shop_kana
     *
     * @param string $shopKana
     * @return BaseInfo
     */
    public function setShopKana($shopKana)
    {
        $this->shop_kana = $shopKana;

        return $this;
    }

    /**
     * Get shop_kana
     *
     * @return string 
     */
    public function getShopKana()
    {
        return $this->shop_kana;
    }

    /**
     * Set shop_name_eng
     *
     * @param string $shopNameEng
     * @return BaseInfo
     */
    public function setShopNameEng($shopNameEng)
    {
        $this->shop_name_eng = $shopNameEng;

        return $this;
    }

    /**
     * Get shop_name_eng
     *
     * @return string 
     */
    public function getShopNameEng()
    {
        return $this->shop_name_eng;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return BaseInfo
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set good_traded
     *
     * @param string $goodTraded
     * @return BaseInfo
     */
    public function setGoodTraded($goodTraded)
    {
        $this->good_traded = $goodTraded;

        return $this;
    }

    /**
     * Get good_traded
     *
     * @return string 
     */
    public function getGoodTraded()
    {
        return $this->good_traded;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return BaseInfo
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return BaseInfo
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return BaseInfo
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set delivery_free_amount
     *
     * @param string $deliveryFreeAmount
     * @return BaseInfo
     */
    public function setDeliveryFreeAmount($deliveryFreeAmount)
    {
        $this->delivery_free_amount = $deliveryFreeAmount;

        return $this;
    }

    /**
     * Get delivery_free_amount
     *
     * @return string 
     */
    public function getDeliveryFreeAmount()
    {
        return $this->delivery_free_amount;
    }

    /**
     * Set delivery_free_quantity
     *
     * @param integer $deliveryFreeQuantity
     * @return BaseInfo
     */
    public function setDeliveryFreeQuantity($deliveryFreeQuantity)
    {
        $this->delivery_free_quantity = $deliveryFreeQuantity;

        return $this;
    }

    /**
     * Get delivery_free_quantity
     *
     * @return integer 
     */
    public function getDeliveryFreeQuantity()
    {
        return $this->delivery_free_quantity;
    }

    /**
     * Set option_multiple_shipping
     *
     * @param integer $optionMultipleShipping
     * @return BaseInfo
     */
    public function setOptionMultipleShipping($optionMultipleShipping)
    {
        $this->option_multiple_shipping = $optionMultipleShipping;

        return $this;
    }

    /**
     * Get option_multiple_shipping
     *
     * @return integer 
     */
    public function getOptionMultipleShipping()
    {
        return $this->option_multiple_shipping;
    }

    /**
     * Set option_mypage_order_status_display
     *
     * @param integer $optionMypageOrderStatusDisplay
     * @return BaseInfo
     */
    public function setOptionMypageOrderStatusDisplay($optionMypageOrderStatusDisplay)
    {
        $this->option_mypage_order_status_display = $optionMypageOrderStatusDisplay;

        return $this;
    }

    /**
     * Get option_mypage_order_status_display
     *
     * @return integer 
     */
    public function getOptionMypageOrderStatusDisplay()
    {
        return $this->option_mypage_order_status_display;
    }

    /**
     * Set nostock_hidden
     *
     * @param integer $nostockHidden
     * @return BaseInfo
     */
    public function setNostockHidden($nostockHidden)
    {
        $this->nostock_hidden = $nostockHidden;

        return $this;
    }

    /**
     * Get nostock_hidden
     *
     * @return integer 
     */
    public function getNostockHidden()
    {
        return $this->nostock_hidden;
    }

    /**
     * Set option_favorite_product
     *
     * @param integer $optionFavoriteProduct
     * @return BaseInfo
     */
    public function setOptionFavoriteProduct($optionFavoriteProduct)
    {
        $this->option_favorite_product = $optionFavoriteProduct;

        return $this;
    }

    /**
     * Get option_favorite_product
     *
     * @return integer 
     */
    public function getOptionFavoriteProduct()
    {
        return $this->option_favorite_product;
    }

    /**
     * Set option_product_delivery_fee
     *
     * @param integer $optionProductDeliveryFee
     * @return BaseInfo
     */
    public function setOptionProductDeliveryFee($optionProductDeliveryFee)
    {
        $this->option_product_delivery_fee = $optionProductDeliveryFee;

        return $this;
    }

    /**
     * Get option_product_delivery_fee
     *
     * @return integer 
     */
    public function getOptionProductDeliveryFee()
    {
        return $this->option_product_delivery_fee;
    }

    /**
     * Set option_product_tax_rule
     *
     * @param integer $optionProductTaxRule
     * @return BaseInfo
     */
    public function setOptionProductTaxRule($optionProductTaxRule)
    {
        $this->option_product_tax_rule = $optionProductTaxRule;

        return $this;
    }

    /**
     * Get option_product_tax_rule
     *
     * @return integer 
     */
    public function getOptionProductTaxRule()
    {
        return $this->option_product_tax_rule;
    }

    /**
     * Set option_delivery_fee
     *
     * @param integer $optionDeliveryFee
     * @return BaseInfo
     */
    public function setOptionDeliveryFee($optionDeliveryFee)
    {
        $this->option_delivery_fee = $optionDeliveryFee;

        return $this;
    }

    /**
     * Get option_delivery_fee
     *
     * @return integer 
     */
    public function getOptionDeliveryFee()
    {
        return $this->option_delivery_fee;
    }

    /**
     * Set option_customer_activate
     *
     * @param integer $optionCustomerActivate
     * @return BaseInfo
     */
    public function setOptionCustomerActivate($optionCustomerActivate)
    {
        $this->option_customer_activate = $optionCustomerActivate;

        return $this;
    }

    /**
     * Get option_customer_activate
     *
     * @return integer 
     */
    public function getOptionCustomerActivate()
    {
        return $this->option_customer_activate;
    }

    /**
     * Set Country
     *
     * @param \Eccube\Entity\Master\Country $country
     * @return BaseInfo
     */
    public function setCountry(\Eccube\Entity\Master\Country $country = null)
    {
        $this->Country = $country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return \Eccube\Entity\Master\Country 
     */
    public function getCountry()
    {
        return $this->Country;
    }

    /**
     * Set Pref
     *
     * @param \Eccube\Entity\Master\Pref $pref
     * @return BaseInfo
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref 
     */
    public function getPref()
    {
        return $this->Pref;
    }
}
