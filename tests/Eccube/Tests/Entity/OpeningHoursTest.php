<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Entity;

use Eccube\Entity\OpeningHours;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 営業時間 1 エントリの単体バリデーション（OpeningHours::validate()）を検証する.
 */
final class OpeningHoursTest extends EccubeTestCase
{
    private function validate(OpeningHours $OpeningHours): ConstraintViolationListInterface
    {
        /** @var ValidatorInterface $validator */
        $validator = static::getContainer()->get(ValidatorInterface::class);

        return $validator->validate($OpeningHours);
    }

    private function create(string $opens, string $closes): OpeningHours
    {
        return (new OpeningHours())
            ->setDayOfWeek(['Friday'])
            ->setOpens(new \DateTime($opens))
            ->setCloses(new \DateTime($closes));
    }

    public function testValidSameDay(): void
    {
        $this->assertCount(0, $this->validate($this->create('09:00', '18:00')));
    }

    /**
     * 閉店が開店より前の行は、翌日の閉店時刻として扱う（深夜営業）.
     */
    public function testValidOvernight(): void
    {
        $this->assertCount(0, $this->validate($this->create('18:00', '03:00')));
    }

    /**
     * 24 時間営業は 00:00〜23:59 で表す.
     */
    public function testValidAllDay(): void
    {
        $this->assertCount(0, $this->validate($this->create('00:00', '23:59')));
    }

    /**
     * 開店と閉店が同じ時刻はエラー（Google は 00:00/00:00 を終日休業と解釈するため）.
     */
    public function testInvalidOpensEqualsCloses(): void
    {
        foreach (['00:00', '09:00'] as $time) {
            $violations = $this->validate($this->create($time, $time));

            $this->assertCount(1, $violations);
            $this->assertSame('closes', $violations[0]->getPropertyPath());
            $this->assertSame('admin.setting.shop.opening_hours.error.opens_equals_closes', $violations[0]->getMessageTemplate());
        }
    }

    public function testEmptyRowIsNotValidated(): void
    {
        $this->assertCount(0, $this->validate(new OpeningHours()));
    }
}
