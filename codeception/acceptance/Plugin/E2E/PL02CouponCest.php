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

namespace Plugin\E2E;

use AcceptanceTester;
use Carbon\Carbon;
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Facebook\WebDriver\WebDriverKeys;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL02CouponCest
{
    private string $couponCode = '';
    /**
     * @var string
     */
    private string $orderNumber = '';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * ⓪ インストール
     *
     * @param AcceptanceTester $I
     * @skip
     *
     * @return void
     */
    public function coupon_0(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $couponRow = Locator::contains('//tr', 'Coupon Plugin for EC-CUBE42');
        $I->see('インストール', $couponRow);
        $I->click("(//tr[contains(.,'Coupon Plugin for EC-CUBE42')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
    }

    /**
     * ① 無効化できる
     *
     * @param AcceptanceTester $I
     *
     * @return void
     *
     * @throws \Exception
     */
    public function coupon_1(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $couponRow = Locator::contains('//tr', 'Coupon Plugin for EC-CUBE42');
        $I->see('Coupon Plugin for EC-CUBE42', $couponRow);
        $I->see('有効', $couponRow);
        $I->clickWithLeftButton("(//tr[contains(.,'Coupon Plugin for EC-CUBE42')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「Coupon Plugin for EC-CUBE42」を無効にしました。');
        $I->see('Coupon Plugin for EC-CUBE42', $couponRow);
        $I->see('無効', $couponRow);
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[3]');
        $I->wait(2);
        $I->dontSee('クーポン', '(//li[@class="c-mainNavArea__navItem"])[3]');
    }

    /**
     * ② 有効化できる
     *
     * @param AcceptanceTester $I
     *
     * @return void
     *
     * @throws \Exception
     */
    public function coupon_2(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $couponRow = Locator::contains('//tr', 'Coupon Plugin for EC-CUBE42');
        $I->see('Coupon Plugin for EC-CUBE42', $couponRow);
        $I->see('無効', $couponRow);
        $I->clickWithLeftButton("(//tr[contains(.,'Coupon Plugin for EC-CUBE42')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「Coupon Plugin for EC-CUBE42」を有効にしました。');
        $I->see('Coupon Plugin for EC-CUBE42', $couponRow);
        $I->see('有効', $couponRow);
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[3]');
        $I->wait(2);
        $I->see('クーポン', '(//li[@class="c-mainNavArea__navItem"])[3]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_3(AcceptanceTester $I, string $attachName = '', bool $isFutureDate = false)
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        $I->fillField('#coupon_coupon_name', 'all products and set discount test ' . $attachName);
        $I->fillField('#coupon_coupon_release', '1');
        // 期間開始日設定
        $this->dateSetter($I, $isFutureDate);
        $I->fillField('#coupon_discount_price', '100');
        $I->clickWithLeftButton('#coupon_coupon_type_2');
        $I->retryDontSee('商品情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'all products and set discount test ' . $attachName);
        $I->see('all products and set discount test ' . $attachName, $couponRow);
        $I->see('有効', $couponRow);
        $this->couponCode = $I->grabTextFrom(Locator::contains('//tr', 'all products and set discount test ' . $attachName) . '//td[2]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_4(AcceptanceTester $I, string $attachName = '')
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        if (empty($attachName)) {
            $attachName = bin2hex(random_bytes(10));
        }
        $I->fillField('#coupon_coupon_name', 'product and set discount test'. $attachName);
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $xpath = Locator::contains('//a', '商品の追加');
        $I->click($xpath);
        $I->fillField('#admin_search_product_id', '彩');
        $I->click('#searchProductModalButton');
        $I->wait(5);
        $xpathProduct = Locator::contains('//tr', '彩のジェラートCUBE');
        $I->retrySee('cube-01', $xpathProduct);
        $I->click('.btn.btn-default.btn-sm');
        $I->see('彩のジェラートCUBE');
        $I->fillField('#coupon_discount_price', '100');
        $I->see('商品情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'product and set discount test'.$attachName);
        $I->see('test', $couponRow);
        $I->see('有効', $couponRow);
        $this->couponCode = $I->grabTextFrom(Locator::contains('//tr', 'product and set discount test' . $attachName) . '//td[2]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_5(AcceptanceTester $I, string $attachName = '', string $categoryName = '新入荷')
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        if (empty($attachName)) {
            $attachName = bin2hex(random_bytes(10));
        }
        $I->fillField('#coupon_coupon_name', 'category and set discount test'. $attachName);
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $I->clickWithLeftButton('#coupon_coupon_type_1');
        $I->retrySee('カテゴリの追加');
        $xpath = Locator::contains('//a', 'カテゴリの追加');
        $I->click($xpath);
        $I->selectOption('#coupon_search_category_category_id', $categoryName);
        $I->click('#searchCategoryModalButton');
        $xpathProduct = Locator::contains('//tr', $categoryName);
        $I->retrySee($categoryName, $xpathProduct);
        $I->click('.btn.btn-default.btn-sm');
        $I->retrySee($categoryName);
        $I->see('カテゴリ情報');
        $I->fillField('#coupon_discount_price', '100');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'category and set discount test'. $attachName);
        $I->see('category and set discount test'. $attachName, $couponRow);
        $I->see('有効', $couponRow);
        $this->couponCode = $I->grabTextFrom(Locator::contains('//tr', 'category and set discount test' . $attachName) . '//td[2]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_6(AcceptanceTester $I, $attachName = '')
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        if (empty($attachName)) {
            $attachName = bin2hex(random_bytes(10));
        }
        $I->fillField('#coupon_coupon_name', 'all products and percentage discount test'.$attachName);
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $I->clickWithLeftButton('#coupon_coupon_type_2');
        $I->clickWithLeftButton('#coupon_discount_type_1');
        $I->fillField('#coupon_discount_rate', '33');
        $I->retryDontSee('商品情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'all products and percentage discount test'.$attachName);
        $I->see('all products and percentage discount test'.$attachName, $couponRow);
        $I->see('有効', $couponRow);
        $this->couponCode = $I->grabTextFrom(Locator::contains('//tr', 'all products and percentage discount test' . $attachName) . '//td[2]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_7(AcceptanceTester $I, $attachName = '')
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        if (empty($attachName)) {
            $attachName = bin2hex(random_bytes(10));
        }
        $I->fillField('#coupon_coupon_name', 'product and discount rate test'.$attachName);
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $xpath = Locator::contains('//a', '商品の追加');
        $I->click($xpath);
        $I->fillField('#admin_search_product_id', '彩');
        $I->click('#searchProductModalButton');
        $I->wait(5);
        $xpathProduct = Locator::contains('//tr', '彩のジェラートCUBE');
        $I->retrySee('cube-01', $xpathProduct);
        $I->click('.btn.btn-default.btn-sm');
        $I->see('彩のジェラートCUBE');
        $I->see('商品情報');
        $I->clickWithLeftButton('#coupon_discount_type_1');
        $I->fillField('#coupon_discount_rate', '33');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'product and discount rate test'.$attachName);
        $I->see('product and discount rate test'.$attachName, $couponRow);
        $I->see('有効', $couponRow);
        $this->couponCode = $I->grabTextFrom(Locator::contains('//tr', 'product and discount rate test' . $attachName) . '//td[2]');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function coupon_8(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        $I->fillField('#coupon_coupon_name', 'category and discount rate test');
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $I->clickWithLeftButton('#coupon_coupon_type_1');
        $I->clickWithLeftButton('#coupon_discount_type_1');
        $I->fillField('#coupon_discount_rate', '33');
        $I->retrySee('カテゴリの追加');
        $xpath = Locator::contains('//a', 'カテゴリの追加');
        $I->click($xpath);
        $I->selectOption('#coupon_search_category_category_id', '新入荷');
        $I->click('#searchCategoryModalButton');
        $xpathProduct = Locator::contains('//tr', '新入荷');
        $I->retrySee('新入荷', $xpathProduct);
        $I->click('.btn.btn-default.btn-sm');
        $I->retrySee('新入荷');
        $I->see('カテゴリ情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'category and discount rate test');
        $I->see('category and discount rate test', $couponRow);
        $I->see('有効', $couponRow);
    }

    public function coupon_9(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        $I->fillField('#coupon_coupon_name', 'member only test');
        $I->fillField('#coupon_coupon_release', '1');
        $this->dateSetter($I);
        $I->clickWithLeftButton('#coupon_coupon_member_0');
        $I->fillField('#coupon_discount_price', '100');
        $I->clickWithLeftButton('#coupon_coupon_type_2');
        $I->retryDontSee('商品情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'member only test');
        $I->see('member only test', $couponRow);
        $I->see('有効', $couponRow);
    }

    public function coupon_10(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $this->baseRegistrationPage($I);
        $I->fillField('#coupon_coupon_name', 'coupon with lower limit test');
        $I->fillField('#coupon_coupon_release', '1');
        $I->fillField('#coupon_coupon_lower_limit', '50');
        $this->dateSetter($I);
        $I->fillField('#coupon_discount_price', '100');
        $I->clickWithLeftButton('#coupon_coupon_type_2');
        $I->retryDontSee('商品情報');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンを登録しました。');
        $couponRow = Locator::contains('//tr', 'coupon with lower limit test');
        $I->see('coupon with lower limit test', $couponRow);
        $I->see('有効', $couponRow);
    }

    public function coupon_11(AcceptanceTester $I, $string = '')
    {
        $I->retry(7, 400);
        if (empty($string)) {
            $string = bin2hex(random_bytes(10));
        }
        $this->coupon_3($I, $string);
        $xcouponRow = Locator::contains('//tr', 'all products and set discount test ' . $string);
        $I->click($xcouponRow . Locator::contains('//a', '有効'));
        $I->see('クーポンの状態を変更しました。');
        $I->see('無効', $xcouponRow);
    }

    public function coupon_12(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $string = bin2hex(random_bytes(10));
        $this->coupon_11($I, $string);
        $xcouponRow = Locator::contains('//tr', 'all products and set discount test ' . $string);
        $I->click($xcouponRow . Locator::contains('//a', '無効'));
        $I->see('クーポンの状態を変更しました。');
        $I->see('有効', $xcouponRow);
    }

    public function coupon_13(AcceptanceTester $I, string $string = '')
    {
        $I->retry(7, 400);
        if (empty($string)) {
            $string = bin2hex(random_bytes(10));
        }
        $this->coupon_3($I, $string);
        $xcouponRow = Locator::contains('//tr', 'all products and set discount test ' . $string);
        $I->click($xcouponRow . '//i[@class="fa fa-close fa-lg text-secondary"]');
        $I->retrySee('このクーポンを削除しても宜しいですか？');
        $I->click(Locator::contains('//a', '削除'));
        $I->see('クーポンを削除しました。');
        $I->dontSee('all products and set discount test ' . $string);
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     *
     * @throws \Exception
     */
    public function coupon_14_20(AcceptanceTester $I, $randomTokenName = '')
    {
        $I->retry(7, 400);
        if (empty($randomTokenName)) {
            $randomTokenName = bin2hex(random_bytes(10));
        }
        $this->coupon_3($I, $randomTokenName);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '確認する'));
        // 確認画面
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '注文する'));
        $I->see('ご注文ありがとうございました');
        // 注文番号の数字のみを取得する
        $this->orderNumber = mb_substr($I->grabTextFrom('.ec-reportDescription > strong'), 8);
    }

    public function coupon_15(AcceptanceTester $I)
    {
        $this->coupon_14_20($I);
        $I->amOnPage(sprintf('/admin/order/%s/edit', $this->orderNumber));
        $I->see('ご利用クーポンコード');
        $I->see($this->couponCode);
        $I->see('-￥100');
    }

    public function coupon_16(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        if (empty($randomTokenName)) {
            $randomTokenName = bin2hex(random_bytes(10));
        }
        $this->coupon_3($I, $randomTokenName);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        $I->retrySee('クーポンコードの入力');
        $dontUseOption = Locator::contains('div.form-check', 'クーポンを利用しない');
        $I->see('クーポンを利用しない', $dontUseOption);
        $I->click($dontUseOption . '//input');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->dontSee(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->dontSee($randomTokenName);
        $I->dontSee('-￥100');
        $I->click(Locator::contains('//button', '確認する'));
        $I->dontSee(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->dontSee($randomTokenName);
        $I->dontSee('-￥100');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     *
     * @throws \Exception
     */
    public function coupon_17(AcceptanceTester $I)
    {
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_14_20($I, $randomTokenName);
        $I->amOnPage(sprintf('/mypage/history/%s', $this->orderNumber));
        $I->see('マイページ/ご注文履歴詳細');
        $I->see($randomTokenName);
        $I->see('ご利用クーポンコード');
        $I->see('￥100');
    }

    public function coupon_18(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        if (empty($string)) {
            $string = bin2hex(random_bytes(10));
        }
        $this->coupon_11($I, $string);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンコードの入力');
        $I->see('クーポン対象商品はございません。クーポンコードをご確認ください。');
    }

    public function coupon_19(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $string = bin2hex(random_bytes(10));
        $this->coupon_13($I, $string);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンコードの入力');
        $I->see('クーポン対象商品はございません。クーポンコードをご確認ください。');
    }

    public function coupon_21(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_3($I, $randomTokenName, true);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('クーポンコードの入力');
        $I->see('クーポン対象商品はございません。クーポンコードをご確認ください。');
    }

    public function coupon_22(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_4($I, $randomTokenName);
        $this->generateTestMemberAndLogin($I);
        // Product Includes Coupon
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '確認する'));
        // 確認画面
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '注文する'));
        $I->see('ご注文ありがとうございました');

        // Product Excludes Coupon
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_4($I, $randomTokenName);
        $I->amOnPage('products/detail/2');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->dontSee('クーポンコードの入力');
        $I->see('このクーポンはご利用いただくことができません。');
    }

    public function coupon_23(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_5($I, $randomTokenName, 'ジェラート');
        $this->generateTestMemberAndLogin($I);
        // Product Includes Coupon
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '確認する'));
        // 確認画面
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥100');
        $I->click(Locator::contains('//button', '注文する'));
        $I->see('ご注文ありがとうございました');
        // 確認
        // Product Excludes Coupon
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_5($I, $randomTokenName, 'ジェラート');
        $I->amOnPage('products/detail/2');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->dontSee('クーポンコードの入力');
        $I->see('このクーポンはご利用いただくことができません。');
    }

    public function coupon_24(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_7($I, $randomTokenName);
        $this->generateTestMemberAndLogin($I);
        // Product Includes Coupon
        $I->amOnPage('products/detail/1');
        $I->selectOption('#classcategory_id1', 'チョコ');
        $I->selectOption('#classcategory_id2', '64cm × 64cm');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥6,534');
        $I->click(Locator::contains('//button', '確認する'));
        // 確認画面
        $I->see(sprintf('クーポンコード %s を利用しています。', $this->couponCode));
        $I->see($randomTokenName);
        $I->see('-￥6,534');
        $I->click(Locator::contains('//button', '注文する'));
        $I->see('ご注文ありがとうございました');
        // 確認
        // Product Excludes Coupon
        $randomTokenName = bin2hex(random_bytes(10));
        $this->coupon_7($I, $randomTokenName);
        $I->amOnPage('products/detail/2');
        $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
        $I->retrySee('カートに追加しました。');
        $I->clickWithLeftButton('a.ec-inlineBtn--action');
        $I->see('ショッピングカート');
        $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
        $I->see('ご注文手続き');
        $I->see('クーポン');
        $I->clickWithLeftButton(Locator::contains('a', 'クーポンを変更する'));
        // クーポン入力画面
        $I->retrySee('クーポンコードの入力');
        $I->fillField('#coupon_use_coupon_cd', $this->couponCode);
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->dontSee('クーポンコードの入力');
        $I->see('このクーポンはご利用いただくことができません。');
    }

    public function coupon_25(AcceptanceTester $I)
    {

    }


    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    private function baseRegistrationPage(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/plugin/coupon');
        $I->see('クーポンを新規登録');
        $I->click(Locator::contains('//a', 'クーポンを新規登録'));
        $I->seeInCurrentUrl('/admin/plugin/coupon/new');
        $I->see('クーポン情報');
        $I->see('商品情報');
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    private function dateSetter(AcceptanceTester $I, bool $isFutureDate = false): void
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDay();
        if ($isFutureDate) {
            $from->addDay();
            $to->addDay();
        }
        // 期間開始日設定
        $I->clickWithLeftButton('#coupon_available_from_date');
        $I->type($from->format('Y'));
        $I->pressKey('#coupon_available_from_date', WebDriverKeys::TAB);
        $I->type($from->format('m'));
        $I->type($from->format('d'));
        // 期間終了日設定
        $I->clickWithLeftButton('#coupon_available_to_date');
        $I->type($to->format('Y'));
        $I->pressKey('#coupon_available_to_date', WebDriverKeys::TAB);
        $I->type($to->format('m'));
        $I->type($to->format('d'));
    }

    private function generateTestMemberAndLogin(AcceptanceTester $I): void
    {
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
    }
}
