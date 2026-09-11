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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Faq;
use Eccube\Entity\Product;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FaqControllerTest extends AbstractAdminWebTestCase
{
    private function createCommonFaq(): Faq
    {
        $Faq = new Faq();
        $Faq->setQuestion('よくある質問')
            ->setAnswer('回答')
            ->setSortNo(1)
            ->setVisible(true);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        return $Faq;
    }

    private function createProductFaq(): Faq
    {
        $Product = $this->createProduct();
        $Faq = new Faq();
        $Faq->setQuestion('商品FAQ質問')
            ->setAnswer('商品FAQ回答')
            ->setSortNo(1)
            ->setVisible(true)
            ->setProduct($Product);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        return $Faq;
    }

    public function testRoutingAdminContentFaq(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentFaqNew(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentFaqEdit(): void
    {
        $Faq = $this->createCommonFaq();

        $this->client->request(Request::METHOD_GET,
            $this->generateUrl('admin_content_faq_edit', ['id' => $Faq->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testFaqCreate(): void
    {
        // フォーム CSRF が有効なため、描画済みフォームを取得して送信する（実トークンを使う）。
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq_new'));
        $form = $crawler->filter('#form1')->form();
        $form['admin_faq[question]'] = 'テスト質問';
        $form['admin_faq[answer]'] = 'テスト回答';
        $form['admin_faq[sort_no]'] = '5';
        $form['admin_faq[visible]']->select('1');
        $this->client->submit($form);

        // 登録成功で編集画面へリダイレクトされる。
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString($this->generateUrl('admin_content_faq').'/', $location);
        $this->assertStringEndsWith('/edit', $location);

        // DB に永続化されていることを検証する。
        $this->entityManager->clear();
        $Faq = $this->entityManager->getRepository(Faq::class)->findOneBy(['question' => 'テスト質問']);
        $this->assertInstanceOf(Faq::class, $Faq);
        $this->assertSame('テスト回答', $Faq->getAnswer());
        $this->assertSame(Faq::FAQ_TYPE_COMMON, $Faq->getFaqType());
    }

    public function testFaqNewRendersEmptySortNo(): void
    {
        // 新規登録画面の表示順は空欄で開く。エンティティの既定値 0（NOT NULL 対策）が
        // そのまま入力欄に出ると、Assert\Range(min: 1) に掛かって登録できなくなる。
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq_new'));

        $this->assertSame('', $crawler->filter('#admin_faq_sort_no')->attr('value') ?? '');
    }

    public function testFaqCreateWithoutSortNoAssignsNextNumber(): void
    {
        // 表示順を空欄で登録すると 0 ではなく最大値 + 1 が採番される。
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq_new'));
        $form = $crawler->filter('#form1')->form();
        $form['admin_faq[question]'] = '表示順未入力の質問';
        $form['admin_faq[answer]'] = '回答';
        $form['admin_faq[sort_no]'] = '';
        $form['admin_faq[visible]']->select('1');
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $Faq = $this->entityManager->getRepository(Faq::class)->findOneBy(['question' => '表示順未入力の質問']);
        $this->assertInstanceOf(Faq::class, $Faq);
        $this->assertGreaterThan(0, $Faq->getSortNo());
    }

    public function testFaqCreateRejectsZeroSortNo(): void
    {
        // 表示順 0 はバリデーションで弾かれる（1 始まりに揃える）。
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq_new'));
        $form = $crawler->filter('#form1')->form();
        $form['admin_faq[question]'] = '表示順ゼロの質問';
        $form['admin_faq[answer]'] = '回答';
        $form['admin_faq[sort_no]'] = '0';
        $form['admin_faq[visible]']->select('1');
        $this->client->submit($form);

        // リダイレクトせず入力画面に留まる。
        $this->assertFalse($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $this->assertNotInstanceOf(Faq::class, $this->entityManager->getRepository(Faq::class)->findOneBy(['question' => '表示順ゼロの質問']));
    }

    public function testRoutingAdminContentFaqDelete(): void
    {
        $Faq = $this->createCommonFaq();
        $id = $Faq->getId();

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_content_faq_delete', ['id' => $id])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_faq')));

        // DB から削除されていることを検証する。
        $this->entityManager->clear();
        $this->assertNotInstanceOf(Faq::class, $this->entityManager->getRepository(Faq::class)->find($id));
    }

    public function testFaqEditReturns404ForNonCommonFaq(): void
    {
        // 商品FAQ（サイト共通以外）はサイト共通FAQ専用の編集画面では 404 になる。
        $Faq = $this->createProductFaq();

        $this->client->request(Request::METHOD_GET,
            $this->generateUrl('admin_content_faq_edit', ['id' => $Faq->getId()])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testFaqDeleteReturns404ForNonCommonFaq(): void
    {
        // 商品FAQ（サイト共通以外）はサイト共通FAQ専用の削除では 404 になる。
        $Faq = $this->createProductFaq();
        $id = $Faq->getId();

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_content_faq_delete', ['id' => $id])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        // 404 のため削除されず残っていることを検証する。
        $this->entityManager->clear();
        $this->assertInstanceOf(Faq::class, $this->entityManager->getRepository(Faq::class)->find($id));
    }

    public function testIndexListsAllFaqTypes(): void
    {
        // 一覧はページングされるため、残留データがあると検証対象が1ページ目から溢れる。
        $this->deleteAllRows(['dtb_faq']);

        $CommonFaq = $this->createCommonFaq();
        $ProductFaq = $this->createProductFaq();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_faq'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 区分を絞り込まない一覧には、サイト共通FAQと商品FAQの双方が並ぶ。
        $content = $crawler->filter('.c-primaryCol')->text();
        $this->assertStringContainsString((string) $CommonFaq->getQuestion(), $content);
        $this->assertStringContainsString((string) $ProductFaq->getQuestion(), $content);

        // 紐付け先として商品名が表示される。
        $Product = $ProductFaq->getProduct();
        $this->assertInstanceOf(Product::class, $Product);
        $this->assertStringContainsString($Product->getName(), $content);
    }

    public function testIndexFiltersByFaqType(): void
    {
        // 一覧はページングされるため、残留データがあると検証対象が1ページ目から溢れる。
        $this->deleteAllRows(['dtb_faq']);

        $CommonFaq = $this->createCommonFaq();
        $ProductFaq = $this->createProductFaq();

        $crawler = $this->client->request(Request::METHOD_GET,
            $this->generateUrl('admin_content_faq', ['faq_type' => Faq::FAQ_TYPE_COMMON])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $content = $crawler->filter('.c-primaryCol')->text();
        $this->assertStringContainsString((string) $CommonFaq->getQuestion(), $content);
        $this->assertStringNotContainsString((string) $ProductFaq->getQuestion(), $content);
    }

    public function testMoveSortNoUpdatesOnlyCommonFaq(): void
    {
        $CommonFaq = $this->createCommonFaq();
        $ProductFaq = $this->createProductFaq();

        $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_content_faq_sort_no_move'),
            [
                (string) $CommonFaq->getId() => 20,
                (string) $ProductFaq->getId() => 30,
            ],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->entityManager->clear();
        $repository = $this->entityManager->getRepository(Faq::class);

        $Common = $repository->find($CommonFaq->getId());
        $Product = $repository->find($ProductFaq->getId());
        $this->assertInstanceOf(Faq::class, $Common);
        $this->assertInstanceOf(Faq::class, $Product);

        // サイト共通FAQの表示順は更新される。
        $this->assertSame(20, $Common->getSortNo());

        // 商品FAQは並び替えの対象外なので、送信しても更新されない。
        $this->assertSame(1, $Product->getSortNo());
    }
}
