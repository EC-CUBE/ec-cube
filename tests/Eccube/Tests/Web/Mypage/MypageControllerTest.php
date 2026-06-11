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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\Shipping;
use Eccube\Tests\Fixture\Generator;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MypageControllerTest extends AbstractWebTestCase
{
    protected ?Customer $Customer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function testRoutingFavorite()
    {
        $this->logInTo($this->Customer);

        $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage_favorite'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingFavoriteDelete()
    {
        $this->logInTo($this->Customer);

        // before
        $TestFavorite = $this->newTestFavorite();
        $this->entityManager->persist($TestFavorite);
        $this->entityManager->flush();

        // main
        $redirectUrl = $this->generateUrl('mypage_favorite');
        // mypage_favorite_deleteはprocutt_idを受け取る
        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('mypage_favorite_delete', ['id' => $TestFavorite->getProduct()->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->entityManager->remove($TestFavorite);
        $this->entityManager->flush();
    }

    public function testRoutingOrder()
    {
        $this->loginTo($this->Customer);
        $client = $this->client;

        $Order = $this->createOrder($this->Customer);

        $client->request(Request::METHOD_PUT,
            $this->generateUrl('mypage_order', ['order_no' => $Order->getOrderNo()])
        );

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testLogin()
    {
        $this->logInTo($this->Customer);
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_login')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage')));
    }

    public function testLoginWithFailure()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_login')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndex()
    {
        $this->createOrder($this->Customer);
        $this->logInTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testHistory()
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = static::getContainer()->get(Generator::class)->createOrder($this->Customer, [$ProductClasses[0]], null,
            0, 0, OrderStatus::NEW);
        $this->loginTo($this->Customer);
        $client = $this->client;

        $client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_history', ['order_no' => $Order->getOrderNo()])
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testHistory404()
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = static::getContainer()->get(Generator::class)->createOrder($this->Customer, [$ProductClasses[0]], null,
            0, 0, OrderStatus::PROCESSING);
        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_history', ['order_no' => $Order->getOrderNo()])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testHistoryWithNotFound()
    {
        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_history', ['order_no' => 999999999])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    /**
     * 注文履歴詳細を閲覧可能なステータス (NEW) の受注を生成する.
     *
     * createOrder() の既定ステータスは PROCESSING (仮受注) で mypage_history が 404 になるため,
     * NEW へ遷移させてから返す.
     */
    private function createOrderForHistory(): Order
    {
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));

        return $Order;
    }

    /**
     * 受注に 2 つ目の配送先を追加し, お問い合わせ番号を設定する.
     */
    private function addShipping(Order $Order, ?string $trackingNumber): Shipping
    {
        /** @var Shipping $Base */
        $Base = $Order->getShippings()->first();

        $Shipping = new Shipping();
        $Shipping->copyProperties($this->Customer);
        $Shipping
            ->setOrder($Order)
            ->setPref($Base->getPref())
            ->setDelivery($Base->getDelivery())
            ->setShippingDeliveryName($Base->getShippingDeliveryName())
            ->setTrackingNumber($trackingNumber)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $Order->addShipping($Shipping);
        $this->entityManager->persist($Shipping);

        return $Shipping;
    }

    /**
     * 注文履歴詳細をログイン会員として取得する.
     */
    private function requestHistory(Order $Order): \Symfony\Component\DomCrawler\Crawler
    {
        $this->loginTo($this->Customer);

        return $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_history', ['order_no' => $Order->getOrderNo()])
        );
    }

    /**
     * お問い合わせ番号が入力されている場合, 注文履歴詳細に表示される.
     */
    public function testHistoryWithTrackingNumber()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber('1234567890123');
        $this->entityManager->flush();

        $crawler = $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('dt:contains("お問い合わせ番号")'));
        $this->assertStringContainsString('1234567890123', $crawler->text());
    }

    /**
     * お問い合わせ番号が null の場合, 項目自体が表示されない.
     */
    public function testHistoryWithoutTrackingNumber()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber(null);
        $this->entityManager->flush();

        $crawler = $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('dt:contains("お問い合わせ番号")'));
    }

    /**
     * お問い合わせ番号が空文字列の場合, 項目自体が表示されない.
     */
    public function testHistoryWithEmptyTrackingNumber()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber('');
        $this->entityManager->flush();

        $crawler = $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('dt:contains("お問い合わせ番号")'));
    }

    /**
     * 複数配送先がある場合, 入力済みの配送先ごとにお問い合わせ番号が表示される.
     */
    public function testHistoryWithMultipleShippings()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber('1111111111111');
        $this->addShipping($Order, '2222222222222');
        $this->entityManager->flush();

        $crawler = $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('1111111111111', $crawler->text());
        $this->assertStringContainsString('2222222222222', $crawler->text());
        // 配送先ごとに「お問い合わせ番号」ラベルが出力される (2 件).
        $this->assertCount(2, $crawler->filter('dt:contains("お問い合わせ番号")'));
    }

    /**
     * 複数配送先のうち一部のみ入力されている場合, 入力済みの配送先のみ表示される.
     */
    public function testHistoryWithMultipleShippingsPartiallyFilled()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber('1111111111111');
        // 2 つ目の配送先はお問い合わせ番号未入力.
        $this->addShipping($Order, null);
        $this->entityManager->flush();

        $crawler = $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('1111111111111', $crawler->text());
        // 入力済みは 1 件のみ.
        $this->assertCount(1, $crawler->filter('dt:contains("お問い合わせ番号")'));
    }

    /**
     * お問い合わせ番号に HTML 特殊文字が含まれていても自動エスケープされ, スクリプトが実行されない.
     */
    public function testHistoryTrackingNumberXss()
    {
        $Order = $this->createOrderForHistory();
        $Order->getShippings()->first()->setTrackingNumber('<script>alert("XSS")</script>');
        $this->entityManager->flush();

        $this->requestHistory($Order);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $html = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert', $html);
    }

    /**
     * お問い合わせ番号ラベルの翻訳定義 (日本語 / 英語).
     */
    public function testTrackingNumberLabelTranslations()
    {
        /** @var TranslatorInterface $translator */
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->assertSame('お問い合わせ番号', $translator->trans('front.mypage.tracking_number', [], null, 'ja'));
        $this->assertSame('Tracking No.', $translator->trans('front.mypage.tracking_number', [], null, 'en'));
    }

    /**
     * Paginator を経由したお気に入りの取得
     *
     * 主に正しくソートされているかチェックする.
     */
    public function testFavoriteWithPaginator()
    {
        // bulk 生成. createProduct を 30 回ループするのではなく、
        // createProducts(30) で 4 テーブル (product/image/class/stock) を一括投入する.
        $Products = $this->createProducts(30);
        $expectedIds = array_map(static fn ($p) => $p->getId(), $Products);

        foreach ($Products as $i => $Product) {
            $CustomerFavoriteProduct = new CustomerFavoriteProduct();
            $CustomerFavoriteProduct->setCustomer($this->Customer);
            // id とは 逆順に create_date を設定する.
            // 画面表示は create_date 降順なので, id 昇順にソートされるはず
            $CustomerFavoriteProduct->setCreateDate(new \DateTime('-'.$i.' days'));
            $CustomerFavoriteProduct->setUpdateDate(new \DateTime());
            $CustomerFavoriteProduct->setProduct($Product);
            $this->entityManager->persist($CustomerFavoriteProduct);
        }
        $this->entityManager->flush();

        $this->loginTo($this->Customer);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_favorite')
        );
        // 最初の画面で表示されているお気に入りの ID を取得する
        $actualIds = [];
        $nodes = $crawler->filterXPath('//div[@class="product_item"]/a[1]');
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (preg_match('/detail\/([0-9]+)/', (string) $href, $matched)) {
                $actualIds[] = $matched[1];
            }
        }
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = array_slice($expectedIds, 0, count($actualIds));
        $this->actual = $actualIds;
        $this->verify('画面表示は create_date 降順なので, id 昇順にソートされるはず');
    }

    private function newTestFavorite()
    {
        $CustomerFavoriteProduct = new CustomerFavoriteProduct();
        $CustomerFavoriteProduct->setCustomer($this->Customer);
        $Product = $this->entityManager->getRepository(Product::class)->find(1);
        $CustomerFavoriteProduct->setCreateDate(new \DateTime());
        $CustomerFavoriteProduct->setUpdateDate(new \DateTime());
        $CustomerFavoriteProduct->setProduct($Product);

        return $CustomerFavoriteProduct;
    }
}
