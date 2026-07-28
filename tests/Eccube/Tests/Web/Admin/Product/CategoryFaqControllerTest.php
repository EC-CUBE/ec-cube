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

        $this->client->request(Request::METHOD_POST, $url, [
            'admin_category_faq' => [
                '_token' => $token,
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
    }
}
