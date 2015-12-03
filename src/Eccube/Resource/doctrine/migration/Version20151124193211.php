<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\AuthorityRole;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151124193211 extends AbstractMigration
{

    const NAME = 'dtb_authority_role';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        // AuthorityRoleを追加
        $app = new \Eccube\Application();
        $app->initialize();
        $app->boot();
        $em = $app['orm.em'];

        $Member = $app['eccube.repository.member']->find(2);
        $Authority = $app['eccube.repository.master.authority']->find(1);

        $AuthorityRole = new AuthorityRole();
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/setting/system');
        $AuthorityRole->setCreator($Member);
        $em->persist($AuthorityRole);

        $AuthorityRole = new AuthorityRole();
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/store');
        $AuthorityRole->setCreator($Member);
        $em->persist($AuthorityRole);

        $AuthorityRole = new AuthorityRole();
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/content/file_manager');
        $AuthorityRole->setCreator($Member);
        $em->persist($AuthorityRole);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
