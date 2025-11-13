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

namespace Eccube\Tests\Repository;

use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Repository\LoginHistoryRepository;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * LoginHistoryRepository test cases.
 */
class LoginHistoryRepositoryGetQueryBuilderBySearchDataAdminTest extends EccubeTestCase
{
    protected ?array $Results = null;

    protected ?array $searchData = null;

    private ?LoginHistoryRepository $loginHistoryRepository = null;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginHistoryRepository = $this->entityManager->getRepository(LoginHistory::class);
        $this->Member1 = $this->createMember('member1');
        $this->LoginHistory1 = $this->createLoginHistory('member1', '127.0.0.1', LoginHistoryStatus::SUCCESS, $this->Member1);
        $this->LoginHistory2 = $this->createLoginHistory('member1', '127.0.0.1', LoginHistoryStatus::FAILURE, $this->Member1);
        $this->LoginHistory3 = $this->createLoginHistory('member2', '127.0.0.2', LoginHistoryStatus::FAILURE);
    }

    public function scenario()
    {
        $this->Results = $this->loginHistoryRepository->getQueryBuilderBySearchDataForAdmin($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testMulti()
    {
        $this->searchData = [
            'multi' => 'member1',
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUserName()
    {
        $this->searchData = [
            'user_name' => 'member1',
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testClientIp()
    {
        $this->searchData = [
            'client_ip' => '127.0.0.1',
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * @param $status
     * @param $expected
     */
    #[DataProvider(methodName: 'dataStatusProvider')]
    public function testStatus($status, $expected)
    {
        $this->searchData = [
            'Status' => $status,
        ];
        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * @return \Iterator<(int | string), array<mixed>>
     */
    public static function dataStatusProvider(): \Iterator
    {
        yield [[LoginHistoryStatus::SUCCESS], 1];
        yield [[LoginHistoryStatus::FAILURE], 2];
        yield [[LoginHistoryStatus::SUCCESS, LoginHistoryStatus::FAILURE], 3];
    }

    #[DataProvider(methodName: 'dataFormDateProvider')]
    public function testDate(string $formName, string $time, int $expected)
    {
        $this->searchData = [
            $formName => new \DateTime($time),
        ];

        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data provider date form test.
     *
     * time:
     * - today: 今日の00:00:00
     * - tomorrow: 明日の00:00:00
     * - yesterday: 昨日の00:00:00
     */
    public static function dataFormDateProvider(): \Iterator
    {
        yield ['create_date_start', 'today', 3];
        yield ['create_date_start', 'tomorrow', 0];
        yield ['create_date_end', 'today', 3];
        yield ['create_date_end', 'yesterday', 0];
    }

    #[DataProvider(methodName: 'dataFormDateTimeProvider')]
    public function testDateTime(string $formName, string $time, int $expected)
    {
        $this->searchData = [
            $formName => new \DateTime($time),
        ];

        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data provider datetime form test.
     */
    public static function dataFormDateTimeProvider(): \Iterator
    {
        yield ['create_datetime_start', '- 1 hour', 3];
        yield ['create_datetime_start', '+ 1 hour', 0];
        yield ['create_datetime_end', '+ 1 hour', 3];
        yield ['create_datetime_end', '- 1 hour', 0];
    }
}
