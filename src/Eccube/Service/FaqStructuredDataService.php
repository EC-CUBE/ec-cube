<?php

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

namespace Eccube\Service;

use Eccube\Entity\Faq;

/**
 * FAQ の構造化データ（JSON-LD / schema.org FAQPage）を組み立てる Service.
 *
 * FAQ の一覧（Faq エンティティ）から FAQPage の連想配列を組み立てて返す。
 * 戻り値は EccubeExtension の `json_ld` フィルタ経由で出力し、値に含まれる
 * `" < > &` などを機械的にエスケープする（テンプレート文字列補間による JSON 破壊・XSS を防ぐ）。
 *
 * FAQ が 0 件のときは空配列を返し、出力側で `<script>` の出力自体を抑止する。
 */
class FaqStructuredDataService
{
    /**
     * FAQPage の JSON-LD 構造を組み立てて返す.
     *
     * @param iterable<Faq> $Faqs
     *
     * @return array<string, mixed>
     */
    public function createFaqPageJsonLd(iterable $Faqs): array
    {
        $mainEntity = [];
        foreach ($Faqs as $Faq) {
            $question = $Faq->getQuestion();
            $answer = $Faq->getAnswer();
            if ($question === null || $question === '' || $answer === null || $answer === '') {
                continue;
            }
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($mainEntity === []) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }
}
