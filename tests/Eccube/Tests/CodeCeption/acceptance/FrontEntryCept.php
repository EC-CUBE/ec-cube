<?php 
$I = new AcceptanceTester($scenario);

$token = date('dHis');

$I->amOnPage('/entry');
$I->fillField('entry[name][name01]', '高橋');
$I->fillField('entry[name][name02]', '慎一');
$I->fillField('entry[kana][kana01]', 'タｋハシ');
$I->fillField('entry[kana][kana02]', 'シンイチ');
$I->fillField('entry[zip][zip01]', '530');
$I->fillField('entry[zip][zip02]', '0001');
$I->selectOption('entry[address][pref]', '大阪府');
$I->fillField('entry[address][addr01]', '大阪市北区梅田');
$I->fillField('entry[address][addr02]', '2-4-9 ブリーゼタワー13F');
$I->fillField('entry[tel][tel01]', '06');
$I->fillField('entry[tel][tel02]', '4795');
$I->fillField('entry[tel][tel03]', '7500');
$I->fillField('entry[fax][fax01]', '06');
$I->fillField('entry[fax][fax02]', '4795');
$I->fillField('entry[fax][fax03]', '7501');
$I->fillField('entry[email][first]', 'takahashi' . $token .  '@lockon.co.jp');
$I->fillField('entry[email][second]', 'takahashi' . $token . '@lockon.co.jp');
$I->fillField('entry[password]', '1234abcd');
$I->selectOption('entry[birth][year]', '1987');
$I->selectOption('entry[birth][month]', '08');
$I->selectOption('entry[birth][day]', '20');
$I->selectOption('entry[sex]', '1');
$I->selectOption('entry[job]', 'コンピューター関連技術職');

$I->click('同意する');

$I->see('下記の内容で送信してもよろしいでしょうか？');
$I->click('会員登録をする');

$I->see('本登録が完了いたしました。');
$I->click('トップページへ');
?>