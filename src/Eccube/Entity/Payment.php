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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\PaymentRepository;

if (!class_exists(Payment::class)) {
    /**
     * Payment
     */
    #[ORM\Table(name: 'dtb_payment')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: PaymentRepository::class)]
    class Payment extends AbstractEntity implements \Stringable
    {
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->getMethod();
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'payment_method', type: 'string', length: 255, nullable: true)]
        private $method;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'charge', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true, 'default' => 0])]
        private $charge = '0';

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'rule_max', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true])]
        private $rule_max;

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', nullable: true, options: ['unsigned' => true])]
        private $sort_no;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'fixed', type: 'boolean', options: ['default' => true])]
        private $fixed = true;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'payment_image', type: 'string', length: 255, nullable: true)]
        private $payment_image;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'rule_min', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true])]
        private $rule_min;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'method_class', type: 'string', length: 255, nullable: true)]
        private $method_class;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'visible', type: 'boolean', options: ['default' => true])]
        private $visible;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,PaymentOption>
         */
        #[ORM\OneToMany(targetEntity: PaymentOption::class, mappedBy: 'Payment')]
        private $PaymentOptions;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->PaymentOptions = new \Doctrine\Common\Collections\ArrayCollection();
        }

        /**
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set method.
         *
         * @param string|null $method
         *
         * @return Payment
         */
        public function setMethod($method = null): Payment
        {
            $this->method = $method;

            return $this;
        }

        /**
         * Get method.
         *
         * @return string|null
         */
        public function getMethod(): ?string
        {
            return $this->method;
        }

        /**
         * Set charge.
         *
         * @param string|null $charge
         *
         * @return Payment
         */
        public function setCharge($charge = null): Payment
        {
            $this->charge = $charge;

            return $this;
        }

        /**
         * Get charge.
         *
         * @return string|null
         */
        public function getCharge(): ?string
        {
            return $this->charge;
        }

        /**
         * Set ruleMax.
         *
         * @param string|null $ruleMax
         *
         * @return Payment
         */
        public function setRuleMax($ruleMax = null): Payment
        {
            $this->rule_max = $ruleMax;

            return $this;
        }

        /**
         * Get ruleMax.
         *
         * @return string|null
         */
        public function getRuleMax(): ?string
        {
            return $this->rule_max;
        }

        /**
         * Set sortNo.
         *
         * @param int|null $sortNo
         *
         * @return Payment
         */
        public function setSortNo($sortNo = null): Payment
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int|null
         */
        public function getSortNo(): ?int
        {
            return $this->sort_no;
        }

        /**
         * Set fixed.
         *
         * @param bool $fixed
         *
         * @return Payment
         */
        public function setFixed($fixed): Payment
        {
            $this->fixed = $fixed;

            return $this;
        }

        /**
         * Get fixed.
         *
         * @return bool
         */
        public function isFixed(): bool
        {
            return $this->fixed;
        }

        /**
         * Set paymentImage.
         *
         * @param string|null $paymentImage
         *
         * @return Payment
         */
        public function setPaymentImage($paymentImage = null): Payment
        {
            $this->payment_image = $paymentImage;

            return $this;
        }

        /**
         * Get paymentImage.
         *
         * @return string|null
         */
        public function getPaymentImage(): ?string
        {
            return $this->payment_image;
        }

        /**
         * Set ruleMin.
         *
         * @param string|null $ruleMin
         *
         * @return Payment
         */
        public function setRuleMin($ruleMin = null): Payment
        {
            $this->rule_min = $ruleMin;

            return $this;
        }

        /**
         * Get ruleMin.
         *
         * @return string|null
         */
        public function getRuleMin(): ?string
        {
            return $this->rule_min;
        }

        /**
         * Set methodClass.
         *
         * @param string|null $methodClass
         *
         * @return Payment
         */
        public function setMethodClass($methodClass = null): Payment
        {
            $this->method_class = $methodClass;

            return $this;
        }

        /**
         * Get methodClass.
         *
         * @return string|null
         */
        public function getMethodClass(): ?string
        {
            return $this->method_class;
        }

        /**
         * @return bool
         */
        public function isVisible(): bool
        {
            return $this->visible;
        }

        /**
         * @param bool $visible
         *
         * @return Payment
         */
        public function setVisible($visible): Payment
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Payment
         */
        public function setCreateDate($createDate): Payment
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return Payment
         */
        public function setUpdateDate($updateDate): Payment
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Add paymentOption.
         *
         * @param PaymentOption $paymentOption
         *
         * @return Payment
         */
        public function addPaymentOption(PaymentOption $paymentOption): Payment
        {
            $this->PaymentOptions[] = $paymentOption;

            return $this;
        }

        /**
         * Remove paymentOption.
         *
         * @param PaymentOption $paymentOption
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removePaymentOption(PaymentOption $paymentOption): bool
        {
            return $this->PaymentOptions->removeElement($paymentOption);
        }

        /**
         * Get paymentOptions.
         *
         * @return \Doctrine\Common\Collections\Collection<int,PaymentOption>
         */
        public function getPaymentOptions(): \Doctrine\Common\Collections\Collection
        {
            return $this->PaymentOptions;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return Payment
         */
        public function setCreator(?Member $creator = null): Payment
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}
