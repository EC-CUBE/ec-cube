<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160216215635 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $app = \Eccube\Application::getInstance();

        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(array(
            'url' => 'forgot_reset',
            'name' => 'パスワード変更((完了ページ)',
        ));
        if ($PageLayout) {
            $PageLayout->setName('パスワード変更(完了ページ)');
            $app['orm.em']->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
