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
use Codeception\Example;
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Doctrine\ORM\EntityManager;
use Plugin\ProductReview42\Entity\ProductReview;
use Plugin\ProductReview42\Entity\ProductReviewStatus;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL07ProductReviewCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     * @throws \Exception
     */
    public function review_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('商品レビュー管理プラグイン', true)) {
            $I->wantToUninstallPlugin('商品レビュー管理プラグイン');
            $I->seePluginIsNotInstalled('商品レビュー管理プラグイン');
        }
        $I->wantToInstallPlugin('商品レビュー管理プラグイン');
        $I->seePluginIsInstalled('商品レビュー管理プラグイン');
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '商品レビュー管理プラグイン');
        $I->see('商品レビュー管理プラグイン');
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'商品レビュー管理プラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「商品レビュー管理プラグイン」を有効にしました。');
        $I->see('商品レビュー管理プラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return ReviewData
     */
    public function review_03(AcceptanceTester $I)
    {
        $faker = \Faker\Factory::create('ja_JP');
        $reviewData = new ReviewData(
            $faker->userName(),
            $faker->url(),
            $faker->realText(20),
            $faker->realText(100)
        );
        // 商品詳細ページでレビュー投稿
        $this->writeFrontEndReviewNoLogin($I, $reviewData);
        return $reviewData;
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return ReviewData
     */
    public function review_04(AcceptanceTester $I)
    {
        $faker = \Faker\Factory::create('ja_JP');
        $reviewData = new ReviewData(
            $faker->userName(),
            $faker->url(),
            $faker->realText(20),
            $faker->realText(100)
        );
        $this->writeFrontEndReviewNoLogin($I, $reviewData);
        $rowIdentifier = Locator::contains('//tr', $reviewData->reviewer_name);

        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');
        $I->see($reviewData->reviewer_name, $rowIdentifier);
        $I->see('非公開', $rowIdentifier);
        $I->clickWithLeftButton($rowIdentifier . '//i[@class="fa fa-pencil fa-lg text-secondary"]');
        $I->seeInField('#product_review_reviewer_name', $reviewData->reviewer_name);
        $I->seeInField('#product_review_reviewer_url', $reviewData->reviewer_url);
        $I->seeInField('#product_review_title', $reviewData->title);
        $I->seeInField('#product_review_comment', $reviewData->comment);
        $I->selectOption('#product_review_Status', '公開');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');
        $I->seeOptionIsSelected('#product_review_Status', '公開');
        // フロント側
        $I->amOnPage('products/detail/1');
        $I->see('この商品のレビュー');
        $I->see($reviewData->reviewer_name);
        $I->seeInSource($reviewData->reviewer_url);
        $I->see($reviewData->title);
        $I->see($reviewData->comment);
        return $reviewData;
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_05(AcceptanceTester $I)
    {
        $reviewContents = $this->review_04($I);
        $rowIdentifier = Locator::contains('//tr', $reviewContents->reviewer_name);
        $faker = \Faker\Factory::create('ja_JP');
        $name = $faker->userName();
        $url = $faker->url();
        $title = $faker->realText(20);
        $comment = $faker->realText(100);

        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');
        $I->see($reviewContents->reviewer_name, $rowIdentifier);
        $I->see('公開', $rowIdentifier);
        $I->clickWithLeftButton($rowIdentifier . '//i[@class="fa fa-pencil fa-lg text-secondary"]');
        $I->seeInField('#product_review_reviewer_name', $reviewContents->reviewer_name);
        $I->seeInField('#product_review_reviewer_url', $reviewContents->reviewer_url);
        $I->seeInField('#product_review_title', $reviewContents->title);
        $I->seeInField('#product_review_comment', $reviewContents->comment);

        $I->fillField('#product_review_reviewer_name', $name);
        $I->fillField('#product_review_reviewer_url', $url);
        $I->fillField('#product_review_title', $title);
        $I->fillField('#product_review_comment', $comment);

        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');
        $I->seeOptionIsSelected('#product_review_Status', '公開');

        // フロント側
        $I->amOnPage('products/detail/1');
        $I->see('この商品のレビュー');
        $I->see($name);
        $I->seeInSource($url);
        $I->see($title);
        $I->see($comment);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_06(AcceptanceTester $I)
    {
        $reviewContents = $this->review_04($I);
        $rowIdentifier = Locator::contains('//tr', $reviewContents->reviewer_name);

        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');
        $I->see($reviewContents->reviewer_name, $rowIdentifier);
        $I->see('公開', $rowIdentifier);
        $I->clickWithLeftButton($rowIdentifier . '//i[@class="fa fa-pencil fa-lg text-secondary"]');
        $I->seeInField('#product_review_reviewer_name', $reviewContents->reviewer_name);
        $I->seeInField('#product_review_reviewer_url', $reviewContents->reviewer_url);
        $I->seeInField('#product_review_title', $reviewContents->title);
        $I->seeInField('#product_review_comment', $reviewContents->comment);
        $I->selectOption('#product_review_Status', '非公開');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');
        $I->seeOptionIsSelected('#product_review_Status', '非公開');

        // フロント側
        $I->amOnPage('products/detail/1');
        $I->see('この商品のレビュー');
        $I->dontSee($reviewContents->reviewer_name);
        $I->dontSeeInSource($reviewContents->reviewer_url);
        $I->dontSee($reviewContents->title);
        $I->dontSee($reviewContents->comment);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_07(AcceptanceTester $I)
    {
        $reviewContents = $this->review_03($I);
        $I->retry(7, 400);

        $rowIdentifier = Locator::contains('//tr', $reviewContents->reviewer_name);
        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');
        $I->see($reviewContents->reviewer_name, $rowIdentifier);
        $I->see('非公開', $rowIdentifier);
        $I->clickWithLeftButton($rowIdentifier . '//i[@class="fa fa-close fa-lg text-secondary"]');
        $I->retrySee('レビューを削除します');
        $I->clickWithLeftButton('//div[@class="modal fade show"]' . Locator::contains('//a', '削除'));
        $I->retrySee('商品レビューを削除しました。');
        $I->dontSee($reviewContents->reviewer_name);
        $I->dontSee($reviewContents->title);
        $I->dontSee($reviewContents->comment);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_08(AcceptanceTester $I)
    {
        $I->retry(7, 400);

        $resultCount = count($I->grabEntitiesFromRepository(ProductReview::class, [
            'Status' => '1'
        ]));
        $neededResults = 10 - $resultCount;


        if ($neededResults > 0) {
            for ($i = 0; $i < $neededResults; $i++) {
                $this->review_05($I);
            }
        }

        $I->amOnPage('admin/store/plugin');
        $reviewPluginRow = Locator::contains('//tr', '商品レビュー管理プラグイン');
        $I->clickWithLeftButton(sprintf("(%s//i[@class='fa fa-cog fa-lg text-secondary'])[1]", $reviewPluginRow));
        $I->fillField('#product_review_config_review_max', '5');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');

        $I->amOnPage('products/detail/1');
        $I->seeNumberOfElementsInDOM('//ul[@class="review_list"]/li', 5);

        $I->amOnPage('admin/store/plugin');
        $reviewPluginRow = Locator::contains('//tr', '商品レビュー管理プラグイン');
        $I->clickWithLeftButton(sprintf("(%s//i[@class='fa fa-cog fa-lg text-secondary'])[1]", $reviewPluginRow));
        $I->fillField('#product_review_config_review_max', '10');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');

        $I->amOnPage('products/detail/1');
        $I->seeNumberOfElementsInDOM('//ul[@class="review_list"]/li', 10);

        $I->amOnPage('admin/store/plugin');
        $reviewPluginRow = Locator::contains('//tr', '商品レビュー管理プラグイン');
        $I->clickWithLeftButton(sprintf("(%s//i[@class='fa fa-cog fa-lg text-secondary'])[1]", $reviewPluginRow));
        $I->fillField('#product_review_config_review_max', '5');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('登録しました。');

        $I->amOnPage('products/detail/1');
        $I->seeNumberOfElementsInDOM('//ul[@class="review_list"]/li', 5);

    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_09(AcceptanceTester $I)
    {
        $reviewDetails = $this->review_03($I);
        $I->retry(7, 400);
        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');
        $I->fillField('#product_review_search_multi', $reviewDetails->reviewer_name);
        $I->clickWithLeftButton(Locator::contains('//div[@class="c-outsideBlock__contents mb-5"]//button', '検索'));
        $I->see($reviewDetails->reviewer_name);
        $I->see($reviewDetails->title);
        $I->seeNumberOfElementsInDOM('//table[@class="table table-sm"]/tbody/tr', 1);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @param Example $example
     * @return void
     * @dataProvider searchFormProvider
     */
    public function review_10(AcceptanceTester $I, Example $example)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $name = $faker->userName();
        $url = $faker->url();
        $title = $faker->realText(20);
        $comment = $faker->realText(100);

        $I->amOnPage('admin/product_review/');
        $I->see('レビュー管理');

        /**
         * @var EntityManager $em
         */
        $entityManager = Fixtures::get('entityManager');

        $reviewTarget = new ProductReview();
        $reviewTarget->setReviewerName($name);
        $reviewTarget->setReviewerUrl($url);
        $reviewTarget->setTitle($title);
        $reviewTarget->setComment($comment);
        $reviewTarget->setRecommendLevel(!empty(@$example['target']['recommend_level']) ? $example['target']['recommend_level'] : 5);
        $reviewTarget->setSex($entityManager->getRepository('Eccube\Entity\Master\Sex')->find(!empty(@$example['target']['sex']) ? $example['target']['sex'] : 1));
        $reviewTarget->setProduct($entityManager->getRepository('Eccube\Entity\Product')->find(!empty(@$example['target']['product_id']) ? $example['target']['product_id'] : 1));
        $reviewTarget->setStatus($entityManager->getRepository('Plugin\ProductReview42\Entity\ProductReviewStatus')->find(ProductReviewStatus::SHOW));
        $reviewTarget->setCreateDate(!empty(@$example['target']['create_date']) ?  Carbon::createFromFormat('Y/m/d H:i', $example['target']['create_date'])->toDateTime() : new \DateTime());
        $reviewTarget->setUpdateDate(new \DateTime());
        $entityManager->persist($reviewTarget);

        $name = $faker->userName();
        $url = $faker->url();
        $title = $faker->realText(20);
        $comment = $faker->realText(100);
        $reviewAvoid = new ProductReview();
        $reviewAvoid->setReviewerName($name);
        $reviewAvoid->setReviewerUrl($url);
        $reviewAvoid->setTitle($title);
        $reviewAvoid->setComment($comment);
        $reviewAvoid->setSex($entityManager->getRepository('Eccube\Entity\Master\Sex')->find(!empty(@$example['avoid']['sex']) ? $example['avoid']['sex'] :  1));
        $reviewAvoid->setProduct($entityManager->getRepository('Eccube\Entity\Product')->find(!empty(@$example['avoid']['product_id']) ? $example['avoid']['product_id'] : 1));
        $reviewAvoid->setRecommendLevel(!empty(@$example['avoid']['recommend_level']) ? $example['avoid']['recommend_level'] : 5);
        $reviewAvoid->setStatus($entityManager->getRepository('Plugin\ProductReview42\Entity\ProductReviewStatus')->find(ProductReviewStatus::HIDE));
        $reviewAvoid->setCreateDate(!empty(@$example['avoid']['create_date']) ? Carbon::createFromFormat('Y/m/d H:i', $example['avoid']['create_date'])->toDateTime() : new \DateTime());
        $reviewAvoid->setUpdateDate(new \DateTime());
        $entityManager->persist($reviewAvoid);
        $entityManager->flush();

        $I->clickWithLeftButton('//div[@href="#searchDetail"]');
        $I->retrySee('検索条件をクリア');

        switch ($example['search']['type']) {
            case 'option':
                $I->selectOption($example['search']['id'], $example['search']['value']);
                break;
            case 'date':
                $I->fillDate($example['search']['id'], Carbon::createFromFormat('Y/m/d', $example['search']['value']), 'jp');
                break;
            case 'checkbox':
                $I->checkOption($example['search']['id']);
                break;
            case 'input':
                $I->fillField($example['search']['id'], $example['search']['value']);
                break;
        }

        $I->clickWithLeftButton(Locator::contains('//div[@class="c-outsideBlock__contents mb-5"]//button', '検索'));
        $I->retrySee($reviewTarget->getReviewerName());
        $I->see($reviewTarget->getTitle());
        $I->dontSee($reviewAvoid->getReviewerName());
        $I->dontSee($reviewAvoid->getTitle());
    }

    /**
     * @group uninstall
     * @param AcceptanceTester $I
     * @return void
     */
    public function review_11(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '商品レビュー管理プラグイン');
        $I->see('商品レビュー管理プラグイン', "//tr[contains(.,'商品レビュー管理プラグイン')]");
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'商品レビュー管理プラグイン')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「商品レビュー管理プラグイン」を無効にしました。');
        $I->see('商品レビュー管理プラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    /**
     * @group uninstall
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function review_12(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(20, 1000);
        $I->wantToUninstallPlugin('商品レビュー管理プラグイン');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', '商品レビュー管理プラグイン');
        $I->see('インストール', $xpath);
    }

    private function writeFrontEndReviewNoLogin(AcceptanceTester $I, ReviewData $reviewData): AcceptanceTester
    {
        $I->amOnPage('products/detail/1');
        $I->retry(7, 400);
        $I->retrySee('彩のジェラートCUBE');
        $I->wait(5);
        $I->see('この商品のレビュー');
        $I->clickWithLeftButton(Locator::contains('//a', 'レビューを投稿'));

        $I->see('レビューを投稿');
        $I->see('彩のジェラートCUBE', Locator::contains('//dl', '商品名'));
        $I->fillField('#product_review_reviewer_name', $reviewData->reviewer_name);
        $I->fillField('#product_review_reviewer_url', $reviewData->reviewer_url);
        $I->checkOption('#product_review_sex_1');
        $I->checkOption('#product_review_recommend_level_0');
        $I->fillField('#product_review_title', $reviewData->title);
        $I->fillField('#product_review_comment', $reviewData->comment);
        $I->clickWithLeftButton('.ec-blockBtn--action');
        $I->see('下記の内容で送信してもよろしいでしょうか？');
        $I->see($reviewData->reviewer_name);
        $I->see($reviewData->reviewer_url);
        $I->see($reviewData->title);
        $I->see($reviewData->comment);
        $I->clickWithLeftButton('.ec-blockBtn--action');
        $I->see('ご投稿ありがとうございます。');
        $I->clickWithLeftButton('.ec-blockBtn--cancel');
        $I->see('★★★★★');
        $I->dontSee($reviewData->reviewer_name);
        $I->dontSee($reviewData->reviewer_url);
        $I->dontSee($reviewData->title);
        $I->dontSee($reviewData->comment);
        return $I;
    }

    protected function searchFormProvider(): array
    {
        return [
            [
                "target" => [
                    "recommend_level" => 5
                ],
                "avoid" => [
                    "recommend_level" => 4
                ],
                "search" => [
                    "type" => "option",
                    "id" => "#product_review_search_recommend_level",
                    "value" => "★★★★★"
                ]
            ],
//            [
//                "target" => [
//                    "create_date" => "2000/02/02 13:00"
//                ],
//                "avoid" => [
//                    "create_date" => "1998/02/01 13:00"
//                ],
//                "search" => [
//                    "type" => "date",
//                    "id" => "#product_review_search_review_start",
//                    "value" => "1999/02/01"
//                ]
//            ],
            [
                "target" => [
                    "sex" => 1
                ],
                "avoid" => [
                    "sex" => 2
                ],
                "search" => [
                    "type" => "checkbox",
                    "id" => "#product_review_search_sex_1"
                ]
            ],
            [
                "target" => [
                    "product_id" => 1
                ],
                "avoid" => [
                    "product_id" =>  2
                ],
                "search" => [
                    "type" => "input",
                    "id" => "#product_review_search_product_name",
                    "value" => "彩のジェラートCUBE"
                ]
            ]
        ];
    }
}

class ReviewData
{
    public string $reviewer_name;
    public string $reviewer_url;
    public string $title;
    public string $comment;

    /**
     *
     * @param $reviewer_name
     * @param $reviewer_url
     * @param $title
     * @param $comment
     */
    public function __construct($reviewer_name, $reviewer_url, $title, $comment)
    {
        $this->reviewer_name = $reviewer_name;
        $this->reviewer_url = $reviewer_url;
        $this->title = $title;
        $this->comment = $comment;
    }
}
