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

/**
 * 構造化データの説明文を正規化する共通処理.
 *
 * 説明文系の項目（商品説明・取り扱い商品説明文・店舗からのメッセージ等）は
 * HTML タグや改行を含む長文テキストなので、JSON-LD へ出力する前に共通の加工を通す。
 */
trait StructuredDataDescriptionTrait
{
    /**
     * 説明文を HTML 除去・空白正規化して指定文字数に丸める.
     */
    private function normalizeDescription(?string $description, int $limit = 300): string
    {
        if ($description === null || $description === '') {
            return '';
        }

        // strip_tags() はタグを削除するだけなので、そのままでは <p>商品A</p><p>商品B</p> が
        // 「商品A商品B」と連結してしまう。改行・ブロック要素の境界を空白へ置換してから除去する。
        $description = preg_replace(
            '#<(?:br\s*/?|/?(?:p|div|li|ul|ol|tr|td|th|table|h[1-6]|section|article|header|footer|blockquote|pre|dl|dt|dd))\b[^>]*>#i',
            ' ',
            $description
        ) ?? $description;
        $description = strip_tags($description);
        $description = preg_replace('/\s+/u', ' ', $description) ?? $description;
        $description = trim($description);

        return mb_substr($description, 0, $limit);
    }
}
