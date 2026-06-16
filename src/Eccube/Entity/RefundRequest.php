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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Repository\RefundRequestRepository;

if (!class_exists(RefundRequest::class)) {
    /**
     * RefundRequest
     *
     * 返品申請（商品＝注文明細単位）.
     */
    #[ORM\Table(name: 'dtb_refund_request')]
    #[ORM\Index(columns: ['order_item_id'], name: 'dtb_refund_request_order_item_id_idx')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: RefundRequestRepository::class)]
    class RefundRequest extends AbstractEntity
    {
        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'quantity', type: Types::DECIMAL, precision: 10, scale: 0, options: ['default' => 0])]
        private ?string $quantity = '0';

        #[ORM\Column(name: 'reason', type: Types::TEXT)]
        private ?string $reason = null;

        #[ORM\Column(name: 'admin_note', type: Types::TEXT, nullable: true)]
        private ?string $admin_note = null;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private $update_date;

        #[ORM\ManyToOne(targetEntity: Order::class)]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
        private ?Order $Order = null;

        #[ORM\ManyToOne(targetEntity: OrderItem::class)]
        #[ORM\JoinColumn(name: 'order_item_id', referencedColumnName: 'id')]
        private ?OrderItem $OrderItem = null;

        #[ORM\ManyToOne(targetEntity: Customer::class)]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private ?Customer $Customer = null;

        #[ORM\ManyToOne(targetEntity: RefundRequestStatus::class)]
        #[ORM\JoinColumn(name: 'refund_request_status_id', referencedColumnName: 'id')]
        private ?RefundRequestStatus $RefundRequestStatus = null;

        /**
         * @var Collection<int, RefundRequestFile>
         */
        #[ORM\OneToMany(targetEntity: RefundRequestFile::class, mappedBy: 'RefundRequest', cascade: ['persist', 'remove'])]
        #[ORM\OrderBy(['sort_no' => 'ASC'])]
        private Collection $RefundRequestFiles;

        public function __construct()
        {
            $this->RefundRequestFiles = new ArrayCollection();
        }

        public function getId(): ?int
        {
            return $this->id;
        }

        public function setQuantity(string $quantity): self
        {
            $this->quantity = $quantity;

            return $this;
        }

        public function getQuantity(): ?string
        {
            return $this->quantity;
        }

        public function setReason(?string $reason): self
        {
            $this->reason = $reason;

            return $this;
        }

        public function getReason(): ?string
        {
            return $this->reason;
        }

        public function setAdminNote(?string $adminNote): self
        {
            $this->admin_note = $adminNote;

            return $this;
        }

        public function getAdminNote(): ?string
        {
            return $this->admin_note;
        }

        public function setCreateDate(\DateTime $createDate): self
        {
            $this->create_date = $createDate;

            return $this;
        }

        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        public function setUpdateDate(\DateTime $updateDate): self
        {
            $this->update_date = $updateDate;

            return $this;
        }

        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        public function setOrder(?Order $order = null): self
        {
            $this->Order = $order;

            return $this;
        }

        public function getOrder(): ?Order
        {
            return $this->Order;
        }

        public function setOrderItem(?OrderItem $orderItem = null): self
        {
            $this->OrderItem = $orderItem;

            return $this;
        }

        public function getOrderItem(): ?OrderItem
        {
            return $this->OrderItem;
        }

        public function setCustomer(?Customer $customer = null): self
        {
            $this->Customer = $customer;

            return $this;
        }

        public function getCustomer(): ?Customer
        {
            return $this->Customer;
        }

        public function setRefundRequestStatus(?RefundRequestStatus $refundRequestStatus = null): self
        {
            $this->RefundRequestStatus = $refundRequestStatus;

            return $this;
        }

        public function getRefundRequestStatus(): ?RefundRequestStatus
        {
            return $this->RefundRequestStatus;
        }

        /**
         * @return Collection<int, RefundRequestFile>
         */
        public function getRefundRequestFiles(): Collection
        {
            return $this->RefundRequestFiles;
        }

        public function addRefundRequestFile(RefundRequestFile $refundRequestFile): self
        {
            if (!$this->RefundRequestFiles->contains($refundRequestFile)) {
                $this->RefundRequestFiles[] = $refundRequestFile;
                $refundRequestFile->setRefundRequest($this);
            }

            return $this;
        }

        public function removeRefundRequestFile(RefundRequestFile $refundRequestFile): bool
        {
            if (!$this->RefundRequestFiles->removeElement($refundRequestFile)) {
                return false;
            }
            if ($refundRequestFile->getRefundRequest() === $this) {
                $refundRequestFile->setRefundRequest();
            }

            return true;
        }
    }
}
