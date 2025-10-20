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
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * PointHelper constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BaseInfoRepository $baseInfoRepository, EntityManagerInterface $entityManager)
    {
        $this->baseInfoRepository = $baseInfoRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * ポイント設定が有効かどうか.
     *
     * @return bool
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
    public function pointToPrice($point): string
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
    public function pointToDiscount($point): string
    {
        return bcmul($this->pointToPrice($point), '-1', 0);
    }

    /**
     * 金額をポイントに変換する.
     *
     * @param string $price
     *
     * @return string ポイント
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function priceToPoint($price): string
    {
        $BaseInfo = $this->baseInfoRepository->get();

        return bcfloor(bcdiv($price, (string) $BaseInfo->getPointConversionRate(), 4));
    }

    /**
     * 明細追加処理.
     *
     * @param ItemHolderInterface $itemHolder
     * @param string $discount
     *
     * @return void
     *
     * @throws \Exception
     */
    public function addPointDiscountItem(ItemHolderInterface $itemHolder, $discount): void
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
            ->setTaxRate('0')
            ->setRoundingType(null)
            ->setOrderItemType($DiscountType)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setOrder($itemHolder)
            ->setProcessorName(PointProcessor::class);
        $itemHolder->addItem($OrderItem);
    }

    /**
     * 既存のポイント明細を削除する.
     *
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
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

    /**
     * @param ItemHolderInterface $itemHolder
     * @param string $point
     *
     * @return void
     */
    public function prepare(ItemHolderInterface $itemHolder, $point): void
    {
        // ユーザの保有ポイントを減算
        $Customer = $itemHolder->getCustomer();
        $Customer->setPoint(bcsub($Customer->getPoint(), $point));
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param string $point
     *
     * @return void
     */
    public function rollback(ItemHolderInterface $itemHolder, $point): void
    {
        // 利用したポイントをユーザに戻す.
        $Customer = $itemHolder->getCustomer();
        $Customer->setPoint(bcadd($Customer->getPoint(), $point));
    }
}
