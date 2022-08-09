<?php

namespace security;

use AcceptanceTester;
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;

class SO02JavascriptInjectionSecurityCest {

    public function javascript_injection_01(AcceptanceTester $I) {
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $createOrders = Fixtures::get('createOrders');
        /** @var Order $order */
        $order = $createOrders(
            $customer,
            1,
            [],
            OrderStatus::IN_PROGRESS
        )[0];
        $I->comment('Checking Javascript injection via order email page');
        $I->loginAsAdmin();
        $I->amOnPage(sprintf('/admin/order/%s/edit', $order->getId()));
        $I->fillField('#order_Shipping_address_addr01', '<script>alert("message");</script>');
        $I->fillField('#order_Shipping_address_addr02', '<script>alert(\'message\')</script>');
        $I->clickWithLeftButton(Locator::contains('button', '登録'));
        $I->see('保存しました');
        $I->clickWithLeftButton(Locator::contains('a', 'メールを作成'));
        $I->retrySee('ページを移動します');
        $I->retryClickWithLeftButton(Locator::contains('a', '保存せずに移動'));
        $I->see('メール通知');
        $I->wait(10);
        if($I->tryToSeePopup('message') === true) {
            $I->fail('Javascript injection via order email page is detected, terminating test...');
        }
        $I->selectOption('#template-change', '注文受付メール');
        $I->canSee('.ace_content', '<script>alert(\'message\')</script>');
        $I->clickWithLeftButton(Locator::contains('button', '送信内容を確認'));
        $I->retrySee('<script>alert(\'message\')</script>', '#detail_box__tpl_data');
        $I->wait(10);
        if($I->tryToSeePopup('message') === true) {
            $I->fail('Javascript injection via order email page is detected, terminating test...');
        }
        $I->clickWithLeftButton(Locator::contains('button', '送信'));
        $I->see('メールを送信しました。');
        $I->see('受注登録');
        $I->wait(10);
        if($I->tryToSeePopup('message') === true) {
            $I->fail('Javascript injection via order email page is detected, terminating test...');
        }
        $I->clickWithLeftButton(Locator::contains('a', '[EC-CUBE SHOP] ご注文ありがとうございます'));
        $I->retrySee('この度はご注文いただき誠にありがとうございます。');
        if($I->tryToSeePopup('message') === true) {
            $I->fail('Javascript injection via order email page is detected, terminating test...');
        }
        $I->see('<script>alert(\'message\')</script>');
    }
}
