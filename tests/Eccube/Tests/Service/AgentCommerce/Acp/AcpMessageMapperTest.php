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

namespace Eccube\Tests\Service\AgentCommerce\Acp;

use Eccube\Service\AgentCommerce\Acp\AcpMessageMapper;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 / Layer 0: 中立メッセージ → ACP messages[] 変換と markdown raw HTML 拒否.
 */
final class AcpMessageMapperTest extends TestCase
{
    private AcpMessageMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AcpMessageMapper();
    }

    public function testErrorMessageCarriesCodeAndResolution(): void
    {
        $messages = $this->mapper->toAcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'Out of stock'),
        ]);

        $this->assertCount(1, $messages);
        $this->assertSame('error', $messages[0]['type']);
        $this->assertSame('invalid', $messages[0]['code'], 'MessageError は code が必須 (MUST)');
        $this->assertSame('recoverable', $messages[0]['resolution']);
        $this->assertSame('plain', $messages[0]['content_type']);
        $this->assertSame('Out of stock', $messages[0]['content']);
    }

    public function testWarningCarriesCode(): void
    {
        $messages = $this->mapper->toAcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::WARNING, 'Limited availability'),
        ]);

        $this->assertSame('warning', $messages[0]['type']);
        $this->assertArrayHasKey('code', $messages[0], 'MessageWarning は code が必須 (MUST)');
    }

    public function testInfoMessageHasNoCode(): void
    {
        $messages = $this->mapper->toAcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::INFO, 'FYI'),
        ]);

        $this->assertSame('info', $messages[0]['type']);
        $this->assertArrayNotHasKey('code', $messages[0], 'MessageInfo は code 不要');
    }

    public function testHasErrorDetectsErrorMessages(): void
    {
        $withError = $this->mapper->toAcpMessages([new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, 'x')]);
        $withoutError = $this->mapper->toAcpMessages([new AgentCheckoutMessage(AgentCheckoutMessageLevel::INFO, 'x')]);

        $this->assertTrue($this->mapper->hasError($withError));
        $this->assertFalse($this->mapper->hasError($withoutError));
    }

    public function testAssertNoRawHtmlRejectsRawHtml(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        // ACP MUST: markdown content は raw HTML 要素を含んではならない。
        $this->mapper->assertNoRawHtml('Please see <script>alert(1)</script>');
    }

    public function testAssertNoRawHtmlAllowsPlainMarkdown(): void
    {
        $this->mapper->assertNoRawHtml('**Bold** and [a link](https://example.com)');
        $this->expectNotToPerformAssertions();
    }

    /**
     * whitelist では取りこぼしていた任意タグ・HTML コメントも検知する (MUST: raw HTML 拒否).
     */
    #[DataProvider(methodName: 'rawHtmlProvider')]
    public function testAssertNoRawHtmlRejectsAnyTagOrComment(string $markdown): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->mapper->assertNoRawHtml($markdown);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function rawHtmlProvider(): iterable
    {
        yield 'paragraph tag' => ['Hello <p>world</p>'];
        yield 'heading tag' => ['<h1>Title</h1>'];
        yield 'self-closing br' => ['line<br/>break'];
        yield 'html comment' => ['before <!-- secret --> after'];
        yield 'closing tag' => ['text </span> more'];
    }

    public function testAssertNoRawHtmlAllowsAutolink(): void
    {
        // CommonMark の autolink (<scheme:...>) は raw HTML ではないため誤検知しない。
        $this->mapper->assertNoRawHtml('See <https://example.com> for details');
        $this->expectNotToPerformAssertions();
    }
}
