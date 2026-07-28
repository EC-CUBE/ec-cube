<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\PaymentOptionRepository;

/**
 * PaymentOption
 */
#[ORM\Table(name: 'dtb_payment_option')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PaymentOptionRepository::class)]
class PaymentOption extends AbstractEntity
{
    #[ORM\Column(name: 'delivery_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $delivery_id = null;

    #[ORM\Column(name: 'payment_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $payment_id = null;

    #[ORM\ManyToOne(targetEntity: Delivery::class, inversedBy: 'PaymentOptions')]
    #[ORM\JoinColumn(name: 'delivery_id', referencedColumnName: 'id')]
    private ?Delivery $Delivery = null;

    #[ORM\ManyToOne(targetEntity: Payment::class, inversedBy: 'PaymentOptions')]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id')]
    private ?Payment $Payment = null;

    /**
     * Set deliveryId.
     */
    public function setDeliveryId(int $deliveryId): PaymentOption
    {
        $this->delivery_id = $deliveryId;

        return $this;
    }

    /**
     * Get deliveryId.
     */
    public function getDeliveryId(): int
    {
        return $this->delivery_id;
    }

    /**
     * Set paymentId.
     */
    public function setPaymentId(int $paymentId): PaymentOption
    {
        $this->payment_id = $paymentId;

        return $this;
    }

    /**
     * Get paymentId.
     */
    public function getPaymentId(): int
    {
        return $this->payment_id;
    }

    /**
     * Set delivery.
     */
    public function setDelivery(?Delivery $delivery = null): PaymentOption
    {
        $this->Delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery.
     */
    public function getDelivery(): ?Delivery
    {
        return $this->Delivery;
    }

    /**
     * Set payment.
     */
    public function setPayment(?Payment $payment = null): PaymentOption
    {
        $this->Payment = $payment;

        return $this;
    }

    /**
     * Get payment.
     */
    public function getPayment(): ?Payment
    {
        return $this->Payment;
    }
}
