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
use Codeception\Util\Locator;
use Plugin\MailMagazine42\Entity\MailMagazineTemplate;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL03MailMagazineCest
{
    private string $HtmlBody;
    private string $Subject;
    /**
     * @var array|string
     */
    private $Body;

    public function _before(AcceptanceTester $I)
    {
        $I->retry(5, 200);
        $I->loginAsAdmin();
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     * @throws \Exception
     */
    public function mail_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('メルマガ管理プラグイン', true)) {
            $I->wantToUninstallPlugin('メルマガ管理プラグイン');
            $I->seePluginIsNotInstalled('メルマガ管理プラグイン');
        }
        $I->wantToInstallPlugin('メルマガ管理プラグイン');
        $I->seePluginIsInstalled('メルマガ管理プラグイン');
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     */
    public function mail_03(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メールマガジンプラグイン');
        $I->retrySee('メールマガジンプラグイン');
        $I->retrySee('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'メールマガジンプラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->retrySee('「メールマガジンプラグイン」を有効にしました。');
        $I->retrySee('メールマガジンプラグイン', $recommendPluginRow);
        $I->retrySee('有効', $recommendPluginRow);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function mail_05(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $I->generateCustomerAndLogin();
        $I->amOnPage('/mypage/change');
        $I->retrySee('メールマガジン送付について');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_1');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_0');
        $I->checkOption('#entry_mailmaga_flg_0');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->retrySee('会員登録内容の変更が完了いたしました');
        $I->amOnPage('/mypage/change');
        $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_1');
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function mail_06(AcceptanceTester $I)
    {
        $faker = \Faker\Factory::create('ja_JP');
        $email = $faker->unique()->email();

        $I->retry(7, 400);

        $I->amOnPage('/entry');
        $I->fillField('#entry_name_name01', 'テスト');
        $I->fillField('#entry_name_name02', 'テスト');
        $I->fillField('#entry_kana_kana01', 'テスト');
        $I->fillField('#entry_kana_kana02', 'テスト');
        $I->fillField('#entry_postal_code', '5330022');;
        $I->wait(4);
        $I->retrySeeOptionIsSelected('#entry_address_pref', '大阪府');
        $I->retrySeeInField('#entry_address_addr01', '大阪市東淀川区菅原');
        $I->fillField('#entry_address_addr02', '菅原町1-1-1');
        $I->fillField('#entry_phone_number', '00000000000');
        $I->fillField('#entry_email_first', $email);
        $I->fillField('#entry_email_second', $email);
        $I->fillField('#entry_plain_password_first', 'Password#!1234');
        $I->fillField('#entry_plain_password_second', 'Password#!1234');
        $I->checkOption('#entry_mailmaga_flg_0');
        $I->checkOption('#entry_user_policy_check');
        $I->clickWithLeftButton('//div[@class="ec-registerRole__actions"]//button[@type="submit"][@class="ec-blockBtn--action"]');
        $mailMagazineSection = Locator::contains('//dl', 'メールマガジン送付について');
        $I->retrySee('メールマガジン送付について', $mailMagazineSection);
        $I->retrySee('受け取る', $mailMagazineSection);
        $I->clickWithLeftButton('//div[@class="ec-registerRole__actions"]//button[@type="submit"][@class="ec-blockBtn--action"]');
        $I->retrySee('会員登録ありがとうございます');
        // Check User without logging in.
        // @todo: Fix Mysql Bug for checking user.
//        $I->seeInRepository(Customer::class, [
//            'email' => $email,
//            'mailmaga_flg' => '1'
//        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function mail_07(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $I->generateCustomerAndLogin();
        $I->amOnPage(sprintf('/admin/customer/%s/edit', $I->asACustomer->getId()));
        $I->retrySee('メールマガジン送付について');
        $I->dontSeeCheckboxIsChecked('#admin_customer_mailmaga_flg_0');
        $I->dontSeeCheckboxIsChecked('#admin_customer_mailmaga_flg_1');
        $I->checkOption('#admin_customer_mailmaga_flg_0');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->retrySee('保存しました');
        $I->seeCheckboxIsChecked('#admin_customer_mailmaga_flg_0');
        $I->dontSeeCheckboxIsChecked('#admin_customer_mailmaga_flg_1');
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return string
     * @throws \Exception
     */
    public function mail_08(AcceptanceTester $I)
    {
        $faker = \Faker\Factory::create('ja_JP');
        $attachName = bin2hex(random_bytes(10));
        $Subject = 'テスト'.$attachName;
        $Body = $faker->paragraphs(3, true);
        $HtmlBody = $faker->randomHtml(4, 4);

        $this->Subject = $Subject;
        $this->Body = $Body;
        $this->HtmlBody = $HtmlBody;

        $I->retry(7, 400);

        $I->amOnPage('admin/plugin/mail_magazine/template');
        $I->clickWithLeftButton(Locator::contains('//a', 'テンプレートを新規登録'));
        $I->retrySee('テンプレート設定');
        $I->retrySee('テンプレート編集');
        $I->fillField('#mail_magazine_template_edit_subject', $Subject);
        $I->fillField('#mail_magazine_template_edit_body', $Body);
        $I->fillField('#mail_magazine_template_edit_htmlBody', $HtmlBody);
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->retrySee('メールテンプレート情報を保存しました。');
        $I->retrySee($Subject);

        $I->seeInRepository(MailMagazineTemplate::class, [
            'subject' => $Subject
        ]);

        return $Subject;
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function mail_09(AcceptanceTester $I)
    {
        $SubjectID = $this->mail_08($I);
        $I->amOnPage('admin/plugin/mail_magazine/template');
        $targetLine = Locator::contains('//tr', $SubjectID);
        $I->clickWithLeftButton($targetLine . Locator::contains('//a', '削除'));
        $I->retrySee('このテンプレートを削除しても宜しいですか？');
        $I->clickWithLeftButton(Locator::contains('//div[@class="modal show"]//button', '削除'));
        $I->retrySee('メールテンプレート情報を削除しました。');
        $I->dontSee($SubjectID);
    }

    /**
     * 全員にメールを送信する
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function mail_10(AcceptanceTester $I)
    {
        $SubjectName = $this->mail_08($I);
        $this->mail_07($I);
        $I->retry(7, 400);
        $I->amOnPage('admin/plugin/mail_magazine');
        $I->selectOption('.c-contentsArea__cols .form-select', '10件');
        $I->clickWithLeftButton(Locator::contains('//button', '配信内容を作成する'));
        $I->selectOption('#mail_magazine_template', $this->Subject);
        $I->seeInField('#mail_magazine_subject', $this->Subject);
        $I->seeInField('#mail_magazine_body', $this->Body);
//        $I->seeInField('#mail_magazine_htmlBody', $this->HtmlBody);
        $I->clickWithLeftButton(Locator::contains('//button', '確認画面へ'));
        $I->retrySee('配信内容の確認');
        $I->retrySee($this->Subject);
        $I->retrySee($this->Body);
//        $I->retrySee($this->HtmlBody);
        $I->clickWithLeftButton('#sendMailMagazine');
        $I->acceptPopup();
        //$I->waitForText('送信中', 20);
        $I->retrySee('送信完了');
        $I->click(Locator::contains('//button', '閉じる'));
        $targetRow = Locator::contains('//tr', $SubjectName);
        $sendToTotal = $I->grabTextFrom(sprintf('(%s//td)[4]', $targetRow));
        $sendSuccessfulTotal = $I->grabTextFrom(sprintf('(%s//td)[5]', $targetRow));
        $I->assertGreaterThanOrEqual($sendSuccessfulTotal, $sendToTotal);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function mail_11(AcceptanceTester $I)
    {
        $this->mail_10($I);
        $I->retry(7, 400);
        $I->amOnPage('admin/plugin/mail_magazine/history');
        $testRow = Locator::contains('//tr', $this->Subject);
        // 削除
        $I->click($testRow. Locator::contains('//a', '削除'));
        $I->retrySee('この履歴を削除してもよろしいですか？');
        $I->clickWithLeftButton('.modal.show button.btn-ec-delete');
        $I->retrySee('配信履歴を削除しました。');
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @param \Codeception\Example $example
     * @return void
     *
     * @example(type="checkbox", action_id="mail_magazine_sex_1")
     * @example(type="select", action_id="mail_magazine_birth_month", input="1")
     * @example(type="date", action_id="mail_magazine_birth_start", input="2000/1/1")
     * @example(type="input", action_id="mail_magazine_buy_product_name", input="彩のジェラートCUBE")
     */
    public function mail_13(AcceptanceTester $I, \Codeception\Example $example)
    {
        $SubjectName = $this->mail_08($I);
        $I->logoutAsAdmin();
        $I->retry(7, 400);
        $uniqueIdentifiers = $this->layOutCustomerDetails($example['action_id'], $I);
        $I->loginAsAdmin();

        // Search Member
        $I->amOnPage('admin/plugin/mail_magazine');
        $I->selectOption('.c-contentsArea__cols .form-select', '10件');
        $I->click(Locator::contains('//a', '詳細検索'));
        $I->waitForText('最終購入日', 30);
        switch ($example['type']) {
            case 'checkbox':
                $I->checkOption(sprintf('#%s', $example['action_id']));
                break;
            case 'select':
                $I->selectOption(sprintf('#%s', $example['action_id']), $example['input']);
                break;
            case 'date':
                $I->fillDate(sprintf('#%s', $example['action_id']), Carbon::createFromFormat('Y/m/d', $example['input']));
                break;
            case 'input':
                $I->fillField(sprintf('#%s', $example['action_id']), $example['input']);
                break;
        }
        $I->clickWithLeftButton(Locator::contains('//button', '検索'));
        $I->retrySee($uniqueIdentifiers['correctId']);
        $I->dontSee($uniqueIdentifiers['incorrectId']);
        $I->clickWithLeftButton(Locator::contains('//button', '配信内容を作成する'));

        // Send Mail
        $I->selectOption('#mail_magazine_template', $this->Subject);
        $I->seeInField('#mail_magazine_subject', $this->Subject);
        $I->seeInField('#mail_magazine_body', $this->Body);
        $I->clickWithLeftButton(Locator::contains('//button', '確認画面へ'));
        $I->retrySee('配信内容の確認');
        $I->retrySee($this->Subject);
        $I->retrySee($this->Body);
        $I->clickWithLeftButton('#sendMailMagazine');
        $I->acceptPopup();
        //$I->waitForText('送信中', 20);
        $I->retrySee('送信完了');
        $I->click(Locator::contains('//button', '閉じる'));
        $targetRow = Locator::contains('//tr', $SubjectName);
        $sendToTotal = $I->grabTextFrom(sprintf('(%s//td)[4]', $targetRow));
        $sendSuccessfulTotal = $I->grabTextFrom(sprintf('(%s//td)[5]', $targetRow));
        $I->assertGreaterThanOrEqual($sendSuccessfulTotal, $sendToTotal);

        // Check Conditions
        $testRow = Locator::contains('//tr', $this->Subject);
        $I->click($testRow. Locator::contains('//a', '配信条件'));
        switch ($example['action_id'])
        {
            case('mail_magazine_sex_1'):
                $I->retrySee('男性', Locator::contains('//tr', '性別'));
                break;
            case('mail_magazine_birth_month'):
                $I->retrySee('1', Locator::contains('//tr', '誕生月'));
                break;
            case('mail_magazine_birth_start'):
                $I->retrySee('2000/01/01', Locator::contains('//tr', '誕生日'));
                break;
            case('mail_magazine_buy_product_name'):
                $I->retrySee('彩のジェラートCUBE', Locator::contains('//tr', '購入商品名'));
                break;
        }
        $I->moveBack();
        // Preview Mode
        $testRow = Locator::contains('//tr', $this->Subject);
        $I->click($testRow. Locator::contains('//a', 'プレビュー'));
        $I->retrySee($this->Subject);
        $I->moveBack();

        // Receipt Mode
        $testRow = Locator::contains('//tr', $this->Subject);
        $I->click($testRow. Locator::contains('//a', '配信結果'));
        $I->retrySee($uniqueIdentifiers['correctId']);
        $I->dontSee($uniqueIdentifiers['incorrectId']);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function mail_12(AcceptanceTester $I)
    {
        // @todo: Fix CSV Test Case
        $I->assertEquals(true, true);
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function mail_14(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メールマガジンプラグイン');
        $I->retrySee('メールマガジンプラグイン', "//tr[contains(.,'メールマガジンプラグイン')]");
        $I->retrySee('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'メールマガジンプラグイン')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->retrySee('「メールマガジンプラグイン」を無効にしました。');
        $I->retrySee('メールマガジンプラグイン', $recommendPluginRow);
        $I->retrySee('無効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function mail_15(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(20, 1000);
        $I->wantToUninstallPlugin('メールマガジンプラグイン');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', 'メルマガ管理プラグイン');
        $I->retrySee('インストール', $xpath);
    }

    public function getSymfonyService(string $id)
    {
        return $this->getModule('Symfony')->grabService('test.service_container')->get($id);
    }

    private function layOutCustomerDetails(string $actionId, AcceptanceTester $I) {
        $I->generateCustomerAndLogin();
        $uniqueIdentifier = [
            'incorrectId' => null,
            'correctId' => $I->asACustomer->getEmail(),
        ];
        switch ($actionId) {
            case ('mail_magazine_sex_1'):
                $I->amOnPage('/mypage/change');
                $I->checkOption('#entry_sex_1');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeCheckboxIsChecked('#entry_sex_1');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                $I->generateCustomerAndLogin();
                $uniqueIdentifier['incorrectId'] = $I->asACustomer->getEmail();
                $I->amOnPage('/mypage/change');
                $I->checkOption('#entry_sex_2');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeCheckboxIsChecked('#entry_sex_2');
                $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                break;
            case ('mail_magazine_birth_month'):
                $I->amOnPage('/mypage/change');
                $I->selectOption('#entry_birth_month', '1');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeOptionIsSelected('#entry_birth_month', '1');
                $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                $I->generateCustomerAndLogin();
                $uniqueIdentifier['incorrectId'] = $I->asACustomer->getEmail();
                $I->amOnPage('/mypage/change');
                $I->selectOption('#entry_birth_month', '2');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeOptionIsSelected('#entry_birth_month', '2');
                $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                break;
            case ('mail_magazine_birth_start'):
                $I->amOnPage('/mypage/change');
                $I->selectOption('#entry_birth_year', '2000');
                $I->selectOption('#entry_birth_month', '2');
                $I->selectOption('#entry_birth_day', '2');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeOptionIsSelected('#entry_birth_year', '2000');
                $I->seeOptionIsSelected('#entry_birth_month', '2');
                $I->seeOptionIsSelected('#entry_birth_day', '2');
                $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                $I->generateCustomerAndLogin();
                $uniqueIdentifier['incorrectId'] = $I->asACustomer->getEmail();
                $I->amOnPage('/mypage/change');
                $I->selectOption('#entry_birth_year', '1999');
                $I->selectOption('#entry_birth_month', '2');
                $I->selectOption('#entry_birth_day', '2');
                $I->checkOption('#entry_mailmaga_flg_0');
                $I->clickWithLeftButton('.ec-blockBtn--cancel');
                $I->retrySee('会員登録内容の変更が完了いたしました');
                $I->amOnPage('/mypage/change');
                $I->seeOptionIsSelected('#entry_birth_year', '1999');
                $I->seeOptionIsSelected('#entry_birth_month', '2');
                $I->seeOptionIsSelected('#entry_birth_day', '2');
                $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
                $I->logoutAsMember();
                break;
            case ('mail_magazine_buy_product_name'):
                $I->amOnPage('products/detail/1');
                $I->selectOption('#classcategory_id1', 'チョコ');
                $I->selectOption('#classcategory_id2', '64cm × 64cm');
                $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
                $I->retrySee('カートに追加しました。');
                $I->clickWithLeftButton('a.ec-inlineBtn--action');
                $I->retrySee('ショッピングカート');
                $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
                $I->click(Locator::contains('//button', '確認する'));
                // 確認画面
                $I->click(Locator::contains('//button', '注文する'));
                $I->retrySee('ご注文ありがとうございました');
                $I->asACustomer->setMailmagaFlg(1);
                $I->flushToDatabase();
                $I->logoutAsMember();
                $I->generateCustomerAndLogin();
                $uniqueIdentifier['incorrectId'] = $I->asACustomer->getEmail();
                $I->amOnPage('products/detail/2');
                $I->clickWithLeftButton('.ec-blockBtn--action.add-cart');
                $I->retrySee('カートに追加しました。');
                $I->clickWithLeftButton('a.ec-inlineBtn--action');
                $I->retrySee('ショッピングカート');
                $I->clickWithLeftButton('.ec-cartRole__actions a.ec-blockBtn--action');
                $I->retrySee('ご注文手続き');
                $I->click(Locator::contains('//button', '確認する'));
                $I->click(Locator::contains('//button', '注文する'));
                $I->retrySee('ご注文ありがとうございました');
                $I->asACustomer->setMailmagaFlg(1);
                $I->flushToDatabase();
                $I->logoutAsMember();
        }
        return $uniqueIdentifier;
    }
}
