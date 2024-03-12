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

namespace Plugin;

use AcceptanceTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Customer;
use Page\Admin\MailMagazineEditPage;
use Page\Admin\MailMagazineHistoryPage;
use Page\Admin\MailMagazinePage;
use Page\Admin\MailMagazineTemplateEditPage;
use Page\Admin\MailMagazineTemplatePage;

/**
 * @group plugin
 * @group vaddy
 */
class PL03MailMagazineCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function 新規登録(AcceptanceTester $I)
    {
        MailMagazineTemplatePage::go($I)
            ->新規作成();

        MailMagazineTemplateEditPage::at($I)
            ->入力_件名('test')
            ->入力_本文テキスト('{name} test')
            ->入力_本文HTML('<h1>{name}</h1> test')
            ->登録();

        MailMagazineTemplatePage::at($I);
    }

    public function 編集(AcceptanceTester $I)
    {
        MailMagazineTemplatePage::go($I)
            ->編集(1);

        MailMagazineTemplateEditPage::at($I)
            ->登録();

        MailMagazineTemplatePage::at($I);
    }

    public function プレビュー(AcceptanceTester $I)
    {
        MailMagazineTemplatePage::go($I)
            ->プレビュー(1);

        $I->wait(3);
    }

    public function 削除(AcceptanceTester $I)
    {
        MailMagazineTemplatePage::go($I)
            ->削除(1);

        MailMagazineTemplatePage::at($I);
    }

    public function 配信(AcceptanceTester $I)
    {
        /** @var EntityManager $entityManager */
        $entityManager = Fixtures::get('entityManager');
        $entityManager->createQueryBuilder()
            ->update(Customer::class, 'c')
            ->set('c.mailmaga_flg', true)
            ->getQuery()->execute();
        $entityManager->flush();

        MailMagazinePage::go($I)
            ->検索()
            ->配信内容を作成する();

        MailMagazineEditPage::at($I)
            ->入力_件名('test')
            ->入力_本文テキスト('test')
            ->入力_本文HTML('test')
            ->確認画面へ()
            ->テスト配信する()
            ->配信();
    }

    public function 配信履歴(AcceptanceTester $I)
    {
        MailMagazineHistoryPage::go($I)->プレビュー(1);
        MailMagazineHistoryPage::go($I)->配信条件(1);
        MailMagazineHistoryPage::go($I)->配信結果(1);
        MailMagazineHistoryPage::go($I)->削除(1);
    }
}
