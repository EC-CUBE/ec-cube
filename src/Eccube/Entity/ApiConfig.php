<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiConfig
 */
class ApiConfig extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $operation_name;

    /**
     * @var string
     */
    private $operation_description;

    /**
     * @var string
     */
    private $auth_types;

    /**
     * @var integer
     */
    private $enable;

    /**
     * @var integer
     */
    private $is_log;

    /**
     * @var string
     */
    private $sub_data;

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
     * Set operation_name
     *
     * @param string $operationName
     * @return ApiConfig
     */
    public function setOperationName($operationName)
    {
        $this->operation_name = $operationName;

        return $this;
    }

    /**
     * Get operation_name
     *
     * @return string 
     */
    public function getOperationName()
    {
        return $this->operation_name;
    }

    /**
     * Set operation_description
     *
     * @param string $operationDescription
     * @return ApiConfig
     */
    public function setOperationDescription($operationDescription)
    {
        $this->operation_description = $operationDescription;

        return $this;
    }

    /**
     * Get operation_description
     *
     * @return string 
     */
    public function getOperationDescription()
    {
        return $this->operation_description;
    }

    /**
     * Set auth_types
     *
     * @param string $authTypes
     * @return ApiConfig
     */
    public function setAuthTypes($authTypes)
    {
        $this->auth_types = $authTypes;

        return $this;
    }

    /**
     * Get auth_types
     *
     * @return string 
     */
    public function getAuthTypes()
    {
        return $this->auth_types;
    }

    /**
     * Set enable
     *
     * @param integer $enable
     * @return ApiConfig
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
     * Set is_log
     *
     * @param integer $isLog
     * @return ApiConfig
     */
    public function setIsLog($isLog)
    {
        $this->is_log = $isLog;

        return $this;
    }

    /**
     * Get is_log
     *
     * @return integer 
     */
    public function getIsLog()
    {
        return $this->is_log;
    }

    /**
     * Set sub_data
     *
     * @param string $subData
     * @return ApiConfig
     */
    public function setSubData($subData)
    {
        $this->sub_data = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string 
     */
    public function getSubData()
    {
        return $this->sub_data;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return ApiConfig
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
     * @return ApiConfig
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
     * @return ApiConfig
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
