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

namespace Eccube\Service\Content;

use Eccube\Util\StringUtil;

/**
 * テンプレート本文の読み取りと比較.
 *
 * ページ・ブロック・メールテンプレートで共通の, 「配置先に既にあるテンプレートを初期値にする」
 * 処理と「内容が同じかどうかを判定する」処理をまとめる.
 */
trait TemplateBodyTrait
{
    /**
     * 新規登録時に, 配置先へ既にあるテンプレートを本文の初期値として読む.
     *
     * テンプレートはリポジトリで管理されることを前提にする. 本文を指定しない取り込み
     * (eccube:contents:import) は「コミット済みのテンプレートに対応するレコードを作る」操作のため,
     * 初期値を空文字列にすると FormType の NotBlank で弾かれ, Git で管理しているテンプレートから
     * ページやブロックを作れない.
     *
     * @param string $dir      配置先のディレクトリ
     * @param string $fileName 拡張子を除いたファイル名 (FormType の検証を通す前の値)
     * @param string $suffix   拡張子
     *
     * @return string|null ファイルが無い場合は null. メールの HTML パートのように
     *                     「無い」と「空」を区別する呼び出し元があるため '' へ丸めない
     */
    private function readExistingTemplate(string $dir, string $fileName, string $suffix = '.twig'): ?string
    {
        // 検証前の値のため, 英数字・ハイフン・アンダースコア・スラッシュだけを受け入れる.
        // ドットを許さないので配置先の外は指せない (検証そのものは後段の FormType が行う).
        if (1 !== preg_match('/^[0-9a-zA-Z_\-\/]+$/', $fileName)) {
            return null;
        }

        $filePath = $dir.'/'.$fileName.$suffix;

        return is_file($filePath) ? (string) file_get_contents($filePath) : null;
    }

    /**
     * テンプレート本文を比較用に正規化する.
     *
     * FormType は trim が既定で有効なため (FormType::configureOptions の 'trim' => true),
     * submit を通った本文は前後の空白が落ちている. 一方 src/Eccube/Resource/template の
     * テンプレートは末尾に改行を持つため, 揃えずに比較すると内容が同じでも差分と判定され,
     * app/template へ写しを書き出してしまう.
     */
    private static function normalizeTemplateBody(string $body): string
    {
        return trim(StringUtil::convertLineFeed($body));
    }
}
