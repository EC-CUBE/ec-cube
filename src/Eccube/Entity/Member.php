<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Member
 */
class Member
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $department;

    /**
     * @var string
     */
    private $login_id;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var integer
     */
    private $authority;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $work;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var integer
     */
    private $creator_id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \DateTime
     */
    private $login_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductStatusCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductClassCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MemberCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $NewsCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BestProductCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ClassCategoryCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ClassNameCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $HolidayCreators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MailTemplateCreators;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ProductCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductStatusCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductClassCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MakerCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MemberCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->NewsCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->BestProductCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ClassCategoryCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ClassNameCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->HolidayCreators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MailTemplateCreators = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Member
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return Member
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set login_id
     *
     * @param string $loginId
     * @return Member
     */
    public function setLoginId($loginId)
    {
        $this->login_id = $loginId;

        return $this;
    }

    /**
     * Get login_id
     *
     * @return string 
     */
    public function getLoginId()
    {
        return $this->login_id;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Member
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Member
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set authority
     *
     * @param integer $authority
     * @return Member
     */
    public function setAuthority($authority)
    {
        $this->authority = $authority;

        return $this;
    }

    /**
     * Get authority
     *
     * @return integer 
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return Member
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set work
     *
     * @param integer $work
     * @return Member
     */
    public function setWork($work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * Get work
     *
     * @return integer 
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return Member
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set creator_id
     *
     * @param integer $creatorId
     * @return Member
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;

        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Member
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
     * @return Member
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
     * Set login_date
     *
     * @param \DateTime $loginDate
     * @return Member
     */
    public function setLoginDate($loginDate)
    {
        $this->login_date = $loginDate;

        return $this;
    }

    /**
     * Get login_date
     *
     * @return \DateTime 
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }

    /**
     * Add ProductCreators
     *
     * @param \Eccube\Entity\Product $productCreators
     * @return Member
     */
    public function addProductCreator(\Eccube\Entity\Product $productCreators)
    {
        $this->ProductCreators[] = $productCreators;

        return $this;
    }

    /**
     * Remove ProductCreators
     *
     * @param \Eccube\Entity\Product $productCreators
     */
    public function removeProductCreator(\Eccube\Entity\Product $productCreators)
    {
        $this->ProductCreators->removeElement($productCreators);
    }

    /**
     * Get ProductCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductCreators()
    {
        return $this->ProductCreators;
    }

    /**
     * Add ProductStatusCreators
     *
     * @param \Eccube\Entity\ProductStatus $productStatusCreators
     * @return Member
     */
    public function addProductStatusCreator(\Eccube\Entity\ProductStatus $productStatusCreators)
    {
        $this->ProductStatusCreators[] = $productStatusCreators;

        return $this;
    }

    /**
     * Remove ProductStatusCreators
     *
     * @param \Eccube\Entity\ProductStatus $productStatusCreators
     */
    public function removeProductStatusCreator(\Eccube\Entity\ProductStatus $productStatusCreators)
    {
        $this->ProductStatusCreators->removeElement($productStatusCreators);
    }

    /**
     * Get ProductStatusCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductStatusCreators()
    {
        return $this->ProductStatusCreators;
    }

    /**
     * Add ProductClassCreators
     *
     * @param \Eccube\Entity\ProductClass $productClassCreators
     * @return Member
     */
    public function addProductClassCreator(\Eccube\Entity\ProductClass $productClassCreators)
    {
        $this->ProductClassCreators[] = $productClassCreators;

        return $this;
    }

    /**
     * Remove ProductClassCreators
     *
     * @param \Eccube\Entity\ProductClass $productClassCreators
     */
    public function removeProductClassCreator(\Eccube\Entity\ProductClass $productClassCreators)
    {
        $this->ProductClassCreators->removeElement($productClassCreators);
    }

    /**
     * Get ProductClassCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductClassCreators()
    {
        return $this->ProductClassCreators;
    }

    /**
     * Add MakerCreators
     *
     * @param \Eccube\Entity\Maker $makerCreators
     * @return Member
     */
    public function addMakerCreator(\Eccube\Entity\Maker $makerCreators)
    {
        $this->MakerCreators[] = $makerCreators;

        return $this;
    }

    /**
     * Remove MakerCreators
     *
     * @param \Eccube\Entity\Maker $makerCreators
     */
    public function removeMakerCreator(\Eccube\Entity\Maker $makerCreators)
    {
        $this->MakerCreators->removeElement($makerCreators);
    }

    /**
     * Get MakerCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMakerCreators()
    {
        return $this->MakerCreators;
    }

    /**
     * Add MemberCreators
     *
     * @param \Eccube\Entity\Member $memberCreators
     * @return Member
     */
    public function addMemberCreator(\Eccube\Entity\Member $memberCreators)
    {
        $this->MemberCreators[] = $memberCreators;

        return $this;
    }

    /**
     * Remove MemberCreators
     *
     * @param \Eccube\Entity\Member $memberCreators
     */
    public function removeMemberCreator(\Eccube\Entity\Member $memberCreators)
    {
        $this->MemberCreators->removeElement($memberCreators);
    }

    /**
     * Get MemberCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberCreators()
    {
        return $this->MemberCreators;
    }

    /**
     * Add NewsCreators
     *
     * @param \Eccube\Entity\News $newsCreators
     * @return Member
     */
    public function addNewsCreator(\Eccube\Entity\News $newsCreators)
    {
        $this->NewsCreators[] = $newsCreators;

        return $this;
    }

    /**
     * Remove NewsCreators
     *
     * @param \Eccube\Entity\News $newsCreators
     */
    public function removeNewsCreator(\Eccube\Entity\News $newsCreators)
    {
        $this->NewsCreators->removeElement($newsCreators);
    }

    /**
     * Get NewsCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNewsCreators()
    {
        return $this->NewsCreators;
    }

    /**
     * Add BestProductCreators
     *
     * @param \Eccube\Entity\BestProduct $bestProductCreators
     * @return Member
     */
    public function addBestProductCreator(\Eccube\Entity\BestProduct $bestProductCreators)
    {
        $this->BestProductCreators[] = $bestProductCreators;

        return $this;
    }

    /**
     * Remove BestProductCreators
     *
     * @param \Eccube\Entity\BestProduct $bestProductCreators
     */
    public function removeBestProductCreator(\Eccube\Entity\BestProduct $bestProductCreators)
    {
        $this->BestProductCreators->removeElement($bestProductCreators);
    }

    /**
     * Get BestProductCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBestProductCreators()
    {
        return $this->BestProductCreators;
    }

    /**
     * Add ClassCategoryCreators
     *
     * @param \Eccube\Entity\ClassCategory $classCategoryCreators
     * @return Member
     */
    public function addClassCategoryCreator(\Eccube\Entity\ClassCategory $classCategoryCreators)
    {
        $this->ClassCategoryCreators[] = $classCategoryCreators;

        return $this;
    }

    /**
     * Remove ClassCategoryCreators
     *
     * @param \Eccube\Entity\ClassCategory $classCategoryCreators
     */
    public function removeClassCategoryCreator(\Eccube\Entity\ClassCategory $classCategoryCreators)
    {
        $this->ClassCategoryCreators->removeElement($classCategoryCreators);
    }

    /**
     * Get ClassCategoryCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassCategoryCreators()
    {
        return $this->ClassCategoryCreators;
    }

    /**
     * Add ClassNameCreators
     *
     * @param \Eccube\Entity\ClassName $classNameCreators
     * @return Member
     */
    public function addClassNameCreator(\Eccube\Entity\ClassName $classNameCreators)
    {
        $this->ClassNameCreators[] = $classNameCreators;

        return $this;
    }

    /**
     * Remove ClassNameCreators
     *
     * @param \Eccube\Entity\ClassName $classNameCreators
     */
    public function removeClassNameCreator(\Eccube\Entity\ClassName $classNameCreators)
    {
        $this->ClassNameCreators->removeElement($classNameCreators);
    }

    /**
     * Get ClassNameCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassNameCreators()
    {
        return $this->ClassNameCreators;
    }

    /**
     * Add HolidayCreators
     *
     * @param \Eccube\Entity\Holiday $holidayCreators
     * @return Member
     */
    public function addHolidayCreator(\Eccube\Entity\Holiday $holidayCreators)
    {
        $this->HolidayCreators[] = $holidayCreators;

        return $this;
    }

    /**
     * Remove HolidayCreators
     *
     * @param \Eccube\Entity\Holiday $holidayCreators
     */
    public function removeHolidayCreator(\Eccube\Entity\Holiday $holidayCreators)
    {
        $this->HolidayCreators->removeElement($holidayCreators);
    }

    /**
     * Get HolidayCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHolidayCreators()
    {
        return $this->HolidayCreators;
    }

    /**
     * Add MailTemplateCreators
     *
     * @param \Eccube\Entity\MailTemplate $mailTemplateCreators
     * @return Member
     */
    public function addMailTemplateCreator(\Eccube\Entity\MailTemplate $mailTemplateCreators)
    {
        $this->MailTemplateCreators[] = $mailTemplateCreators;

        return $this;
    }

    /**
     * Remove MailTemplateCreators
     *
     * @param \Eccube\Entity\MailTemplate $mailTemplateCreators
     */
    public function removeMailTemplateCreator(\Eccube\Entity\MailTemplate $mailTemplateCreators)
    {
        $this->MailTemplateCreators->removeElement($mailTemplateCreators);
    }

    /**
     * Get MailTemplateCreators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMailTemplateCreators()
    {
        return $this->MailTemplateCreators;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return Member
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member 
     */
    public function getCreator()
    {
        return $this->Creator;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreateDateAuto()
    {
        // Add your code here
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateAuto()
    {
        // Add your code here
    }
}
