<?php


namespace Eccube\Repository;


use Doctrine\ORM\EntityRepository;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\AbstractEntity;

class AbstractRepository extends EntityRepository
{

    protected $app;

    /**
     * @param Application $app
     */
    public function setApplication($app)
    {
        $this->app = $app;
    }

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

    /**
     * IDに紐づくエンティティを取得します。
     * Result Cacheが有効になっている場合は、Result Cache機能を利用します。
     *
     * @param $id|long エンティティのID
     * @return AbstractEntity
     */
    public function get($id)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id);

        if (!$this->app['debug']) {
            $qb->setCacheable(true);
        }

        return $qb->getQuery()
            ->useResultCache(true, $this->getCacheLifetime())
            ->getSingleResult();
    }

    /**
     * エンティティの一覧を取得します。
     * Result Cacheが有効になっている場合は、Result Cache機能を利用します。
     *
     * @param $sortOptions|array ソート順
     * @return AbstractEntity[]
     */
    public function getAll($sortOptions = [])
    {
        $qb = $this->createQueryBuilder('l');
        foreach ($sortOptions as $sort=>$order) {
            $qb->addOrderBy('l.'.$sort, $order);
        }

        if (!$this->app['debug']) {
            $qb->setCacheable(true);
        }

        return $qb->getQuery()
            ->useResultCache(true, $this->getCacheLifetime())
            ->getResult();
    }

    private function getCacheLifetime()
    {
        $options = $this->app['config']['doctrine_cache'];
        return $options['result_cache']['lifetime'];
    }
}