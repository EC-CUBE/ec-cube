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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Master\DeviceType;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

final class BlockControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminContentBlockIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_block'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentBlockEdit()
    {
        $this->client->request(Request::METHOD_GET,
            $this->generateUrl(
                'admin_content_block_edit',
                ['id' => 1]
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentBlockEditWithPost()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_block_edit', ['id' => 1]),
            [
                'block' => [
                    'name' => 'newblock',
                    'file_name' => 'file_name',
                    'block_html' => '<p>test</p>',
                    'DeviceType' => DeviceType::DEVICE_TYPE_MB,
                    'id' => 1,
                    '_token' => 'dummy',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_content_block_edit', ['id' => 1])
        ));

        $dir = sprintf('%s/app/template/%s/Block',
            static::getContainer()->getParameter('kernel.project_dir'),
            static::getContainer()->getParameter('eccube.theme'));

        $this->expected = '<p>test</p>';
        $this->actual = file_get_contents($dir.'/file_name.twig');
        $this->verify();

        // Filesystem::dumpFile() がエラーになるので bovigo\vfs が使用できない
        if (file_exists($dir.'/file_name.twig')) {
            unlink($dir.'/file_name.twig');
        }
    }

    public function testRoutingAdminContentBlockDefaultBlockDelete()
    {
        $this->loginTo($this->createMember());

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_content_block_delete', ['id' => 1])
        );

        $redirectUrl = $this->generateUrl('admin_content_block');
        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertTrue($actual);
    }

    /**
     * 必須項目を送らない場合は、入力エラーとして再表示する.
     */
    #[DataProvider(methodName: 'provideMissingField')]
    public function testEditWithMissingField(string $field): void
    {
        $formData = [
            'name' => 'newblock',
            'file_name' => 'file_name',
            'block_html' => '<p>test</p>',
            'DeviceType' => DeviceType::DEVICE_TYPE_MB,
            'id' => 1,
            '_token' => 'dummy',
        ];
        unset($formData[$field]);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_block_edit', ['id' => 1]),
            ['block' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return \Iterator<string, array{string}>
     */
    public static function provideMissingField(): \Iterator
    {
        yield 'name' => ['name'];
        yield 'file_name' => ['file_name'];
    }

    /**
     * 送信された id でブロックの主キーを書き換えない.
     */
    public function testEditWithInvalidId(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_block_edit', ['id' => 1]),
            [
                'block' => [
                    'name' => 'newblock',
                    'file_name' => 'file_name',
                    'block_html' => '<p>test</p>',
                    'DeviceType' => DeviceType::DEVICE_TYPE_MB,
                    'id' => 'abc',
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_content_block_edit', ['id' => 1])
        ));
    }
}
