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
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @var string|null
     *
     * @ORM\Column(name="manager", type="string", length=255, nullable=true)
     */
    private $manager;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip01", type="string", length=3, nullable=true)
     */
    private $zip01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip02", type="string", length=4, nullable=true)
     */
    private $zip02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zipcode", type="string", length=7, nullable=true)
     */
    private $zipcode;

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
     * @ORM\Column(name="tel01", type="string", length=5, nullable=true)
     */
    private $tel01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tel02", type="string", length=4, nullable=true)
     */
    private $tel02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tel03", type="string", length=4, nullable=true)
     */
    private $tel03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax01", type="string", length=5, nullable=true)
     */
    private $fax01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax02", type="string", length=4, nullable=true)
     */
    private $fax02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax03", type="string", length=4, nullable=true)
     */
    private $fax03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=4000, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term01", type="text", nullable=true)
     */
    private $term01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term02", type="text", nullable=true)
     */
    private $term02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term03", type="text", nullable=true)
     */
    private $term03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term04", type="text", nullable=true)
     */
    private $term04;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term05", type="text", nullable=true)
     */
    private $term05;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term06", type="text", nullable=true)
     */
    private $term06;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term07", type="text", nullable=true)
     */
    private $term07;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term08", type="text", nullable=true)
     */
    private $term08;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term09", type="text", nullable=true)
     */
    private $term09;

    /**
     * @var string|null
     *
     * @ORM\Column(name="term10", type="text", nullable=true)
     */
    private $term10;

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
     * Set company.
     *
     * @param string|null $company
     *
     * @return Help
     */
    public function setCompany($company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company.
     *
     * @return string|null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set manager.
     *
     * @param string|null $manager
     *
     * @return Help
     */
    public function setManager($manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager.
     *
     * @return string|null
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set zip01.
     *
     * @param string|null $zip01
     *
     * @return Help
     */
    public function setZip01($zip01 = null)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01.
     *
     * @return string|null
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02.
     *
     * @param string|null $zip02
     *
     * @return Help
     */
    public function setZip02($zip02 = null)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02.
     *
     * @return string|null
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode.
     *
     * @param string|null $zipcode
     *
     * @return Help
     */
    public function setZipcode($zipcode = null)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return string|null
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set addr01.
     *
     * @param string|null $addr01
     *
     * @return Help
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
     * @return Help
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
     * Set tel01.
     *
     * @param string|null $tel01
     *
     * @return Help
     */
    public function setTel01($tel01 = null)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01.
     *
     * @return string|null
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02.
     *
     * @param string|null $tel02
     *
     * @return Help
     */
    public function setTel02($tel02 = null)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02.
     *
     * @return string|null
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03.
     *
     * @param string|null $tel03
     *
     * @return Help
     */
    public function setTel03($tel03 = null)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03.
     *
     * @return string|null
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01.
     *
     * @param string|null $fax01
     *
     * @return Help
     */
    public function setFax01($fax01 = null)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01.
     *
     * @return string|null
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02.
     *
     * @param string|null $fax02
     *
     * @return Help
     */
    public function setFax02($fax02 = null)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02.
     *
     * @return string|null
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03.
     *
     * @param string|null $fax03
     *
     * @return Help
     */
    public function setFax03($fax03 = null)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03.
     *
     * @return string|null
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Help
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return Help
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set term01.
     *
     * @param string|null $term01
     *
     * @return Help
     */
    public function setTerm01($term01 = null)
    {
        $this->term01 = $term01;

        return $this;
    }

    /**
     * Get term01.
     *
     * @return string|null
     */
    public function getTerm01()
    {
        return $this->term01;
    }

    /**
     * Set term02.
     *
     * @param string|null $term02
     *
     * @return Help
     */
    public function setTerm02($term02 = null)
    {
        $this->term02 = $term02;

        return $this;
    }

    /**
     * Get term02.
     *
     * @return string|null
     */
    public function getTerm02()
    {
        return $this->term02;
    }

    /**
     * Set term03.
     *
     * @param string|null $term03
     *
     * @return Help
     */
    public function setTerm03($term03 = null)
    {
        $this->term03 = $term03;

        return $this;
    }

    /**
     * Get term03.
     *
     * @return string|null
     */
    public function getTerm03()
    {
        return $this->term03;
    }

    /**
     * Set term04.
     *
     * @param string|null $term04
     *
     * @return Help
     */
    public function setTerm04($term04 = null)
    {
        $this->term04 = $term04;

        return $this;
    }

    /**
     * Get term04.
     *
     * @return string|null
     */
    public function getTerm04()
    {
        return $this->term04;
    }

    /**
     * Set term05.
     *
     * @param string|null $term05
     *
     * @return Help
     */
    public function setTerm05($term05 = null)
    {
        $this->term05 = $term05;

        return $this;
    }

    /**
     * Get term05.
     *
     * @return string|null
     */
    public function getTerm05()
    {
        return $this->term05;
    }

    /**
     * Set term06.
     *
     * @param string|null $term06
     *
     * @return Help
     */
    public function setTerm06($term06 = null)
    {
        $this->term06 = $term06;

        return $this;
    }

    /**
     * Get term06.
     *
     * @return string|null
     */
    public function getTerm06()
    {
        return $this->term06;
    }

    /**
     * Set term07.
     *
     * @param string|null $term07
     *
     * @return Help
     */
    public function setTerm07($term07 = null)
    {
        $this->term07 = $term07;

        return $this;
    }

    /**
     * Get term07.
     *
     * @return string|null
     */
    public function getTerm07()
    {
        return $this->term07;
    }

    /**
     * Set term08.
     *
     * @param string|null $term08
     *
     * @return Help
     */
    public function setTerm08($term08 = null)
    {
        $this->term08 = $term08;

        return $this;
    }

    /**
     * Get term08.
     *
     * @return string|null
     */
    public function getTerm08()
    {
        return $this->term08;
    }

    /**
     * Set term09.
     *
     * @param string|null $term09
     *
     * @return Help
     */
    public function setTerm09($term09 = null)
    {
        $this->term09 = $term09;

        return $this;
    }

    /**
     * Get term09.
     *
     * @return string|null
     */
    public function getTerm09()
    {
        return $this->term09;
    }

    /**
     * Set term10.
     *
     * @param string|null $term10
     *
     * @return Help
     */
    public function setTerm10($term10 = null)
    {
        $this->term10 = $term10;

        return $this;
    }

    /**
     * Get term10.
     *
     * @return string|null
     */
    public function getTerm10()
    {
        return $this->term10;
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
     * Set country.
     *
     * @param \Eccube\Entity\Master\Country|null $country
     *
     * @return Help
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
     * @return Help
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
}
