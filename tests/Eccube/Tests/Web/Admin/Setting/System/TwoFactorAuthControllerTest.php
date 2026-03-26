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

use Eccube\Entity\Member;
use Eccube\Repository\MemberRepository;
use Eccube\Service\TwoFactorAuthService;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorAuthControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var TwoFactorAuthService
     */
    protected $twoFactorAuthService;

    /**
     * @var TwoFactorAuth
     */
    protected $tfa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberRepository = $this->entityManager->getRepository(Member::class);
        $this->twoFactorAuthService = static::getContainer()->get(TwoFactorAuthService::class);
        $this->tfa = new TwoFactorAuth();
    }

    /**
     * 正常系1: 2FA有効済み・登録済みユーザーの認証成功
     * 管理画面 → ログイン → 2FA認証 → ログイン成功
     */
    public function testAuthSuccessWithRegisteredUser()
    {
        if (!$this->twoFactorAuthService->isEnabled()) {
            $this->markTestSkipped('2FAが無効のためスキップ');
        }

        // 2FA設定済みの新規メンバーを作成
        $authKey = $this->twoFactorAuthService->createSecret();
        $Member = $this->createMember();
        $Member->setTwoFactorAuthEnabled(true);
        $Member->setTwoFactorAuthKey($authKey);
        $this->entityManager->persist($Member);
        $this->entityManager->flush();

        // 新しいMemberでログイン
        $this->loginTo($Member);

        // 2FA認証画面にアクセス（CSRFトークン取得のため）
        $crawler = $this->client->request('GET', $this->generateUrl('admin_two_factor_auth'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // CSRFトークンを取得
        $token = $crawler->filter('input[name="admin_two_factor_auth[_token]"]')->attr('value');

        // 正しいTOTPコードを生成して送信
        $validCode = $this->tfa->getCode($authKey);
        $this->client->request('POST', $this->generateUrl('admin_two_factor_auth'), [
            'admin_two_factor_auth' => [
                'device_token' => $validCode,
                '_token' => $token,
            ],
        ]);

        // ホームにリダイレクトされることを確認（ログイン成功）
        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('admin_homepage')),
            '2FA認証成功後、ホームにリダイレクトされるべき'
        );
    }

    /**
     * 正常系2: 2FA有効済み・未登録ユーザーのセットアップ成功
     * 管理画面 → ログイン → 2FAセットアップ画面 → セットアップ完了
     */
    public function testSetupSuccessWithUnregisteredUser()
    {
        if (!$this->twoFactorAuthService->isEnabled()) {
            $this->markTestSkipped('2FAが無効のためスキップ');
        }

        // 2FA未設定の新規メンバーを作成
        $Member = $this->createMember();
        $Member->setTwoFactorAuthEnabled(true);
        $Member->setTwoFactorAuthKey(null); // 未登録
        $this->entityManager->persist($Member);
        $this->entityManager->flush();

        // 新しいMemberでログイン
        $this->loginTo($Member);

        // 2FAセットアップ画面にアクセス
        $crawler = $this->client->request('GET', $this->generateUrl('admin_two_factor_auth_set'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // フォームから秘密鍵とCSRFトークンを取得
        $authKey = $crawler->filter('input[name="admin_two_factor_auth[auth_key]"]')->attr('value');
        $token = $crawler->filter('input[name="admin_two_factor_auth[_token]"]')->attr('value');

        // 正しいTOTPコードを生成して送信
        $validCode = $this->tfa->getCode($authKey);
        $this->client->request('POST', $this->generateUrl('admin_two_factor_auth_set'), [
            'admin_two_factor_auth' => [
                'device_token' => $validCode,
                'auth_key' => $authKey,
                '_token' => $token,
            ],
        ]);

        // ホームにリダイレクトされることを確認（セットアップ完了）
        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('admin_homepage')),
            '2FAセットアップ完了後、ホームにリダイレクトされるべき'
        );

        // 2FAキーが保存されていることを確認（DBから再取得）
        $savedMember = $this->memberRepository->find($Member->getId());
        $this->assertNotNull($savedMember->getTwoFactorAuthKey(), '2FAキーが保存されているべき');
    }

    /**
     * 失敗系: 2FA認証失敗
     * 間違ったコードで認証を試みた場合、エラーメッセージが表示される
     */
    public function testAuthFailureWithInvalidCode()
    {
        if (!$this->twoFactorAuthService->isEnabled()) {
            $this->markTestSkipped('2FAが無効のためスキップ');
        }

        // 2FA設定済みの新規メンバーを作成
        $authKey = $this->twoFactorAuthService->createSecret();
        $Member = $this->createMember();
        $Member->setTwoFactorAuthEnabled(true);
        $Member->setTwoFactorAuthKey($authKey);
        $this->entityManager->persist($Member);
        $this->entityManager->flush();

        // 新しいMemberでログイン
        $this->loginTo($Member);

        // 2FA認証画面にアクセス
        $crawler = $this->client->request('GET', $this->generateUrl('admin_two_factor_auth'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // CSRFトークンを取得
        $token = $crawler->filter('input[name="admin_two_factor_auth[_token]"]')->attr('value');

        // 間違ったコードを送信
        $crawler = $this->client->request('POST', $this->generateUrl('admin_two_factor_auth'), [
            'admin_two_factor_auth' => [
                'device_token' => '000000', // 無効なコード
                '_token' => $token,
            ],
        ]);

        // リダイレクトされずに同じ画面に留まる（エラー表示）
        $this->assertFalse(
            $this->client->getResponse()->isRedirect(),
            '無効なコードの場合、リダイレクトされないべき'
        );

        // エラーメッセージが表示されていることを確認（text-dangerクラスで表示）
        $errorElement = $crawler->filter('.text-danger');
        $this->assertGreaterThan(0, $errorElement->count(), 'エラーメッセージが表示されるべき');
    }

    /**
     * 脆弱性テスト: MFAバイパス脆弱性
     * 2FAキーが設定済みのユーザーが2FA未認証状態で設定画面にアクセスした場合、
     * 認証画面にリダイレクトされることを確認
     */
    public function testSetRedirectsToAuthWhenTwoFactorAuthKeyAlreadyConfigured()
    {
        // 2FAが無効な場合はスキップ
        if (!$this->twoFactorAuthService->isEnabled()) {
            $this->markTestSkipped('2FAが無効のためスキップ');
        }

        // 2FA設定済みの新規メンバーを作成
        $Member = $this->createMember();
        $Member->setTwoFactorAuthEnabled(true);
        $Member->setTwoFactorAuthKey($this->twoFactorAuthService->createSecret());
        $this->entityManager->persist($Member);
        $this->entityManager->flush();

        // 新しいMemberでログインし直す
        $this->loginTo($Member);

        // 2FA未認証状態で設定画面にアクセス
        $this->client->request('GET', $this->generateUrl('admin_two_factor_auth_set'));

        $response = $this->client->getResponse();

        // 認証画面にリダイレクトされることを確認
        $this->assertTrue(
            $response->isRedirect($this->generateUrl('admin_two_factor_auth')),
            '2FAキー設定済みユーザーが未認証で設定画面にアクセスした場合、認証画面にリダイレクトされるべき。実際のレスポンス: Status='.$response->getStatusCode().', Location='.$response->headers->get('Location')
        );
    }
}
