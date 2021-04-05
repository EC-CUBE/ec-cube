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

namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Entity\Template;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\TemplateRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TemplateControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var TemplateRepository
     */
    protected $templateRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var string
     */
    protected $envFile;

    /**
     * @var string
     */
    protected $env;

    public function setUp()
    {
        parent::setUp();

        $this->templateRepository = $this->entityManager->getRepository(\Eccube\Entity\Template::class);
        $this->deviceTypeRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\DeviceType::class);

        $this->dir = \tempnam(\sys_get_temp_dir(), 'TemplateControllerTest');
        $fs = new Filesystem();
        $fs->remove($this->dir);
        $fs->mkdir($this->dir);

        $file = $this->dir.'/template.zip';
        $zip = new \ZipArchive();
        $zip->open($file, \ZipArchive::CREATE);
        $zip->addEmptyDir('app');
        $zip->addEmptyDir('html');
        $zip->close();
        $this->file = new UploadedFile($file, 'dummy.zip', 'application/zip');

        $this->code = StringUtil::random(6);

        $this->envFile = self::$container->getParameter('kernel.project_dir').'/.env';
        if (file_exists($this->envFile)) {
            $this->env = file_get_contents($this->envFile);
        }
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->dir);

        $templatePath = self::$container->getParameter('kernel.project_dir').'/app/template/'.$this->code;
        if ($fs->exists($templatePath)) {
            $fs->remove($templatePath);
        }

        if ($this->env) {
            file_put_contents($this->envFile, $this->env);
        }

        parent::tearDown();
    }

    /**
     * 一覧表示
     */
    public function testDisplayList()
    {
        $this->client->request('GET', $this->generateUrl('admin_store_template'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * テンプレートの変更
     *
     * @group cache-clear
     */
    public function testChangeTemplate()
    {
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);

        // テンプレートを選択
        $this->client->request('POST', $this->generateUrl('admin_store_template'), [
            'form' => [
                '_token' => 'dummy',
                'selected' => $Template->getId(),
            ],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirection());

        // .envが更新されている
        self::assertRegexp('/ECCUBE_TEMPLATE_CODE='.$Template->getCode().'/', file_get_contents($this->envFile));
    }

    /**
     * アップロード画面表示
     */
    public function testDiaplayUpload()
    {
        $this->client->request('GET', $this->generateUrl('admin_store_template_install'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * アップロード
     */
    public function testUpload()
    {
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();
    }

    /**
     * アップロード(大文字の拡張子)
     */
    public function testUploadWithUppercaseSuffix()
    {
        // テンプレートをアップロード
        $this->scenarioUpload(true);
        $this->verifyUpload();
    }

    /**
     * ダウンロード
     */
    public function testDownload()
    {
        $this->markTestIncomplete("See: \Eccube\Controller\Admin\Store\TemplateController::L151");

        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);

        // XXX failed to open stream: No such file or directoryが発生する
        $this->client->request('GET',
            $this->generateUrl('admin_store_template_download', ['id' => $Template->getId()]));
    }

    /**
     * 削除
     */
    public function testDelete()
    {
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);

        $id = $Template->getId();
        $code = $Template->getCode();

        // 削除
        $this->client->request('DELETE',
            $this->generateUrl('admin_store_template_delete', ['id' => $Template->getId()]));

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Template = $this->templateRepository->find($id);
        self::assertNull($Template);
        self::assertFalse(file_exists(self::$container->getParameter('kernel.project_dir').'/app/template/'.$code));
    }

    protected function scenarioUpload($uppercase = false)
    {
        $formData = $this->createFormData();
        $fileData = $this->createFileData($uppercase);

        return $this->client->request(
            'POST',
            $this->generateUrl('admin_store_template_install'),
            [
                'admin_template' => $formData,
            ],
            [
                'admin_template' => $fileData,
            ]);
    }

    protected function verifyUpload()
    {
        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);
        self::assertInstanceOf(Template::class, $Template);
    }

    protected function createFormData()
    {
        return [
            '_token' => 'dummy',
            'code' => $this->code,
            'name' => 'template name',
        ];
    }

    protected function createFileData($uppercase = false)
    {
        if ($uppercase) {
            $file = $this->dir.'/template.ZIP';
            $zip = new \ZipArchive();
            $zip->open($file, \ZipArchive::CREATE);
            $zip->addEmptyDir('app');
            $zip->addEmptyDir('html');
            $zip->close();
            $this->file = new UploadedFile($file, 'dummy.ZIP', 'application/zip');
        }

        return [
            'file' => $this->file,
        ];
    }
}
