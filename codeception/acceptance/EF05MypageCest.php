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
use Page\Front\CustomerAddressEditPage;
use Page\Front\CustomerAddressListPage;
use Page\Front\HistoryPage;
use Page\Front\MyPage;
use Page\Front\ProductDetailPage;

/**
 * @group front
 * @group mypage
 * @group ef5
 */
class EF05MypageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function mypage_初期表示(AcceptanceTester $I)
    {
        $I->wantTo('EF0501-UC01-T01 Mypage 初期表示');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        MyPage::go($I);
        MyPage::at($I);
    }

    public function mypage_ご注文履歴_(AcceptanceTester $I)
    {
        $I->wantTo('EF0502-UC01-T01 Mypage ご注文履歴');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $createOrders = Fixtures::get('createOrders');
        $Orders = $createOrders($customer, 5, [], \Eccube\Entity\Master\OrderStatus::NEW);

        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>ご注文履歴
        MyPage::go($I)->注文履歴();

        // 注文内容の状況/簡易情報が表示される、各注文履歴に「詳細を見る」ボタンが表示される
        $I->see('ご注文履歴', 'div.ec-pageHeader h1');
        $I->see('ご注文番号', 'div.ec-historyRole dl.ec-definitions');
        $I->see('詳細を見る', 'div.ec-historyRole p.ec-historyListHeader__action a');
    }

    /**
     * @group vaddy
     */
    public function mypage_ご注文履歴詳細(AcceptanceTester $I)
    {
        $I->wantTo('EF0503-UC01-T01 Mypage ご注文履歴詳細');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $createOrders = Fixtures::get('createOrders');
        $createOrders($customer, 5, [], \Eccube\Entity\Master\OrderStatus::NEW);

        $I->loginAsMember($customer->getEmail(), 'password');

        MyPage::go($I)->注文履歴詳細(1);

        HistoryPage::at($I);

        $I->see('ご注文状況', 'div.ec-orderOrder div.ec-definitions:nth-child(3) dt');
        // $I->see('注文受付', '#main_middle .order_detail'); TODO 受注ステータスが可変するためテストが通らない場合がある
        $I->see('配送情報', 'div.ec-orderRole div.ec-orderDelivery div.ec-rectHeading h2');
        $I->see('お届け先', 'div.ec-orderRole div.ec-orderDelivery div.ec-orderDelivery__title');
        $I->see('お支払い情報', 'div.ec-orderRole div.ec-orderPayment div.ec-rectHeading h2');
        $I->see('お問い合わせ', 'div.ec-orderRole div.ec-orderConfirm div.ec-rectHeading h2');
        $I->see('メール配信履歴一覧', 'div.ec-orderRole div.ec-orderMails div.ec-rectHeading h2');
        $I->see('小計', 'div.ec-orderRole__summary div.ec-totalBox dl:nth-child(1)');
        $I->see('手数料', 'div.ec-orderRole__summary div.ec-totalBox dl:nth-child(2)');
        $I->see('送料', 'div.ec-orderRole__summary div.ec-totalBox dl:nth-child(3)');
        $I->see('合計', 'div.ec-orderRole__summary div.ec-totalBox .ec-totalBox__total');
    }

    /**
     * @group excludeCoverage
     * @group vaddy
     */
    public function mypage_お気に入り一覧(AcceptanceTester $I)
    {
        $I->wantTo('EF0503-UC01-T02 Mypage お気に入り一覧');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>ご注文履歴
        MyPage::go($I)->お気に入り一覧();

        // 最初はなにも登録されていない
        $I->see('お気に入り一覧', 'div.ec-pageHeader h1');
        $I->see('お気に入りは登録されていません。', 'div.ec-favoriteRole');

        // お気に入り登録
        ProductDetailPage::go($I, 2)->お気に入りに追加();

        $I->wantTo('EF0503-UC01-T03 Mypage お気に入り一覧');
        MyPage::go($I)->お気に入り一覧();
        $I->see('チェリーアイスサンド', 'ul.ec-favoriteRole__itemList li:nth-child(1) p.ec-favoriteRole__itemTitle');

        // お気に入りを削除
        $I->click('ul.ec-favoriteRole__itemList li:nth-child(1) a.ec-closeBtn--circle');
        $I->acceptPopup();
    }

    /**
     * @group vaddy
     */
    public function mypage_会員情報編集(AcceptanceTester $I)
    {
        $I->wantTo('EF0504-UC01-T01 Mypage 会員情報編集');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // TOPページ>マイページ>会員情報編集
        MyPage::go($I)->会員情報編集();

        // 会員情報フォームに既存の登録情報が表示される
        $I->seeInField(['id' => 'entry_name_name01'], $customer->getName01());

        $form = [
            'entry[name][name01]' => '姓05',
            'entry[name][name02]' => '名05',
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
        ];

        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            // 必須入力が効いてない https://github.com/EC-CUBE/mail-magazine-plugin/issues/29
            $form['entry[mailmaga_flg]'] = '1';
        }
        // 会員情報フォームに会員情報を入力する
        $I->submitForm('div.ec-editRole form', $form);

        // 会員情報編集（完了）画面が表示される
        $I->see('会員情報編集(完了)', 'div.ec-pageHeader h1');

        // 「トップページへ」ボタンを押下する
        $I->click('div.ec-registerCompleteRole a.ec-blockBtn--cancel');

        // TOPページヘ遷移する
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function mypage_お届け先編集表示(AcceptanceTester $I)
    {
        $I->wantTo('EF0506-UC01-T01 Mypage お届け先編集表示');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>お届け先一覧
        MyPage::go($I)->お届け先編集();

        $I->see('お届け先一覧', 'div.ec-pageHeader h1');
    }

    /**
     * @group vaddy
     */
    public function mypage_お届け先編集作成変更(AcceptanceTester $I)
    {
        $I->wantTo('EF0506-UC01-T02 Mypage お届け先編集作成変更');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // お届先作成
        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)
            ->お届け先編集()
            ->追加();

        // 入力 & submit
        CustomerAddressEditPage::at($I)
            ->入力_姓('姓05')
            ->入力_名('名05')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        // お届け先編集ページ
        CustomerAddressListPage::at($I);

        // 一覧に追加されている
        $I->see('大阪市北区', 'div.ec-addressList div:nth-child(1) div.ec-addressList__address');

        // お届先編集
        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)
            ->お届け先編集()
            ->変更(1);

        CustomerAddressEditPage::at($I)
            ->入力_姓('姓05')
            ->入力_名('名05')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市南区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        // お届け先編集ページ
        CustomerAddressListPage::at($I);

        // 一覧に反映されている
        $I->see('大阪市南区', 'div.ec-addressList div:nth-child(1) div.ec-addressList__address');
    }

    /**
     * @group excludeCoverage
     * @vaddy
     */
    public function mypage_お届け先編集削除(AcceptanceTester $I)
    {
        $I->wantTo('EF0506-UC03-T01 Mypage お届け先編集削除');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)->お届け先編集()->追加();

        CustomerAddressEditPage::at($I)
            ->入力_姓('姓0501')
            ->入力_名('名0501')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市西区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        $I->see('大阪市西区', 'div.ec-addressList div:nth-child(1) div.ec-addressList__address');

        CustomerAddressListPage::at($I)
            ->削除(1);

        $I->wait(1);

        // 確認
        $I->see('お届け先は登録されていません。', '#page_mypage_delivery > div.ec-layoutRole > div.ec-layoutRole__contents > div > div > div:nth-child(2) > p');
    }

    public function mypage_退会手続き未実施(AcceptanceTester $I)
    {
        $I->wantTo('EF0507-UC03-T01 Mypage 退会手続き 未実施');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>退会手続き
        MyPage::go($I)
            ->退会手続き();

        // 会員退会手続きへ
        $I->click('div.ec-withdrawRole form button');

        // 未実施
        $I->click('div.ec-withdrawConfirmRole form a.ec-withdrawConfirmRole__cancel');

        MyPage::at($I);
    }

    /**
     * @group vaddy
     */
    public function mypage_退会手続き(AcceptanceTester $I)
    {
        $I->wantTo('EF0507-UC03-T02 Mypage 退会手続き');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)
            ->退会手続き();

        // 会員退会手続きへ
        $I->click('div.ec-withdrawRole form button');

        // 実施
        $I->click('div.ec-withdrawConfirmRole form button');
        $I->see('退会手続き', 'div.ec-pageHeader h1');
        $I->see('退会が完了いたしました', 'div.ec-withdrawCompleteRole div.ec-reportHeading');
        $I->click('div.ec-withdrawCompleteRole a.ec-blockBtn--cancel');

        // TOPページヘ遷移する
        $I->see('新着情報', '.ec-secHeading__ja');
    }
}
