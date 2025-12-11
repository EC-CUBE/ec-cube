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

namespace Eccube\Tests\Security\PasswordHasher;

use Eccube\Entity\Member;
use Eccube\Security\PasswordHasher\PasswordHasher;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

final class PasswordMigrationTest extends EccubeTestCase
{
    private ?LegacyPasswordHasherInterface $legacyPasswordHasher = null;

    #[\Override]
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
        $crawler = $this->client->request(Request::METHOD_GET, '/admin/login');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('ログイン')->form();
        $form['login_id'] = $username;
        $form['password'] = $password;
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        // ログイン後、パスワードがマイグレーションされていることを確認.
        $this->entityManager->clear();
        $Member = $this->entityManager->find(Member::class, $Member->getId());
        $this->assertInstanceOf(Member::class, $Member);

        $this->assertNotSame($hash, $Member->getPassword(), $hash.':'.$Member->getPassword());
        $this->assertInstanceOf(Member::class, $Member);
        $this->assertStringStartsWith('$', $Member->getPassword(), $Member->getPassword());
    }
}
