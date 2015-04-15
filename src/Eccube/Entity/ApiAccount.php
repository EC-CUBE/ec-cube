<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiAccount
 */
class ApiAccount extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $api_access_key;

    /**
     * @var string
     */
    private $api_secret_key;

    /**
     * @var integer
     */
    private $enable;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;


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
     * Set api_access_key
     *
     * @param string $apiAccessKey
     * @return ApiAccount
     */
    public function setApiAccessKey($apiAccessKey)
    {
        $this->api_access_key = $apiAccessKey;

        return $this;
    }

    /**
     * Get api_access_key
     *
     * @return string 
     */
    public function getApiAccessKey()
    {
        return $this->api_access_key;
    }

    /**
     * Set api_secret_key
     *
     * @param string $apiSecretKey
     * @return ApiAccount
     */
    public function setApiSecretKey($apiSecretKey)
    {
        $this->api_secret_key = $apiSecretKey;

        return $this;
    }

    /**
     * Get api_secret_key
     *
     * @return string 
     */
    public function getApiSecretKey()
    {
        return $this->api_secret_key;
    }

    /**
     * Set enable
     *
     * @param integer $enable
     * @return ApiAccount
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get enable
     *
     * @return integer 
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return ApiAccount
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
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return ApiAccount
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
     * @return ApiAccount
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
}
