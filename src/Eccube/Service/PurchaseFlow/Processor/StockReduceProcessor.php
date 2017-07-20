<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\DBAL\LockMode;
use Eccube\Common\Constant;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 在庫制御.
 */
class StockReduceProcessor implements ItemProcessor
{

    private $app;

    /**
     * DeliveryFeeProcessor constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @return ProcessResult
     * @internal param ItemHolderInterface $itemHolder
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item instanceof ShipmentItem) {
            // ShipmentItem 以外の場合は何もしない
            return ProcessResult::success();
        }

        // 在庫処理を実装
        // warning も作りたい
        if (!$item->isProduct()) {
            // FIXME 配送明細を考慮する必要がある
            return ProcessResult::success();
        }
        // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
        if ($item->getProductClass()->getStockUnlimited() == Constant::DISABLED) {
            // 在庫チェックあり
            // 在庫に対してロック(select ... for update)を実行
            $productStock = $this->app['orm.em']->getRepository('Eccube\Entity\ProductStock')->find(
                $item->getProductClass()->getProductStock()->getId(), LockMode::PESSIMISTIC_WRITE
            );
            // 購入数量と在庫数をチェックして在庫がなければエラー
            if ($productStock->getStock() < 1) {
                return ProcessResult::fail('在庫エラー');
            } elseif ($item->getQuantity() > $productStock->getStock()) {
                return ProcessResult::fail('在庫エラー');
            }
        }
        return ProcessResult::success();
    }
}
