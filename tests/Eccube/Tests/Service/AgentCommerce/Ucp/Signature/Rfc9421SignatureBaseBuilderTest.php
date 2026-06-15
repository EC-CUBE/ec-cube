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

namespace Eccube\Tests\Service\AgentCommerce\Ucp\Signature;

use Eccube\Service\AgentCommerce\Ucp\Signature\Rfc9421SignatureBaseBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Layer 1 (純ロジック) tests for Rfc9421SignatureBaseBuilder.
 *
 * covered component から RFC 9421 signature base を再構成できること、@signature-params 行が
 * 末尾に付くこと、未解決の派生 component や欠落ヘッダで例外になることを検証する。
 *
 * @see https://www.rfc-editor.org/rfc/rfc9421 RFC 9421 Section 2.5
 */
final class Rfc9421SignatureBaseBuilderTest extends TestCase
{
    private Rfc9421SignatureBaseBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new Rfc9421SignatureBaseBuilder();
    }

    public function testBuildsSignatureBaseForPostWithDerivedAndHeaderComponents(): void
    {
        $body = '{"line_items":[]}';
        $request = Request::create('https://shop.example/ucp/checkout-sessions', Request::METHOD_POST, [], [], [], [], $body);
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Content-Digest', 'sha-256=:abc:');

        $params = '("@method" "@authority" "@path" "content-digest" "content-type");keyid="k1";alg="ecdsa-p256-sha256"';
        $base = $this->builder->build(
            $request,
            ['@method', '@authority', '@path', 'content-digest', 'content-type'],
            $params,
        );

        $expected = implode("\n", [
            '"@method": POST',
            '"@authority": shop.example',
            '"@path": /ucp/checkout-sessions',
            '"content-digest": sha-256=:abc:',
            '"content-type": application/json',
            '"@signature-params": '.$params,
        ]);

        $this->assertSame($expected, $base);
    }

    public function testThrowsOnMissingSignedHeader(): void
    {
        $request = Request::create('https://shop.example/ucp/checkout-sessions', Request::METHOD_POST);

        $this->expectException(\InvalidArgumentException::class);
        $this->builder->build($request, ['content-digest'], '("content-digest")');
    }

    public function testThrowsOnUnsupportedDerivedComponent(): void
    {
        $request = Request::create('https://shop.example/ucp/checkout-sessions', Request::METHOD_POST);

        $this->expectException(\InvalidArgumentException::class);
        $this->builder->build($request, ['@unknown'], '("@unknown")');
    }
}
