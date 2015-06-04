<?php 
$I = new AcceptanceTester($scenario);

AdminLoginPage::of($I)->login('admin', 'password');

$I->amOnPage('/admin/customer/new');
$I->see('会員登録・編集');

$token = date('dHis');

$I->fillField('admin_customer[name][name01]', '高橋');
$I->fillField('admin_customer[name][name02]', '慎一');
$I->fillField('admin_customer[kana][kana01]', 'タｋハシ');
$I->fillField('admin_customer[kana][kana02]', 'シンイチ');
$I->fillField('admin_customer[zip][zip01]', '530');
$I->fillField('admin_customer[zip][zip02]', '0001');
$I->selectOption('admin_customer[address][pref]', '大阪府');
$I->fillField('admin_customer[address][addr01]', '大阪市北区梅田');
$I->fillField('admin_customer[address][addr02]', '2-4-9 ブリーゼタワー13F');
$I->fillField('admin_customer[tel][tel01]', '06');
$I->fillField('admin_customer[tel][tel02]', '4795');
$I->fillField('admin_customer[tel][tel03]', '7500');
$I->fillField('admin_customer[fax][fax01]', '06');
$I->fillField('admin_customer[fax][fax02]', '4795');
$I->fillField('admin_customer[fax][fax03]', '7501');
$I->fillField('admin_customer[email]', 'takahashi' . $token .  '@lockon.co.jp');
$I->fillField('admin_customer[password]', '1234abcd');
$I->selectOption('admin_customer[birth][year]', '1987');
$I->selectOption('admin_customer[birth][month]', '08');
$I->selectOption('admin_customer[birth][day]', '20');
$I->selectOption('admin_customer[sex]', '1');
$I->selectOption('admin_customer[job]', 'コンピューター関連技術職');

$I->click('会員情報を登録');

$I->see(' 会員情報を保存しました。');
?>