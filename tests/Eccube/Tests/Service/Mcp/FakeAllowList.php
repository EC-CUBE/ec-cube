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

namespace Eccube\Tests\Service\Mcp;

/**
 * テスト用の AllowList ダブル。
 *
 * Api44 の `Plugin\Api44\GraphQL\AllowList` と同じく `$allows` プロパティを持ち、
 * `AllowListResolver` のリフレクション読み取り経路を検証する。 PSR-4 単独ファイルに置くのは
 * 複数のテストクラスから共有して使うため (個別実行でも autoload で見つかるように)。
 *
 * `$allows` は `\ReflectionObject` 経由で読まれるため、 静的解析・Rector が「使われていない」と
 * 判定しないよう `public` にしてある (Api44 本体では private だが、 テストでは可視性は本質でない)。
 */
final readonly class FakeAllowList
{
    /**
     * @param array<mixed, mixed>|\ArrayObject<mixed, mixed> $allows
     */
    public function __construct(
        public array|\ArrayObject $allows,
    ) {
    }
}
