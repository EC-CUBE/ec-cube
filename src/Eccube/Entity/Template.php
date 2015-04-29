<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Template
 */
class Template extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTemplateName();
    }

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $device_type_id;

    /**
     * @var string
     */
    private $template_name;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;


    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set device_type_id
     *
     * @param integer $deviceTypeId
     * @return Template
     */
    public function setDeviceTypeId($deviceTypeId)
    {
        $this->device_type_id = $deviceTypeId;

        return $this;
    }

    /**
     * Get device_type_id
     *
     * @return integer 
     */
    public function getDeviceTypeId()
    {
        return $this->device_type_id;
    }

    /**
     * Set template_name
     *
     * @param string $templateName
     * @return Template
     */
    public function setTemplateName($templateName)
    {
        $this->template_name = $templateName;

        return $this;
    }

    /**
     * Get template_name
     *
     * @return string 
     */
    public function getTemplateName()
    {
        return $this->template_name;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Template
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
     * @return Template
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
     * Set code
     *
     * @param string $code
     * @return Template
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
