<?php

namespace Eccube\Entity;

/**
 * DelivFee
 */
class DelivFee extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $deliv_id;

    /**
     * @var integer
     */
    private $fee_id;

    /**
     * @var string
     */
    private $fee;

    /**
     * @var \Eccube\Entity\Deliv
     */
    private $Deliv;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * Set deliv_id
     *
     * @param  integer  $delivId
     * @return DelivFee
     */
    public function setDelivId($delivId)
    {
        $this->deliv_id = $delivId;

        return $this;
    }

    /**
     * Get deliv_id
     *
     * @return integer
     */
    public function getDelivId()
    {
        return $this->deliv_id;
    }

    /**
     * Set fee_id
     *
     * @param  integer  $feeId
     * @return DelivFee
     */
    public function setFeeId($feeId)
    {
        $this->fee_id = $feeId;

        return $this;
    }

    /**
     * Get fee_id
     *
     * @return integer
     */
    public function getFeeId()
    {
        return $this->fee_id;
    }

    /**
     * Set fee
     *
     * @param  string   $fee
     * @return DelivFee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return string
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set Deliv
     *
     * @param  \Eccube\Entity\Deliv $deliv
     * @return DelivFee
     */
    public function setDeliv(\Eccube\Entity\Deliv $deliv = null)
    {
        $this->Deliv = $deliv;

        return $this;
    }

    /**
     * Get Deliv
     *
     * @return \Eccube\Entity\Deliv
     */
    public function getDeliv()
    {
        return $this->Deliv;
    }

    /**
     * Set Pref
     *
     * @param  \Eccube\Entity\Master\Pref $pref
     * @return DelivFee
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref
     */
    public function getPref()
    {
        return $this->Pref;
    }
}
