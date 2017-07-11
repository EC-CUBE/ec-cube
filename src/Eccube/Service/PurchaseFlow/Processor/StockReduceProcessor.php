<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Common\Constant;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Doctrine\DBAL\LockMode;

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
     * @param ItemHolderInterface $itemHolder
     * @return ProcessResult
     */
    public function process(ItemInterface $item)
    {
        if (!$item instanceof \Eccube\Entity\ShipmentItem) {
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
