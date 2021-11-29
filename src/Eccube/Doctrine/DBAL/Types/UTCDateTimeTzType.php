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

namespace Eccube\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeTzType;

class UTCDateTimeTzType extends DateTimeTzType
{
    /**
     * UTCのタイムゾーン
     *
     * @var \DateTimeZone
     */
    protected static $utc;

    /**
     * アプリケーションのタイムゾーン
     *
     * @var \DateTimeZone
     */
    protected static $timezone;

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
            $platform->getDateTimeTzFormatString(),
            $value,
            self::getUtcTimeZone()
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
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

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
