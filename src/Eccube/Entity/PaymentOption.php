<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

/**
 * PaymentOption
 */
class PaymentOption extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $deliv_id;

    /**
     * @var integer
     */
    private $payment_id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \Eccube\Entity\Deliv
     */
    private $Deliv;

    /**
     * @var \Eccube\Entity\Payment
     */
    private $Payment;

    /**
     * Set deliv_id
     *
     * @param  integer       $delivId
     * @return PaymentOption
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
     * Set payment_id
     *
     * @param  integer       $paymentId
     * @return PaymentOption
     */
    public function setPaymentId($paymentId)
    {
        $this->payment_id = $paymentId;

        return $this;
    }

    /**
     * Get payment_id
     *
     * @return integer
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * Set rank
     *
     * @param  integer       $rank
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

    /**
     * Set Deliv
     *
     * @param  \Eccube\Entity\Deliv $deliv
     * @return PaymentOption
     */
    public function setDeliv(\Eccube\Entity\Deliv $deliv)
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
     * Set Payment
     *
     * @param  \Eccube\Entity\Payment $payment
     * @return PaymentOption
     */
    public function setPayment(\Eccube\Entity\Payment $payment)
    {
        $this->Payment = $payment;

        return $this;
    }

    /**
     * Get Payment
     *
     * @return \Eccube\Entity\Payment
     */
    public function getPayment()
    {
        return $this->Payment;
    }
}
