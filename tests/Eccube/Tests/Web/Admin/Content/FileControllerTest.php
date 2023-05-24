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
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileControllerTest extends AbstractAdminWebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_file'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testView()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            'GET',
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

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_content_file_view').'?file=/../user_data/aaa.html'
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDownload()
    {
        $filepath = $this->getUserDataDir().'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            'GET',
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
            'DELETE',
            $this->generateUrl('admin_content_file_delete').'?select_file='.$this->getJailDir($filepath)
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file', ['tree_select_file' => dirname($this->getJailDir($filepath))])));
        $this->assertFalse(file_exists($filepath));
    }

    /**
     * `select_file` が空の場合は `admin_content_file` へリダイレクトする.
     *
     * see https://github.com/EC-CUBE/ec-cube/pull/5298
     */
    public function testDeleteWithEmpty()
    {
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_content_file_delete').'?select_file='
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file')));
    }

    public function testIndexWithCreate()
    {
        $folder = 'create_folder';
        $this->client->request(
            'POST',
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
        $this->assertTrue(is_dir($this->getUserDataDir().'/'.$folder));
    }

    /**
     * 名前の重複するディレクトリを作る
     */
    public function testIndexWithCreateDuplicateDir()
    {
        $folder = 'create_folder';
        $this->client->request(
            'POST',
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
        $this->assertTrue(is_dir($this->getUserDataDir().'/'.$folder));
        $crawler = $this->client->request(
            'POST',
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
        $this->assertTrue(is_dir($this->getUserDataDir().'/'.$folder));
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
        $crawler = $this->client->request(
            'POST',
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
        $this->assertTrue(file_exists($this->getUserDataDir().'/aaa.html'));
        $this->assertTrue(file_exists($this->getUserDataDir().'/bbb.html'));
    }

    public function dataProviderUploadIgnoreFiles(): array
    {
        return [
            ['test.php', 'x-php', 'アップロードできないファイル拡張子です', false],
            ['.dotfile', 'text/plain', '.で始まるファイルはアップロードできません。', false],
            ['test.jpg', 'image/jpeg', '', true],
            ['test.jpeg', 'image/jpeg', '', true],
            ['test.png', 'image/png', '', true],
            ['test.gif', 'image/gif', '', true],
            ['test.webp', 'image/webp', '', true],
            ['test.svg', 'image/svg+xml', '', true],
            ['test.ico', 'image/ico', '', true],
            ['test.html', 'text/html', '', true],
            ['test.htm', 'text/htm', '', true],
            ['test.js', 'text/javascript', '', true],
            ['test.css', 'text/css', '', true],
            ['test.txt', 'text/txt', '', true],
            ['test.pdf', 'application/pdf', '', true],
            ['test.zip', 'application/zip', 'アップロードできないファイル拡張子です', false],
            ['test.gz', 'application/gzip', 'アップロードできないファイル拡張子です', false],
            ['test.tar', 'application/tar', 'アップロードできないファイル拡張子です', false],
            ['test.doc', 'application/msword', 'アップロードできないファイル拡張子です', false],
            ['test.xls', 'application/vnd.ms-excel', 'アップロードできないファイル拡張子です', false],
            ['test.ppt', 'application/vnd.ms-powerpoint', 'アップロードできないファイル拡張子です', false],
            ['test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'アップロードできないファイル拡張子です', false],
            ['test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'アップロードできないファイル拡張子です', false],
            ['test.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'アップロードできないファイル拡張子です', false],
            ['test.woff', 'application/font-woff', 'アップロードできないファイル拡張子です', false],
            ['test.woff2', 'application/font-woff2', 'アップロードできないファイル拡張子です', false],
            ['test.ttf', 'application/font-ttf', 'アップロードできないファイル拡張子です', false],
            ['test.otf', 'application/font-otf', 'アップロードできないファイル拡張子です', false],
            ['test.eot', 'application/vnd.ms-fontobject', 'アップロードできないファイル拡張子です', false],
            ['test.xml', 'text/xml', 'アップロードできないファイル拡張子です', false],
            ['test.csv', 'text/csv', 'アップロードできないファイル拡張子です', false],
            ['test.json', 'application/json', 'アップロードできないファイル拡張子です', false],
        ];
    }
    /**
     * @dataProvider dataProviderUploadIgnoreFiles
     */
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
            'POST',
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

        $messages = $crawler->filter('p.errormsg')->each(function (Crawler $node) {
            return $node->text();
        });

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
            'POST',
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

        $messages = $crawler->filter('p.errormsg')->each(function (Crawler $node) {
            return $node->text();
        });

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains('使用できない文字が含まれています。', $messages);
        $this->assertFalse(file_exists($this->getUserDataDir()."/'quote'.txt"));

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

        return $jailPath ? $jailPath : '/';
    }

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
