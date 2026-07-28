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

namespace Eccube\Tests\Web;

use Eccube\Entity\Faq;
use Eccube\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

/**
 * 商品詳細ページでの商品FAQ表示・FAQPage(JSON-LD)出力の確認.
 */
final class ProductFaqDisplayTest extends AbstractWebTestCase
{
    private function createProductFaq(Product $Product, string $question, string $answer, bool $visible, int $sortNo): Faq
    {
        $Faq = new Faq();
        $Faq->setQuestion($question)
            ->setAnswer($answer)
            ->setVisible($visible)
            ->setSortNo($sortNo)
            ->setProduct($Product);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        return $Faq;
    }

    public function testProductDetailShowsVisibleFaqAndFaqPageJsonLd(): void
    {
        $Product = $this->createProduct('FAQ表示テスト商品', 1);
        $this->createProductFaq($Product, '表示される質問ですか', '表示される回答テキスト', true, 1);
        $this->createProductFaq($Product, '非表示の質問ですか', '非表示の回答テキスト', false, 2);

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $Product->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 表示FAQの質問・回答が本文に出る／非表示FAQは出ない
        $faqHtml = $crawler->filter('.ec-faqRole')->html();
        $this->assertStringContainsString('表示される質問ですか', $faqHtml);
        $this->assertStringContainsString('表示される回答テキスト', $faqHtml);
        $this->assertStringNotContainsString('非表示の質問ですか', $faqHtml);
        $this->assertStringNotContainsString('非表示の回答テキスト', $faqHtml);

        // FAQPage の JSON-LD が出力され、表示分のみ含む
        $jsonLdList = $crawler->filter('script[type="application/ld+json"]')->each(
            static fn ($node): string => $node->text()
        );
        $faqPageJsonLd = array_values(array_filter(
            $jsonLdList,
            static fn (string $json): bool => str_contains($json, 'FAQPage')
        ));
        $this->assertCount(1, $faqPageJsonLd);
        $this->assertStringContainsString('表示される質問ですか', $faqPageJsonLd[0]);
        $this->assertStringNotContainsString('非表示の質問ですか', $faqPageJsonLd[0]);
    }

    public function testProductDetailWithoutFaqHasNoFaqPage(): void
    {
        $Product = $this->createProduct('FAQ無し商品', 1);

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $Product->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('.ec-faqRole'));

        $jsonLdList = $crawler->filter('script[type="application/ld+json"]')->each(
            static fn ($node): string => $node->text()
        );
        $faqPageJsonLd = array_filter($jsonLdList, static fn (string $json): bool => str_contains($json, 'FAQPage'));
        $this->assertCount(0, $faqPageJsonLd);
    }
}
