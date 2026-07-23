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

namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Template;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\TemplateRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\StringUtil;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class TemplateControllerTest extends AbstractAdminWebTestCase
{
    protected ?string $dir = null;

    protected ?UploadedFile $file = null;

    protected ?string $code = null;

    protected ?TemplateRepository $templateRepository = null;

    protected ?DeviceTypeRepository $deviceTypeRepository = null;

    protected ?string $envFile = null;

    protected ?string $env = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->templateRepository = $this->entityManager->getRepository(Template::class);
        $this->deviceTypeRepository = $this->entityManager->getRepository(DeviceType::class);
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
        $this->envFile = static::getContainer()->getParameter('kernel.project_dir').'/.env';
        if (file_exists($this->envFile)) {
            $this->env = file_get_contents($this->envFile);
        }
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->dir);
        $templatePath = static::getContainer()->getParameter('kernel.project_dir').'/app/template/'.$this->code;
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
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_store_template'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * テンプレートの変更
     */
    #[Group(name: 'cache-clear')]
    public function testChangeTemplate()
    {
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);
        $this->assertInstanceOf(Template::class, $Template);

        // テンプレートを選択
        $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_store_template'), [
            'form' => [
                '_token' => 'dummy',
                'selected' => $Template->getId(),
            ],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirection());

        // .envが更新されている
        $this->assertMatchesRegularExpression('/ECCUBE_TEMPLATE_CODE='.$Template->getCode().'/', file_get_contents($this->envFile));
    }

    /**
     * テンプレートの変更（ECCUBE_TEMPLATE_CODEがプロセス環境変数として設定されている場合）
     *
     * Docker などでプロセス環境変数として ECCUBE_TEMPLATE_CODE が設定されている場合、
     * .env への書き込みは反映されないため、保存を拒否しエラーを表示することを確認する。
     */
    #[Group(name: 'cache-clear')]
    public function testChangeTemplateWithEnvOverride()
    {
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);

        // プロセス環境変数として ECCUBE_TEMPLATE_CODE を設定
        $key = 'ECCUBE_TEMPLATE_CODE';
        $original = getenv($key);
        putenv($key.'=default');

        try {
            $session = $this->createSession($this->client);

            // テンプレートを選択
            $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_store_template'), [
                'form' => [
                    '_token' => 'dummy',
                    'selected' => $Template->getId(),
                ],
            ]);
            $this->assertTrue($this->client->getResponse()->isRedirection());

            // 環境変数オーバーライド時は保存が拒否され、エラーが表示される
            $errors = $session->getFlashBag()->get('eccube.admin.error');
            $this->assertContains('admin.common.save_error', $errors);

            // .env は書き換えられていない
            $this->assertDoesNotMatchRegularExpression('/ECCUBE_TEMPLATE_CODE='.$Template->getCode().'/', file_get_contents($this->envFile));
        } finally {
            // 実行環境が事前に設定していた値を復元する
            false === $original ? putenv($key) : putenv($key.'='.$original);
        }
    }

    /**
     * アップロード画面表示
     */
    public function testDiaplayUpload()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_store_template_install'));
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
        // テンプレートをアップロード
        $this->scenarioUpload();
        $this->verifyUpload();

        $Template = $this->templateRepository->findOneBy(['code' => $this->code]);
        $this->assertInstanceOf(Template::class, $Template);

        // NOTE: BrowserKit の HttpKernelBrowser::doRequest() は handle() の直後に
        // terminate() を呼び, その後 filterResponse() で sendContent() する.
        // TemplateController::download() は kernel.terminate で一時ファイルを削除するため,
        // $this->client->request() 経由だと BinaryFileResponse の読み出し前に
        // ファイルが消えてしまう (実際のリクエストでは送信後に terminate されるため問題ない).
        // そのため, ここではカーネルを直接 handle して検証し, 最後に terminate する.
        $kernel = $this->client->getKernel();
        $cookies = [];
        foreach ($this->client->getCookieJar()->all() as $cookie) {
            $cookies[$cookie->getName()] = $cookie->getValue();
        }
        $request = Request::create(
            $this->generateUrl('admin_store_template_download', ['id' => $Template->getId()]),
            Request::METHOD_GET,
            [],
            $cookies
        );
        $response = $kernel->handle($request);

        try {
            $this->assertInstanceOf(BinaryFileResponse::class, $response);
            $this->assertTrue($response->isSuccessful());
            $this->assertSame(
                'attachment; filename='.$this->code.'.tar.gz',
                $response->headers->get('content-disposition')
            );

            // terminate 前は一時ファイルが存在し, tar.gz として読み出せる.
            $tarGzFile = $response->getFile()->getPathname();
            $this->assertFileExists($tarGzFile);
            // tar.gz として読み出せ, app ディレクトリを含むこと.
            // アップロードした zip は app/html とも空ディレクトリで,
            // PharData::buildFromDirectory は空ディレクトリを含めないため,
            // app は download() 側の addEmptyDir で補われる.
            // @see https://github.com/EC-CUBE/ec-cube/issues/742
            $phar = new \PharData($tarGzFile);
            $this->assertArrayHasKey('app', $phar);
        } finally {
            // kernel.terminate のリスナが一時ファイルを削除する.
            $kernel->terminate($request, $response);
        }

        $this->assertFileDoesNotExist($tarGzFile);
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
        $this->assertInstanceOf(Template::class, $Template);

        $id = $Template->getId();
        $this->assertInstanceOf(Template::class, $Template);
        $code = $Template->getCode();

        // 削除
        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_store_template_delete', ['id' => $Template->getId()]));

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Template = $this->templateRepository->find($id);
        $this->assertNotInstanceOf(Template::class, $Template);
        $this->assertFileDoesNotExist(static::getContainer()->getParameter('kernel.project_dir').'/app/template/'.$code);
    }

    protected function scenarioUpload($uppercase = false)
    {
        $formData = $this->createFormData();
        $fileData = $this->createFileData($uppercase);

        return $this->client->request(
            Request::METHOD_POST,
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
        $this->assertInstanceOf(Template::class, $Template);
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
