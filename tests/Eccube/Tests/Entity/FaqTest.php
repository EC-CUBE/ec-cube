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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * FAQ の区分（サイト共通 / 商品ごと / カテゴリごと）が
 * Product・Category の設定状態から導出されることを検証する。
 */
final class FaqTest extends TestCase
{
    public function testFaqTypeIsCommonWhenNeitherSet(): void
    {
        $Faq = new Faq();

        $this->assertNotInstanceOf(Product::class, $Faq->getProduct());
        $this->assertNotInstanceOf(Category::class, $Faq->getCategory());
        $this->assertSame(Faq::FAQ_TYPE_COMMON, $Faq->getFaqType());
    }

    public function testFaqTypeIsProductWhenProductSet(): void
    {
        $Faq = new Faq();
        $Faq->setProduct(new Product());

        $this->assertInstanceOf(Product::class, $Faq->getProduct());
        $this->assertNotInstanceOf(Category::class, $Faq->getCategory());
        $this->assertSame(Faq::FAQ_TYPE_PRODUCT, $Faq->getFaqType());
    }

    public function testFaqTypeIsCategoryWhenCategorySet(): void
    {
        $Faq = new Faq();
        $Faq->setCategory(new Category());

        $this->assertInstanceOf(Category::class, $Faq->getCategory());
        $this->assertNotInstanceOf(Product::class, $Faq->getProduct());
        $this->assertSame(Faq::FAQ_TYPE_CATEGORY, $Faq->getFaqType());
    }
}
