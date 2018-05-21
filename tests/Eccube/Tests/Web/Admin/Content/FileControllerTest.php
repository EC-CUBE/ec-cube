<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
            $this->generateUrl('admin_content_file_view').'?file='.$filepath
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
            $this->generateUrl('admin_content_file_download').'?select_file='.$filepath
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
            $this->generateUrl('admin_content_file_delete').'?select_file='.$filepath
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_file')));
        $this->assertFalse(file_exists($filepath));
    }

    public function testIndexWithCreate()
    {
        $folder = 'create_folder';
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
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(is_dir($this->getUserDataDir().'/'.$folder));
    }

    public function testIndexWithUpload()
    {
        $filepath = $this->getUserDataDir().'/../aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $file = new UploadedFile(
            realpath($filepath),          // file path
            'aaa.html',         // original name
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
                    'file' => $file,
                ],
                'mode' => 'upload',
                'now_dir' => '/',
            ],
            ['file' => $file]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(file_exists($this->getUserDataDir().'/aaa.html'));
    }

    protected function getUserDataDir()
    {
        return $this->container->getParameter('kernel.project_dir').'/html/user_data';
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
