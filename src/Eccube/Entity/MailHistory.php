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
use Eccube\Repository\MailHistoryRepository;

if (!class_exists(MailHistory::class)) {
    /**
     * MailHistory
     */
    #[ORM\Table(name: 'dtb_mail_history')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: MailHistoryRepository::class)]
    class MailHistory extends AbstractEntity implements \Stringable
    {
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->getMailSubject();
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
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'send_date', type: 'datetimetz', nullable: true)]
        private $send_date;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'mail_subject', type: 'string', length: 255, nullable: true)]
        private $mail_subject;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'mail_body', type: 'text', nullable: true)]
        private $mail_body;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'mail_html_body', type: 'text', nullable: true)]
        private $mail_html_body;

        /**
         * @var Order|null
         */
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'MailHistories')]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: true)]
        private $Order;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: true)]
        private $Creator;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId(): ?int
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
        public function setSendDate($sendDate = null): MailHistory
        {
            $this->send_date = $sendDate;

            return $this;
        }

        /**
         * Get sendDate.
         *
         * @return \DateTime|null
         */
        public function getSendDate(): ?\DateTime
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
        public function setMailSubject($mailSubject = null): MailHistory
        {
            $this->mail_subject = $mailSubject;

            return $this;
        }

        /**
         * Get mailSubject.
         *
         * @return string|null
         */
        public function getMailSubject(): ?string
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
        public function setMailBody($mailBody = null): MailHistory
        {
            $this->mail_body = $mailBody;

            return $this;
        }

        /**
         * Get mailBody.
         *
         * @return string|null
         */
        public function getMailBody(): ?string
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
        public function setMailHtmlBody($mailHtmlBody = null): MailHistory
        {
            $this->mail_html_body = $mailHtmlBody;

            return $this;
        }

        /**
         * Get mailHtmlBody.
         *
         * @return string|null
         */
        public function getMailHtmlBody(): ?string
        {
            return $this->mail_html_body;
        }

        /**
         * Set order.
         *
         * @param Order|null $order
         *
         * @return MailHistory
         */
        public function setOrder(?Order $order = null): MailHistory
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return Order|null
         */
        public function getOrder(): ?Order
        {
            return $this->Order;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return MailHistory
         */
        public function setCreator(?Member $creator = null): MailHistory
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
