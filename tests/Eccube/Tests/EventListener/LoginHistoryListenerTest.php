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

namespace Eccube\Tests\EventListener;

use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LoginHistoryListenerTest extends AbstractWebTestCase
{
    public function activeLogin()
    {
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => 'dummy',
            ]
        );

        $LoginHistory = $this->entityManager->getRepository(LoginHistory::class)
            ->findOneBy([
                'user_name' => 'admin',
                'Status' => LoginHistoryStatus::SUCCESS,
            ]);
        $this->assertInstanceOf(LoginHistory::class, $LoginHistory);

        // $LoginHistoryの比較だと、RECURSIONが発生するため、IDの有無で確認
        $this->assertNotNull($LoginHistory->getId());
    }

    public function testOnAuthenticationFailure()
    {
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password2',
                '_csrf_token' => 'dummy',
            ]
        );

        $LoginHistory = $this->entityManager->getRepository(LoginHistory::class)
            ->findOneBy([
                'user_name' => 'admin',
                'Status' => LoginHistoryStatus::FAILURE,
            ]);

        $this->assertInstanceOf(LoginHistory::class, $LoginHistory);
    }
}
