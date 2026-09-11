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

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

#[Group('cache-clear')]
final class CacheControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminContentCache()
    {
        $client = $this->client;
        $client->request(Request::METHOD_GET,
            $this->generateUrl('admin_content_cache')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * キャッシュ管理画面から, kernel.cache_dir と実行時 cache pool の双方が削除されること.
     *
     * 検証対象のファイルは必ず作ってから実行する. 存在しないパスへ置くと
     * assertFileDoesNotExist が素通りし, 何も検証しないテストになる.
     *
     * kernel.build_dir は検証しない. CacheClearCommand は「コンテナが本リクエスト中に
     * 生成された」場合 (REQUEST_TIME <= filemtime(containerFile)) はビルドディレクトリを
     * 差し替えないため, 直前の状態によって結果が変わる.
     */
    public function testRoutingAdminContentCachePost()
    {
        $client = $this->client;

        $url = $this->generateUrl('admin_content_cache');

        $fs = new Filesystem();
        $cacheDir = rtrim((string) static::getContainer()->getParameter('kernel.cache_dir'), '/');
        // cache pool は var/cache 配下ではなく実行時ディレクトリにあるため, cache:clear の
        // ディレクトリ削除では消えない (RuntimeCachePoolClearer が削除する)
        $poolDir = rtrim((string) static::getContainer()->getParameter('eccube_runtime_dir'), '/').'/pools';

        $fs->dumpFile($cacheDir.'/sample', 'test');
        $fs->dumpFile($poolDir.'/sample', 'test');

        $client->request(Request::METHOD_POST, $url, [
            'form' => [
                '_token' => 'dummy',
            ],
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileDoesNotExist($cacheDir.'/sample', 'kernel.cache_dir は削除済');
        $this->assertFileDoesNotExist($poolDir.'/sample', '実行時 cache pool は削除済');
    }
}
