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

namespace Eccube\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Entity\Product;

/**
 * @extends AbstractRepository<Faq>
 */
class FaqRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * FAQ を登録/保存します.
     *
     * 表示順が未採番（null）のときは、同じ区分・紐付け先の中での最大値 + 1 を割り当てる（1 始まり）。
     */
    #[\Override]
    public function save(AbstractEntity $entity): void
    {
        if ($entity instanceof Faq && $entity->getSortNo() === null) {
            $entity->setSortNo($this->getMaxSortNo($entity) + 1);
        }

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * 対象FAQと同じ区分・紐付け先における表示順の最大値を返す（未登録なら 0）.
     *
     * sort_no はフロント取得（getCommonFaq / getProductFaq / getCategoryFaq）と同じく
     * 区分・紐付け先ごとに独立した並び順なので、採番の母集団もそのスコープに合わせる。
     */
    private function getMaxSortNo(Faq $Faq): int
    {
        $qb = $this->createQueryBuilder('f')
            ->select('COALESCE(MAX(f.sort_no), 0)');

        match ($Faq->getFaqType()) {
            Faq::FAQ_TYPE_PRODUCT => $qb->where('f.Product = :Product')
                ->setParameter('Product', $Faq->getProduct()),
            Faq::FAQ_TYPE_CATEGORY => $qb->where('f.Category = :Category')
                ->setParameter('Category', $Faq->getCategory()),
            default => $qb->where('f.Product IS NULL')
                ->andWhere('f.Category IS NULL'),
        };

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * FAQ を削除します.
     */
    #[\Override]
    public function delete(AbstractEntity $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    /**
     * 3区分を横断したFAQ一覧の QueryBuilder を返す（管理画面用）.
     *
     * 商品ごと・カテゴリごとのFAQは各編集画面を開かないと存在が分からないため、
     * 管理画面の一覧は既定で全区分を対象にする。$faqType を渡すと区分で絞り込む。
     *
     * 紐付け先（商品名・カテゴリ名）を一覧に表示するため Product / Category を
     * fetch join し、行ごとの追加クエリ（N+1）を避けている。
     *
     * 並びは「サイト共通 → カテゴリごと → 商品ごと」の順に、紐付け先ごとにまとまる。
     * NULL の並び順は DB 実装によって異なるため、COALESCE で 0 に寄せて揃えている。
     *
     * @param string|null $faqType Faq::FAQ_TYPE_* のいずれか。null なら全区分
     */
    public function getQueryBuilderAll(?string $faqType = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.Product', 'p')
            ->leftJoin('f.Category', 'c')
            ->addSelect('p')
            ->addSelect('c')
            ->addSelect('COALESCE(p.id, 0) AS HIDDEN product_order')
            ->addSelect('COALESCE(c.id, 0) AS HIDDEN category_order')
            ->orderBy('product_order', 'ASC')
            ->addOrderBy('category_order', 'ASC')
            ->addOrderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC');

        match ($faqType) {
            Faq::FAQ_TYPE_COMMON => $qb->where('f.Product IS NULL')
                ->andWhere('f.Category IS NULL'),
            Faq::FAQ_TYPE_PRODUCT => $qb->where('f.Product IS NOT NULL'),
            Faq::FAQ_TYPE_CATEGORY => $qb->where('f.Category IS NOT NULL'),
            default => $qb,
        };

        return $qb;
    }

    /**
     * 指定したカテゴリのFAQ件数をまとめて返す（管理画面のカテゴリツリー用）.
     *
     * カテゴリ行ごとに件数を数えると N+1 になるため、1クエリで取得する。
     *
     * @param int[] $categoryIds
     *
     * @return array<int, int> カテゴリID => FAQ件数（0件のカテゴリはキーを持たない）
     */
    public function countByCategoryIds(array $categoryIds): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('f')
            ->select('c.id AS category_id')
            ->addSelect('COUNT(f.id) AS faq_count')
            ->innerJoin('f.Category', 'c')
            ->where('c.id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds)
            ->groupBy('c.id')
            ->getQuery()
            ->getScalarResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row['category_id']] = (int) $row['faq_count'];
        }

        return $counts;
    }

    /**
     * フロント表示用のサイト共通FAQ（表示のみ・並び順）を返す.
     *
     * サイト共通FAQは全ページに配置され得るため、$limit で取得件数の上限を設ける。
     *
     * @return Faq[]
     */
    public function getCommonFaq(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.Product IS NULL')
            ->andWhere('f.Category IS NULL')
            ->andWhere('f.visible = true')
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * フロント表示用の商品ごとFAQ（表示のみ・並び順）を返す.
     *
     * @return Faq[]
     */
    public function getProductFaq(Product $Product, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.Product = :Product')
            ->andWhere('f.visible = true')
            ->setParameter('Product', $Product)
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * フロント表示用のカテゴリごとFAQ（表示のみ・並び順）を返す.
     *
     * 祖先カテゴリに登録されたFAQも継承して表示する。上位カテゴリに共通のFAQをまとめて置く
     * 運用に対応するため、対象は Category::getPath() が返す「ルート〜自身」の集合。
     *
     * 並び順は「自カテゴリを先頭・祖先は近い順に後ろ」（カテゴリ階層 hierarchy の降順）とし、
     * 同一カテゴリ内は表示順・IDの昇順。$limit はこの並びの統合後に適用するため、
     * 件数超過で切り捨てられるのは常に遠い祖先側になる。
     *
     * @return Faq[]
     */
    public function getCategoryFaq(Category $Category, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->innerJoin('f.Category', 'c')
            ->where('f.Category IN (:Categories)')
            ->andWhere('f.visible = true')
            ->setParameter('Categories', $Category->getPath())
            ->orderBy('c.hierarchy', 'DESC')
            ->addOrderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
