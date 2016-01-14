<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * セッションテーブルの作成.
 */
class Version20150821134922 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // https://github.com/EC-CUBE/ec-cube/pull/1392
        // NativeSessionHandler の変更に伴いユニットテストがエラーになるためコメントアウト
        //
        // $tableName = 'dtb_session';

        // if ($schema->hasTable($tableName)) {
        //     return;
        // }

        // $pdoSessionHandler = new PdoSessionHandler(
        //     $this->connection->getWrappedConnection(),
        //     array(
        //         'db_table' => $tableName,
        //     )
        // );
        // $pdoSessionHandler->createTable();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
