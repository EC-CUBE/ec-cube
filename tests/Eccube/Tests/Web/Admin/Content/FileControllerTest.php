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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileControllerTest extends AbstractAdminWebTestCase
{
    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_file'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testView()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_content_file_view').'?file='.$this->getJailDir($filepath)
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testViewWithFailure()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_content_file_view').'?file=/../user_data/aaa.html'
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testDownload()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_content_file_download').'?select_file='.$this->getJailDir($filepath)
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testDelete()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $this->client->request(
            Request::METHOD_DELETE,
            $this->generateUrl('admin_content_file_delete').'?select_file='.$this->getJailDir($filepath)
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file', ['tree_select_file' => dirname((string) $this->getJailDir($filepath))])));
        $this->assertFileDoesNotExist($filepath);
    }

    /**
     * `select_file` が空の場合は `admin_content_file` へリダイレクトする.
     *
     * see https://github.com/EC-CUBE/ec-cube/pull/5298
     */
    public function testDeleteWithEmpty()
    {
        $this->client->request(
            Request::METHOD_DELETE,
            $this->generateUrl('admin_content_file_delete').'?select_file='
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file')));
    }

    public function testIndexWithCreate()
    {
        $folder = 'create_folder';
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => $folder,
                    'file' => '',
                ],
                'mode' => 'create',
                'now_dir' => $this->getUserDataDir(),
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertDirectoryExists($this->getUserDataDir().'/'.$folder);
    }

    /**
     * 名前の重複するディレクトリを作る
     */
    public function testIndexWithCreateDuplicateDir()
    {
        $folder = 'create_folder';
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => $folder,
                    'file' => '',
                ],
                'mode' => 'create',
                'now_dir' => $this->getUserDataDir(),
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertDirectoryExists($this->getUserDataDir().'/'.$folder);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => $folder,
                    'file' => '',
                ],
                'mode' => 'create',
                'now_dir' => $this->getUserDataDir(),
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertDirectoryExists($this->getUserDataDir().'/'.$folder);
        $this->assertCount(1, $crawler->filter('p.errormsg'));
    }

    public function testIndexWithUpload()
    {
        $filepath1 = $this->getUserDataDir().'/../aaa.html';
        $contents1 = '<html><body><h1>test1</h1></body></html>';
        file_put_contents($filepath1, $contents1);

        $filepath2 = $this->getUserDataDir().'/../bbb.html';
        $contents2 = '<html><body><h1>test2</h1></body></html>';
        file_put_contents($filepath2, $contents2);

        $file1 = new UploadedFile(
            realpath($filepath1),          // file path
            'aaa.html',         // original name
            'text/html',        // mimeType
            null,               // error
            true                // test mode
        );
        $file2 = new UploadedFile(
            realpath($filepath2),          // file path
            'bbb.html',         // original name
            'text/html',        // mimeType
            null,               // error
            true                // test mode
        );
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => '',
                    'file' => [$file1, $file2],
                ],
                'mode' => 'upload',
                'now_dir' => '/',
            ],
            ['form' => ['file' => [$file1, $file2]]]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileExists($this->getUserDataDir().'/aaa.html');
        $this->assertFileExists($this->getUserDataDir().'/bbb.html');
    }

    public static function dataProviderUploadIgnoreFiles(): \Iterator
    {
        yield ['test.php', 'x-php', 'アップロードできないファイル拡張子です', false];
        yield ['.dotfile', 'text/plain', '.で始まるファイルはアップロードできません。', false];
        yield ['test.jpg', 'image/jpeg', '', true];
        yield ['test.jpeg', 'image/jpeg', '', true];
        yield ['test.png', 'image/png', '', true];
        yield ['test.gif', 'image/gif', '', true];
        yield ['test.webp', 'image/webp', '', true];
        yield ['test.svg', 'image/svg+xml', '', true];
        yield ['test.ico', 'image/ico', '', true];
        yield ['test.html', 'text/html', '', true];
        yield ['test.htm', 'text/htm', '', true];
        yield ['test.js', 'text/javascript', '', true];
        yield ['test.css', 'text/css', '', true];
        yield ['test.txt', 'text/txt', '', true];
        yield ['test.pdf', 'application/pdf', '', true];
        yield ['test.zip', 'application/zip', 'アップロードできないファイル拡張子です', false];
        yield ['test.gz', 'application/gzip', 'アップロードできないファイル拡張子です', false];
        yield ['test.tar', 'application/tar', 'アップロードできないファイル拡張子です', false];
        yield ['test.doc', 'application/msword', 'アップロードできないファイル拡張子です', false];
        yield ['test.xls', 'application/vnd.ms-excel', 'アップロードできないファイル拡張子です', false];
        yield ['test.ppt', 'application/vnd.ms-powerpoint', 'アップロードできないファイル拡張子です', false];
        yield ['test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'アップロードできないファイル拡張子です', false];
        yield ['test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'アップロードできないファイル拡張子です', false];
        yield ['test.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'アップロードできないファイル拡張子です', false];
        yield ['test.woff', 'application/font-woff', 'アップロードできないファイル拡張子です', false];
        yield ['test.woff2', 'application/font-woff2', 'アップロードできないファイル拡張子です', false];
        yield ['test.ttf', 'application/font-ttf', 'アップロードできないファイル拡張子です', false];
        yield ['test.otf', 'application/font-otf', 'アップロードできないファイル拡張子です', false];
        yield ['test.eot', 'application/vnd.ms-fontobject', 'アップロードできないファイル拡張子です', false];
        yield ['test.xml', 'text/xml', 'アップロードできないファイル拡張子です', false];
        yield ['test.csv', 'text/csv', 'アップロードできないファイル拡張子です', false];
        yield ['test.json', 'application/json', 'アップロードできないファイル拡張子です', false];
    }

    /**
     * @param mixed $fileName
     * @param mixed $mimeType
     * @param mixed $errorMessage
     * @param mixed $exists
     */
    #[DataProvider(methodName: 'dataProviderUploadIgnoreFiles')]
    public function testUploadIgnoreFiles($fileName, $mimeType, $errorMessage, $exists)
    {
        $file = $this->getUserDataDir().'/../'.$fileName;
        touch($file);

        $uploadFile = new UploadedFile(
            realpath($file),          // file path
            $file,         // original name
            $mimeType,        // mimeType
            null,               // error
            true                // test mode
        );

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => '',
                    'file' => [$uploadFile],
                ],
                'mode' => 'upload',
                'now_dir' => '/',
            ],
            ['form' => ['file' => [$uploadFile]]]
        );

        $messages = $crawler->filter('p.errormsg')->each(fn (Crawler $node) => $node->text());

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertStringContainsString($errorMessage, implode(',', $messages));
        $this->assertSame($exists, file_exists($this->getUserDataDir().'/'.$fileName));

        if ($exists) {
            unlink($this->getUserDataDir().'/'.$fileName);
        } else {
            unlink($file);
        }
    }

    public function testUploadInvalidFileName()
    {
        $quote = $this->getUserDataDir()."/../'quote'.txt";
        touch($quote);

        $quotefile = new UploadedFile(
            realpath($quote),          // file path
            "'quote'.txt",         // original name
            'text/plain',        // mimeType
            null,               // error
            true                // test mode
        );

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_file'),
            [
                'form' => [
                    '_token' => 'dummy',
                    'create_file' => '',
                    'file' => [$quotefile],
                ],
                'mode' => 'upload',
                'now_dir' => '/',
            ],
            ['form' => ['file' => [$quotefile]]]
        );

        $messages = $crawler->filter('p.errormsg')->each(fn (Crawler $node) => $node->text());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains('使用できない文字が含まれています。', $messages);
        $this->assertFileDoesNotExist($this->getUserDataDir()."/'quote'.txt");

        unlink($quote);
    }

    protected function getUserDataDir()
    {
        return __DIR__.'/../../../../../../html/user_data';
    }

    private function getJailDir($path)
    {
        $realpath = realpath($path);
        $jailPath = str_replace(realpath($this->getUserDataDir()), '', $realpath);

        return $jailPath ?: '/';
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->getUserDataDir().'/aaa.html')) {
            unlink($this->getUserDataDir().'/aaa.html');
        }
        if (file_exists($this->getUserDataDir().'/create_folder')) {
            rmdir($this->getUserDataDir().'/create_folder');
        }
    }
}
