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
     * getLawPrefName
     *
     * @param  string $default デフォルト
     * @return string
     */
    public function getLawPrefName($default = '')
    {
        if ($this->getLawPref()) {
            return $this->getLawPref()->getName();
        } else {
            return $default;
        }
    }

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
    private $law_company;

    /**
     * @var string
     */
    private $law_manager;

    /**
     * @var string
     */
    private $law_zip01;

    /**
     * @var string
     */
    private $law_zip02;

    /**
     * @var string
     */
    private $law_zipcode;

    /**
     * @var string
     */
    private $law_addr01;

    /**
     * @var string
     */
    private $law_addr02;

    /**
     * @var string
     */
    private $law_tel01;

    /**
     * @var string
     */
    private $law_tel02;

    /**
     * @var string
     */
    private $law_tel03;

    /**
     * @var string
     */
    private $law_fax01;

    /**
     * @var string
     */
    private $law_fax02;

    /**
     * @var string
     */
    private $law_fax03;

    /**
     * @var string
     */
    private $law_email;

    /**
     * @var string
     */
    private $law_url;

    /**
     * @var string
     */
    private $law_term01;

    /**
     * @var string
     */
    private $law_term02;

    /**
     * @var string
     */
    private $law_term03;

    /**
     * @var string
     */
    private $law_term04;

    /**
     * @var string
     */
    private $law_term05;

    /**
     * @var string
     */
    private $law_term06;

    /**
     * @var string
     */
    private $law_term07;

    /**
     * @var string
     */
    private $law_term08;

    /**
     * @var string
     */
    private $law_term09;

    /**
     * @var string
     */
    private $law_term10;

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
    private $free_rule;

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
     * @var string
     */
    private $point_rate;

    /**
     * @var string
     */
    private $welcome_point;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $top_tpl;

    /**
     * @var string
     */
    private $product_tpl;

    /**
     * @var string
     */
    private $detail_tpl;

    /**
     * @var string
     */
    private $mypage_tpl;

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
    private $regular_holiday_ids;

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
    private $downloadable_days;

    /**
     * @var integer
     */
    private $downloadable_days_unlimited;

    /**
     * @var \Eccube\Entity\Master\Country
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Master\Country
     */
    private $LawCountry;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $LawPref;

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
     * @param  string   $companyName
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
     * @param  string   $companyKana
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
     * @param  string   $zip01
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
     * @param  string   $zip02
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
     * @param  string   $zipcode
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
     * @param  string   $addr01
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
     * @param  string   $addr02
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
     * @param  string   $tel01
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
     * @param  string   $tel02
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
     * @param  string   $tel03
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
     * @param  string   $fax01
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
     * @param  string   $fax02
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
     * @param  string   $fax03
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
     * @param  string   $businessHour
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
     * Set law_company
     *
     * @param  string   $lawCompany
     * @return BaseInfo
     */
    public function setLawCompany($lawCompany)
    {
        $this->law_company = $lawCompany;

        return $this;
    }

    /**
     * Get law_company
     *
     * @return string
     */
    public function getLawCompany()
    {
        return $this->law_company;
    }

    /**
     * Set law_manager
     *
     * @param  string   $lawManager
     * @return BaseInfo
     */
    public function setLawManager($lawManager)
    {
        $this->law_manager = $lawManager;

        return $this;
    }

    /**
     * Get law_manager
     *
     * @return string
     */
    public function getLawManager()
    {
        return $this->law_manager;
    }

    /**
     * Set law_zip01
     *
     * @param  string   $lawZip01
     * @return BaseInfo
     */
    public function setLawZip01($lawZip01)
    {
        $this->law_zip01 = $lawZip01;

        return $this;
    }

    /**
     * Get law_zip01
     *
     * @return string
     */
    public function getLawZip01()
    {
        return $this->law_zip01;
    }

    /**
     * Set law_zip02
     *
     * @param  string   $lawZip02
     * @return BaseInfo
     */
    public function setLawZip02($lawZip02)
    {
        $this->law_zip02 = $lawZip02;

        return $this;
    }

    /**
     * Get law_zip02
     *
     * @return string
     */
    public function getLawZip02()
    {
        return $this->law_zip02;
    }

    /**
     * Set law_zipcode
     *
     * @param  string   $lawZipcode
     * @return BaseInfo
     */
    public function setLawZipcode($lawZipcode)
    {
        $this->law_zipcode = $lawZipcode;

        return $this;
    }

    /**
     * Get law_zipcode
     *
     * @return string
     */
    public function getLawZipcode()
    {
        return $this->law_zipcode;
    }

    /**
     * Set law_addr01
     *
     * @param  string   $lawAddr01
     * @return BaseInfo
     */
    public function setLawAddr01($lawAddr01)
    {
        $this->law_addr01 = $lawAddr01;

        return $this;
    }

    /**
     * Get law_addr01
     *
     * @return string
     */
    public function getLawAddr01()
    {
        return $this->law_addr01;
    }

    /**
     * Set law_addr02
     *
     * @param  string   $lawAddr02
     * @return BaseInfo
     */
    public function setLawAddr02($lawAddr02)
    {
        $this->law_addr02 = $lawAddr02;

        return $this;
    }

    /**
     * Get law_addr02
     *
     * @return string
     */
    public function getLawAddr02()
    {
        return $this->law_addr02;
    }

    /**
     * Set law_tel01
     *
     * @param  string   $lawTel01
     * @return BaseInfo
     */
    public function setLawTel01($lawTel01)
    {
        $this->law_tel01 = $lawTel01;

        return $this;
    }

    /**
     * Get law_tel01
     *
     * @return string
     */
    public function getLawTel01()
    {
        return $this->law_tel01;
    }

    /**
     * Set law_tel02
     *
     * @param  string   $lawTel02
     * @return BaseInfo
     */
    public function setLawTel02($lawTel02)
    {
        $this->law_tel02 = $lawTel02;

        return $this;
    }

    /**
     * Get law_tel02
     *
     * @return string
     */
    public function getLawTel02()
    {
        return $this->law_tel02;
    }

    /**
     * Set law_tel03
     *
     * @param  string   $lawTel03
     * @return BaseInfo
     */
    public function setLawTel03($lawTel03)
    {
        $this->law_tel03 = $lawTel03;

        return $this;
    }

    /**
     * Get law_tel03
     *
     * @return string
     */
    public function getLawTel03()
    {
        return $this->law_tel03;
    }

    /**
     * Set law_fax01
     *
     * @param  string   $lawFax01
     * @return BaseInfo
     */
    public function setLawFax01($lawFax01)
    {
        $this->law_fax01 = $lawFax01;

        return $this;
    }

    /**
     * Get law_fax01
     *
     * @return string
     */
    public function getLawFax01()
    {
        return $this->law_fax01;
    }

    /**
     * Set law_fax02
     *
     * @param  string   $lawFax02
     * @return BaseInfo
     */
    public function setLawFax02($lawFax02)
    {
        $this->law_fax02 = $lawFax02;

        return $this;
    }

    /**
     * Get law_fax02
     *
     * @return string
     */
    public function getLawFax02()
    {
        return $this->law_fax02;
    }

    /**
     * Set law_fax03
     *
     * @param  string   $lawFax03
     * @return BaseInfo
     */
    public function setLawFax03($lawFax03)
    {
        $this->law_fax03 = $lawFax03;

        return $this;
    }

    /**
     * Get law_fax03
     *
     * @return string
     */
    public function getLawFax03()
    {
        return $this->law_fax03;
    }

    /**
     * Set law_email
     *
     * @param  string   $lawEmail
     * @return BaseInfo
     */
    public function setLawEmail($lawEmail)
    {
        $this->law_email = $lawEmail;

        return $this;
    }

    /**
     * Get law_email
     *
     * @return string
     */
    public function getLawEmail()
    {
        return $this->law_email;
    }

    /**
     * Set law_url
     *
     * @param  string   $lawUrl
     * @return BaseInfo
     */
    public function setLawUrl($lawUrl)
    {
        $this->law_url = $lawUrl;

        return $this;
    }

    /**
     * Get law_url
     *
     * @return string
     */
    public function getLawUrl()
    {
        return $this->law_url;
    }

    /**
     * Set law_term01
     *
     * @param  string   $lawTerm01
     * @return BaseInfo
     */
    public function setLawTerm01($lawTerm01)
    {
        $this->law_term01 = $lawTerm01;

        return $this;
    }

    /**
     * Get law_term01
     *
     * @return string
     */
    public function getLawTerm01()
    {
        return $this->law_term01;
    }

    /**
     * Set law_term02
     *
     * @param  string   $lawTerm02
     * @return BaseInfo
     */
    public function setLawTerm02($lawTerm02)
    {
        $this->law_term02 = $lawTerm02;

        return $this;
    }

    /**
     * Get law_term02
     *
     * @return string
     */
    public function getLawTerm02()
    {
        return $this->law_term02;
    }

    /**
     * Set law_term03
     *
     * @param  string   $lawTerm03
     * @return BaseInfo
     */
    public function setLawTerm03($lawTerm03)
    {
        $this->law_term03 = $lawTerm03;

        return $this;
    }

    /**
     * Get law_term03
     *
     * @return string
     */
    public function getLawTerm03()
    {
        return $this->law_term03;
    }

    /**
     * Set law_term04
     *
     * @param  string   $lawTerm04
     * @return BaseInfo
     */
    public function setLawTerm04($lawTerm04)
    {
        $this->law_term04 = $lawTerm04;

        return $this;
    }

    /**
     * Get law_term04
     *
     * @return string
     */
    public function getLawTerm04()
    {
        return $this->law_term04;
    }

    /**
     * Set law_term05
     *
     * @param  string   $lawTerm05
     * @return BaseInfo
     */
    public function setLawTerm05($lawTerm05)
    {
        $this->law_term05 = $lawTerm05;

        return $this;
    }

    /**
     * Get law_term05
     *
     * @return string
     */
    public function getLawTerm05()
    {
        return $this->law_term05;
    }

    /**
     * Set law_term06
     *
     * @param  string   $lawTerm06
     * @return BaseInfo
     */
    public function setLawTerm06($lawTerm06)
    {
        $this->law_term06 = $lawTerm06;

        return $this;
    }

    /**
     * Get law_term06
     *
     * @return string
     */
    public function getLawTerm06()
    {
        return $this->law_term06;
    }

    /**
     * Set law_term07
     *
     * @param  string   $lawTerm07
     * @return BaseInfo
     */
    public function setLawTerm07($lawTerm07)
    {
        $this->law_term07 = $lawTerm07;

        return $this;
    }

    /**
     * Get law_term07
     *
     * @return string
     */
    public function getLawTerm07()
    {
        return $this->law_term07;
    }

    /**
     * Set law_term08
     *
     * @param  string   $lawTerm08
     * @return BaseInfo
     */
    public function setLawTerm08($lawTerm08)
    {
        $this->law_term08 = $lawTerm08;

        return $this;
    }

    /**
     * Get law_term08
     *
     * @return string
     */
    public function getLawTerm08()
    {
        return $this->law_term08;
    }

    /**
     * Set law_term09
     *
     * @param  string   $lawTerm09
     * @return BaseInfo
     */
    public function setLawTerm09($lawTerm09)
    {
        $this->law_term09 = $lawTerm09;

        return $this;
    }

    /**
     * Get law_term09
     *
     * @return string
     */
    public function getLawTerm09()
    {
        return $this->law_term09;
    }

    /**
     * Set law_term10
     *
     * @param  string   $lawTerm10
     * @return BaseInfo
     */
    public function setLawTerm10($lawTerm10)
    {
        $this->law_term10 = $lawTerm10;

        return $this;
    }

    /**
     * Get law_term10
     *
     * @return string
     */
    public function getLawTerm10()
    {
        return $this->law_term10;
    }

    /**
     * Set email01
     *
     * @param  string   $email01
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
     * @param  string   $email02
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
     * @param  string   $email03
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
     * @param  string   $email04
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
     * Set free_rule
     *
     * @param  string   $freeRule
     * @return BaseInfo
     */
    public function setFreeRule($freeRule)
    {
        $this->free_rule = $freeRule;

        return $this;
    }

    /**
     * Get free_rule
     *
     * @return string
     */
    public function getFreeRule()
    {
        return $this->free_rule;
    }

    /**
     * Set shop_name
     *
     * @param  string   $shopName
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
     * @param  string   $shopKana
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
     * @param  string   $shopNameEng
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
     * Set point_rate
     *
     * @param  string   $pointRate
     * @return BaseInfo
     */
    public function setPointRate($pointRate)
    {
        $this->point_rate = $pointRate;

        return $this;
    }

    /**
     * Get point_rate
     *
     * @return string
     */
    public function getPointRate()
    {
        return $this->point_rate;
    }

    /**
     * Set welcome_point
     *
     * @param  string   $welcomePoint
     * @return BaseInfo
     */
    public function setWelcomePoint($welcomePoint)
    {
        $this->welcome_point = $welcomePoint;

        return $this;
    }

    /**
     * Get welcome_point
     *
     * @return string
     */
    public function getWelcomePoint()
    {
        return $this->welcome_point;
    }

    /**
     * Set update_date
     *
     * @param  \DateTime $updateDate
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
     * Set top_tpl
     *
     * @param  string   $topTpl
     * @return BaseInfo
     */
    public function setTopTpl($topTpl)
    {
        $this->top_tpl = $topTpl;

        return $this;
    }

    /**
     * Get top_tpl
     *
     * @return string
     */
    public function getTopTpl()
    {
        return $this->top_tpl;
    }

    /**
     * Set product_tpl
     *
     * @param  string   $productTpl
     * @return BaseInfo
     */
    public function setProductTpl($productTpl)
    {
        $this->product_tpl = $productTpl;

        return $this;
    }

    /**
     * Get product_tpl
     *
     * @return string
     */
    public function getProductTpl()
    {
        return $this->product_tpl;
    }

    /**
     * Set detail_tpl
     *
     * @param  string   $detailTpl
     * @return BaseInfo
     */
    public function setDetailTpl($detailTpl)
    {
        $this->detail_tpl = $detailTpl;

        return $this;
    }

    /**
     * Get detail_tpl
     *
     * @return string
     */
    public function getDetailTpl()
    {
        return $this->detail_tpl;
    }

    /**
     * Set mypage_tpl
     *
     * @param  string   $mypageTpl
     * @return BaseInfo
     */
    public function setMypageTpl($mypageTpl)
    {
        $this->mypage_tpl = $mypageTpl;

        return $this;
    }

    /**
     * Get mypage_tpl
     *
     * @return string
     */
    public function getMypageTpl()
    {
        return $this->mypage_tpl;
    }

    /**
     * Set good_traded
     *
     * @param  string   $goodTraded
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
     * @param  string   $message
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
     * Set regular_holiday_ids
     *
     * @param  string   $regularHolidayIds
     * @return BaseInfo
     */
    public function setRegularHolidayIds($regularHolidayIds)
    {
        $this->regular_holiday_ids = $regularHolidayIds;

        return $this;
    }

    /**
     * Get regular_holiday_ids
     *
     * @return string
     */
    public function getRegularHolidayIds()
    {
        return $this->regular_holiday_ids;
    }

    /**
     * Set latitude
     *
     * @param  string   $latitude
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
     * @param  string   $longitude
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
     * Set downloadable_days
     *
     * @param  string   $downloadableDays
     * @return BaseInfo
     */
    public function setDownloadableDays($downloadableDays)
    {
        $this->downloadable_days = $downloadableDays;

        return $this;
    }

    /**
     * Get downloadable_days
     *
     * @return string
     */
    public function getDownloadableDays()
    {
        return $this->downloadable_days;
    }

    /**
     * Set downloadable_days_unlimited
     *
     * @param  integer  $downloadableDaysUnlimited
     * @return BaseInfo
     */
    public function setDownloadableDaysUnlimited($downloadableDaysUnlimited)
    {
        $this->downloadable_days_unlimited = $downloadableDaysUnlimited;

        return $this;
    }

    /**
     * Get downloadable_days_unlimited
     *
     * @return integer
     */
    public function getDownloadableDaysUnlimited()
    {
        return $this->downloadable_days_unlimited;
    }

    /**
     * Set Country
     *
     * @param  \Eccube\Entity\Master\Country $country
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
     * @param  \Eccube\Entity\Master\Pref $pref
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

    /**
     * Set LawCountry
     *
     * @param  \Eccube\Entity\Master\Country $lawCountry
     * @return BaseInfo
     */
    public function setLawCountry(\Eccube\Entity\Master\Country $lawCountry = null)
    {
        $this->LawCountry = $lawCountry;

        return $this;
    }

    /**
     * Get LawCountry
     *
     * @return \Eccube\Entity\Master\Country
     */
    public function getLawCountry()
    {
        return $this->LawCountry;
    }

    /**
     * Set LawPref
     *
     * @param  \Eccube\Entity\Master\Pref $lawPref
     * @return BaseInfo
     */
    public function setLawPref(\Eccube\Entity\Master\Pref $lawPref = null)
    {
        $this->LawPref = $lawPref;

        return $this;
    }

    /**
     * Get LawPref
     *
     * @return \Eccube\Entity\Master\Pref
     */
    public function getLawPref()
    {
        return $this->LawPref;
    }
}
