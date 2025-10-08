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
use Eccube\Repository\OrderPdfRepository;

if (!class_exists(OrderPdf::class)) {
    /**
     * OrderPdf
     */
    #[ORM\Table(name: 'dtb_order_pdf')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\Entity(repositoryClass: OrderPdfRepository::class)]
    class OrderPdf extends AbstractEntity
    {
        /** @var mixed */
        public $ids;
        /** @var \DateTime|string */
        public $issue_date;
        /** @var mixed */
        public $default;

        /**
         * @var int
         */
        #[ORM\Column(name: 'member_id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        private $member_id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'title', type: 'string', nullable: true)]
        private $title;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'message1', type: 'string', nullable: true)]
        private $message1;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'message2', type: 'string', nullable: true)]
        private $message2;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'message3', type: 'string', nullable: true)]
        private $message3;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note1', type: 'string', nullable: true)]
        private $note1;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note2', type: 'string', nullable: true)]
        private $note2;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note3', type: 'string', nullable: true)]
        private $note3;

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
         * @var bool
         */
        #[ORM\Column(name: 'visible', type: 'boolean', options: ['default' => true])]
        private $visible = true;

        /**
         * @return int
         */
        public function getMemberId(): int
        {
            return $this->member_id;
        }

        /**
         * @param int $member_id
         *
         * @return $this
         */
        public function setMemberId($member_id): static
        {
            $this->member_id = $member_id;

            return $this;
        }

        /**
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }

        /**
         * @param string $title
         *
         * @return $this
         */
        public function setTitle($title): static
        {
            $this->title = $title;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getMessage1(): ?string
        {
            return $this->message1;
        }

        /**
         * @param string $message1
         *
         * @return $this
         */
        public function setMessage1($message1): static
        {
            $this->message1 = $message1;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getMessage2(): ?string
        {
            return $this->message2;
        }

        /**
         * @param string $message2
         *
         * @return $this
         */
        public function setMessage2($message2): static
        {
            $this->message2 = $message2;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getMessage3(): ?string
        {
            return $this->message3;
        }

        /**
         * @param string|null $message3
         *
         * @return $this
         */
        public function setMessage3($message3): static
        {
            $this->message3 = $message3;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getNote1(): ?string
        {
            return $this->note1;
        }

        /**
         * @param string $note1
         *
         * @return $this
         */
        public function setNote1($note1): static
        {
            $this->note1 = $note1;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getNote2(): ?string
        {
            return $this->note2;
        }

        /**
         * @param string $note2
         *
         * @return $this
         */
        public function setNote2($note2): static
        {
            $this->note2 = $note2;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getNote3(): ?string
        {
            return $this->note3;
        }

        /**
         * @param string $note3
         *
         * @return $this
         */
        public function setNote3($note3): static
        {
            $this->note3 = $note3;

            return $this;
        }

        /**
         * @return \DateTime
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * @param \DateTime|string $create_date
         *
         * @return $this
         */
        public function setCreateDate($create_date): static
        {
            $this->create_date = $create_date;

            return $this;
        }

        /**
         * @return \DateTime
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * @param \DateTime|string $update_date
         *
         * @return $this
         */
        public function setUpdateDate($update_date): static
        {
            $this->update_date = $update_date;

            return $this;
        }

        /**
         * Set visible
         *
         * @param bool $visible
         *
         * @return OrderPdf
         */
        public function setVisible($visible): OrderPdf
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Is the visibility visible?
         *
         * @return bool
         */
        public function isVisible(): bool
        {
            return $this->visible;
        }
    }
}
