<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\EventListener\AgentCommerce;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;

/**
 * Product / ProductClass の変更で UCP Catalog キャッシュを無効化する Doctrine リスナ.
 *
 * postUpdate / postPersist / postRemove で対象エンティティの変更を検知し、postFlush で
 * 1 度だけ UcpCatalogCache::clear() を呼ぶ (バッチ更新でのクリア多重呼び出しを避ける)。
 *
 * ACP Feed (push) 側の「次回 push 対象マーク」は本実装の対象外 (stock_updated_at カラム追加は
 * しない)。後続で差分検出マークを導入する。
 */
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
class CatalogCacheInvalidationListener
{
    /**
     * 当該フラッシュでカタログ関連の変更が発生したか.
     */
    private bool $dirty = false;

    public function __construct(
        private readonly UcpCatalogCache $cache,
    ) {
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->markIfCatalogEntity($args);
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->markIfCatalogEntity($args);
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->markIfCatalogEntity($args);
    }

    /**
     * フラッシュ完了後、カタログ関連の変更があればキャッシュを 1 度だけクリアする.
     *
     * TODO: ACP Feed の「次回 push 対象マーク」をここで記録する (差分 upsert 用)。
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!$this->dirty) {
            return;
        }

        $this->dirty = false;
        $this->cache->clear();
    }

    /**
     * 対象が Product / ProductClass なら dirty フラグを立てる.
     *
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    private function markIfCatalogEntity(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Product || $entity instanceof ProductClass) {
            $this->dirty = true;
        }
    }
}
