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

namespace Eccube\Tests\Web\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Tests\Web\AbstractWebTestCase;

abstract class AbstractAdminWebTestCase extends AbstractWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->logIn();
    }

    /**
     * @deprecated \Eccube\Tests\Web\AbstractWebTestCase::loginTo() を使用してください.
     *
     * @param mixed|null $user
     */
    #[\Override]
    public function logIn(mixed $user = null)
    {
        if (!is_object($user)) {
            $user = $this->createMember();
        }

        $this->loginTo($user);

        return $user;
    }

    /**
     * CSV のレコード数を返す（ヘッダ行を除く）.
     */
    protected function countCsvRows(string $csv): int
    {
        return count($this->parseCsv($csv)) - 1;
    }

    /**
     * CSV をレコード単位にパースする（ヘッダ行を含む）.
     *
     * 項目の値に改行が含まれるため, 行数は改行では数えられない.
     * 出力は eccube_csv_export_encoding のエンコーディングなので UTF-8 に戻してから読む
     * (SJIS は 2 バイト目に 0x5C を含む文字があり, escape と誤認して行が結合される).
     * escape は PHP 8.4 以降の既定値に合わせて '' を明示する
     * (省略すると deprecation。'\\' はデータ中のバックスラッシュで行が結合される).
     *
     * @return array<int, array<int, string|null>>
     */
    protected function parseCsv(string $csv): array
    {
        $eccubeConfig = static::getContainer()->get(EccubeConfig::class);
        $csv = (string) mb_convert_encoding($csv, 'UTF-8', $eccubeConfig->get('eccube_csv_export_encoding'));

        $fp = fopen('php://memory', 'r+');
        $this->assertNotFalse($fp);
        fwrite($fp, $csv);
        rewind($fp);

        $records = [];
        while (($row = fgetcsv($fp, null, $eccubeConfig->get('eccube_csv_export_separator'), '"', '')) !== false) {
            if ($row !== [null]) {
                $records[] = $row;
            }
        }
        fclose($fp);

        return $records;
    }
}
