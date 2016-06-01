<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\Tag;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160114142234 extends AbstractMigration
{
    private $tag_datas = array(
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
        array(
            'id' => 3,
            'name' => '限定品',
            'rank' => 3,
        ),
    );

    private $csv_datas = array(
        array(
            'entity_name' => 'Eccube\\\\Entity\\\\Product',
            'field_name' => 'ProductTag',
            'reference_field_name' => 'tag_id',
            'disp_name' => 'タグ(ID)',
        ),
        array(
            'entity_name' => 'Eccube\\\\Entity\\\\Product',
            'field_name' => 'ProductTag',
            'reference_field_name' => 'Tag',
            'disp_name' => 'タグ(名称)',
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
        $this->dtb_csv($em);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $app = \Eccube\Application::getInstance();
        $em = $app['orm.em'];

        $this->delete_dtb_csv($em);

        $em->flush();
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

        foreach ($this->tag_datas as $data) {
            $Tag = new Tag();
            $Tag
                ->setId($data['id'])
                ->setName($data['name'])
                ->setRank($data['rank']);

            $em->persist($Tag);
        }
    }

    /**
     * insert dtb_csv
     *
     * @param EntityManager $em
     */
    private function dtb_csv(EntityManager $em)
    {
        $filter = $em->getFilters()->getFilter('soft_delete');
        $filter->setExcludes(array(
            'Eccube\Entity\Member'
        ));
        $CsvType = $em->getRepository('Eccube\Entity\Master\CsvType')->find(CsvType::CSV_TYPE_PRODUCT);
        $Member = $em->getRepository('Eccube\Entity\Member')->find(1);
        $Csv = $em->getRepository('Eccube\Entity\Csv')->findOneBy(
            array('CsvType' => $CsvType),
            array('rank' => 'DESC')
        );
        $rank = $Csv->getRank();

        foreach ($this->csv_datas as $data) {
            $rank++;

            $Csv = new Csv();
            $Csv
                ->setCsvType($CsvType)
                ->setCreator($Member)
                ->setEntityName($data['entity_name'])
                ->setFieldName($data['field_name'])
                ->setReferenceFieldName($data['reference_field_name'])
                ->setDispName($data['disp_name'])
                ->setRank($rank)
                ->setEnableFlg(Constant::ENABLED);

            $em->persist($Csv);
        }
    }

    /**
     * delete dtb_csv
     *
     * @param EntityManager $em
     */
    private function delete_dtb_csv(EntityManager $em)
    {
        $repository = $em->getRepository('Eccube\Entity\Csv');
        foreach ($this->csv_datas as $data) {
            $Csv = $repository->findOneBy(array(
                'entity_name' => $data['entity_name'],
                'field_name' => $data['field_name'],
                'reference_field_name' => $data['reference_field_name'],
            ));

            if ($Csv) {
                $em->remove($Csv);
            }
        }
    }
}
