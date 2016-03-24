<?php
namespace DoctrineMigrations;

use Eccube\Application;
use Eccube\Entity\Master\Tag;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160114142234 extends AbstractMigration
{
    private $datas = array(
        array(
            'id' => 1,
            'name' => '新商品',
            'rank' => 1,
        ),
        array(
            'id' => 2,
            'name' => 'おすすめ商品',
            'rank' => 2,
        ),
    );

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $app = \Eccube\Application::getInstance();
        $em = $app['orm.em'];

        $this->mtb_tag($em);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    /**
     * insert mtb_tag
     *
     * @param EntityManager $em
     */
    private function mtb_tag(EntityManager $em)
    {
        $Tags = $em->getRepository('\Eccube\Entity\Master\Tag')->findAll();
        if (0 < count($Tags)) {
            // すでに使用している場合は登録しない。
            return;
        }

        foreach ($this->datas as $data) {
            $Tag = new Tag();
            $Tag
                ->setId($data['id'])
                ->setName($data['name'])
                ->setRank($data['rank']);

            $em->persist($Tag);
        }
    }
}
