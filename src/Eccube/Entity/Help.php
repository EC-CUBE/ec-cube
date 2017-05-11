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
 *
 * @ORM\Table(name="dtb_help")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\HelpRepository")
 */
class Help extends \Eccube\Entity\AbstractEntity
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
     * @ORM\Column(name="customer_agreement", type="text", nullable=true)
     */
    private $customer_agreement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_company", type="string", length=255, nullable=true)
     */
    private $law_company;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_manager", type="string", length=255, nullable=true)
     */
    private $law_manager;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_zip01", type="string", length=3, nullable=true)
     */
    private $law_zip01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_zip02", type="string", length=4, nullable=true)
     */
    private $law_zip02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_zipcode", type="string", length=7, nullable=true)
     */
    private $law_zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_addr01", type="string", length=255, nullable=true)
     */
    private $law_addr01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_addr02", type="string", length=255, nullable=true)
     */
    private $law_addr02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_tel01", type="string", length=5, nullable=true)
     */
    private $law_tel01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_tel02", type="string", length=4, nullable=true)
     */
    private $law_tel02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_tel03", type="string", length=4, nullable=true)
     */
    private $law_tel03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_fax01", type="string", length=5, nullable=true)
     */
    private $law_fax01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_fax02", type="string", length=4, nullable=true)
     */
    private $law_fax02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_fax03", type="string", length=4, nullable=true)
     */
    private $law_fax03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_email", type="string", length=255, nullable=true)
     */
    private $law_email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_url", type="string", length=4000, nullable=true)
     */
    private $law_url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term01", type="text", nullable=true)
     */
    private $law_term01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term02", type="text", nullable=true)
     */
    private $law_term02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term03", type="text", nullable=true)
     */
    private $law_term03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term04", type="text", nullable=true)
     */
    private $law_term04;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term05", type="text", nullable=true)
     */
    private $law_term05;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term06", type="text", nullable=true)
     */
    private $law_term06;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term07", type="text", nullable=true)
     */
    private $law_term07;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term08", type="text", nullable=true)
     */
    private $law_term08;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term09", type="text", nullable=true)
     */
    private $law_term09;

    /**
     * @var string|null
     *
     * @ORM\Column(name="law_term10", type="text", nullable=true)
     */
    private $law_term10;

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
     * @var \Eccube\Entity\Master\Country
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="law_country_id", referencedColumnName="id")
     * })
     */
    private $LawCountry;

    /**
     * @var \Eccube\Entity\Master\Pref
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="law_pref", referencedColumnName="id")
     * })
     */
    private $LawPref;


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
     * Set customerAgreement.
     *
     * @param string|null $customerAgreement
     *
     * @return Help
     */
    public function setCustomerAgreement($customerAgreement = null)
    {
        $this->customer_agreement = $customerAgreement;

        return $this;
    }

    /**
     * Get customerAgreement.
     *
     * @return string|null
     */
    public function getCustomerAgreement()
    {
        return $this->customer_agreement;
    }

    /**
     * Set lawCompany.
     *
     * @param string|null $lawCompany
     *
     * @return Help
     */
    public function setLawCompany($lawCompany = null)
    {
        $this->law_company = $lawCompany;

        return $this;
    }

    /**
     * Get lawCompany.
     *
     * @return string|null
     */
    public function getLawCompany()
    {
        return $this->law_company;
    }

    /**
     * Set lawManager.
     *
     * @param string|null $lawManager
     *
     * @return Help
     */
    public function setLawManager($lawManager = null)
    {
        $this->law_manager = $lawManager;

        return $this;
    }

    /**
     * Get lawManager.
     *
     * @return string|null
     */
    public function getLawManager()
    {
        return $this->law_manager;
    }

    /**
     * Set lawZip01.
     *
     * @param string|null $lawZip01
     *
     * @return Help
     */
    public function setLawZip01($lawZip01 = null)
    {
        $this->law_zip01 = $lawZip01;

        return $this;
    }

    /**
     * Get lawZip01.
     *
     * @return string|null
     */
    public function getLawZip01()
    {
        return $this->law_zip01;
    }

    /**
     * Set lawZip02.
     *
     * @param string|null $lawZip02
     *
     * @return Help
     */
    public function setLawZip02($lawZip02 = null)
    {
        $this->law_zip02 = $lawZip02;

        return $this;
    }

    /**
     * Get lawZip02.
     *
     * @return string|null
     */
    public function getLawZip02()
    {
        return $this->law_zip02;
    }

    /**
     * Set lawZipcode.
     *
     * @param string|null $lawZipcode
     *
     * @return Help
     */
    public function setLawZipcode($lawZipcode = null)
    {
        $this->law_zipcode = $lawZipcode;

        return $this;
    }

    /**
     * Get lawZipcode.
     *
     * @return string|null
     */
    public function getLawZipcode()
    {
        return $this->law_zipcode;
    }

    /**
     * Set lawAddr01.
     *
     * @param string|null $lawAddr01
     *
     * @return Help
     */
    public function setLawAddr01($lawAddr01 = null)
    {
        $this->law_addr01 = $lawAddr01;

        return $this;
    }

    /**
     * Get lawAddr01.
     *
     * @return string|null
     */
    public function getLawAddr01()
    {
        return $this->law_addr01;
    }

    /**
     * Set lawAddr02.
     *
     * @param string|null $lawAddr02
     *
     * @return Help
     */
    public function setLawAddr02($lawAddr02 = null)
    {
        $this->law_addr02 = $lawAddr02;

        return $this;
    }

    /**
     * Get lawAddr02.
     *
     * @return string|null
     */
    public function getLawAddr02()
    {
        return $this->law_addr02;
    }

    /**
     * Set lawTel01.
     *
     * @param string|null $lawTel01
     *
     * @return Help
     */
    public function setLawTel01($lawTel01 = null)
    {
        $this->law_tel01 = $lawTel01;

        return $this;
    }

    /**
     * Get lawTel01.
     *
     * @return string|null
     */
    public function getLawTel01()
    {
        return $this->law_tel01;
    }

    /**
     * Set lawTel02.
     *
     * @param string|null $lawTel02
     *
     * @return Help
     */
    public function setLawTel02($lawTel02 = null)
    {
        $this->law_tel02 = $lawTel02;

        return $this;
    }

    /**
     * Get lawTel02.
     *
     * @return string|null
     */
    public function getLawTel02()
    {
        return $this->law_tel02;
    }

    /**
     * Set lawTel03.
     *
     * @param string|null $lawTel03
     *
     * @return Help
     */
    public function setLawTel03($lawTel03 = null)
    {
        $this->law_tel03 = $lawTel03;

        return $this;
    }

    /**
     * Get lawTel03.
     *
     * @return string|null
     */
    public function getLawTel03()
    {
        return $this->law_tel03;
    }

    /**
     * Set lawFax01.
     *
     * @param string|null $lawFax01
     *
     * @return Help
     */
    public function setLawFax01($lawFax01 = null)
    {
        $this->law_fax01 = $lawFax01;

        return $this;
    }

    /**
     * Get lawFax01.
     *
     * @return string|null
     */
    public function getLawFax01()
    {
        return $this->law_fax01;
    }

    /**
     * Set lawFax02.
     *
     * @param string|null $lawFax02
     *
     * @return Help
     */
    public function setLawFax02($lawFax02 = null)
    {
        $this->law_fax02 = $lawFax02;

        return $this;
    }

    /**
     * Get lawFax02.
     *
     * @return string|null
     */
    public function getLawFax02()
    {
        return $this->law_fax02;
    }

    /**
     * Set lawFax03.
     *
     * @param string|null $lawFax03
     *
     * @return Help
     */
    public function setLawFax03($lawFax03 = null)
    {
        $this->law_fax03 = $lawFax03;

        return $this;
    }

    /**
     * Get lawFax03.
     *
     * @return string|null
     */
    public function getLawFax03()
    {
        return $this->law_fax03;
    }

    /**
     * Set lawEmail.
     *
     * @param string|null $lawEmail
     *
     * @return Help
     */
    public function setLawEmail($lawEmail = null)
    {
        $this->law_email = $lawEmail;

        return $this;
    }

    /**
     * Get lawEmail.
     *
     * @return string|null
     */
    public function getLawEmail()
    {
        return $this->law_email;
    }

    /**
     * Set lawUrl.
     *
     * @param string|null $lawUrl
     *
     * @return Help
     */
    public function setLawUrl($lawUrl = null)
    {
        $this->law_url = $lawUrl;

        return $this;
    }

    /**
     * Get lawUrl.
     *
     * @return string|null
     */
    public function getLawUrl()
    {
        return $this->law_url;
    }

    /**
     * Set lawTerm01.
     *
     * @param string|null $lawTerm01
     *
     * @return Help
     */
    public function setLawTerm01($lawTerm01 = null)
    {
        $this->law_term01 = $lawTerm01;

        return $this;
    }

    /**
     * Get lawTerm01.
     *
     * @return string|null
     */
    public function getLawTerm01()
    {
        return $this->law_term01;
    }

    /**
     * Set lawTerm02.
     *
     * @param string|null $lawTerm02
     *
     * @return Help
     */
    public function setLawTerm02($lawTerm02 = null)
    {
        $this->law_term02 = $lawTerm02;

        return $this;
    }

    /**
     * Get lawTerm02.
     *
     * @return string|null
     */
    public function getLawTerm02()
    {
        return $this->law_term02;
    }

    /**
     * Set lawTerm03.
     *
     * @param string|null $lawTerm03
     *
     * @return Help
     */
    public function setLawTerm03($lawTerm03 = null)
    {
        $this->law_term03 = $lawTerm03;

        return $this;
    }

    /**
     * Get lawTerm03.
     *
     * @return string|null
     */
    public function getLawTerm03()
    {
        return $this->law_term03;
    }

    /**
     * Set lawTerm04.
     *
     * @param string|null $lawTerm04
     *
     * @return Help
     */
    public function setLawTerm04($lawTerm04 = null)
    {
        $this->law_term04 = $lawTerm04;

        return $this;
    }

    /**
     * Get lawTerm04.
     *
     * @return string|null
     */
    public function getLawTerm04()
    {
        return $this->law_term04;
    }

    /**
     * Set lawTerm05.
     *
     * @param string|null $lawTerm05
     *
     * @return Help
     */
    public function setLawTerm05($lawTerm05 = null)
    {
        $this->law_term05 = $lawTerm05;

        return $this;
    }

    /**
     * Get lawTerm05.
     *
     * @return string|null
     */
    public function getLawTerm05()
    {
        return $this->law_term05;
    }

    /**
     * Set lawTerm06.
     *
     * @param string|null $lawTerm06
     *
     * @return Help
     */
    public function setLawTerm06($lawTerm06 = null)
    {
        $this->law_term06 = $lawTerm06;

        return $this;
    }

    /**
     * Get lawTerm06.
     *
     * @return string|null
     */
    public function getLawTerm06()
    {
        return $this->law_term06;
    }

    /**
     * Set lawTerm07.
     *
     * @param string|null $lawTerm07
     *
     * @return Help
     */
    public function setLawTerm07($lawTerm07 = null)
    {
        $this->law_term07 = $lawTerm07;

        return $this;
    }

    /**
     * Get lawTerm07.
     *
     * @return string|null
     */
    public function getLawTerm07()
    {
        return $this->law_term07;
    }

    /**
     * Set lawTerm08.
     *
     * @param string|null $lawTerm08
     *
     * @return Help
     */
    public function setLawTerm08($lawTerm08 = null)
    {
        $this->law_term08 = $lawTerm08;

        return $this;
    }

    /**
     * Get lawTerm08.
     *
     * @return string|null
     */
    public function getLawTerm08()
    {
        return $this->law_term08;
    }

    /**
     * Set lawTerm09.
     *
     * @param string|null $lawTerm09
     *
     * @return Help
     */
    public function setLawTerm09($lawTerm09 = null)
    {
        $this->law_term09 = $lawTerm09;

        return $this;
    }

    /**
     * Get lawTerm09.
     *
     * @return string|null
     */
    public function getLawTerm09()
    {
        return $this->law_term09;
    }

    /**
     * Set lawTerm10.
     *
     * @param string|null $lawTerm10
     *
     * @return Help
     */
    public function setLawTerm10($lawTerm10 = null)
    {
        $this->law_term10 = $lawTerm10;

        return $this;
    }

    /**
     * Get lawTerm10.
     *
     * @return string|null
     */
    public function getLawTerm10()
    {
        return $this->law_term10;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Help
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
     * @return Help
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
     * Set lawCountry.
     *
     * @param \Eccube\Entity\Master\Country|null $lawCountry
     *
     * @return Help
     */
    public function setLawCountry(\Eccube\Entity\Master\Country $lawCountry = null)
    {
        $this->LawCountry = $lawCountry;

        return $this;
    }

    /**
     * Get lawCountry.
     *
     * @return \Eccube\Entity\Master\Country|null
     */
    public function getLawCountry()
    {
        return $this->LawCountry;
    }

    /**
     * Set lawPref.
     *
     * @param \Eccube\Entity\Master\Pref|null $lawPref
     *
     * @return Help
     */
    public function setLawPref(\Eccube\Entity\Master\Pref $lawPref = null)
    {
        $this->LawPref = $lawPref;

        return $this;
    }

    /**
     * Get lawPref.
     *
     * @return \Eccube\Entity\Master\Pref|null
     */
    public function getLawPref()
    {
        return $this->LawPref;
    }
}
