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

namespace Eccube\Tests\Web;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;

class UserDataControllerTest extends AbstractWebTestCase
{
    protected $userDataDir;

    protected $fileName = 'example_page';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->userDataDir = $this->eccubeConfig->get('eccube_theme_user_data_dir');

        $DeviceType = $this->entityManager
            ->getRepository(DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        if ($DeviceType) {
            $page = new Page();
            $page->setUrl($this->fileName)
                ->setFileName($this->fileName)
                ->setDeviceType($DeviceType)
                ->setEditType(Page::EDIT_TYPE_USER);
            $this->entityManager->persist($page);
            $this->entityManager->flush();
        }
    }

    public function tearDown()
    {
        if (file_exists($this->userDataDir.'/'.$this->fileName.'.twig')) {
            unlink($this->userDataDir.'/'.$this->fileName.'.twig');
        }

        parent::tearDown();
    }

    public function testIndex()
    {
        file_put_contents(
            $this->userDataDir.'/'.$this->fileName.'.twig',
            '<h1>test</h1>'
        );

        $crawler = $this->client->request(
            'GET',
            '/user_data/'.$this->fileName
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testIndexWithNotFound()
    {
        $this->client->request(
            'GET',
            '/user_data/aaa'
        );
        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }
}
