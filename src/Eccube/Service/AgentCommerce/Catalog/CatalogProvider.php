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

namespace Eccube\Service\AgentCommerce\Catalog;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;

/**
 * CatalogProviderInterface の標準実装.
 *
 * 公開中の Product を id 昇順で逐次供給する。toIterable() で 1 件ずつ取り出し、
 * EntityManager の identity map に蓄積され続けないよう一定間隔で clear() する
 * (大規模カタログでのメモリ枯渇を防ぐ)。
 */
class CatalogProvider implements CatalogProviderInterface
{
    /**
     * identity map を clear() するまでに処理する件数の閾値.
     */
    private const CLEAR_INTERVAL = 200;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return iterable<Product>
     */
    public function provideDisplayProducts(): iterable
    {
        $qb = $this->productRepository->createQueryBuilder('p')
            ->where('p.Status = :status')
            ->setParameter('status', ProductStatus::DISPLAY_SHOW)
            ->orderBy('p.id', 'ASC');

        $count = 0;
        foreach ($qb->getQuery()->toIterable() as $product) {
            yield $product;

            if (++$count % self::CLEAR_INTERVAL === 0) {
                $this->entityManager->clear();
            }
        }
    }
}
