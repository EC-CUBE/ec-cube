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

namespace Eccube\Tests\Service\AgentCommerce;

use Eccube\Service\AgentCommerce\MinorUnitConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (pure logic) tests for MinorUnitConverter.
 *
 * Verifies minor-unit conversion across zero-decimal (JPY), two-decimal (USD)
 * and three-decimal (BHD) currencies, including negative amounts (UCP discounts),
 * round-half-up boundaries, and toMinorUnits<->toAmountString round trips.
 */
class MinorUnitConverterTest extends TestCase
{
    private MinorUnitConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new MinorUnitConverter();
    }

    public function testToMinorUnitsZeroDecimalJpy(): void
    {
        self::assertSame(1000, $this->converter->toMinorUnits('1000', 'JPY'), 'JPY has 0 fraction digits so the minor unit equals the major amount');
        self::assertSame(1000, $this->converter->toMinorUnits('1000.00', 'JPY'), 'JPY trailing zeros must not introduce extra minor units');
    }

    public function testToMinorUnitsTwoDecimalUsd(): void
    {
        self::assertSame(1000, $this->converter->toMinorUnits('10.00', 'USD'), 'USD 10.00 dollars equals 1000 cents');
        self::assertSame(1099, $this->converter->toMinorUnits('10.99', 'USD'), 'USD 10.99 dollars equals 1099 cents');
        self::assertSame(5, $this->converter->toMinorUnits('0.05', 'USD'), 'USD 0.05 dollars equals 5 cents');
    }

    public function testToMinorUnitsThreeDecimalBhd(): void
    {
        self::assertSame(1500, $this->converter->toMinorUnits('1.500', 'BHD'), 'BHD has 3 fraction digits so 1.5 dinar equals 1500 fils');
        self::assertSame(1234, $this->converter->toMinorUnits('1.234', 'BHD'), 'BHD 1.234 dinar equals 1234 fils');
    }

    public function testToMinorUnitsNegativeAmount(): void
    {
        self::assertSame(-500, $this->converter->toMinorUnits('-5.00', 'USD'), 'Negative amounts (UCP discounts) must convert to negative minor units');
        self::assertSame(-1000, $this->converter->toMinorUnits('-1000', 'JPY'), 'Negative zero-decimal amount must remain negative');
    }

    public function testToMinorUnitsRoundHalfUp(): void
    {
        self::assertSame(1010, $this->converter->toMinorUnits('10.095', 'USD'), 'Round-half-up: 10.095 USD rounds up to 1010 cents');
        self::assertSame(1009, $this->converter->toMinorUnits('10.094', 'USD'), 'Round down: 10.094 USD rounds to 1009 cents');
        self::assertSame(-1010, $this->converter->toMinorUnits('-10.095', 'USD'), 'Round-half-up on negative magnitude: -10.095 USD rounds to -1010 cents');
    }

    public function testToAmountStringZeroDecimalJpy(): void
    {
        self::assertSame('1000', $this->converter->toAmountString(1000, 'JPY'), 'JPY minor units map back to the same integer string with no decimal point');
    }

    public function testToAmountStringTwoDecimalUsd(): void
    {
        self::assertSame('10.00', $this->converter->toAmountString(1000, 'USD'), 'USD 1000 cents map back to 10.00 dollars with 2 decimals');
        self::assertSame('10.99', $this->converter->toAmountString(1099, 'USD'), 'USD 1099 cents map back to 10.99 dollars');
        self::assertSame('0.05', $this->converter->toAmountString(5, 'USD'), 'USD 5 cents map back to 0.05 dollars');
    }

    public function testToAmountStringThreeDecimalBhd(): void
    {
        self::assertSame('1.500', $this->converter->toAmountString(1500, 'BHD'), 'BHD 1500 fils map back to 1.500 dinar with 3 decimals');
    }

    public function testToAmountStringNegative(): void
    {
        self::assertSame('-5.00', $this->converter->toAmountString(-500, 'USD'), 'Negative minor units must map back to a negative decimal string');
    }

    #[DataProvider('roundTripProvider')]
    public function testRoundTrip(string $amount, string $currency): void
    {
        $minor = $this->converter->toMinorUnits($amount, $currency);
        $back = $this->converter->toAmountString($minor, $currency);
        $reMinor = $this->converter->toMinorUnits($back, $currency);
        self::assertSame($minor, $reMinor, 'toAmountString output must convert back to the identical minor-unit integer');
    }

    public static function roundTripProvider(): array
    {
        return [
            'JPY positive' => ['1000', 'JPY'],
            'JPY negative' => ['-2500', 'JPY'],
            'USD positive' => ['10.99', 'USD'],
            'USD negative' => ['-3.50', 'USD'],
            'BHD positive' => ['1.234', 'BHD'],
        ];
    }
}
