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
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file', array('tree_select_file' => dirname($this->getJailDir($filepath))))));
        $this->assertFalse(file_exists($filepath));
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
            null,               // file size
            null,               // error
            true                // test mode
        );
        $file2 = new UploadedFile(
            realpath($filepath2),          // file path
            'bbb.html',         // original name
            'text/html',        // mimeType
            null,               // file size
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
            ['file' => [$file1, $file2]]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(file_exists($this->getUserDataDir().'/aaa.html'));
        $this->assertTrue(file_exists($this->getUserDataDir().'/bbb.html'));
    }

    protected function getUserDataDir()
    {
        return $this->container->getParameter('kernel.project_dir').'/html/user_data';
    }

    private function getJailDir($path)
    {
        $realpath = realpath($path);
        $jailPath = str_replace(realpath($this->getUserDataDir()), '', $realpath);

        return $jailPath ? $jailPath : '/';
    }

    public function tearDown()
    {
        if (file_exists($this->getUserDataDir().'/aaa.html')) {
            unlink($this->getUserDataDir().'/aaa.html');
        }
        if (file_exists($this->getUserDataDir().'/create_folder')) {
            rmdir($this->getUserDataDir().'/create_folder');
        }
    }
}
