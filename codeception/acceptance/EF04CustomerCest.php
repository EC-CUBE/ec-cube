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

use Codeception\Util\Fixtures;
use Page\Front\EntryConfirmPage;
use Page\Front\EntryPage;

/**
 * @group front
 * @group customer
 * @group ef4
 */
class EF04CustomerCest
{
    public function customer_会員登録正常(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T01 会員登録 正常パターン');
        $faker = Fixtures::get('faker');
        $BaseInfo = Fixtures::get('baseinfo');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $EntryPage = EntryPage::go($I)
            ->入力_姓('姓')
            ->入力_名('名')
            ->入力_姓カナ('セイ')
            ->入力_名カナ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村('大阪市北区')
            ->入力_住所('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_メールアドレス($new_email)
            ->入力_メールアドレス確認($new_email)
            ->入力_パスワード('password')
            ->入力_パスワード確認('password')
            ->入力_職業(['value' => '1'])
            ->入力_利用規約同意();
        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $form['entry[mailmaga_flg]'] = '1';
        }
        $EntryPage->同意して登録();

        // 入力した会員情報を確認する。
        $EntryConfirmPage = EntryConfirmPage::at($I);
        $I->assertEquals('姓 名', $EntryConfirmPage->お名前());
        $I->assertEquals('セイ メイ', $EntryConfirmPage->お名前カナ());
        $I->assertEquals('〒5300001 大阪府 大阪市北区 梅田2-4-9 ブリーゼタワー13F', $EntryConfirmPage->住所());
        $I->assertEquals('111111111', $EntryConfirmPage->電話番号());
        $I->assertEquals($new_email, $EntryConfirmPage->メールアドレス());
        $I->assertEquals('公務員', $EntryConfirmPage->職業());

        $I->resetEmails();
        $EntryConfirmPage->会員登録をする();

        $message = $I->lastMessage();
        $I->assertCount(2, $message['recipients'], 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, '会員登録のご確認');
            $I->seeInLastEmailTo($email, '姓 名 様');
            $I->seeInLastEmailTo($email, 'この度は会員登録依頼をいただきまして、有り難うございます。');
        }

        // 「トップページへ」ボタンを押下する
        $I->click('a.ec-blockBtn--cancel');
        $I->see('新着情報', '.ec-secHeading__ja');

        // アクティベートURL取得
        $activateUrl = $I->grabFromLastEmailTo($new_email, '@/entry/activate/(.*)@');
        $I->resetEmails();

        // アクティベートURLからトップページへ
        $I->amOnPage($activateUrl);
        $I->see('新規会員登録(完了)', 'div.ec-pageHeader h1');

        $message = $I->lastMessage();
        $I->assertCount(2, $message['recipients'], 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, '会員登録が完了しました。');
            $I->seeInLastEmailTo($email, '姓 名 様');
            $I->seeInLastEmailTo($email, '本会員登録が完了いたしました。');
        }

        $I->click('div.ec-registerCompleteRole a.ec-blockBtn--cancel');
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function customer_会員登録異常1(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T02 会員登録 異常パターン 重複');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        EntryPage::go($I)
            ->入力_姓('姓')
            ->入力_名('名')
            ->入力_姓カナ('セイ')
            ->入力_名カナ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村('大阪市北区')
            ->入力_住所('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_メールアドレス($customer->getEmail()) // 会員登録済みのメールアドレスを入力する
            ->入力_メールアドレス確認($customer->getEmail())
            ->入力_パスワード('password')
            ->入力_パスワード確認('password')
            ->入力_職業(['value' => '1'])
            ->入力_利用規約同意()
            ->同意して登録();

        // 入力した会員情報を確認する。
        $I->see('このメールアドレスは利用できません', '.ec-registerRole form .ec-borderedDefs dl:nth-child(6) dd');
    }

    public function customer_会員登録異常2(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T03 会員登録 異常パターン 入力ミス');

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        EntryPage::go($I)
            ->入力_姓('')
            ->入力_名('名')
            ->入力_姓カナ('セイ')
            ->入力_名カナ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村('大阪市北区')
            ->入力_住所('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_メールアドレス($new_email)
            ->入力_メールアドレス確認($new_email)
            ->入力_パスワード('password')
            ->入力_パスワード確認('password')
            ->入力_職業(['value' => '1'])
            ->入力_利用規約同意()
            ->同意して登録();

        // 入力した会員情報を確認する。
        $I->see('新規会員登録', '.ec-pageHeader h1');
    }

    public function customer_会員登録同意しない(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T04 会員登録 同意しないボタン');
        $I->amOnPage('/entry');

        $I->click('.ec-layoutRole__main form a.ec-blockBtn--cancel');
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function customer_会員登録戻る(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T05 会員登録 戻るボタン');
        $I->amOnPage('/entry');

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $EntryPage = EntryPage::go($I)
            ->入力_姓('姓')
            ->入力_名('名')
            ->入力_姓カナ('セイ')
            ->入力_名カナ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村('大阪市北区')
            ->入力_住所('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_メールアドレス($new_email)
            ->入力_メールアドレス確認($new_email)
            ->入力_パスワード('password')
            ->入力_パスワード確認('password')
            ->入力_職業(['value' => '1'])
            ->入力_利用規約同意();

        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $form['entry[mailmaga_flg]'] = '1';
        }
        $EntryPage->同意して登録();

        EntryConfirmPage::at($I)
            ->戻る();

        EntryPage::at($I);
    }

    public function customer_会員登録利用規約(AcceptanceTester $I)
    {
        $I->wantTo('EF0404-UC01-T01 会員登録 利用規約');
        $I->amOnPage('/entry');

        $I->click('.ec-registerRole form a.ec-link');

        $I->switchToNewWindow();
        $I->seeInCurrentUrl('/help/agreement');
    }
}
