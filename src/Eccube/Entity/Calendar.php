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
         *
         * @return bool
         */
        public function isDefaultCalendar(): bool
        {
            return self::DEFAULT_CALENDAR_ID === $this->getId();
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: true)]
        private $title;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'holiday', type: 'datetimetz')]
        private $holiday;

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
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set title.
         *
         * @param string $title
         *
         * @return Calendar
         */
        public function setTitle($title): Calendar
        {
            $this->title = $title;

            return $this;
        }

        /**
         * Get title.
         *
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }

        /**
         * Set holiday.
         *
         * @param \DateTime $holiday
         *
         * @return Calendar
         */
        public function setHoliday($holiday): Calendar
        {
            $this->holiday = $holiday;

            return $this;
        }

        /**
         * Get holiday.
         *
         * @return \DateTime|null
         */
        public function getHoliday(): ?\DateTime
        {
            return $this->holiday;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Calendar
         */
        public function setCreateDate($createDate): Calendar
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
         * @return Calendar
         */
        public function setUpdateDate($updateDate): Calendar
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
    }
}
