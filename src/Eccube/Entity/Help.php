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

use Doctrine\ORM\Mapping as ORM;

/**
 * Help
 */
class Help extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $customer_agreement;

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
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

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
     * Set customer_agreement
     *
     * @param string $customerAgreement
     * @return Help
     */
    public function setCustomerAgreement($customerAgreement)
    {
        $this->customer_agreement = $customerAgreement;

        return $this;
    }

    /**
     * Get customer_agreement
     *
     * @return string 
     */
    public function getCustomerAgreement()
    {
        return $this->customer_agreement;
    }

    /**
     * Set law_company
     *
     * @param string $lawCompany
     * @return Help
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
     * @param string $lawManager
     * @return Help
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
     * @param string $lawZip01
     * @return Help
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
     * @param string $lawZip02
     * @return Help
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
     * @param string $lawZipcode
     * @return Help
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
     * @param string $lawAddr01
     * @return Help
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
     * @param string $lawAddr02
     * @return Help
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
     * @param string $lawTel01
     * @return Help
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
     * @param string $lawTel02
     * @return Help
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
     * @param string $lawTel03
     * @return Help
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
     * @param string $lawFax01
     * @return Help
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
     * @param string $lawFax02
     * @return Help
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
     * @param string $lawFax03
     * @return Help
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
     * @param string $lawEmail
     * @return Help
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
     * @param string $lawUrl
     * @return Help
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
     * @param string $lawTerm01
     * @return Help
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
     * @param string $lawTerm02
     * @return Help
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
     * @param string $lawTerm03
     * @return Help
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
     * @param string $lawTerm04
     * @return Help
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
     * @param string $lawTerm05
     * @return Help
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
     * @param string $lawTerm06
     * @return Help
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
     * @param string $lawTerm07
     * @return Help
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
     * @param string $lawTerm08
     * @return Help
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
     * @param string $lawTerm09
     * @return Help
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
     * @param string $lawTerm10
     * @return Help
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
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Help
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return Help
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
     * Set LawCountry
     *
     * @param \Eccube\Entity\Master\Country $lawCountry
     * @return Help
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
     * @param \Eccube\Entity\Master\Pref $lawPref
     * @return Help
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
