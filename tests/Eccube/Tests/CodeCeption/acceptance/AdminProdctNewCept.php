<?php 
$I = new AcceptanceTester($scenario);

AdminLoginPage::of($I)->login('admin', 'password');

# 商品登録

$I->amOnPage('/admin/product/product/new');
$I->see('商品登録');

$token = date('dHis');

$I->fillField('商品名', 'テスト商品' . $token);
$I->selectOption('admin_product[class][product_type]', 1); //商品種別
$I->attachFile('商品画像', 'product_dummy.jpg');
$I->fillField('商品説明(詳細)', 'テスト商品の解説');
$I->fillField('販売価格', '1000');
$I->fillField('admin_product[class][price01]', '2000'); //通常価格
$I->fillField('在庫数', '50');
$I->checkOption('admin_product[Category][]', '雑貨'); //カテゴリ 食品
$I->fillField('商品コード', '123_abc_' . $token);
$I->fillField('販売制限数', '1000');
$I->fillField('検索ワード', 'テスト 商品の解説');
$I->selectOption('発送日目安', '1週間以降');
$I->fillField('商品送料', '650');
$I->fillField('admin_product[free_area]', 'セール限定価格'); // サブ情報
$I->selectOption('admin_product[Status]', 1); // 公開

$I->fillField('admin_product[note]', '原価800円'); //ショップ用メモ欄
$I->click('商品を登録');

$I->see('登録が完了しました');

# 登録済み確認
$I->amOnPage('/');
$I->click('雑貨');
$I->click('テスト商品' . $token);

$I->see('テスト商品' . $token);
$I->see('テスト商品の解説');
$I->see('1,080'); // 1,000 * 1.08
$I->see('2,160'); // 2,000 * 1.08
$I->see('123_abc_' . $token);
$I->see('セール限定価格');
$I->dontsee('原価800円');

?>