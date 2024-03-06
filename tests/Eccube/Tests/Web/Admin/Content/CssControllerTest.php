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

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = static::getContainer()->getParameter('eccube_html_dir').'/user_data/assets/css/';
        $this->contents = file_get_contents($this->dir.self::CSS_FILE);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::CSS_FILE, '');
    }

    protected function tearDown(): void
    {
        chmod($this->dir, 0755);
        $fs = new Filesystem();
        $fs->dumpFile($this->dir.self::CSS_FILE, $this->contents);
        parent::tearDown();
    }

    public function testRoutingAdminContentCssIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_css'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentCssEdit()
    {
        $css = <<<__CSS_CONTENTS__
.title {
    font-size: 1px;
}
__CSS_CONTENTS__;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_content_css'),
            ['form' => [
                 'css' => $css,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[css]'] = $css;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
        $contents = file_get_contents($this->dir.self::CSS_FILE);
        $this->assertEquals($css, $contents);
    }

    public function testRoutingAdminContentCssEditFailure()
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
            ['form' => [
                 'css' => $css,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[css]'] = $css;
        $this->client->submit($form);
        $this->assertFalse($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
    }

    public function testRoutingAdminContentCssDeleted()
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
            ['form' => [
                 'css' => $css,
             ],
            ]
        );
        $form = $crawler->selectButton('登録')->form();
        $form['form[css]'] = $css;
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_css')));
        $contents = file_get_contents($this->dir.self::CSS_FILE);
        $this->assertEquals($css, $contents);
    }
}
