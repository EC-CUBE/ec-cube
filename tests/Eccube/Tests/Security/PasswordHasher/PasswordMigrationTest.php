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

namespace Eccube\Tests\Security\PasswordHasher;

use Eccube\Entity\Member;
use Eccube\Security\PasswordHasher\PasswordHasher;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\StringUtil;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class PasswordMigrationTest extends EccubeTestCase
{
    /**
     * @var LegacyPasswordHasherInterface
     */
    private $legacyPasswordHasher;

    public function setUp(): void
    {
        parent::setUp();
        $this->legacyPasswordHasher = self::getContainer()->get(PasswordHasher::class);
    }

    /**
     * EC-CUBEのハッシュアルゴリズムからSymfony標準のハッシュアルゴリズムへマイグレーションできることを確認する
     */
    public function testPasswordMigration()
    {
        // 旧アルゴリズムでパスワードをハッシュ化
        $username = 'migration-test-uesr';
        $password = 'password';
        $salt = StringUtil::random();
        $hash = $this->legacyPasswordHasher->hash($password, $salt);

        $Member = $this->createMember();
        $Member->setLoginId($username)
            ->setPassword($hash)
            ->setSalt($salt);
        $this->entityManager->flush();

        // ログイン
        $crawler = $this->client->request('GET', '/admin/login');
        self::assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('ログイン')->form();
        $form['login_id'] = $username;
        $form['password'] = $password;
        $this->client->submit($form);

        self::assertTrue($this->client->getResponse()->isRedirection());

        // ログイン後、パスワードがマイグレーションされていることを確認.
        $this->entityManager->clear();
        $Member = $this->entityManager->find(Member::class, $Member->getId());

        self::assertNotSame($hash, $Member->getPassword(), $hash.':'.$Member->getPassword());
        self::assertStringStartsWith('$', $Member->getPassword(), $Member->getPassword());
    }
}
