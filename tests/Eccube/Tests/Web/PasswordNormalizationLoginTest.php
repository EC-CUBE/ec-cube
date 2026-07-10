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

namespace Eccube\Tests\Web;

use Eccube\Entity\Customer;
use Eccube\Util\PasswordNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * NIST SP 800-63B-4 対応の NFKC 正規化が, パスワード設定時とログイン照合時で
 * 一貫して適用され, Unicode パスワードの表記ゆれを吸収してログインできることを確認する.
 */
final class PasswordNormalizationLoginTest extends AbstractWebTestCase
{
    /** 半角カナを含む生パスワード(NFKC で全角へ正規化される) */
    private const RAW_PASSWORD = 'ﾊﾟｽﾜｰﾄﾞﾃｽﾄ123456';

    private ?Customer $Customer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();

        // 設定時の正規化(setPlainPassword)を通して保存する.
        $this->Customer->setPlainPassword(self::RAW_PASSWORD);
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->Customer->setPassword($hasher->hashPassword($this->Customer, $this->Customer->getPlainPassword()));
        $this->entityManager->flush();
    }

    public function testRawPasswordIsNormalizedOnStore()
    {
        // 半角カナは NFKC で正規化され, 生の入力とは異なる文字列で保存される(前提条件の確認).
        $this->assertNotSame(self::RAW_PASSWORD, PasswordNormalizer::normalize(self::RAW_PASSWORD));

        // 保存済みハッシュは正規化後のパスワードで検証できる(生のままでは検証できない).
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($hasher->isPasswordValid($this->Customer, PasswordNormalizer::normalize(self::RAW_PASSWORD)));
    }

    public function testLoginWithNonNormalizedPasswordSucceeds()
    {
        // 保存は正規化済み. ログイン入力は生(非正規化)でも, リスナで正規化され照合に成功する.
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => $this->generateUrl('mypage'),
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => self::RAW_PASSWORD,
        ]);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('mypage', [], UrlGeneratorInterface::ABSOLUTE_PATH)),
            '非正規化のパスワードでログインできること'
        );
    }

    public function testLoginWithNormalizedPasswordSucceeds()
    {
        // 正規化済みの表記でもログインできる.
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => $this->generateUrl('mypage'),
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => PasswordNormalizer::normalize(self::RAW_PASSWORD),
        ]);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('mypage', [], UrlGeneratorInterface::ABSOLUTE_PATH)),
            '正規化済みのパスワードでログインできること'
        );
    }
}
