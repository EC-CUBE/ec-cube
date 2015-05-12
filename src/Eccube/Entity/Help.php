<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Help
 */
class Help
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
     * @var \DateTime
     */
    private $create_date;


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
}
