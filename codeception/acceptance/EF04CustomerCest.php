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

/**
 * @group front
 * @group customer
 * @group ef4
 */
class EF04CustomerCest
{
    /**
     * @group vaddy
     */
    public function customer_会員登録正常(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T01 会員登録 正常パターン');
        $I->amOnPage('/entry');
        $faker = Fixtures::get('faker');
        $BaseInfo = Fixtures::get('baseinfo');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $form = [
            'entry[name][name01]' => '姓',
            'entry[name][name02]' => '名',
            'entry[kana][kana01]' => 'セイ',
            'entry[kana][kana02]' => 'メイ',
            'entry[postal_code]' => '530-0001',
            'entry[address][pref]' => ['value' => '27'],
            'entry[address][addr01]' => '大阪市北区',
            'entry[address][addr02]' => '梅田2-4-9 ブリーゼタワー13F',
            'entry[phone_number]' => '111-111-111',
            'entry[email][first]' => $new_email,
            'entry[email][second]' => $new_email,
            'entry[password][first]' => 'password',
            'entry[password][second]' => 'password',
            'entry[job]' => ['value' => '1'],
            'entry[user_policy_check]' => '1',
        ];
        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $form['entry[mailmaga_flg]'] = '1';
        }
        $I->submitForm(['css' => '.ec-layoutRole__main form'], $form, ['css' => 'button.ec-blockBtn--action']);

        // 入力した会員情報を確認する。
        $I->see('姓 名', '.ec-registerRole form .ec-borderedDefs dl:nth-child(1) dd');
        $I->see('111111111', '.ec-registerRole form .ec-borderedDefs dl:nth-child(5) dd');
        $I->see($new_email, '.ec-registerRole form .ec-borderedDefs dl:nth-child(6) dd');

        $I->resetEmails();
        // 「会員登録をする」ボタンを押下する
        $I->click('.ec-registerRole form button.ec-blockBtn--action');

        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
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
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
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
        $I->amOnPage('/entry');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $I->submitForm(['css' => '.ec-layoutRole__main form'], [
            'entry[name][name01]' => '姓',
            'entry[name][name02]' => '名',
            'entry[kana][kana01]' => 'セイ',
            'entry[kana][kana02]' => 'メイ',
            'entry[postal_code]' => '530-0001',
            'entry[address][pref]' => ['value' => '27'],
            'entry[address][addr01]' => '大阪市北区',
            'entry[address][addr02]' => '梅田2-4-9 ブリーゼタワー13F',
            'entry[phone_number]' => '111-111-111',
            'entry[email][first]' => $customer->getEmail(), // 会員登録済みのメールアドレスを入力する
            'entry[email][second]' => $customer->getEmail(),
            'entry[password][first]' => 'password',
            'entry[password][second]' => 'password',
        ], ['css' => 'button.ec-blockBtn--action']);

        // 入力した会員情報を確認する。
        $I->see('このメールアドレスは利用できません', '.ec-registerRole form .ec-borderedDefs dl:nth-child(6) dd');
    }

    public function customer_会員登録異常2(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T03 会員登録 異常パターン 入力ミス');
        $I->amOnPage('/entry');

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $I->submitForm(['css' => '.ec-layoutRole__main form'], [
            'entry[name][name01]' => '',
            'entry[name][name02]' => '名',
            'entry[kana][kana01]' => 'セイ',
            'entry[kana][kana02]' => 'メイ',
            'entry[postal_code]' => '530-0001',
            'entry[address][pref]' => ['value' => '27'],
            'entry[address][addr01]' => '大阪市北区',
            'entry[address][addr02]' => '梅田2-4-9 ブリーゼタワー13F',
            'entry[phone_number]' => '111-111-111',
            'entry[email][first]' => $new_email,
            'entry[email][second]' => $new_email,
            'entry[password][first]' => 'password',
            'entry[password][second]' => 'password',
        ], ['css' => 'button.ec-blockBtn--action']);

        // 入力した会員情報を確認する。
        $I->see('新規会員登録', '.ec-pageHeader h1');

        // TODO [fixture] 確認画面のあとでのメールアドレス重複エラー
    }

    public function customer_会員登録同意しない(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T04 会員登録 同意しないボタン');
        $I->amOnPage('/entry');

        $I->click('.ec-layoutRole__main form a.ec-blockBtn--cancel');
        $I->see('新着情報', '.ec-secHeading__ja');
        $I->seeInCurrentUrl('/');
    }

    public function customer_会員登録戻る(AcceptanceTester $I)
    {
        $I->wantTo('EF0401-UC01-T05 会員登録 戻るボタン');
        $I->amOnPage('/entry');

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 会員情報入力フォームに、会員情報を入力する
        // 「同意する」ボタンを押下する
        $form = [
            'entry[name][name01]' => '姓',
            'entry[name][name02]' => '名',
            'entry[kana][kana01]' => 'セイ',
            'entry[kana][kana02]' => 'メイ',
            'entry[postal_code]' => '530-0001',
            'entry[address][pref]' => ['value' => '27'],
            'entry[address][addr01]' => '大阪市北区',
            'entry[address][addr02]' => '梅田2-4-9 ブリーゼタワー13F',
            'entry[phone_number]' => '111-111-111',
            'entry[email][first]' => $new_email,
            'entry[email][second]' => $new_email,
            'entry[password][first]' => 'password',
            'entry[password][second]' => 'password',
            'entry[job]' => ['value' => '1'],
            'entry[user_policy_check]' => '1',
        ];

        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $form['entry[mailmaga_flg]'] = '1';
        }
        $I->submitForm(['css' => '.ec-layoutRole__main form'], $form, ['css' => 'button.ec-blockBtn--action']);

        $I->click('.ec-registerRole form button.ec-blockBtn--cancel');
        $I->see('新規会員登録', '.ec-pageHeader h1');
    }

    /**
     * @group vaddy
     */
    public function customer_会員登録利用規約(AcceptanceTester $I)
    {
        $I->wantTo('EF0404-UC01-T01 会員登録 利用規約');
        $I->amOnPage('/entry');

        $I->click('.ec-registerRole form a.ec-link');

        $I->switchToNewWindow();
        $I->seeInCurrentUrl('/help/agreement');
    }
}
