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
use Eccube\Repository\CalendarRepository;

if (!class_exists(Calendar::class)) {
    /**
     * Calendar
     */
    #[ORM\Table(name: 'dtb_calendar')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CalendarRepository::class)]
    class Calendar extends AbstractEntity
    {
        /**
         * @var int
         */
        public const DEFAULT_CALENDAR_ID = 1;

        /**
         * is default
         */
        public function isDefaultCalendar(): bool
        {
            return self::DEFAULT_CALENDAR_ID === $this->getId();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: true)]
        private ?string $title = null;

        #[ORM\Column(name: 'holiday', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $holiday = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $create_date = null;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $update_date = null;

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set title.
         */
        public function setTitle(string $title): Calendar
        {
            $this->title = $title;

            return $this;
        }

        /**
         * Get title.
         */
        public function getTitle(): string
        {
            return $this->title;
        }

        /**
         * Set holiday.
         */
        public function setHoliday(\DateTime $holiday): Calendar
        {
            $this->holiday = $holiday;

            return $this;
        }

        /**
         * Get holiday.
         */
        public function getHoliday(): ?\DateTime
        {
            return $this->holiday;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Calendar
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
        public function setUpdateDate(\DateTime $updateDate): Calendar
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
    }
}
