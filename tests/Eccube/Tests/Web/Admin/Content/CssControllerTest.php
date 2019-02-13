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
use Symfony\Component\Filesystem\Filesystem;

class CssControllerTest extends AbstractAdminWebTestCase
{
    const CSS_FILE = 'customize.css';

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
        $this->dir = $this->container->getParameter('eccube_html_dir').'/user_data/assets/css/';
        $this->contents = file_get_contents($this->dir.self::CSS_FILE);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::CSS_FILE, '');
    }

    public function tearDown()
    {
        chmod($this->dir, 0755);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::CSS_FILE, $this->contents);
        parent::tearDown();
    }
    public function test_routing_AdminContentCss_index()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_css'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentCss_edit()
    {
        $css = <<<__CSS_CONTENTS__
.title {
    font-size: 1px;
}
__CSS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_css'),
            ['form' =>
             [
                 'css' => $css
             ]
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form["form[css]"] = $css;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
        $contents = file_get_contents($this->dir.self::CSS_FILE);
        $this->assertEquals($css, $contents);
    }

    public function test_routing_AdminContentCss_edit_failure()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Nothing support for Windows');
        }
        chmod($this->dir, 0400);

        $css = <<<__CSS_CONTENTS__
.title {
    font-size: 1px;
}
__CSS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_css'),
            ['form' =>
             [
                 'css' => $css
             ]
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form["form[css]"] = $css;
        $this->client->submit($form);
        $this->assertFalse($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
    }


    public function test_routing_AdminContentCss_deleted()
    {
        if (file_exists($this->dir.self::CSS_FILE)) {
            unlink($this->dir.self::CSS_FILE);
        }

        $css = <<<__CSS_CONTENTS__
.title {
    font-size: 1px;
}
__CSS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_css'),
            ['form' =>
             [
                 'css' => $css
             ]
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form["form[css]"] = $css;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
        $contents = file_get_contents($this->dir.self::CSS_FILE);
        $this->assertEquals($css, $contents);
    }
}
