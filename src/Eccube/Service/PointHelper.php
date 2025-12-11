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

namespace Eccube\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;

class PointHelper
{
    /**
     * PointHelper constructor.
     */
    public function __construct(protected BaseInfoRepository $baseInfoRepository, protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * ポイント設定が有効かどうか.
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isPointEnabled(): bool
    {
        $BaseInfo = $this->baseInfoRepository->get();

        return $BaseInfo->isOptionPoint();
    }

    /**
     * ポイントを金額に変換する.
     *
     * @param string $point ポイント
     *
     * @return string 金額
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function pointToPrice(string $point): string
    {
        $BaseInfo = $this->baseInfoRepository->get();

        return bcmul($point, (string) $BaseInfo->getPointConversionRate(), 0);
    }

    /**
     * ポイントを値引き額に変換する. マイナス値を返す.
     *
     * @param string $point ポイント
     *
     * @return string 金額
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function pointToDiscount(string $point): string
    {
        return bcmul($this->pointToPrice($point), '-1', 0);
    }

    /**
     * 金額をポイントに変換する.
     *
     * @return string ポイント
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function priceToPoint(string $price): string
    {
        $BaseInfo = $this->baseInfoRepository->get();

        return bcfloor(bcdiv($price, (string) $BaseInfo->getPointConversionRate(), 4));
    }

    /**
     * 明細追加処理.
     *
     * @throws \Exception
     */
    public function addPointDiscountItem(ItemHolderInterface $itemHolder, string $discount): void
    {
        // 注文明細以外は処理しない.
        if ($itemHolder instanceof Order === false) {
            return;
        }

        $DiscountType = $this->entityManager->find(OrderItemType::class, OrderItemType::POINT);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);

        // 商品明細に保持しているポイント付与率を取得して設定する.
        // 商品明細が取得できない場合は店舗基本情報のポイント付与率を設定する.
        $Baseinfo = $this->baseInfoRepository->get();
        $pointRate = $Baseinfo->getBasicPointRate();
        // 商品別ポイントは未実装なので, 商品明細のポイント付与率はすべて同じ値が設定されているはず
        $ProductOrderItem = $itemHolder->getItems()->getProductClasses()->current();
        if ($ProductOrderItem instanceof OrderItem && $ProductOrderItem->getPointRate() !== null) {
            $pointRate = $ProductOrderItem->getPointRate();
        }

        $OrderItem = new OrderItem();
        $OrderItem->setProductName($DiscountType->getName())
            ->setPrice($discount)
            ->setPointRate($pointRate)
            ->setQuantity('1')
            ->setTax('0')
            ->setTaxRate('0')->setRoundingType()
            ->setOrderItemType($DiscountType)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setOrder($itemHolder)
            ->setProcessorName(PointProcessor::class);
        $itemHolder->addItem($OrderItem);
    }

    /**
     * 既存のポイント明細を削除する.
     */
    public function removePointDiscountItem(ItemHolderInterface $itemHolder): void
    {
        if ($itemHolder instanceof Order) {
            foreach ($itemHolder->getItems() as $item) {
                if ($item instanceof OrderItem && $item->getProcessorName() == PointProcessor::class) {
                    $itemHolder->removeOrderItem($item);
                    $this->entityManager->remove($item);
                }
            }
        }
    }

    public function prepare(ItemHolderInterface $itemHolder, string $point): void
    {
        // ユーザの保有ポイントを減算
        $Customer = $itemHolder->getCustomer();
        $Customer->setPoint(bcsub((string) $Customer->getPoint(), $point));
    }

    public function rollback(ItemHolderInterface $itemHolder, string $point): void
    {
        // 利用したポイントをユーザに戻す.
        $Customer = $itemHolder->getCustomer();
        $Customer->setPoint(bcadd((string) $Customer->getPoint(), $point));
    }
}
