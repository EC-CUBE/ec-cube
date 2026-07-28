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
}
