<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 */
class Session extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $sess_data;

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
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sess_data
     *
     * @param string $sessData
     * @return Session
     */
    public function setSessData($sessData)
    {
        $this->sess_data = $sessData;

        return $this;
    }

    /**
     * Get sess_data
     *
     * @return string 
     */
    public function getSessData()
    {
        return $this->sess_data;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Session
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
     * @return Session
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
