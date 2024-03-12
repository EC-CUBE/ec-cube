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

if (!class_exists('\Eccube\Entity\MailHistory')) {
    /**
     * MailHistory
     *
     * @ORM\Table(name="dtb_mail_history")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\MailHistoryRepository")
     */
    class MailHistory extends AbstractEntity
    {
        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getMailSubject();
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="send_date", type="datetimetz", nullable=true)
         */
        private $send_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="mail_subject", type="string", length=255, nullable=true)
         */
        private $mail_subject;

        /**
         * @var string|null
         *
         * @ORM\Column(name="mail_body", type="text", nullable=true)
         */
        private $mail_body;

        /**
         * @var string|null
         *
         * @ORM\Column(name="mail_html_body", type="text", nullable=true)
         */
        private $mail_html_body;

        /**
         * @var \Eccube\Entity\Order
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order", inversedBy="MailHistories")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
         * })
         */
        private $Order;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
         * })
         */
        private $Creator;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set sendDate.
         *
         * @param \DateTime|null $sendDate
         *
         * @return MailHistory
         */
        public function setSendDate($sendDate = null)
        {
            $this->send_date = $sendDate;

            return $this;
        }

        /**
         * Get sendDate.
         *
         * @return \DateTime|null
         */
        public function getSendDate()
        {
            return $this->send_date;
        }

        /**
         * Set mailSubject.
         *
         * @param string|null $mailSubject
         *
         * @return MailHistory
         */
        public function setMailSubject($mailSubject = null)
        {
            $this->mail_subject = $mailSubject;

            return $this;
        }

        /**
         * Get mailSubject.
         *
         * @return string|null
         */
        public function getMailSubject()
        {
            return $this->mail_subject;
        }

        /**
         * Set mailBody.
         *
         * @param string|null $mailBody
         *
         * @return MailHistory
         */
        public function setMailBody($mailBody = null)
        {
            $this->mail_body = $mailBody;

            return $this;
        }

        /**
         * Get mailBody.
         *
         * @return string|null
         */
        public function getMailBody()
        {
            return $this->mail_body;
        }

        /**
         * Set mailHtmlBody.
         *
         * @param string|null $mailHtmlBody
         *
         * @return MailHistory
         */
        public function setMailHtmlBody($mailHtmlBody = null)
        {
            $this->mail_html_body = $mailHtmlBody;

            return $this;
        }

        /**
         * Get mailHtmlBody.
         *
         * @return string|null
         */
        public function getMailHtmlBody()
        {
            return $this->mail_html_body;
        }

        /**
         * Set order.
         *
         * @param \Eccube\Entity\Order|null $order
         *
         * @return MailHistory
         */
        public function setOrder(Order $order = null)
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return \Eccube\Entity\Order|null
         */
        public function getOrder()
        {
            return $this->Order;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return MailHistory
         */
        public function setCreator(Member $creator = null)
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }
    }
}
