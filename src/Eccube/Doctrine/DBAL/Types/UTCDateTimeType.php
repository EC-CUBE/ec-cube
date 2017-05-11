<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    /**
     * UTCのタイムゾーン
     *
     * @var \DateTimeZone
     */
    static protected $utc;

    /**
     * アプリケーションのタイムゾーン
     *
     * @var \DateTimeZone
     */
    static protected $timezone;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtcTimeZone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUtcTimeZone()
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        $converted->setTimezone(self::getTimezone());

        return $converted;
    }

    /**
     * @return \DateTimeZone
     */
    protected static function getUtcTimeZone()
    {
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    /**
     * @return \DateTimeZone
     */
    public static function getTimezone()
    {
        if (is_null(self::$timezone)) {
            throw new \LogicException(sprintf('%s::$timezone is undefined.', self::class));
        }

        return self::$timezone;
    }

    /**
     * @param string $timezone
     */
    public static function setTimeZone($timezone = 'Asia/Tokyo')
    {
        self::$timezone = new \DateTimeZone($timezone);
    }
}
