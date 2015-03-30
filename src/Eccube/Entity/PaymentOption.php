<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentOption
 */
class PaymentOption extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $delivId;

    /**
     * @var integer
     */
    private $paymentId;

    /**
     * @var integer
     */
    private $rank;


    /**
     * Set delivId
     *
     * @param integer $delivId
     * @return PaymentOption
     */
    public function setDelivId($delivId)
    {
        $this->delivId = $delivId;

        return $this;
    }

    /**
     * Get delivId
     *
     * @return integer 
     */
    public function getDelivId()
    {
        return $this->delivId;
    }

    /**
     * Set paymentId
     *
     * @param integer $paymentId
     * @return PaymentOption
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return PaymentOption
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
}
