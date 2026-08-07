<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Faq;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductTag;
use Eccube\Entity\Tag;
use Eccube\Entity\TaxRule;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ProductTagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Tests\Fixture\Generator;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\StringUtil;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ProductControllerTest extends AbstractAdminWebTestCase
{
    protected ?ProductRepository $productRepository = null;

    protected ?ProductTagRepository $productTagRepository = null;
    protected ?BaseInfo $baseInfo = null;

    protected ?TaxRuleRepository $taxRuleRepository = null;

    protected ?ProductStatusRepository $productStatusRepository = null;

    protected ?string $imageDir = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->baseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->taxRuleRepository = $this->entityManager->getRepository(TaxRule::class);
        $this->productStatusRepository = $this->entityManager->getRepository(ProductStatus::class);
        $this->productTagRepository = $this->entityManager->getRepository(ProductTag::class);
        // Phase (b): 検索時, ID の重複を防ぐため事前に 10 件 Product を投入する.
        // CSV の id レンジ (6001-9040) は初期データ (id 1, 2) と衝突しないため事前削除不要.
        // 詳細は tests/Eccube/Tests/Fixture/csv/product-list-mass/README.md を参照.
        $this->loadCsvFixtures('product-list-mass');
        $this->imageDir = sys_get_temp_dir().'/'.sha1((string) mt_rand());
        $fs = new Filesystem();
        $fs->mkdir($this->imageDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->imageDir);
        parent::tearDown();
    }

    public function createFormData()
    {
        $faker = $this->getFaker();

        $price01 = (string) $faker->randomNumber(5);
        if (mt_rand(0, 1)) {
            $price01 = number_format((int) $price01);
        }

        $price02 = (string) $faker->randomNumber(5);
        if (mt_rand(0, 1)) {
            $price02 = number_format((int) $price02);
        }

        return [
            'class' => [
                'sale_type' => 1,
                'price01' => $price01,
                'price02' => $price02,
                'stock' => $faker->randomNumber(3),
                'stock_unlimited' => 0,
                'code' => $faker->word(),
                'sale_limit' => null,
                'delivery_duration' => '',
            ],
            'name' => $faker->word(),
            'product_image' => [],
            'description_detail' => $faker->realText,
            'description_list' => $faker->paragraph,
            'Category' => [],
            'Tag' => [1],
            'search_word' => $faker->word(),
            'free_area' => $faker->realText,
            'order_memo' => $faker->realText,
            'Status' => 1,
            'note' => $faker->realText,
            'tags' => [],
            'images' => [],
            'add_images' => [],
            'delete_images' => [],
            // FAQ 欄を描画したテンプレートからの送信であることを示すセンチネル.
            // 実際のフォームでは @admin/Content/faq_collection.twig が常に出力する.
            'faqs_rendered' => '1',
            Constant::TOKEN_NAME => 'dummy',
        ];
    }

    public function testRoutingAdminProductProduct()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductNew()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_product_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testProductSearchAll()
    {
        $AllProducts = $this->productRepository->findAll();
        $cnt = count($AllProducts);
        $this->createProduct();
        $cnt++;

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => '',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product'), $post);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '検索結果：'.$cnt.'件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('表示件数100件テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：13件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
    }

    public function testProductSearchByName()
    {
        $TestProduct = $this->createProduct();
        $TestProduct->setName(StringUtil::random());
        $this->entityManager->persist($TestProduct);
        $this->entityManager->flush();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => $TestProduct->getName(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product'), $post);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('表示件数100件テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
    }

    public function testProductSearchById()
    {
        $TestProduct = $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => $TestProduct->getId(),
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product'), $post);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数100件テスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 100]);
        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify();

        // 表示件数入力値は正しくない場合はデフォルトのの表示件数になるテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['page_count' => 999999]);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form > div:nth-child(4) > span')->text();
        $this->verify();

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_page', ['page_no' => 1]), ['status' => 1]);

        $this->expected = '100件';
        $this->actual = $crawler->filter('select.form-select > option:selected')->text();
        $this->verify();
    }

    public function testProductSearchByIdZero()
    {
        $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => 99999999,
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product'), $post);
        $this->expected = '検索条件に合致するデータが見つかりませんでした';
        $this->actual = $crawler->filter('div.text-center.text-muted.mb-4.h5')->text();
        $this->verify();
    }

    public function testProductSearchByNameZero()
    {
        $this->createProduct();

        $post = [
            'admin_search_product' => [
                Constant::TOKEN_NAME => 'dummy',
                'id' => 'not Exists product name',
                'category_id' => '',
                'create_date_start' => '',
                'create_date_end' => '',
                'update_date_start' => '',
                'update_date_end' => '',
            ],
        ];

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product'), $post);
        $this->expected = '検索条件に合致するデータが見つかりませんでした';
        $this->actual = $crawler->filter('div.text-center.text-muted.mb-4.h5')->text();
        $this->verify();
    }

    public function testRoutingAdminProductProductEdit()
    {
        $TestProduct = $this->createProduct();

        $id = $this->productRepository
            ->findOneBy(['name' => $TestProduct->getName()])
            ->getId();

        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_product_edit', ['id' => $id]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $rUrl = $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        // 編集前の更新日時を取得
        /** @var Product $PreProduct */
        $PreProduct = $this->productRepository->findOneBy(['id' => $Product->getId()]);
        $PreUpdateDate = $PreProduct->getUpdateDate();
        $this->assertInstanceOf(\DateTime::class, $PreUpdateDate);

        // sleep(3) を避けるため update_date を 3 秒前に巻き戻す.
        // SaveEventSubscriber::preUpdate が Doctrine flush 時に
        // updateDate を NOW で強制上書きするため、ORM 経由ではなく
        // DBAL で直接 UPDATE して preUpdate を回避する.
        $threeSecondsAgo = new \DateTime('-3 seconds');
        $this->entityManager->getConnection()->update(
            'dtb_product',
            ['update_date' => $threeSecondsAgo->format('Y-m-d H:i:s')],
            ['id' => $Product->getId()]
        );
        $this->entityManager->refresh($PreProduct);
        $preTimestamp = $PreProduct->getUpdateDate()->getTimestamp();

        $formData['return_link'] = $this->generateUrl('admin_product_category');
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($formData['return_link']));

        $EditedProduct = $this->productRepository->find($Product->getId());
        $this->expected = $formData['name'];
        $this->assertInstanceOf(Product::class, $EditedProduct);
        $this->actual = $EditedProduct->getName();
        $this->verify();

        // 商品の更新日時が更新されているか確認
        /** @var \DateTime $EditedUpdateDate */
        $EditedUpdateDate = $EditedProduct->getUpdateDate();
        $editedTimestamp = $EditedUpdateDate->getTimestamp();

        $this->assertNotSame($preTimestamp, $editedTimestamp);
    }

    /**
     * 商品編集経由で商品ごとFAQを追加・更新・削除する保存経路を検証する.
     *
     * CollectionType + allow_delete + Product::$Faqs の orphanRemoval を通るため、
     * 送信内容から漏れた FAQ が削除される（＝全削除事故が起き得る）最も危険な経路。
     */
    public function testEditWithProductFaq()
    {
        $Product = $this->createProduct(null, 0);
        $productId = $Product->getId();
        $faqRepository = $this->entityManager->getRepository(Faq::class);

        // FAQ を2件付与して保存
        $formData = $this->createFormData();
        $formData['faqs'] = [
            [
                'question' => '発送はいつですか',
                'answer' => 'ご注文から3営業日以内に発送します',
                'sort_no' => '1',
                'visible' => '1',
            ],
            [
                'question' => '返品できますか',
                'answer' => '到着後8日以内なら可能です',
                'sort_no' => '2',
                'visible' => '0',
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_product_product_edit', ['id' => $productId])
        ));

        $this->entityManager->clear();
        /** @var Faq[] $Faqs */
        $Faqs = $faqRepository->findBy(['Product' => $productId], ['sort_no' => 'ASC']);
        $this->assertCount(2, $Faqs);
        $this->assertSame('発送はいつですか', $Faqs[0]->getQuestion());
        $this->assertSame(Faq::FAQ_TYPE_PRODUCT, $Faqs[0]->getFaqType());
        $this->assertTrue($Faqs[0]->isVisible());
        $this->assertFalse($Faqs[1]->isVisible());
        $deletedId = $Faqs[1]->getId();

        // 2件目を除外し1件目を更新して再送信 → orphanRemoval で2件目が削除される
        $formData['faqs'] = [
            [
                'question' => '発送はいつですか（更新）',
                'answer' => '当日出荷に変更しました',
                'sort_no' => '1',
                'visible' => '1',
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_product_product_edit', ['id' => $productId])
        ));

        $this->entityManager->clear();
        $Remaining = $faqRepository->findBy(['Product' => $productId]);
        $this->assertCount(1, $Remaining);
        $this->assertSame('発送はいつですか（更新）', $Remaining[0]->getQuestion());
        $this->assertNotInstanceOf(Faq::class, $faqRepository->find($deletedId));
    }

    /**
     * FAQ欄を描画していないテンプレートからの保存では、既存の商品ごとFAQが維持されることを検証する.
     *
     * app/template/admin/Product/product.twig を上書きして FAQ 欄を落としている店舗を想定。
     * faqs キーも faqs_rendered も送信されないため、ProductType の PRE_SUBMIT が faqs を
     * フォームから取り除き、orphanRemoval による全削除を発生させない。
     */
    public function testEditKeepsProductFaqWhenCollectionIsNotRendered()
    {
        $Product = $this->createProduct(null, 0);
        $productId = $Product->getId();
        $faqRepository = $this->entityManager->getRepository(Faq::class);

        $formData = $this->createFormData();
        $formData['faqs'] = [
            [
                'question' => '上書きテンプレートでも残るか',
                'answer' => '残る',
                'sort_no' => '1',
                'visible' => '1',
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->entityManager->clear();
        $this->assertCount(1, $faqRepository->findBy(['Product' => $productId]));

        // FAQ欄が描画されていない画面からの保存（faqs / faqs_rendered ともに送信されない）
        $formData = $this->createFormData();
        unset($formData['faqs_rendered']);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_product_product_edit', ['id' => $productId])
        ));

        $this->entityManager->clear();
        /** @var Faq[] $Faqs */
        $Faqs = $faqRepository->findBy(['Product' => $productId]);
        $this->assertCount(1, $Faqs);
        $this->assertSame('上書きテンプレートでも残るか', $Faqs[0]->getQuestion());
    }

    /**
     * FAQ欄を描画した画面で全行を削除した保存では、商品ごとFAQが全削除されることを検証する.
     *
     * JS が行を DOM ごと除去するため、全行削除時も faqs キーは送信されない。
     * faqs_rendered の有無だけが「未描画」との違いになる。
     */
    public function testEditRemovesAllProductFaqWhenAllRowsDeleted()
    {
        $Product = $this->createProduct(null, 0);
        $productId = $Product->getId();
        $faqRepository = $this->entityManager->getRepository(Faq::class);

        $formData = $this->createFormData();
        $formData['faqs'] = [
            [
                'question' => 'UIで全行削除されるか',
                'answer' => 'される',
                'sort_no' => '1',
                'visible' => '1',
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->entityManager->clear();
        $this->assertCount(1, $faqRepository->findBy(['Product' => $productId]));

        // 全行を削除した状態での保存（faqs キーは無いが faqs_rendered は送信される）
        $formData = $this->createFormData();
        $this->assertSame('1', $formData['faqs_rendered']);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_product_product_edit', ['id' => $productId])
        ));

        $this->entityManager->clear();
        $this->assertCount(0, $faqRepository->findBy(['Product' => $productId]));
    }

    public function testDisplayProduct()
    {
        $productClassNum = 0;
        $Product = $this->createProduct('Test', $productClassNum);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])
        );

        // Only have 1 div button
        $this->expected = 1;
        $this->actual = $crawler->filter('#standardConfig > div > div')->count();
        $this->verify();
    }

    public function testDisplayProductHasClass()
    {
        $productClassNum = 3;
        $Product = $this->createProduct('Test', $productClassNum);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])
        );

        $expected = '規格1';
        $actual = $crawler->filter('#standardConfig > div > table')->text();
        $this->assertStringContainsString($expected, $actual);

        $this->expected = $productClassNum;
        $this->actual = $crawler->filter('#standardConfig > div > table > tbody > tr')->count();
        $this->verify();
    }

    public function testDelete()
    {
        $Product = $this->createProduct();

        $Tag = new Tag();
        $Tag->setName('Tag-102')->setSortNo(999);
        $this->entityManager->persist($Tag);

        $ProductTag = new ProductTag();
        $ProductTag->setProduct($Product);
        $ProductTag->setTag($Tag);
        $this->entityManager->persist($ProductTag);

        $Product->addProductTag($ProductTag);
        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        $params = [
            'id' => $Product->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $productTagId = $Product->getProductTag()->first()->getId();

        $this->client->request(Request::METHOD_DELETE, $this->generateUrl('admin_product_product_delete', $params));

        $rUrl = $this->generateUrl('admin_product_page', ['page_no' => 1]).'?resume=1';

        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        $this->assertNotInstanceOf(Product::class, $this->productRepository->find($params['id']));

        $this->assertNotInstanceOf(ProductTag::class, $this->productTagRepository->find($productTagId));
    }

    public function testCopy()
    {
        $Product = $this->createProduct();
        $AllProducts = $this->productRepository->findAll();
        $params = [
            'id' => $Product->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product_product_copy', $params));

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $AllProducts2 = $this->productRepository->findAll();
        $this->expected = count($AllProducts) + 1;
        $this->actual = count($AllProducts2);
        $this->verify();
    }

    /**
     * 商品コピーで商品ごとFAQが複製され、コピー元のFAQが残ることを検証する.
     *
     * copyProperties() が $Faqs の PersistentCollection をコピー元と共有した状態で
     * persist()＋flush() される経路を通るため、orphanRemoval: true との組み合わせで
     * コピー元が消えないことを固定する（安全性が ORM の実行順に依存している）。
     */
    public function testCopyWithProductFaq()
    {
        $Product = $this->createProduct();
        $faqRepository = $this->entityManager->getRepository(Faq::class);

        $Faq = new Faq();
        $Faq->setQuestion('コピー元FAQ')
            ->setAnswer('コピー元の回答')
            ->setSortNo(1)
            ->setVisible(true)
            ->setProduct($Product);
        $Product->addFaq($Faq);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        $sourceId = $Product->getId();
        $sourceFaqId = $Faq->getId();

        $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_product_product_copy', [
            'id' => $sourceId,
            Constant::TOKEN_NAME => 'dummy',
        ]));
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->entityManager->clear();

        // コピー元のFAQが残っている（orphanRemoval で消えない）。
        $SourceFaq = $faqRepository->find($sourceFaqId);
        $this->assertInstanceOf(Faq::class, $SourceFaq);
        $this->assertSame('コピー元FAQ', $SourceFaq->getQuestion());

        // コピー先にFAQが複製されている。
        $CopiedFaqs = $faqRepository->createQueryBuilder('f')
            ->where('f.Product != :Product')
            ->andWhere('f.question = :question')
            ->setParameter('Product', $sourceId)
            ->setParameter('question', 'コピー元FAQ')
            ->getQuery()
            ->getResult();
        $this->assertCount(1, $CopiedFaqs);
        $this->assertSame(Faq::FAQ_TYPE_PRODUCT, $CopiedFaqs[0]->getFaqType());
    }

    /**
     * @param $taxRate
     * @param $expected
     */
    #[DataProvider(methodName: 'dataNewProductProvider')]
    public function testNewWithPostTaxRate($taxRate, $expected)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $formData = $this->createFormData();

        $formData['class']['tax_rate'] = $taxRate;
        // When
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', (string) $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp) - 2];
        $Product = $this->productRepository->find($productId);

        $this->expected = $expected;
        $Taxrule = $this->taxRuleRepository->findOneBy(['Product' => $Product]);
        $taxRate = is_null($taxRate) ? null : $Taxrule?->getTaxRate();
        $this->actual = $taxRate;
        $this->assertSame($this->expected, $this->actual);
    }

    /**
     * 在庫なしで絞り込んで CSV 出力するテスト.
     */
    public function testExportWithFilterNoStock(): void
    {
        $testProduct = $this->createProduct('Product with stock 01');
        $this->createProduct('Product with stock 02', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $testProduct->getProductClasses()->first();
        $ProductClass->setStock('0');
        $ProductClass->getProductStock()->setStock('0');
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();

        $searchForm['id'] = 'Product with stock';

        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // 検索フォームの在庫で「在庫なし」に絞り込む.
        $searchForm['stock'] = [ProductStock::OUT_OF_STOCK];
        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('在庫なしで絞り込んだ検索結果件数の確認テスト');

        $content = $this->exportProductCsv();
        $this->assertStringContainsString('Product with stock 01', $content);
        $this->assertStringNotContainsString('Product with stock 02', $content);
    }

    /**
     * 非公開で絞り込んで CSV 出力するテスト.
     */
    public function testExportWithFilterPrivate(): void
    {
        $testProduct = $this->createProduct('Product with status 01', 0);
        $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $this->assertInstanceOf(ProductStatus::class, $display);
        $testProduct->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // 検索フォームの公開ステータスで「非公開」に絞り込む.
        $searchForm['status'] = [ProductStatus::DISPLAY_HIDE];
        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('非公開で絞り込んだ検索結果件数の確認テスト');

        $content = $this->exportProductCsv();
        $this->assertStringContainsString('Product with status 01', $content);
        $this->assertStringNotContainsString('Product with status 02', $content);
    }

    /**
     * 公開で絞り込んで CSV 出力するテスト.
     */
    public function testExportWithFilterPublic(): void
    {
        $this->createProduct('Product with status 01', 0);
        $testProduct02 = $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $this->assertInstanceOf(ProductStatus::class, $display);
        $testProduct02->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        // 検索フォームの公開ステータスで「公開」に絞り込む.
        $searchForm['status'] = [ProductStatus::DISPLAY_SHOW];
        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('公開で絞り込んだ検索結果件数の確認テスト');

        $content = $this->exportProductCsv();
        $this->assertStringContainsString('Product with status 01', $content);
        $this->assertStringNotContainsString('Product with status 02', $content);
    }

    /**
     * 公開・非公開を絞り込まずに CSV 出力するテスト.
     */
    public function testExportWithAll(): void
    {
        $this->createProduct('Product with status 01', 0);
        $testProduct02 = $this->createProduct('Product with status 02', 1);
        $display = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $this->assertInstanceOf(ProductStatus::class, $display);
        $testProduct02->setStatus($display);
        $this->entityManager->flush();

        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product with status';

        // 公開ステータスを絞り込まない場合は公開・非公開の両方が対象になる.
        $crawler = $this->searchProduct($searchForm);
        $this->expected = '検索結果：2件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        $content = $this->exportProductCsv();
        $this->assertStringContainsString('Product with status 01', $content);
        $this->assertStringContainsString('Product with status 02', $content);
    }

    /**
     * Test search + export product with list product order by product_id
     */
    public function testExportWithOrderByProduct()
    {
        $Products = $this->createProducts(10, [
            'productClassNum' => 0,
            'nameTemplate' => static fn (int $i): string => 'Product name '.($i + 1),
        ]);
        $expectedIds = array_reverse(array_map(static fn ($p) => $p->getId(), $Products));

        // 更新日をすべて同一日時に更新
        $qb = $this->entityManager->createQueryBuilder();
        $qb->update(Product::class, 'p')
            ->set('p.update_date', ':update_date')
            ->where('p.name LIKE :name')
            ->setParameter('update_date', new \DateTime())
            ->setParameter('name', 'Product name%')
            ->getQuery()
            ->execute();

        // 商品名：Product nameで検索
        $searchForm = $this->createSearchForm();
        $searchForm['id'] = 'Product name';

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify('検索結果件数の確認テスト');

        $csvExportUrl = $crawler->filter('.btn-ec-regular')->selectLink('CSVダウンロード')->link()->getUri();
        $this->client->request(Request::METHOD_GET, $csvExportUrl);

        $content = $this->client->getInternalResponse()->getContent();
        $this->assertMatchesRegularExpression('/Product name [10-1]/', $content);

        // get list product after call admin_product_export function
        $arr = explode("\n", $content);
        // unset header
        unset($arr[0]);
        $actualIds = [];
        foreach ($arr as $v) {
            if (!empty($v)) {
                $data = explode(',', $v);
                $actualIds[] = (int) $data[0];
            }
        }

        $this->assertSame($expectedIds, $actualIds);
    }

    public static function dataNewProductProvider()
    {
        return [
            [null, null],
            ['0', '0'],
            ['1', '1'],
        ];
    }

    /**
     * 個別税率設定のテストケース
     * 個別税率設定を有効にし、商品編集時に更新されることを確認する
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1547
     *
     * @param string|null $before 更新前の税率
     * @param string|null $after POST値
     * @param string|null $expected 期待値
     */
    #[DataProvider(methodName: 'dataEditProductProvider')]
    public function testEditWithPostTaxRate(?string $before, ?string $after, ?string $expected)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $Product = $this->createProduct(null, 0);
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $formData = $this->createFormData();

        if ($after !== null) {
            $formData['class']['tax_rate'] = $after;
        }
        if ($before !== null) {
            $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
            $TaxRule = new TaxRule();
            $TaxRule->setProductClass($ProductClass)
                ->setCreator($Product->getCreator())
                ->setProduct($Product)
                ->setRoundingType($RoundingType)
                ->setTaxRate($before)
                ->setTaxAdjust('0')
                ->setApplyDate(new \DateTime());
            $ProductClass->setTaxRule($TaxRule);
            $this->entityManager->persist($TaxRule);
            $this->entityManager->flush();
        }

        // When
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()])));

        $this->expected = $expected;
        $TaxRule = $this->taxRuleRepository->findOneBy(['Product' => $Product, 'ProductClass' => $ProductClass]);

        if (is_null($TaxRule)) {
            $this->actual = null;
            $this->assertNull($TaxRule);
        } else {
            $this->actual = $TaxRule->getTaxRate();
        }

        $this->assertSame($this->expected, $this->actual);
    }

    /**
     * 個別税率設定をした場合の RoundingType のテストケース
     *
     * @param string|null $tax_rate 個別税率
     * @param string|null $currentRoundingTypeId 現在の RoundingType ID
     * @param string|null $expected RoundingType ID の期待値
     * @param bool $isNew 商品を新規作成の場合 true
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/2114
     */
    #[DataProvider(methodName: 'dataEditRoundingTypeProvider')]
    public function testEditWithCurrnetRoundingType(?string $tax_rate, ?int $currentRoundingTypeId, ?int $expected, ?bool $isNew)
    {
        // Give
        $this->baseInfo->setOptionProductTaxRule(true);
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();

        if ($tax_rate !== null) {
            $formData['class']['tax_rate'] = $tax_rate;
        }
        if ($currentRoundingTypeId !== null) {
            $RoundingType = $this->entityManager->find(RoundingType::class, $currentRoundingTypeId);
            $TaxRule = new TaxRule();
            $TaxRule->setProductClass()
                ->setCreator($Product->getCreator())->setProduct()
                ->setRoundingType($RoundingType)
                ->setTaxRate($tax_rate)
                ->setTaxAdjust('0')
                ->setApplyDate(new \DateTime('-1 days'));
            $this->entityManager->persist($TaxRule);
            $this->entityManager->flush();
        }
        $url = $isNew ? $this->generateUrl('admin_product_product_new') :
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]);
        // When
        $this->client->request(
            Request::METHOD_POST,
            $url,
            ['admin_product' => $formData]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', (string) $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp) - 2];
        $EditProduct = $this->productRepository->find($productId);

        $TaxRule = $this->taxRuleRepository->getByRule($EditProduct);
        if ($tax_rate !== null) {
            $this->assertInstanceOf(TaxRule::class, $TaxRule);
            $this->expected = $expected;
            $this->actual = $TaxRule->getRoundingType()->getId();
            $this->verify('tax_rate が設定されている場合は税率設定と RoundingType が取得できる');
        } else {
            $this->expected = $expected;
            $this->actual = RoundingType::ROUND;
            $this->verify('tax_rate が設定されていない場合は初期設定の RoundingType');
        }
    }

    /**
     * Product export test
     */
    public function testProductExport(): void
    {
        $productName = 'test01';
        $this->createProduct($productName);

        $this->searchProduct($this->createSearchForm());
        $content = $this->exportProductCsv();

        $this->assertStringContainsString($productName, $content);
    }

    /**
     * Test for bulk action update product status
     */
    public function testProductBulkProductStatus()
    {
        // case invalid method
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_bulk_product_status', ['id' => ProductStatus::DISPLAY_SHOW]),
            []
        );
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        // case invalid product status id
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_bulk_product_status', ['id' => 0]),
            []
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        // case true
        $productIds = [];
        /** @var Product[] $Products */
        $Products = $this->productRepository->findBy([], [], 5);
        foreach ($Products as $Product) {
            $productIds[] = $Product->getId();
        }

        $productStatuses = [
            ProductStatus::DISPLAY_SHOW,
            ProductStatus::DISPLAY_HIDE,
            ProductStatus::DISPLAY_ABOLISHED,
        ];
        foreach ($productStatuses as $productStatusId) {
            $ProductStatus = $this->productStatusRepository->find($productStatusId);
            $this->client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_product_bulk_product_status', ['id' => $productStatusId]),
                ['ids' => $productIds]
            );
            $result = $this->productRepository->findBy(['id' => $productIds, 'Status' => $ProductStatus]);
            $this->assertCount(count($productIds), $result);
        }
    }

    public function testLoadProductClass()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_classes_load', ['id' => 1]),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_ECCUBE_CSRF_TOKEN' => 'dummy',
            ]
        );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * アップロード画像が save_image にコピーされているか確認する.
     */
    public function testEditWithImage()
    {
        $path = __DIR__.'/../../../../../../html/upload';

        $fs = new Filesystem();
        // アップロード画像が存在する場合は削除しておく
        $fs->remove($path.'/temp_image/new_image.png');
        $fs->remove($path.'/save_image/new_image.png');

        $fs->copy(
            $path.'/save_image/sand-1.png',
            $path.'/temp_image/new_image.png'
        );

        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $formData['add_images'][] = 'new_image.png';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $rUrl = $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        $this->assertFileExists($path.'/save_image/new_image.png', 'temp_image の画像が save_imageにコピーされている');
        $fs->remove($path.'/temp_image/new_image.png');
        $fs->remove($path.'/save_image/new_image.png');
    }

    /**
     * アップロード画像に相対パスが指定された場合は save_image にコピーされない.
     */
    public function testEditWithImageFailure()
    {
        $path = __DIR__.'/../../../../../../html/upload';

        $fs = new Filesystem();
        // アップロード画像が存在する場合は削除しておく
        $fs->remove($path.'/temp_image/new_image.png');
        $fs->remove($path.'/save_image/new_image.png');

        $fs->copy(
            $path.'/save_image/sand-1.png',
            $path.'/temp_image/new_image.png'
        );

        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $formData['add_images'][] = '../temp_image/new_image.png';

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $this->assertStringContainsString('画像のパスが不正です。', $crawler->html());

        $this->assertFileDoesNotExist($path.'/save_image/new_image.png', 'temp_image の画像が save_imageにコピーされない');
        $fs->remove($path.'/temp_image/new_image.png');
        $fs->remove($path.'/save_image/new_image.png');
    }

    public function testImageLoad()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_image_load', ['source' => 'sand-1.png']),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testImageLoadWithFailure()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_image_load', ['source' => '../save_image/sand-1.png']),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testImageLoadWithNotfound()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_product_image_load', ['source' => 'xxxxx.png']),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * 個別税率編集時のテストデータ
     * 更新前の税率 / POST値 / 期待値の配列を返す
     */
    public static function dataEditProductProvider(): array
    {
        return [
            ['0', '0', '0'],
            ['0', '1', '1'],
            ['0', null, null],
            ['1', '0', '0'],
            ['1', '1', '1'],
            ['1', null, null],
            [null, '0', '0'],
            [null, '1', '1'],
            [null, null, null],
        ];
    }

    /**
     * 個別税率編集時のテストデータ
     * 個別税率 / 現在の RoundingType / RoundingType 期待値 / 新規商品 の配列を返す
     */
    public static function dataEditRoundingTypeProvider(): array
    {
        return [
            [null, null, RoundingType::ROUND, false],
            ['10', null, RoundingType::ROUND, false],
            ['10', RoundingType::CEIL, RoundingType::CEIL, false],
            ['10', RoundingType::CEIL, RoundingType::CEIL, true],
        ];
    }

    /**
     * 商品検索を実行し, 検索条件をセッションに保持する.
     *
     * @param array<string, mixed> $searchForm
     */
    private function searchProduct(array $searchForm): Crawler
    {
        return $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product'),
            ['admin_search_product' => $searchForm]
        );
    }

    /**
     * セッションに保持された検索条件で商品 CSV を出力し, 出力内容を返す.
     *
     * admin_product_export は StreamedResponse を返すため, 出力バッファで内容を受け取る.
     */
    private function exportProductCsv(): string
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_export'));

        $Response = $this->client->getResponse();
        $this->assertInstanceOf(StreamedResponse::class, $Response);
        $this->assertSame('application/octet-stream', $Response->headers->get('Content-Type'));

        return $this->client->getInternalResponse()->getContent();
    }

    private function createSearchForm(): array
    {
        return [
            Constant::TOKEN_NAME => 'dummy',
            'id' => '',
            'category_id' => '',
            'create_date_start' => '',
            'create_date_end' => '',
            'update_date_start' => '',
            'update_date_end' => '',
        ];
    }

    /**
     * 商品画像を削除する際に、他の商品画像が参照しているファイルは削除せず、それ以外は削除することをテスト
     */
    public function testDeleteImage()
    {
        /** @var Generator $generator */
        $generator = static::getContainer()->get(Generator::class);
        $Product1 = $generator->createProduct(null, 0, true);
        $Product2 = $generator->createProduct(null, 0, true);

        $DuplicatedImage = $Product1->getProductImage()->first();
        $this->assertInstanceOf(ProductImage::class, $DuplicatedImage);

        $NotDuplicatedImage = $Product1->getProductImage()->last();
        $this->assertInstanceOf(ProductImage::class, $NotDuplicatedImage);

        $NewProduct2Image = new ProductImage();
        $NewProduct2Image
            ->setProduct($Product2)
            ->setFileName($DuplicatedImage->getFileName())
            ->setSortNo(999)
        ;
        $Product2->addProductImage($NewProduct2Image);
        $this->entityManager->persist($NewProduct2Image);
        $this->entityManager->flush();

        $data = $this->createFormData();
        $data['delete_images'] = $Product1->getProductImage()->map(static fn (ProductImage $ProductImage) => $ProductImage->getFileName())->toArray();
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product1->getId()]),
            ['admin_product' => $data]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $dir = __DIR__.'/../../../../../../html/upload/save_image/';
        $this->assertFileExists($dir.$DuplicatedImage->getFileName());
        $this->assertFileDoesNotExist($dir.$NotDuplicatedImage->getFileName());
    }

    public function testDeleteAndDeleteProductImage()
    {
        /** @var Generator $generator */
        $generator = static::getContainer()->get(Generator::class);
        $Product1 = $generator->createProduct(null, 0, true);
        $Product2 = $generator->createProduct(null, 0, true);

        $DuplicatedImage = $Product1->getProductImage()->first();
        $this->assertInstanceOf(ProductImage::class, $DuplicatedImage);

        $NotDuplicatedImage = $Product1->getProductImage()->last();
        $this->assertInstanceOf(ProductImage::class, $NotDuplicatedImage);

        $NewProduct2Image = new ProductImage();
        $NewProduct2Image
            ->setProduct($Product2)
            ->setFileName($DuplicatedImage->getFileName())
            ->setSortNo(999)
        ;
        $Product2->addProductImage($NewProduct2Image);
        $this->entityManager->persist($NewProduct2Image);
        $this->entityManager->flush();

        $params = [
            'id' => $Product1->getId(),
            Constant::TOKEN_NAME => 'dummy',
        ];

        $this->client->request(Request::METHOD_DELETE, $this->generateUrl('admin_product_product_delete', $params));

        $rUrl = $this->generateUrl('admin_product_page', ['page_no' => 1]).'?resume=1';

        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));

        $dir = __DIR__.'/../../../../../../html/upload/save_image/';
        $this->assertFileExists($dir.$DuplicatedImage->getFileName());
        $this->assertFileDoesNotExist($dir.$NotDuplicatedImage->getFileName());
    }

    public function test絵文字()
    {
        $name = '🍣🍺';
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_list', ['name' => $name]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $message = $crawler->filter('.ec-searchnavRole__counter > span')->text();
        $this->assertSame('お探しの商品は見つかりませんでした', $message);

        // 絵文字の商品を登録
        $this->createProduct($name);

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_list', ['name' => $name]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $message = $crawler->filter('.ec-searchnavRole__counter > span')->text();
        $this->assertSame('1件', $message);
    }

    /**
     * フリーエリア/商品説明/商品説明(一覧)で
     * 危険なXSS htmlインジェクションが削除されたことを確認するテスト
     * 下記のものをチェックします。
     * ・ ID属性の追加
     * ・ <script> スクリプトインジェクション
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5372
     *
     * @param mixed $formName
     * @param mixed $methodName
     */
    #[DataProvider(methodName: 'purifyTarget')]
    public function testPurifyXssInput($formName, $methodName): void
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();

        $formData[$formName] = "<div id='dangerous-id' class='safe_to_use_class'>
            <p>商品説明文テスト</p>
            <script>alert('XSS Attack')</script>
            <a href='https://www.google.com'>safe html</a>
        </div>";

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $crawler = new Crawler($Product->$methodName());

        // <div>タグから危険なid属性が削除されていることを確認する。
        // Find that dangerous id attributes are removed from <div> tags.
        $target = $crawler->filter('#dangerous-id');
        $this->assertCount(0, $target);

        // 安全なclass属性が出力されているかどうかを確認する。
        // Find if classes (which are safe) have been outputted
        $target = $crawler->filter('.safe_to_use_class');
        $this->assertCount(1, $target);

        // 安全なHTMLが存在するかどうかを確認する
        // Find if the safe HTML exists
        $this->assertStringContainsString('<p>商品説明文テスト</p>', $target->outerHtml());
        $this->assertStringContainsString('<a href="https://www.google.com">safe html</a>', $target->outerHtml());

        // 安全でないスクリプトが存在しないかどうかを確認する
        // Find if the unsafe script does not exist
        $this->assertStringNotContainsString("<script>alert('XSS Attack')</script>", $target->outerHtml());
    }

    public static function purifyTarget(): array
    {
        return [
            ['description_list', 'getDescriptionList'],
            ['description_detail', 'getDescriptionDetail'],
            ['free_area', 'getFreeArea'],
        ];
    }

    /**
     * 受注管理用メモが保存できることを確認する.
     */
    public function testEditWithOrderMemo(): void
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $formData['order_memo'] = '梱包時は割れ物注意';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
        // 保存後の永続化状態を確認するため DB から再読込する
        $this->entityManager->refresh($Product);
        $this->assertSame('梱包時は割れ物注意', $Product->getOrderMemo());
    }

    /**
     * 受注管理用メモが文字数上限を超えるとバリデーションエラーになることを確認する.
     */
    public function testEditWithOrderMemoOverMaxLength(): void
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $formData['order_memo'] = str_repeat('a', $this->eccubeConfig['eccube_lltext_len'] + 1);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['admin_product' => $formData]
        );

        // バリデーションエラーのため再描画され、リダイレクトしない
        $this->assertFalse($this->client->getResponse()->isRedirection());
    }
}
