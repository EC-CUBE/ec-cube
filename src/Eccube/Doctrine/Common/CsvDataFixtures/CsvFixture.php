<?php

namespace Eccube\Doctrine\Common\CsvDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @see https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/FixtureInterface.php
 */
class CsvFixture implements FixtureInterface
{
    protected $file;

    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    public function load(ObjectManager $manager)
    {
        // CSV Reader に設定
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

        // ヘッダ行を取得
        $headers = $this->file->current();
        $this->file->next();

        // ファイル名からテーブル名を取得
        $table_name = str_replace('.'.$this->file->getExtension(), '', $this->file->getFilename());
        $sql = $this->getSql($table_name, $headers);
        $Connection = $manager->getConnection();
        $Connection->beginTransaction();
        // TODO エラーハンドリング
        $prepare = $Connection->prepare($sql);
        while (!$this->file->eof()) {
            $rows = $this->file->current();
            $index = 1;
            // データ行をバインド
            foreach ($rows as $col) {
                $prepare->bindValue($index, $col);
                $index++;
            }
            // batch insert
            $result = $prepare->execute();
            $this->file->next();
        }
        $Connection->commit();
    }

    public function getSql($table_name, array $headers)
    {
        return 'INSERT INTO '.$table_name.' ('.implode(', ', $headers).') VALUES ('.implode(', ', array_fill(0, count($headers), '?')).')';
    }
}
