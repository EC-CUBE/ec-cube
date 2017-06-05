<?php

namespace Plugin\Strategy\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
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
        error_log(__METHOD__.' called');
    }

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        error_log(__METHOD__.' called');
    }

    public function setOrder(Order $Order)
    {
        error_log(__METHOD__.' called');

        // XXX 明細のみ必要な場合もOrderをセットする必要がある
        $this->Order = $Order;
    }

    public function setApplication(Application $app)
    {
        error_log(__METHOD__.' called');
    }
}
