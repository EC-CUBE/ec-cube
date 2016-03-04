<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160303223925 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $app = \Eccube\Application::getInstance();
        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(array(
            'url' => 'contact',
            'name' => 'お問い合わせ(入力ページ)',
        ));
        if ($PageLayout) {
            $PageLayout->setName('お問い合わせ(確認ページ)');
            $app['orm.em']->flush($PageLayout);
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
