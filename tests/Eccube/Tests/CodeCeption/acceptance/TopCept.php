<?php
/**
 * Created by PhpStorm.
 * User: shunsuke_mihara
 * Date: 2015/06/03
 * Time: 16:24
 */

$I = new AcceptanceTester($scenario);
$I->amOnPage('/');
$I->see('EC-CUBE SHOP');
$I->click('一覧を見る');
$I->see('の商品がみつかりました。');
?>