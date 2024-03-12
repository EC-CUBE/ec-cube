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

if (!class_exists('\Eccube\Entity\Calendar')) {
    /**
     * Calendar
     *
     * @ORM\Table(name="dtb_calendar")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\CalendarRepository")
     */
    class Calendar extends \Eccube\Entity\AbstractEntity
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
        public function isDefaultCalendar()
        {
            return self::DEFAULT_CALENDAR_ID === $this->getId();
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
         * @var string
         *
         * @ORM\Column(name="title", type="string", length=255, nullable=true)
         */
        private $title;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="holiday", type="datetimetz")
         */
        private $holiday;

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
         * Get id.
         *
         * @return int
         */
        public function getId()
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
        public function setTitle($title)
        {
            $this->title = $title;

            return $this;
        }

        /**
         * Get title.
         *
         * @return string
         */
        public function getTitle()
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
        public function setHoliday($holiday)
        {
            $this->holiday = $holiday;

            return $this;
        }

        /**
         * Get holiday.
         *
         * @return \DateTime
         */
        public function getHoliday()
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
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
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
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }
    }
}
