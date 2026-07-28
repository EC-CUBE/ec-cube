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
    }

    public function testRoutingAdminContentFaqDelete(): void
    {
        $Faq = $this->createCommonFaq();

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_content_faq_delete', ['id' => $Faq->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_faq')));
    }
}
