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

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class LoginHistoryControllerTest extends AbstractAdminWebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $loginHistoryStatusRepository = $this->entityManager->getRepository(LoginHistoryStatus::class);

        // 履歴を10個生成しておく
        for ($i = 0; $i < 10; $i++) {
            $userName = 'member'.$i;

            $clientIp = $i % 2 === 1 ? '127.0.0.1' : '127.0.0.2';
            $LoginHistoryStatus = $loginHistoryStatusRepository->find($i % 2 === 1 ? LoginHistoryStatus::SUCCESS : LoginHistoryStatus::FAILURE);
            $Member = $this->createMember($userName);
            $this->createLoginHistory($userName, $clientIp, $LoginHistoryStatus, $Member);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('admin_setting_system_login_history'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('表示件数テスト');
    }

    public function testIndexPage()
    {
        // 表示件数100件テスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_setting_system_login_history_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数100件テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_setting_system_login_history_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_setting_system_login_history_page', ['page_no' => 1]), ['status' => 1]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
    }

    /**
     * testIndexWithPost
     */
    public function testIndexWithPost()
    {
        $post = [
            'admin_search_login_history' => ['_token' => 'dummy'],
        ];

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_login_history'), $post
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.custom-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');
    }

    public function testIndexWithPostSearchByUserName()
    {
        $post = [
            'admin_search_login_history' => ['_token' => 'dummy', 'user_name' => 'member1'],
        ];

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_login_history'), $post
        );

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');
    }

    public function testIndexWithPostSearchByClientIp()
    {
        $post = [
            'admin_search_login_history' => ['_token' => 'dummy', 'client_ip' => '127.0.0.1'],
        ];

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_login_history'), $post
        );

        $this->expected = '検索結果：5件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');
    }

    /**
     * @dataProvider dataStatusProvider
     */
    public function testIndexWithPostSearchByStatus($status, $count)
    {
        $post = [
            'admin_search_login_history' => ['_token' => 'dummy', 'Status' => $status],
        ];

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_login_history'),
            $post
        );

        $this->expected = sprintf('検索結果：%d件が該当しました', $count);
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');
    }

    /**
     * @return array[]
     */
    public function dataStatusProvider()
    {
        return [
            [[LoginHistoryStatus::SUCCESS], 5],
            [[LoginHistoryStatus::FAILURE], 5],
            [[LoginHistoryStatus::SUCCESS, LoginHistoryStatus::FAILURE], 10],
        ];
    }
}
