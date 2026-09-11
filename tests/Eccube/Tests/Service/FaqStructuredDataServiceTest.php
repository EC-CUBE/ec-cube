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

namespace Eccube\Tests\Service;

use Eccube\Entity\Faq;
use Eccube\Service\FaqStructuredDataService;
use Eccube\Tests\EccubeTestCase;

final class FaqStructuredDataServiceTest extends EccubeTestCase
{
    private ?FaqStructuredDataService $service = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FaqStructuredDataService();
    }

    public function testCreateFaqPageJsonLd(): void
    {
        $faqs = [
            (new Faq())->setQuestion('配送はいつ届きますか？')->setAnswer('通常3営業日以内に発送します。'),
            (new Faq())->setQuestion('返品はできますか？')->setAnswer('到着後7日以内なら可能です。'),
        ];

        $result = $this->service->createFaqPageJsonLd($faqs);

        $this->assertSame('https://schema.org', $result['@context']);
        $this->assertSame('FAQPage', $result['@type']);
        $this->assertCount(2, $result['mainEntity']);
        $this->assertSame('Question', $result['mainEntity'][0]['@type']);
        $this->assertSame('配送はいつ届きますか？', $result['mainEntity'][0]['name']);
        $this->assertSame('Answer', $result['mainEntity'][0]['acceptedAnswer']['@type']);
        $this->assertSame('通常3営業日以内に発送します。', $result['mainEntity'][0]['acceptedAnswer']['text']);
    }

    public function testEmptyReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->service->createFaqPageJsonLd([]));
    }

    public function testQuestionOrAnswerBlankIsSkipped(): void
    {
        $faqs = [
            (new Faq())->setQuestion('質問のみ')->setAnswer(),
            (new Faq())->setQuestion('有効な質問')->setAnswer('有効な回答'),
        ];

        $result = $this->service->createFaqPageJsonLd($faqs);

        $this->assertCount(1, $result['mainEntity']);
        $this->assertSame('有効な質問', $result['mainEntity'][0]['name']);
    }
}
