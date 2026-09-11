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

use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Entity\Product;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * 商品詳細・商品一覧でのFAQ表示・FAQPage(JSON-LD)出力の確認.
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

    /**
     * ページ内の FAQPage な JSON-LD だけを抜き出す.
     *
     * @return list<string>
     */
    private function findFaqPageJsonLd(Crawler $crawler): array
    {
        $jsonLdList = $crawler->filter('script[type="application/ld+json"]')->each(
            static fn (Crawler $node): string => $node->text()
        );

        return array_values(array_filter(
            $jsonLdList,
            static fn (string $json): bool => str_contains($json, 'FAQPage')
        ));
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
        $faqPageJsonLd = $this->findFaqPageJsonLd($crawler);
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
        $this->assertCount(0, $this->findFaqPageJsonLd($crawler));
    }

    /**
     * カテゴリFAQ は商品一覧の1ページ目のみに出す（全ページに出すと FAQPage が重複するため）.
     */
    public function testCategoryFaqIsShownOnFirstPageOnly(): void
    {
        $Category = $this->entityManager->getRepository(Category::class)->findOneBy([]);
        $this->assertInstanceOf(Category::class, $Category);

        $Faq = new Faq();
        $Faq->setQuestion('カテゴリFAQの質問ですか')
            ->setAnswer('カテゴリFAQの回答テキスト')
            ->setVisible(true)
            ->setSortNo(1)
            ->setCategory($Category);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        // 1ページ目: 可視FAQ と FAQPage が出る
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_list', ['category_id' => $Category->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('カテゴリFAQの質問ですか', $crawler->filter('.ec-faqRole')->html());
        $this->assertCount(1, $this->findFaqPageJsonLd($crawler));

        // 2ページ目: 出ない
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_list', ['category_id' => $Category->getId(), 'pageno' => 2])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('.ec-faqRole'));
        $this->assertCount(0, $this->findFaqPageJsonLd($crawler));
    }
}
