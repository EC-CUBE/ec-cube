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
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->getMailSubject();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private ?int $id = null;

        #[ORM\Column(name: 'send_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private ?\DateTime $send_date = null;

        #[ORM\Column(name: 'mail_subject', type: Types::STRING, length: 255, nullable: true)]
        private ?string $mail_subject = null;

        #[ORM\Column(name: 'mail_body', type: Types::TEXT, nullable: true)]
        private ?string $mail_body = null;

        #[ORM\Column(name: 'mail_html_body', type: Types::TEXT, nullable: true)]
        private ?string $mail_html_body = null;

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'MailHistories')]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: true)]
        private ?Order $Order = null;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: true)]
        private ?Member $Creator = null;

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
         */
        public function setSendDate(?\DateTime $sendDate = null): MailHistory
        {
            $this->send_date = $sendDate;

            return $this;
        }

        /**
         * Get sendDate.
         */
        public function getSendDate(): ?\DateTime
        {
            return $this->send_date;
        }

        /**
         * Set mailSubject.
         */
        public function setMailSubject(?string $mailSubject = null): MailHistory
        {
            $this->mail_subject = $mailSubject;

            return $this;
        }

        /**
         * Get mailSubject.
         */
        public function getMailSubject(): ?string
        {
            return $this->mail_subject;
        }

        /**
         * Set mailBody.
         */
        public function setMailBody(?string $mailBody = null): MailHistory
        {
            $this->mail_body = $mailBody;

            return $this;
        }

        /**
         * Get mailBody.
         */
        public function getMailBody(): ?string
        {
            return $this->mail_body;
        }

        /**
         * Set mailHtmlBody.
         */
        public function setMailHtmlBody(?string $mailHtmlBody = null): MailHistory
        {
            $this->mail_html_body = $mailHtmlBody;

            return $this;
        }

        /**
         * Get mailHtmlBody.
         */
        public function getMailHtmlBody(): ?string
        {
            return $this->mail_html_body;
        }

        /**
         * Set order.
         */
        public function setOrder(?Order $order = null): MailHistory
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         */
        public function getOrder(): ?Order
        {
            return $this->Order;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): MailHistory
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
