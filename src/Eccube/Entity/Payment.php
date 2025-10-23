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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
         * @var Collection<int,PaymentOption>
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
            $this->PaymentOptions = new ArrayCollection();
        }

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set method.
         */
        public function setMethod(?string $method = null): Payment
        {
            $this->method = $method;

            return $this;
        }

        /**
         * Get method.
         */
        public function getMethod(): ?string
        {
            return $this->method;
        }

        /**
         * Set charge.
         */
        public function setCharge(?string $charge = null): Payment
        {
            $this->charge = $charge;

            return $this;
        }

        /**
         * Get charge.
         */
        public function getCharge(): ?string
        {
            return $this->charge;
        }

        /**
         * Set ruleMax.
         */
        public function setRuleMax(?string $ruleMax = null): Payment
        {
            $this->rule_max = $ruleMax;

            return $this;
        }

        /**
         * Get ruleMax.
         */
        public function getRuleMax(): ?string
        {
            return $this->rule_max;
        }

        /**
         * Set sortNo.
         */
        public function setSortNo(?int $sortNo = null): Payment
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         */
        public function getSortNo(): ?int
        {
            return $this->sort_no;
        }

        /**
         * Set fixed.
         */
        public function setFixed(?bool $fixed): Payment
        {
            $this->fixed = $fixed;

            return $this;
        }

        /**
         * Get fixed.
         */
        public function isFixed(): bool
        {
            return $this->fixed;
        }

        /**
         * Set paymentImage.
         */
        public function setPaymentImage(?string $paymentImage = null): Payment
        {
            $this->payment_image = $paymentImage;

            return $this;
        }

        /**
         * Get paymentImage.
         */
        public function getPaymentImage(): ?string
        {
            return $this->payment_image;
        }

        /**
         * Set ruleMin.
         */
        public function setRuleMin(?string $ruleMin = null): Payment
        {
            $this->rule_min = $ruleMin;

            return $this;
        }

        /**
         * Get ruleMin.
         */
        public function getRuleMin(): ?string
        {
            return $this->rule_min;
        }

        /**
         * Set methodClass.
         */
        public function setMethodClass(?string $methodClass = null): Payment
        {
            $this->method_class = $methodClass;

            return $this;
        }

        /**
         * Get methodClass.
         */
        public function getMethodClass(): ?string
        {
            return $this->method_class;
        }

        public function isVisible(): bool
        {
            return $this->visible;
        }

        public function setVisible(bool $visible): Payment
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Payment
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         */
        public function setUpdateDate(\DateTime $updateDate): Payment
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Add paymentOption.
         */
        public function addPaymentOption(PaymentOption $paymentOption): Payment
        {
            $this->PaymentOptions[] = $paymentOption;

            return $this;
        }

        /**
         * Remove paymentOption.
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
         * @return Collection<int,PaymentOption>
         */
        public function getPaymentOptions(): Collection
        {
            return $this->PaymentOptions;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): Payment
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}
