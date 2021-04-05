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

/**
 * LoginHistoryRepository test cases.
 */
class LoginHistoryRepositoryGetQueryBuilderBySearchDataAdminTest extends EccubeTestCase
{
    /**
     * @var array
     */
    protected $Results;

    /**
     * @var array
     */
    protected $searchData;

    /**
     * @var LoginHistoryRepository
     */
    private $loginHistoryRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
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
     * @dataProvider dataStatusProvider
     *
     * @param $status
     * @param $expected
     */
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
     * @return array[]
     */
    public function dataStatusProvider()
    {
        return [
            [[LoginHistoryStatus::SUCCESS], 1],
            [[LoginHistoryStatus::FAILURE], 2],
            [[LoginHistoryStatus::SUCCESS, LoginHistoryStatus::FAILURE], 3],
        ];
    }

    /**
     * @dataProvider dataFormDateProvider
     */
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
     *
     * @return array
     */
    public function dataFormDateProvider()
    {
        return [
            ['create_date_start', 'today', 3],
            ['create_date_start', 'tomorrow', 0],
            ['create_date_end', 'today', 3],
            ['create_date_end', 'yesterday', 0],
        ];
    }

    /**
     * @dataProvider dataFormDateTimeProvider
     */
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
     *
     * @return array
     */
    public function dataFormDateTimeProvider()
    {
        return [
            ['create_datetime_start', '- 1 hour', 3],
            ['create_datetime_start', '+ 1 hour', 0],
            ['create_datetime_end', '+ 1 hour', 3],
            ['create_datetime_end', '- 1 hour', 0],
        ];
    }
}
