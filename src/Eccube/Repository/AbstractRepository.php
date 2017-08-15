<?php


namespace Eccube\Repository;


use Doctrine\ORM\EntityRepository;
use Eccube\Annotation\Inject;
use Eccube\Common\Constant;
use Eccube\Entity\AbstractEntity;

class AbstractRepository extends EntityRepository
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * エンティティを削除します。
     * 物理削除ではなく、del_flgを利用した論理削除を行います。
     *
     * @param AbstractEntity $entity
     */
    public function delete($entity)
    {
        $entity->setDelFlg(Constant::ENABLED);
        $this->save($entity);
    }

    /**
     * エンティティの登録/保存します。
     *
     * @param $entity|AbstractEntity エンティティ
     */
    public function save($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    protected function getCacheLifetime()
    {
        $options = $this->appConfig['doctrine_cache'];
        return $options['result_cache']['lifetime'];
    }
}
