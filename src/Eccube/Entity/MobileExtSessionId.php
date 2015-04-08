<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MobileExtSessionId
 */
class MobileExtSessionId extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $param_key;

    /**
     * @var string
     */
    private $param_value;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $create_date;


    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set param_key
     *
     * @param string $paramKey
     * @return MobileExtSessionId
     */
    public function setParamKey($paramKey)
    {
        $this->param_key = $paramKey;

        return $this;
    }

    /**
     * Get param_key
     *
     * @return string 
     */
    public function getParamKey()
    {
        return $this->param_key;
    }

    /**
     * Set param_value
     *
     * @param string $paramValue
     * @return MobileExtSessionId
     */
    public function setParamValue($paramValue)
    {
        $this->param_value = $paramValue;

        return $this;
    }

    /**
     * Get param_value
     *
     * @return string 
     */
    public function getParamValue()
    {
        return $this->param_value;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return MobileExtSessionId
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return MobileExtSessionId
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
}
