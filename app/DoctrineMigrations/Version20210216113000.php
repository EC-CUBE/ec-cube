<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216113000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE dtb_block SET use_controller = 1 WHERE file_name = 'new_item' and use_controller = 0");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE dtb_block SET use_controller = 0 WHERE file_name = 'new_item' and use_controller = 1");
    }
}
