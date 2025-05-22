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

namespace Eccube\Doctrine\Common\CsvDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

/**
 * CSVファイルを扱うためのフィクスチャ.
 *
 * @see https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/FixtureInterface.php
 */
class CsvFixture implements FixtureInterface
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * CsvFixture constructor.
     *
     * @param \SplFileObject|null $file
     */
    public function __construct(\SplFileObject $file = null)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // 日本語windowsの場合はインストール時にエラーとなるので英語のロケールをセット
        // ロケールがミスマッチしてSplFileObject::READ_CSVができないのを回避
        if ('\\' === DIRECTORY_SEPARATOR) {
            setlocale(LC_ALL, 'English_United States.1252');
        }

        // CSV Reader に設定
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        // ヘッダ行を取得
        $headers = $this->file->current();
        $this->file->next();

        // ファイル名からテーブル名を取得
        $table_name = str_replace('.'.$this->file->getExtension(), '', $this->file->getFilename());
        $sql = $this->getSql($table_name, $headers);
        /** @var Connection $Connection */
        $Connection = $manager->getConnection();
        $Connection->beginTransaction();

        // mysqlの場合はNO_AUTO_VALUE_ON_ZEROを設定
        if ('mysql' === $Connection->getDatabasePlatform()->getName()) {
            $Connection->exec("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';");
        }

        // TODO エラーハンドリング
        $prepare = $Connection->prepare($sql);
        while ($rows = $this->file->current()) {
            $index = 1;
            // データ行をバインド
            foreach ($rows as $col) {
                $col = $col === '' ? null : $col;
                $prepare->bindValue($index, $col);
                $index++;
            }
            // batch insert
            $prepare->execute();
            $this->file->next();
            // 大きなサイズのCSVを扱えるようタイムアウトを延長する
            $seconds
                = is_numeric(ini_get('max_execution_time'))
                ? intval(ini_get('max_execution_time'))
                : intval(get_cfg_var('max_execution_time'));
            set_time_limit($seconds);
        }
        $Connection->commit();

        // postgresqlの場合はシーケンスを振り直す
        if ('postgresql' === $Connection->getDatabasePlatform()->getName()) {
            // テーブル情報を取得
            $sm = $Connection->getSchemaManager();
            $table = $sm->listTableDetails($table_name);

            // 主キーがないテーブルはスキップ
            if (!$table->hasPrimaryKey()) {
                return;
            }

            // 複合主キーのテーブルはスキップ
            $pkColumns = $table->getPrimaryKey()->getColumns();
            if (count($pkColumns) != 1) {
                return;
            }

            // シーケンス名を取得
            $pk_name = $pkColumns[0];
            $sequence_name = sprintf('%s_%s_seq', $table_name, $pk_name);

            // シーケンスの存在チェック
            $sql = 'SELECT COUNT(*) FROM information_schema.sequences WHERE sequence_name = ?';
            $count = $Connection->fetchOne($sql, [$sequence_name]);
            if ($count < 1) {
                return;
            }

            // シーケンスを更新
            $sql = sprintf('SELECT MAX(%s) FROM %s', $pk_name, $table_name);
            $max = $Connection->fetchOne($sql);
            if (is_null($max)) {
                // レコードが無い場合は1を初期値に設定
                $sql = sprintf("SELECT SETVAL('%s', 1, false)", $sequence_name);
            } else {
                // レコードがある場合は最大値を設定
                $sql = sprintf("SELECT SETVAL('%s', %s)", $sequence_name, $max);
            }
            $Connection->executeQuery($sql);
        }
    }

    /**
     * INSERT を生成する.
     *
     * @param string $table_name テーブル名
     * @param array $headers カラム名の配列
     *
     * @return string INSERT 文
     */
    public function getSql($table_name, array $headers)
    {
        return 'INSERT INTO '.$table_name.' ('.implode(', ', $headers).') VALUES ('.implode(', ', array_fill(0, count($headers), '?')).')';
    }

    /**
     * 保持している \SplFileObject を返す.
     *
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->file;
    }
}
