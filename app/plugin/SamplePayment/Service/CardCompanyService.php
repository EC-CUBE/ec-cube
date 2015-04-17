<?php

namespace Plugin\SamplePayment\Service;

use Eccube\Application;

class CardCompanyService
{
    // カード決済会社と通信するモック
    public function getResult()
    {
        return true;
    }
}