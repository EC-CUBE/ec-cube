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
use Symfony\Component\Filesystem\Filesystem;

class JsControllerTest extends AbstractAdminWebTestCase
{
    const JS_FILE = 'customize.js';

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $contents;

    public function setUp()
    {
        parent::setUp();
        $this->dir = self::$container->getParameter('eccube_html_dir').'/user_data/assets/js/';
        $this->contents = file_get_contents($this->dir.self::JS_FILE);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::JS_FILE, '');
    }

    public function tearDown()
    {
        chmod($this->dir, 0755);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::JS_FILE, $this->contents);
        parent::tearDown();
    }

    public function testRoutingAdminContentJsIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_js'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentJsEdit()
    {
        $js = <<<__JS_CONTENTS__
$(function() {
    console.log("test");
});
__JS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_js'),
            ['form' => [
                 'js' => $js,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[js]'] = $js;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_js')));
        $contents = file_get_contents($this->dir.self::JS_FILE);
        $this->assertEquals($js, $contents);
    }

    public function testRoutingAdminContentJsEditFailure()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Nothing support for Windows');
        }
        chmod($this->dir, 0400);

        $js = <<<__JS_CONTENTS__
$(function() {
    console.log("test");
});
__JS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_js'),
            ['form' => [
                 'js' => $js,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[js]'] = $js;
        $this->client->submit($form);
        $this->assertFalse($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_js')));
    }

    public function testRoutingAdminContentJsDeleted()
    {
        if (file_exists($this->dir.self::JS_FILE)) {
            unlink($this->dir.self::JS_FILE);
        }

        $js = <<<__JS_CONTENTS__
$(function() {
    console.log("test");
});
__JS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_js'),
            ['form' => [
                 'js' => $js,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[js]'] = $js;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_js')));
        $contents = file_get_contents($this->dir.self::JS_FILE);
        $this->assertEquals($js, $contents);
    }
}
