<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Customer
 */
class Customer implements UserInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name01;

    /**
     * @var string
     */
    private $name02;

    /**
     * @var string
     */
    private $kana01;

    /**
     * @var string
     */
    private $kana02;

    /**
     * @var string
     */
    private $zip01;

    /**
     * @var string
     */
    private $zip02;

    /**
     * @var integer
     */
    private $pref_id;

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
    private $email;

    /**
     * @var string
     */
    private $email_mobile;

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
     * @var integer
     */
    private $sex;

    /**
     * @var integer
     */
    private $job;

    /**
     * @var \DateTime
     */
    private $birth;

    /**
     * @var string
     */
    private $password;

    /**
     * @var integer
     */
    private $reminder;

    /**
     * @var string
     */
    private $reminder_answer;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var \DateTime
     */
    private $first_buy_date;

    /**
     * @var \DateTime
     */
    private $last_buy_date;

    /**
     * @var string
     */
    private $buy_times;

    /**
     * @var string
     */
    private $buy_total;

    /**
     * @var string
     */
    private $point;

    /**
     * @var string
     */
    private $note;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var string
     */
    private $mobile_phone_id;

    /**
     * @var integer
     */
    private $mailmaga_flg;

    /**
     * @var \Eccube\Entity\Pref
     */
    private $Pref;


    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
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
     * Set id
     *
     * @return Customer 
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Set name01
     *
     * @param string $name01
     * @return Customer
     */
    public function setName01($name01)
    {
        $this->name01 = $name01;

        return $this;
    }

    /**
     * Get name01
     *
     * @return string 
     */
    public function getName01()
    {
        return $this->name01;
    }

    /**
     * Set name02
     *
     * @param string $name02
     * @return Customer
     */
    public function setName02($name02)
    {
        $this->name02 = $name02;

        return $this;
    }

    /**
     * Get name02
     *
     * @return string 
     */
    public function getName02()
    {
        return $this->name02;
    }

    /**
     * Set kana01
     *
     * @param string $kana01
     * @return Customer
     */
    public function setKana01($kana01)
    {
        $this->kana01 = $kana01;

        return $this;
    }

    /**
     * Get kana01
     *
     * @return string 
     */
    public function getKana01()
    {
        return $this->kana01;
    }

    /**
     * Set kana02
     *
     * @param string $kana02
     * @return Customer
     */
    public function setKana02($kana02)
    {
        $this->kana02 = $kana02;

        return $this;
    }

    /**
     * Get kana02
     *
     * @return string 
     */
    public function getKana02()
    {
        return $this->kana02;
    }

    /**
     * Set zip01
     *
     * @param string $zip01
     * @return Customer
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
     * @return Customer
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
     * Set pref_id
     *
     * @param integer $prefId
     * @return Customer
     */
    public function setPrefId($prefId)
    {
        $this->pref_id = $prefId;

        return $this;
    }

    /**
     * Get pref_id
     *
     * @return integer 
     */
    public function getPrefId()
    {
        return $this->pref_id;
    }

    /**
     * Set addr01
     *
     * @param string $addr01
     * @return Customer
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
     * @return Customer
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
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email_mobile
     *
     * @param string $emailMobile
     * @return Customer
     */
    public function setEmailMobile($emailMobile)
    {
        $this->email_mobile = $emailMobile;

        return $this;
    }

    /**
     * Get email_mobile
     *
     * @return string 
     */
    public function getEmailMobile()
    {
        return $this->email_mobile;
    }

    /**
     * Set tel01
     *
     * @param string $tel01
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * Set sex
     *
     * @param integer $sex
     * @return Customer
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set job
     *
     * @param integer $job
     * @return Customer
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return integer 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set birth
     *
     * @param \DateTime $birth
     * @return Customer
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;

        return $this;
    }

    /**
     * Get birth
     *
     * @return \DateTime 
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Customer
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
     * Set reminder
     *
     * @param integer $reminder
     * @return Customer
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * Get reminder
     *
     * @return integer 
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Set reminder_answer
     *
     * @param string $reminderAnswer
     * @return Customer
     */
    public function setReminderAnswer($reminderAnswer)
    {
        $this->reminder_answer = $reminderAnswer;

        return $this;
    }

    /**
     * Get reminder_answer
     *
     * @return string 
     */
    public function getReminderAnswer()
    {
        return $this->reminder_answer;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Customer
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
     * Set secret_key
     *
     * @param string $secretKey
     * @return Customer
     */
    public function setSecretKey($secretKey)
    {
        $this->secret_key = $secretKey;

        return $this;
    }

    /**
     * Get secret_key
     *
     * @return string 
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * Set first_buy_date
     *
     * @param \DateTime $firstBuyDate
     * @return Customer
     */
    public function setFirstBuyDate($firstBuyDate)
    {
        $this->first_buy_date = $firstBuyDate;

        return $this;
    }

    /**
     * Get first_buy_date
     *
     * @return \DateTime 
     */
    public function getFirstBuyDate()
    {
        return $this->first_buy_date;
    }

    /**
     * Set last_buy_date
     *
     * @param \DateTime $lastBuyDate
     * @return Customer
     */
    public function setLastBuyDate($lastBuyDate)
    {
        $this->last_buy_date = $lastBuyDate;

        return $this;
    }

    /**
     * Get last_buy_date
     *
     * @return \DateTime 
     */
    public function getLastBuyDate()
    {
        return $this->last_buy_date;
    }

    /**
     * Set buy_times
     *
     * @param string $buyTimes
     * @return Customer
     */
    public function setBuyTimes($buyTimes)
    {
        $this->buy_times = $buyTimes;

        return $this;
    }

    /**
     * Get buy_times
     *
     * @return string 
     */
    public function getBuyTimes()
    {
        return $this->buy_times;
    }

    /**
     * Set buy_total
     *
     * @param string $buyTotal
     * @return Customer
     */
    public function setBuyTotal($buyTotal)
    {
        $this->buy_total = $buyTotal;

        return $this;
    }

    /**
     * Get buy_total
     *
     * @return string 
     */
    public function getBuyTotal()
    {
        return $this->buy_total;
    }

    /**
     * Set point
     *
     * @param string $point
     * @return Customer
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return string 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Customer
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Customer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Customer
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
     * @return Customer
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
     * Set del_flg
     *
     * @param integer $delFlg
     * @return Customer
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
     * Set mobile_phone_id
     *
     * @param string $mobilePhoneId
     * @return Customer
     */
    public function setMobilePhoneId($mobilePhoneId)
    {
        $this->mobile_phone_id = $mobilePhoneId;

        return $this;
    }

    /**
     * Get mobile_phone_id
     *
     * @return string 
     */
    public function getMobilePhoneId()
    {
        return $this->mobile_phone_id;
    }

    /**
     * Set mailmaga_flg
     *
     * @param integer $mailmagaFlg
     * @return Customer
     */
    public function setMailmagaFlg($mailmagaFlg)
    {
        $this->mailmaga_flg = $mailmagaFlg;

        return $this;
    }

    /**
     * Get mailmaga_flg
     *
     * @return integer 
     */
    public function getMailmagaFlg()
    {
        return $this->mailmaga_flg;
    }

    /**
     * Set Pref
     *
     * @param \Eccube\Entity\Pref $pref
     * @return Customer
     */
    public function setPref(\Eccube\Entity\Pref $pref = null)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Pref 
     */
    public function getPref()
    {
        return $this->Pref;
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
