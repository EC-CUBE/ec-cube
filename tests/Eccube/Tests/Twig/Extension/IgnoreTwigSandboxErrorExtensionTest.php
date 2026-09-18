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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Entity\Page;
use Eccube\Tests\Web\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;

#[Group('twig-sandbox-extension')]
final class IgnoreTwigSandboxErrorExtensionTest extends AbstractWebTestCase
{
    /**
     * @param mixed $snippet
     * @param mixed $whitelisted
     */
    #[DataProvider(methodName: 'twigSnippetsProvider')]
    #[DataProvider(methodName: 'twigVarFreeAreaProvider')]
    public function testFreeArea($snippet, $whitelisted)
    {
        $Product = $this->createProduct();
        $Product->setFreeArea('__RENDERED__'.$snippet);
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_detail', ['id' => $Product->getId()]));
        $text = $crawler->text();

        // $snippetがsandboxで制限された場合はフリーエリアは空で出力されるため、__RENDERED__の出力有無で結果を確認する
        $this->assertStringContainsString($whitelisted ? '__RENDERED__' : '', $text);
    }

    /**
     * @param mixed $snippet
     * @param mixed $whitelisted
     */
    #[DataProvider(methodName: 'twigSnippetsProvider')]
    #[DataProvider(methodName: 'twigVarMetaTagsProvider')]
    public function testMetatags($snippet, $whitelisted)
    {
        $Page = $this->entityManager->getRepository(Page::class)->find(1);
        $Page->setMetaTags('__RENDERED__'.$snippet);
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl($Page->getUrl()));
        $text = $crawler->text();

        // ホワイトリストに入っている場合__RENDERED__が表示される
        if ($whitelisted) {
            $this->assertStringContainsString('__RENDERED__', $text);
        } else {
            $this->assertStringNotContainsString('__RENDERED__', $text);
        }
        // 入力可能ではない値の場合は、システムエラーが発生する
        $this->assertStringNotContainsString('システムエラーが発生しました', $text);
    }

    public static function twigSnippetsProvider(): \Iterator
    {
        // 0: twigスニペット, 1: ホワイトリスト対象かどうか
        yield ['{% set foo = "bar" %}', true];
        yield ['{% flush %}', true];
        yield ['{% apply lower|escape("html") %}<strong>SOME TEXT</strong>{% endapply %}', true];
        yield ['{% macro input(name, value, type = "text", size = 20) %}<input type="{{ type }}" name="{{ name }}" value="{{ value|e }}" size="{{ size }}"/>{% endmacro %}', false];
        yield ['{% sandbox %}{% include "user.html" %}{% endsandbox %}', false];
        yield ['{{ "-5"|abs }}', true];
        yield ['{{ "2020/02/01"|date_modify("+1 day")|date("m/d/Y") }}', true];
        yield ['{{ [1, 2, 3, 4]|first }}', true];
        yield ['{{ file|format_file(line, text = null) }}', false];
        yield ['{{ [1, 2, 3]|reduce((carry, v) => carry + v) }}', false];
        yield ['{{ "<p> <strong>test</strong> </p>" |raw }}', false];
        yield ['{{ url("homepage") }}', true];
        yield ['{{ random(1, 100) }}', true];
        yield ['{% for i in range(3, 0) %} {{ i }}, {% endfor %}', true];
        yield ['{{ dump(9) }}', false];
        yield ['{{ constant("RSS", date) }}', false];
        yield ['{{ include(template_from_string("Hello")) }}', false];
        yield ['{{ Product.main_list_image|no_image_product }}', true];
    }

    public static function twigVarFreeAreaProvider(): \Iterator
    {
        // 0: twigスニペット, 1: ホワイトリスト対象かどうか
        yield ['{{ app.user }}', false];
        yield ['{{ Product.name }}', true];
        yield ['{{ app.request.uri }}', true];
        yield ['{{ app.request.getUri }}', true];
    }

    public static function twigVarMetaTagsProvider(): \Iterator
    {
        // 0: twigスニペット, 1: ホワイトリスト対象かどうか
        yield ['{{ app.debug }}', false];
        yield ['{{ BaseInfo.shop_name }}', true];
        yield ['{{ app.request.uri }}', true];
        yield ['{{ app.request.getUri }}', true];
    }
}
