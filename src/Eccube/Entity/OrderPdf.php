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

if (!class_exists('\Eccube\Entity\OrderPdf')) {
    /**
     * OrderPdf
     *
     * @ORM\Table(name="dtb_order_pdf")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\Entity(repositoryClass="Eccube\Repository\OrderPdfRepository")
     */
    class OrderPdf extends AbstractEntity
    {
        public $ids;

        public $issue_date;

        public $default;

        /**
         * @var int
         *
         * @ORM\Column(name="member_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         */
        private $member_id;

        /**
         * @var string
         *
         * @ORM\Column(name="title", type="string", nullable=true)
         */
        private $title;

        /**
         * @var string
         *
         * @ORM\Column(name="message1", type="string", nullable=true)
         */
        private $message1;

        /**
         * @var string
         *
         * @ORM\Column(name="message2", type="string", nullable=true)
         */
        private $message2;

        /**
         * @var string
         *
         * @ORM\Column(name="message3", type="string", nullable=true)
         */
        private $message3;

        /**
         * @var string
         *
         * @ORM\Column(name="note1", type="string", nullable=true)
         */
        private $note1;

        /**
         * @var string
         *
         * @ORM\Column(name="note2", type="string", nullable=true)
         */
        private $note2;

        /**
         * @var string
         *
         * @ORM\Column(name="note3", type="string", nullable=true)
         */
        private $note3;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var boolean
         *
         * @ORM\Column(name="visible", type="boolean", options={"default":true})
         */
        private $visible = true;

        /**
         * @return string
         */
        public function getMemberId()
        {
            return $this->member_id;
        }

        /**
         * @param $member_id
         *
         * @return $this
         */
        public function setMemberId($member_id)
        {
            $this->member_id = $member_id;

            return $this;
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            return $this->title;
        }

        /**
         * @param $title
         *
         * @return $this
         */
        public function setTitle($title)
        {
            $this->title = $title;

            return $this;
        }

        /**
         * @return string
         */
        public function getMessage1()
        {
            return $this->message1;
        }

        /**
         * @param $message1
         *
         * @return $this
         */
        public function setMessage1($message1)
        {
            $this->message1 = $message1;

            return $this;
        }

        /**
         * @return string
         */
        public function getMessage2()
        {
            return $this->message2;
        }

        /**
         * @param $message2
         *
         * @return $this
         */
        public function setMessage2($message2)
        {
            $this->message2 = $message2;

            return $this;
        }

        /**
         * @return string
         */
        public function getMessage3()
        {
            return $this->message3;
        }

        /**
         * @param $message3
         *
         * @return $this
         */
        public function setMessage3($message3)
        {
            $this->message3 = $message3;

            return $this;
        }

        /**
         * @return string
         */
        public function getNote1()
        {
            return $this->note1;
        }

        /**
         * @param $note1
         *
         * @return $this
         */
        public function setNote1($note1)
        {
            $this->note1 = $note1;

            return $this;
        }

        /**
         * @return string
         */
        public function getNote2()
        {
            return $this->note2;
        }

        /**
         * @param $note2
         *
         * @return $this
         */
        public function setNote2($note2)
        {
            $this->note2 = $note2;

            return $this;
        }

        /**
         * @return string
         */
        public function getNote3()
        {
            return $this->note3;
        }

        /**
         * @param $note3
         *
         * @return $this
         */
        public function setNote3($note3)
        {
            $this->note3 = $note3;

            return $this;
        }

        /**
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * @param $create_date
         *
         * @return $this
         */
        public function setCreateDate($create_date)
        {
            $this->create_date = $create_date;

            return $this;
        }

        /**
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * @param $update_date
         *
         * @return $this
         */
        public function setUpdateDate($update_date)
        {
            $this->update_date = $update_date;

            return $this;
        }

        /**
         * Set visible
         *
         * @param boolean $visible
         *
         * @return Delivery
         */
        public function setVisible($visible)
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Is the visibility visible?
         *
         * @return boolean
         */
        public function isVisible()
        {
            return $this->visible;
        }
    }
}
