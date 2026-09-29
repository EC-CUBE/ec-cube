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

use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryFaqControllerTest extends AbstractAdminWebTestCase
{
    private function getCategory(): Category
    {
        $Category = $this->entityManager->getRepository(Category::class)->findOneBy([]);
        $this->assertInstanceOf(Category::class, $Category);

        return $Category;
    }

    public function testRouting(): void
    {
        $Category = $this->getCategory();

        $this->client->request(Request::METHOD_GET,
            $this->generateUrl('admin_product_category_faq', ['id' => $Category->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddFaq(): void
    {
        $Category = $this->getCategory();
        $url = $this->generateUrl('admin_product_category_faq', ['id' => $Category->getId()]);

        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $token = $crawler->filter('input[name="admin_category_faq[_token]"]')->attr('value');

        // FAQ欄の描画センチネルがテンプレートから出力されていること
        $this->assertSame(
            '1',
            $crawler->filter('input[name="admin_category_faq[faqs_rendered]"]')->attr('value')
        );

        $this->client->request(Request::METHOD_POST, $url, [
            'admin_category_faq' => [
                '_token' => $token,
                'faqs_rendered' => '1',
                'faqs' => [
                    [
                        'question' => 'カテゴリFAQ質問',
                        'answer' => 'カテゴリFAQ回答',
                        'sort_no' => '1',
                        'visible' => '1',
                    ],
                ],
            ],
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $this->entityManager->clear();
        $Faqs = $this->entityManager->getRepository(Faq::class)->findBy(['Category' => $Category->getId()]);
        $this->assertCount(1, $Faqs);
        $this->assertSame('カテゴリFAQ質問', $Faqs[0]->getQuestion());
    }

    /**
     * FAQ欄を描画していないテンプレートからの保存では、既存のカテゴリごとFAQが維持されることを検証する.
     */
    public function testKeepsFaqWhenCollectionIsNotRendered(): void
    {
        $Category = $this->getCategory();
        $url = $this->generateUrl('admin_product_category_faq', ['id' => $Category->getId()]);
        $token = $this->client->request(Request::METHOD_GET, $url)
            ->filter('input[name="admin_category_faq[_token]"]')->attr('value');

        $Faq = new Faq();
        $Faq->setQuestion('上書きテンプレートでも残るか')
            ->setAnswer('残る')
            ->setSortNo(1)
            ->setVisible(true)
            ->setCategory($Category);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();
        $faqId = $Faq->getId();

        // faqs / faqs_rendered ともに送信されない保存
        $this->client->request(Request::METHOD_POST, $url, [
            'admin_category_faq' => ['_token' => $token],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $this->entityManager->clear();
        $this->assertInstanceOf(Faq::class, $this->entityManager->getRepository(Faq::class)->find($faqId));
    }

    /**
     * FAQ欄を描画した画面で全行を削除した保存では、カテゴリごとFAQが全削除されることを検証する.
     */
    public function testRemovesAllFaqWhenAllRowsDeleted(): void
    {
        $Category = $this->getCategory();
        $url = $this->generateUrl('admin_product_category_faq', ['id' => $Category->getId()]);
        $token = $this->client->request(Request::METHOD_GET, $url)
            ->filter('input[name="admin_category_faq[_token]"]')->attr('value');

        $Faq = new Faq();
        $Faq->setQuestion('UIで全行削除されるか')
            ->setAnswer('される')
            ->setSortNo(1)
            ->setVisible(true)
            ->setCategory($Category);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();
        $faqId = $Faq->getId();

        // 全行削除では faqs キーは送信されないが、faqs_rendered は送信される
        $this->client->request(Request::METHOD_POST, $url, [
            'admin_category_faq' => ['_token' => $token, 'faqs_rendered' => '1'],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $this->entityManager->clear();
        $this->assertNotInstanceOf(Faq::class, $this->entityManager->getRepository(Faq::class)->find($faqId));
    }
}
