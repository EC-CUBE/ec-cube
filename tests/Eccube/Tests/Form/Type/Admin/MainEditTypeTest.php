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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Entity\Page;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

final class MainEditTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /** @var array デフォルト値（正常系）を設定 */
    protected ?array $formData = [
        'name' => 'テストページ',
        'url' => 'test',
        'file_name' => 'foo/bar/baz',
        'tpl_data' => 'contents',
        'author' => '',
        'description' => '',
        'keyword' => '',
        'meta_robots' => '',
        'meta_tags' => '',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $options = [
            'csrf_protection' => false,
        ];
        $this->form = $this->formFactory
            ->createBuilder(MainEditType::class, $this->createPage(), $options)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNameBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidNameMaxLength()
    {
        $this->formData['name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidUrlSlash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidUrlSymbol()
    {
        $this->formData['url'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidUrlBlank()
    {
        $this->formData['url'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlStartsWithSlash()
    {
        $this->formData['url'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlEndsWithSlash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlContinuousSlash()
    {
        $this->formData['url'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlMaxLength()
    {
        $this->formData['url'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlDuplicateUrl()
    {
        $Page = $this->createPage();
        $this->formData['url'] = $Page->getUrl();
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFileNameSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameSymbol()
    {
        $this->formData['file_name'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameEndsWithSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameNamespace(): void
    {
        $this->form = $this->createFormForDefaultPage();
        $this->formData['file_name'] = '@PluginCode/xxx';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameNamespaceNested(): void
    {
        $this->form = $this->createFormForDefaultPage();
        $this->formData['file_name'] = '@PluginCode/xxx/yyy';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFileNameBlank()
    {
        $this->formData['file_name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameStartsWithSlash()
    {
        $this->formData['file_name'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameNamespaceOnly(): void
    {
        $this->form = $this->createFormForDefaultPage();
        $this->formData['file_name'] = '@PluginCode';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameNamespaceEndsWithSlash(): void
    {
        $this->form = $this->createFormForDefaultPage();
        $this->formData['file_name'] = '@PluginCode/';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * "." を許さないため, 名前空間付きでも上位ディレクトリへ抜けるファイル名は受け付けない.
     * 既定ページのフォームで確かめる (ユーザーのページでは "@" の検証が先に効き, 正規表現を検証できない).
     */
    #[DataProvider(methodName: 'providePathTraversalFileNames')]
    public function testInValidFileNamePathTraversal(string $fileName): void
    {
        $this->form = $this->createFormForDefaultPage();
        $this->formData['file_name'] = $fileName;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * @return \Iterator<string, array{string}>
     */
    public static function providePathTraversalFileNames(): \Iterator
    {
        yield '名前空間の位置で抜ける' => ['@../../../../etc/passwd'];
        yield '名前空間の後で抜ける' => ['@Boomerang/../../../etc/passwd'];
        yield '名前空間なし' => ['../../etc/passwd'];
        yield '絶対パス' => ['/etc/passwd'];
    }

    /**
     * ユーザーが新しく作るページには, "@" で始まるファイル名を付けられない.
     */
    public function testInValidFileNameNamespaceForNewUserPage(): void
    {
        $this->form = $this->formFactory
            ->createBuilder(MainEditType::class, $this->entityManager->getRepository(Page::class)->newPage(), ['csrf_protection' => false])
            ->getForm();
        $this->formData['file_name'] = '@PluginCode/xxx';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertSame(
            trans('admin.content.page_file_name_namespace_not_allowed'),
            $this->form['file_name']->getErrors()[0]->getMessage()
        );
    }

    /**
     * 保存済みのユーザーのページのファイル名を, "@" で始まるものへ変更できない.
     */
    public function testInValidFileNameNamespaceChangedForUserPage(): void
    {
        $this->formData['file_name'] = '@PluginCode/xxx';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertSame(
            trans('admin.content.page_file_name_namespace_not_allowed'),
            $this->form['file_name']->getErrors()[0]->getMessage()
        );
    }

    /**
     * プラグインが DB へ直接登録した "@" で始まるユーザーのページは, 同じファイル名のままなら保存できる.
     */
    public function testValidFileNameNamespaceUnchangedForUserPage(): void
    {
        $Page = $this->createPage()->setFileName('@PluginCode/'.bin2hex(random_bytes(4)));
        $this->entityManager->flush();
        $this->form = $this->formFactory
            ->createBuilder(MainEditType::class, $Page, ['csrf_protection' => false])
            ->getForm();
        $this->formData['file_name'] = $Page->getFileName();
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFileNameContinuousSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameDuplicateSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameMaxLength()
    {
        $this->formData['file_name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidTplDataBlank()
    {
        $this->formData['tpl_data'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidAuthorBlank()
    {
        $this->formData['author'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidAuthorMaxLength()
    {
        $this->formData['author'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidDescriptionBlank()
    {
        $this->formData['description'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDescriptionMaxLength()
    {
        $this->formData['description'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidKeywordBlank()
    {
        $this->formData['keyword'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidKeywordMaxLength()
    {
        $this->formData['keyword'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaRobotsBlank()
    {
        $this->formData['meta_robots'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaRobotsMaxLength()
    {
        $this->formData['meta_robots'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaTagsBlank()
    {
        $this->formData['meta_tags'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidMetaTagsFreeLength()
    {
        $this->formData['meta_tags'] = '<meta name="meta_tags_test" content="test" />';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaTagsMaxLength()
    {
        $this->formData['meta_tags'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * プラグインが登録した既定ページの編集フォーム.
     *
     * 名前空間付きのファイル名は, ユーザーが作るページ (EDIT_TYPE_USER) では新たに付けられず,
     * 既定ページのファイル名 (hidden で送られる) としてだけ受け付ける.
     */
    private function createFormForDefaultPage(): FormInterface
    {
        $Page = $this->createPage()->setEditType(Page::EDIT_TYPE_DEFAULT);
        $this->entityManager->flush();

        return $this->formFactory
            ->createBuilder(MainEditType::class, $Page, ['csrf_protection' => false])
            ->getForm();
    }
}
