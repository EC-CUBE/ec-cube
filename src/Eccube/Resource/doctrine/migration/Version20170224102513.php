<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170224102513 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $Table = $schema->getTable('dtb_payment');
        if ($Table->hasColumn('method_class')) {
            $this->addSql(
                "UPDATE dtb_payment SET method_class = :method",
                ['method' => '\Eccube\Service\Payment\Method\Cash']
            );
        }

        if ($Table->hasColumn('service_class')) {
            $this->addSql(
                "UPDATE dtb_payment SET method_class = :method",
                ['method' => '\Eccube\Service\PaymentService']
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
