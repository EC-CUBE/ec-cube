<?php

namespace Eccube\Service\Calculator\Strategy;

interface CalculateStrategyInterface
{
    // 引数に型パラメータを指定したい
    public function execute(&$OrderDetails);
}
