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
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Doctrine\Common\Collections\Criteria;
use Eccube\Entity\Customer;
use function PHPUnit\Framework\assertEquals;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL03MailMagazineCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {

    }

    /**
     * @param AcceptanceTester $I
     * @skip
     * @return void
     */
    public function mail_01(AcceptanceTester $I)
    {

    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function mail_02(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メールマガジンプラグイン');
        $I->see('メールマガジンプラグイン', "//tr[contains(.,'メールマガジンプラグイン')]");
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'メールマガジンプラグイン')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「メールマガジンプラグイン」を無効にしました。');
        $I->see('メールマガジンプラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function mail_03(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メールマガジンプラグイン');
        $I->see('メールマガジンプラグイン');
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'メールマガジンプラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「メールマガジンプラグイン」を有効にしました。');
        $I->see('メールマガジンプラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     * @skip
     * @return void
     */
    public function mail_04(AcceptanceTester $I)
    {

    }

    public function mail_05(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $this->generateTestMemberAndLogin($I);
        $I->amOnPage('/mypage/change');
        $I->see('メールマガジン送付について');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_1');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_0');
        $I->checkOption('#entry_mailmaga_flg_0');
        $I->clickWithLeftButton(Locator::contains('//button', '登録する'));
        $I->see('会員登録内容の変更が完了いたしました');
        $I->amOnPage('/mypage/change');
        $I->seeCheckboxIsChecked('#entry_mailmaga_flg_0');
        $I->dontSeeCheckboxIsChecked('#entry_mailmaga_flg_1');
    }

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
        $I->retrySeeOptionIsSelected('#entry_address_city', '大阪市東淀川区菅原');
        $I->fillField('#entry_address_addr02', '菅原町1-1-1');
        $I->fillField('#entry_phone_number', '00000000000');
        $I->fillField('#entry_email_first', $email);
        $I->fillField('#entry_email_second', $email);
        $I->fillField('#entry_plain_password_first', 'password');
        $I->fillField('#entry_plain_password_second', 'password');
        $I->checkOption('#entry_mailmaga_flg_0');
        $I->checkOption('#entry_user_policy_check');
        $I->clickWithLeftButton('//div[@class="ec-registerRole__actions"]//button[@type="submit"][@class="ec-blockBtn--action"]');
        $mailMagazineSection = Locator::contains('//dl', 'メールマガジン送付について');
        $I->see('メールマガジン送付について', $mailMagazineSection);
        $I->see('受け取る', $mailMagazineSection);
        $I->clickWithLeftButton('//div[@class="ec-registerRole__actions"]//button[@type="submit"][@class="ec-blockBtn--action"]');
        $I->retrySee('会員登録ありがとうございます');
        // Check User without logging in.
        $I->seeInRepository(Customer::class, [
            'email' => $email,
            Criteria::create()->where(
                Criteria::expr()->contains('plg_mailmagazine_flg', '1')
            ),
        ]);
    }


    private function generateTestMemberAndLogin(AcceptanceTester $I): void
    {
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
    }

    public function getSymfonyService(string $id)
    {
        return $this->getModule('Symfony')->grabService('test.service_container')->get($id);
    }

}
