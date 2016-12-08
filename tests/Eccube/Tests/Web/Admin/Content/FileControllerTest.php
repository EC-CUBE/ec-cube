<?php

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileControllerTest extends AbstractAdminWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['template_default_realdir'] = sys_get_temp_dir().'/FileController'.sha1(mt_rand());; // vfs が使えないため
        if (!file_exists($config['template_default_realdir'].'/user_data')){
            mkdir($config['template_default_realdir'].'/user_data', 0777 , true);
        }
        $config['user_data_realdir'] = $config['template_default_realdir'].'/user_data';
        $this->app['config'] = $config;
    }

    public static function tearDownAfterClass()
    {
        $dirs = array();
        $finder = new Finder();
        //許可がありませんDIR対応
        $finder->ignoreUnreadableDirs(true);
        $iterator = $finder
            ->in(sys_get_temp_dir())
            ->name('FileController*')
            ->directories();
        foreach ($iterator as $dir) {
            $dirs[] = $dir->getPathName();
        }

        foreach ($dirs as $dir) {
            // プロセスが掴んでいるためか、確実に削除できない場合がある
            try {
                $f = new Filesystem();
                $f->remove($dir);
            } catch (\Exception $e) {
                // queit.
            }
        }
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->app->url('admin_content_file'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testView()
    {
        $filepath = $this->app['config']['user_data_realdir'].'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            'GET',
            $this->app->path('admin_content_file_view').'?file='.$filepath
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testDownload()
    {
        $filepath = $this->app['config']['user_data_realdir'].'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $crawler = $this->client->request(
            'GET',
            $this->app->path('admin_content_file_download').'?select_file='.$filepath
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testDelete()
    {
        $filepath = $this->app['config']['user_data_realdir'].'/aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $this->client->request(
            'DELETE',
            $this->app->path('admin_content_file_delete').'?select_file='.$filepath
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_content_file')));
        $this->assertFalse(file_exists($filepath));
    }

    public function testIndexWithCreate()
    {
        $folder = 'create_folder';
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_content_file'),
            array(
                'form' => array(
                    '_token' => 'dummy',
                    'create_file' => $folder,
                    'file' => ''
                ),
                'mode' => 'create'
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(is_dir($this->app['config']['user_data_realdir'].'/'.$folder));
    }

    public function testIndexWithUpload()
    {
        $filepath = $this->app['config']['user_data_realdir'].'/../aaa.html';
        $contents = '<html><body><h1>test</h1></body></html>';
        file_put_contents($filepath, $contents);

        $file = new UploadedFile(
            $filepath,          // file path
            'aaa.html',         // original name
            'text/html',        // mimeType
            null,               // file size
            null,               // error
            true                // test mode
        );
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_content_file'),
            array(
                'form' => array(
                    '_token' => 'dummy',
                    'create_file' => '',
                    'file' => $file
                ),
                'mode' => 'upload',
                'now_dir' => $this->app['config']['user_data_realdir']
            ),
            array('file' => $file)
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(file_exists($this->app['config']['user_data_realdir'].'/aaa.html'));
    }
}
