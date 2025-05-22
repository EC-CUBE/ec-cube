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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\MailTemplate;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class MailControllerTest
 */
class MailControllerTest extends AbstractAdminWebTestCase
{
    protected function tearDown(): void
    {
        $themeDir = static::getContainer()->getParameter('eccube_theme_front_dir');
        $fs = new Filesystem();
        $fs->remove((new Finder())->in($themeDir)->name('test_*.twig'));

        parent::tearDown();
    }

    /**
     * メール設定画面の表示
     *
     * @return void
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_mail'));
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    /**
     * 新規登録
     *
     * @return void
     */
    public function testCreate()
    {
        // 新規登録
        $crawler = $this->senarioCreate();
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();
    }

    /**
     * バリデーションエラー
     *
     * @return void
     */
    public function testValidationError()
    {
        // 必須項目を空で登録し、バリデーションエラーを発生させる
        $crawler = $this->senarioCreate(['file_name' => '']);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->actual = $crawler->filter('span.form-error-message')->text();
        $this->expected = '入力されていません。';
        $this->verify();
    }

    /**
     * ファイル名が既に使用されている
     *
     * @return void
     */
    public function testFileAlreadyExists()
    {
        // 新規登録
        $crawler = $this->senarioCreate(['file_name' => 'test_exists']);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // 同一ファイル名で新規登録
        $crawler = $this->senarioCreate(['file_name' => 'test_exists']);
        $this->assertFalse($this->client->getResponse()->isRedirect());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->actual = $crawler->filter('span.form-error-message')->text();
        $this->expected = 'このファイル名はすでに使用されています。';
        $this->verify();
    }

    /**
     * 編集
     *
     * @return void
     */
    public function testEdit()
    {
        // 新規登録
        $crawler = $this->senarioCreate();
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $location = $this->client->getResponse()->headers->get('location');
        $id = str_replace('/admin/setting/shop/mail/', '', $location);

        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // 編集画面を表示
        $this->client->request('GET',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $id])
        );
        $this->assertTrue($this->client->getResponse()->isOk());

        // 件名を更新
        $subject = 'test_edit_mail_subejct';
        $this->senarioEdit($id, ['mail_subject' => $subject]);

        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // 更新を確認
        $MailTemplate = $this->entityManager->find(MailTemplate::class, $id);
        $this->expected = $subject;
        $this->actual = $MailTemplate->getMailSubject();
        $this->verify();
    }

    /**
     * HTMLを空で登録すると、HTMLテンプレートファイルが削除されることを確認
     *
     * @return void
     */
    public function testEditClearHtml()
    {
        // 新規登録
        $crawler = $this->senarioCreate([
            'file_name' => 'test_edit_clear_html',
            'html_tpl_data' => '<strong>html</strong>',
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $location = $this->client->getResponse()->headers->get('location');
        $id = str_replace('/admin/setting/shop/mail/', '', $location);

        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // テンプレートファイルが生成されていることを確認
        $themeDir = static::getContainer()->getParameter('eccube_theme_front_dir');
        $this->assertTrue(file_exists($themeDir.'/Mail/test_edit_clear_html.twig'));
        $this->assertTrue(file_exists($themeDir.'/Mail/test_edit_clear_html.html.twig'));

        // HTMLを空で更新
        $this->senarioEdit($id, ['html_tpl_data' => '']);
        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // HTMLテンプレートファイルが削除されていることを確認
        $themeDir = static::getContainer()->getParameter('eccube_theme_front_dir');
        $this->assertTrue(file_exists($themeDir.'/Mail/test_edit_clear_html.twig'));
        $this->assertFalse(file_exists($themeDir.'/Mail/test_edit_clear_html.html.twig'));
    }

    /**
     * 存在しないテンプレートIDを指定
     *
     * @return void
     */
    public function testEditNotExists()
    {
        $id = 99999;
        $crawler = $this->senarioEdit($id);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->actual = $crawler->filter('span.form-error-message')->text();
        $this->expected = '選択した値は無効です。';
        $this->verify();
    }

    /**
     * 削除
     *
     * @return void
     */
    public function testDelete()
    {
        // 新規登録
        $crawler = $this->senarioCreate();

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $location = $this->client->getResponse()->headers->get('location');
        $id = str_replace('/admin/setting/shop/mail/', '', $location);

        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // 削除
        $crawler = $this->senarioDelete($id);
        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '削除しました';
        $this->verify();
    }

    /**
     * 削除不可のテンプレートを削除
     *
     * @return void
     */
    public function testDeleteNotDeletable()
    {
        // 新規登録
        $crawler = $this->senarioCreate();

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $location = $this->client->getResponse()->headers->get('location');
        $id = str_replace('/admin/setting/shop/mail/', '', $location);

        $crawler = $this->client->followRedirect();
        $this->actual = $crawler->filter('div.alert')->text();
        $this->expected = '保存しました';
        $this->verify();

        // deletable => falseに更新
        $MailTemplate = $this->entityManager->find(MailTemplate::class, $id);
        $MailTemplate->setDeletable(false);
        $this->entityManager->flush();

        // 削除
        $crawler = $this->senarioDelete($id);

        // 削除されず残っている
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()]),
        );
        // 編集画面を表示可能
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    private function senarioCreate(array $form = [])
    {
        $faker = $this->getFaker();
        $form = array_merge([
            '_token' => 'dummy',
            'name' => $faker->word,
            'file_name' => 'test_'.$faker->lexify('????????'),
            'mail_subject' => $faker->word,
            'tpl_data' => $faker->realText,
            'html_tpl_data' => $faker->realText,
        ], $form);

        return $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail'),
            ['mail' => $form]
        );
    }

    private function senarioEdit($id, array $form = [])
    {
        $faker = $this->getFaker();
        $form = array_merge([
            '_token' => 'dummy',
            'template' => $id,
            'name' => $faker->word,
            'mail_subject' => $faker->word,
            'tpl_data' => $faker->realText,
            'html_tpl_data' => $faker->realText,
        ], $form);

        return $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $id]),
            ['mail' => $form]
        );
    }

    private function senarioDelete($id)
    {
        return $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_mail_delete', ['id' => $id]),
        );
    }
}
