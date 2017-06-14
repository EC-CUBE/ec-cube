<?php

namespace Plugin\Strategy\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Service\Calculator\ShipmentItemCollection;
use Eccube\Service\Calculator\Strategy\CalculateStrategyInterface;

/**
 * プラグインから拡張するための、空のStrategyです.
 * 特に処理は行わず、各メソッドがコールされた際にエラーログを出力します.
 *
 * 購入フロー及び受注登録・編集画面で呼び出されます.
 *
 * @package Plugin\Strategy\Strategy
 */
class EmptyStrategy implements CalculateStrategyInterface
{
    protected $Order;

    public function __construct()
    {
        log_info(__METHOD__.' called');
    }

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        log_info(__METHOD__.' called');
    }

    public function setOrder(PurchaseInterface $Order)
    {
        log_info(__METHOD__.' called');

        // XXX 明細のみ必要な場合もOrderをセットする必要がある
        $this->Order = $Order;
    }

    public function setApplication(Application $app)
    {
        log_info(__METHOD__.' called');
    }

    public function getTargetTypes()
    {
        return [Order::class];
    }
}
