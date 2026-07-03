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

namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\ClassCategory;
use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\ProductRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductControllerTest extends AbstractWebTestCase
{
    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ClassCategoryRepository
     */
    private $classCategoryRepository;

    /**
     * @var CustomerFavoriteProductRepository
     */
    private $customerFavoriteProductRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseInfoRepository = $this->entityManager->getRepository(BaseInfo::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->classCategoryRepository = $this->entityManager->getRepository(ClassCategory::class);
        $this->customerFavoriteProductRepository = $this->entityManager->getRepository(CustomerFavoriteProduct::class);
    }

    public function testRoutingList()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingDetail()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);

        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('product_add_favorite', ['id' => '1'])
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('mypage_login')));
    }

    /**
     * test with category id is invalid.
     */
    public function testCategoryNotFound()
    {
        $client = $this->client;
        $message = 'ご指定のカテゴリは存在しません';
        $crawler = $client->request('GET', $this->generateUrl('product_list', ['category_id' => 'XXX']));
        $this->assertStringContainsString($message, $crawler->html());
    }

    /**
     * test with category id is valid.
     */
    public function testCategoryFound()
    {
        $client = $this->client;
        $message = '商品が見つかりました';
        $crawler = $client->request('GET', $this->generateUrl('product_list', ['category_id' => '6']));
        $this->assertStringContainsString($message, $crawler->html());
    }

    /**
     * testProductClassSortByRank
     */
    public function testProductClassSortByRank()
    {
        /** @var ClassCategory $ClassCategory */
        // set チョコ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'チョコ']);
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        // set 抹茶 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '抹茶']);
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        // set バニラ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'バニラ']);
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $classCategory = $crawler->filter('#classcategory_id1')->text();
        // 選択してください, チョコ, 抹茶, バニラ sort by rank setup above.
        $this->expected = '選択してくださいチョコ抹茶バニラ';
        $this->actual = $classCategory;
        $this->verify();
    }

    /**
     * Test product can add favorite when out of stock.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAddWhenOutOfStock()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct('Product no stock', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(0);
        $ProductStock = $ProductClass->getProductStock();
        $ProductStock->setStock(0);
        $this->entityManager->flush();
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var Client $client */
        $client = $this->client;
        /** @var Crawler $crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 1: render check
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertStringContainsString('ただいま品切れ中です', $html);
        $this->assertStringContainsString('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 2: after add favorite check
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertStringContainsString('ただいま品切れ中です', $html);
        $this->assertStringContainsString('お気に入りから削除', $html);
    }

    /**
     * Test product can add favorite
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct('Product stock', 1);
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var Client $client */
        $client = $this->client;
        /** @var Crawler $crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 3: render check when 商品在庫>0
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertStringContainsString('カートに入れる', $html);
        $this->assertStringContainsString('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 4: after add favorite when 商品在庫>0
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertStringContainsString('カートに入れる', $html);
        $this->assertStringContainsString('お気に入りから削除', $html);
    }

    /**
     * 商品詳細 → ログイン画面 → お気に入り追加 → 商品詳細(お気に入り登録済み)
     */
    public function testProductFavoriteAddThroughLogin()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct();
        $id = $Product->getId();

        $user = $this->createCustomer();

        /** @var Client $client */
        $client = $this->client;

        /** @var Crawler $crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // お気に入りに追加をクリック
        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();
        $client->submit($favoriteForm);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_login')));

        // ログインフォームへメールアドレス・パスワードを入力
        $crawler = $client->followRedirect();
        $loginForm = $crawler->selectButton('ログイン')->form();
        $loginForm['login_email'] = $user->getEmail();
        $loginForm['login_pass'] = 'password';

        // ログインをクリック
        $client->submit($loginForm);

        // ログイン実行後、お気に入り追加へリダイレクト
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('product_add_favorite', ['id' => $Product->getId()], UrlGeneratorInterface::ABSOLUTE_URL)));
        $crawler = $client->followRedirect();

        // お気に入り追加実行後、商品詳細ページへリダイレクト
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('product_detail', ['id' => $Product->getId()])));
        $crawler = $client->followRedirect();

        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertStringContainsString('お気に入りから削除', $html);
    }

    /**
     * 商品詳細ページの構造化データ
     */
    public function testProductStructureData()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => 2]));
        $json = json_decode(html_entity_decode($crawler->filter('script[type="application/ld+json"]')->html()));
        $this->assertSame('Product', $json->{'@type'});
        $this->assertSame('チェリーアイスサンド', $json->name);
        $this->assertSame(3080.00, $json->offers->price);
        $this->assertSame('InStock', $json->offers->availability);

        // 在庫なし商品のテスト
        $Product = $this->createProduct('Product no stock', 1);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(0);
        $ProductStock = $ProductClass->getProductStock();
        $ProductStock->setStock(0);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $Product->getId()]));
        $json = json_decode(html_entity_decode($crawler->filter('script[type="application/ld+json"]')->html()));
        $this->assertSame('Product no stock', $json->name);
        $this->assertSame('OutOfStock', $json->offers->availability);
    }

    /**
     * 一覧ページ metaタグのテスト
     */
    public function testMetaTagsInListPage()
    {
        // カテゴリ指定なし
        $url = $this->generateUrl('product_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);
        $this->assertSame('article', $crawler->filter('meta[property="og:type"]')->attr('content'));
        $this->assertSame($url, $crawler->filter('link[rel="canonical"]')->attr('href'));
        $this->assertSame($url, $crawler->filter('meta[property="og:url"]')->attr('content'));
        $this->assertCount(0, $crawler->filter('meta[name="robots"]'));

        // カテゴリ指定あり
        $url = $this->generateUrl('product_list', ['category_id' => 1], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);
        $this->assertSame($url, $crawler->filter('link[rel="canonical"]')->attr('href'));

        // 検索 0件 → noindex 確認
        $url = $this->generateUrl('product_list', ['category_id' => 1, 'name' => 'notfoundquery'], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);
        $this->assertStringContainsString('お探しの商品は見つかりませんでした', $crawler->html());
        $this->assertSame('noindex', $crawler->filter('meta[name="robots"]')->attr('content'));
    }

    /**
     * 詳細ページ metaタグのテスト
     */
    public function testMetaTagsInDetailPage()
    {
        $product = $this->productRepository->find(2);
        $description_detail = 'またそのなかでいっしょになったたくさんのひとたち、ファゼーロとロザーロ、羊飼のミーロや、顔の赤いこどもたち、地主のテーモ、山猫博士のボーガント・デストゥパーゴなど、いまこの暗い巨きな石の建物のなかで考えていると、みんなむかし風のなつかしい青い幻燈のように思われます。';
        $description_list = 'では、わたくしはいつかの小さなみだしをつけながら、しずかにあの年のイーハトーヴォの五月から十月までを書きつけましょう。';

        // 商品に description_list と description_detail を設定
        //  → meta descriotion には description_listが設定される
        $product->setDescriptionList($description_list);
        $product->setDescriptionDetail($description_detail);
        $this->entityManager->flush();
        $expected_desc = mb_substr($description_list, 0, 120, 'utf-8');

        $url = $this->generateUrl('product_detail', ['id' => 2], UrlGeneratorInterface::ABSOLUTE_URL);
        $imgPath = $this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL).'html/upload/save_image/'.$product->getMainListImage()->getFileName();

        $crawler = $this->client->request('GET', $url);

        $this->assertSame($expected_desc, $crawler->filter('meta[name="description"]')->attr('content'));
        $this->assertSame($expected_desc, $crawler->filter('meta[property="og:description"]')->attr('content'));
        $this->assertSame('og:product', $crawler->filter('meta[property="og:type"]')->attr('content'));
        $this->assertSame($url, $crawler->filter('link[rel="canonical"]')->attr('href'));
        $this->assertSame($url, $crawler->filter('meta[property="og:url"]')->attr('content'));
        $this->assertSame($imgPath, $crawler->filter('meta[property="og:image"]')->attr('content'));
        $this->assertCount(0, $crawler->filter('meta[name="robots"]'));

        // 商品の description_list を削除
        //   → meta description には description_detail が設定される
        $product->setDescriptionList(null);
        $this->entityManager->flush();
        $expected_desc = mb_substr($description_detail, 0, 120, 'utf-8');

        $crawler = $this->client->request('GET', $url);

        $this->assertSame($expected_desc, $crawler->filter('meta[name="description"]')->attr('content'));
        $this->assertSame($expected_desc, $crawler->filter('meta[property="og:description"]')->attr('content'));
    }

    /**
     * 詳細ページ 在庫なし時の metaタグのテスト
     */
    public function testMetaTagsInOutOfStockDetailPage()
    {
        $Product = $this->createProduct('Product out of stock', 1);
        $id = $Product->getId();
        $productUrl = $this->generateUrl('product_detail', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);

        // 在庫切れ商品
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(0);
        $ProductStock = $ProductClass->getProductStock();
        $ProductStock->setStock(0);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $productUrl);

        $this->assertSame('noindex', $crawler->filter('meta[name="robots"]')->attr('content'));
    }

    /**
     * お気に入り削除テスト（正常系）
     *
     * フロントエンドでは削除リンクにCSRFトークンが付与され、JavaScriptが
     * POSTリクエスト + _method: delete で送信する。このテストでは同じフローを再現する。
     */
    public function testProductFavoriteDelete()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);

        $Product = $this->createProduct('Product favorite delete test', 1);
        $Customer = $this->createCustomer();
        $this->loginTo($Customer);

        // お気に入りを追加
        $this->customerFavoriteProductRepository->addFavorite($Customer, $Product);
        $this->entityManager->flush();

        // 商品詳細ページを取得してCSRFトークンを抽出
        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $Product->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 削除リンクからCSRFトークンを取得
        $deleteLink = $crawler->filter('a#favorite[data-method="delete"]');
        $this->assertGreaterThan(0, $deleteLink->count());
        $token = $deleteLink->attr('token-for-anchor');

        // JavaScriptと同様にPOSTリクエストで送信（_method: deleteを含む）
        $this->client->request(
            'POST',
            $this->generateUrl('product_delete_favorite', ['id' => $Product->getId()]),
            [
                '_token' => $token,
                '_method' => 'delete',
            ]
        );

        // 商品詳細ページにリダイレクトされることを確認
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('product_detail', ['id' => $Product->getId()])
        ));

        // お気に入りが削除されていることを確認
        $this->entityManager->clear();
        $CustomerFavoriteProduct = $this->customerFavoriteProductRepository->findOneBy([
            'Customer' => $Customer,
            'Product' => $Product,
        ]);
        $this->assertNull($CustomerFavoriteProduct);
    }

    /**
     * お気に入り削除テスト（お気に入りが存在しない場合は400エラー）
     */
    public function testProductFavoriteDeleteWithNotFavorite()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);

        $Product = $this->createProduct('Product not favorite', 1);
        $Customer = $this->createCustomer();
        $this->loginTo($Customer);

        // 商品詳細ページを取得（お気に入り未登録なので削除リンクはない）
        $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $Product->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // Symfonyのテスト用CSRFトークンを使用
        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')
            ->getToken(\Eccube\Common\Constant::TOKEN_NAME)->getValue();

        // お気に入りに追加していない状態で削除を実行
        $this->client->request(
            'POST',
            $this->generateUrl('product_delete_favorite', ['id' => $Product->getId()]),
            [
                '_token' => $csrfToken,
                '_method' => 'delete',
            ]
        );

        // 400エラーが返されることを確認
        $this->expected = 400;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    /**
     * お気に入り削除テスト（未ログイン時はログインページにリダイレクト）
     *
     * Note: 未ログイン時はCSRFチェックより先にログインリダイレクトが発生するため、
     * CSRFトークンを含めずにテストしています。
     */
    public function testProductFavoriteDeleteWithNotLoggedIn()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);

        $Product = $this->createProduct('Product favorite delete not logged in', 1);

        // 未ログイン状態でお気に入り削除を実行
        $this->client->request(
            'POST',
            $this->generateUrl('product_delete_favorite', ['id' => $Product->getId()]),
            [
                '_method' => 'delete',
            ]
        );

        // ログインページにリダイレクトされることを確認
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('mypage_login')
        ));
    }
}
