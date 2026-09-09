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

use Eccube\Entity\Layout;
use Symfony\Component\Yaml\Yaml;

/**
 * コンテンツ定義のアーカイブ (eccube:contents:export / import) の規約.
 *
 * テンプレート (twig / css / js) はリポジトリ本来の位置で Git 管理される前提のため,
 * アーカイブは**本文を持たない**. Git に残らない DB 側の定義だけを対象にする.
 * 本文をアーカイブへ複製すると, src/Eccube/Resource/template や app/template と
 * 二重管理になり, upstream との git merge で解決できなくなる.
 */
final class ContentsArchive
{
    /**
     * アーカイブの書式版. 互換性の無い変更を入れるときに上げる.
     */
    public const SCHEMA_VERSION = 1;

    public const MANIFEST_FILE = 'manifest.yaml';

    /**
     * --to / --from の既定値 (プロジェクトルートからの相対パス).
     *
     * app/ は Git 管理下のカスタマイズ領域で .gitignore の除外対象にも入っていないため,
     * CI からは引数なしで実行できる.
     */
    public const DEFAULT_DIR = 'app/contents';

    public const SECTION_LAYOUTS = 'layouts';
    public const SECTION_BLOCKS = 'blocks';
    public const SECTION_PAGES = 'pages';
    public const SECTION_MAIL_TEMPLATES = 'mail_templates';
    public const SECTION_USER_DATA = 'user_data';

    /**
     * 既定で扱うセクション. 取り込む順序でもある
     * (ページはレイアウトを, レイアウトはブロックを参照する).
     *
     * @var list<string>
     */
    public const DEFAULT_SECTIONS = [
        self::SECTION_LAYOUTS,
        self::SECTION_BLOCKS,
        self::SECTION_PAGES,
        self::SECTION_MAIL_TEMPLATES,
    ];

    /**
     * --include で明示したときだけ扱うセクション.
     *
     * html/user_data は customize.css / customize.js だけが Git 管理下で,
     * それ以外は .gitignore で意図的に除外されている (アップロード物). 既定で
     * ミラーすると二重管理になるため, リポジトリ丸ごと管理する構成向けの逃げ道に留める.
     *
     * @var list<string>
     */
    public const OPTIONAL_SECTIONS = [
        self::SECTION_USER_DATA,
    ];

    /**
     * dtb_block_position.section の値 => アーカイブでの名前.
     *
     * 数値のままだと差分から配置場所が読めないため, 名前で持つ.
     *
     * @var array<int, string>
     */
    public const BLOCK_SECTIONS = [
        Layout::TARGET_ID_UNUSED => 'unused',
        Layout::TARGET_ID_HEAD => 'head',
        Layout::TARGET_ID_BODY_AFTER => 'body_after',
        Layout::TARGET_ID_HEADER => 'header',
        Layout::TARGET_ID_CONTENTS_TOP => 'contents_top',
        Layout::TARGET_ID_SIDE_LEFT => 'side_left',
        Layout::TARGET_ID_MAIN_TOP => 'main_top',
        Layout::TARGET_ID_MAIN_BOTTOM => 'main_bottom',
        Layout::TARGET_ID_SIDE_RIGHT => 'side_right',
        Layout::TARGET_ID_CONTENTS_BOTTOM => 'contents_bottom',
        Layout::TARGET_ID_FOOTER => 'footer',
        Layout::TARGET_ID_DRAWER => 'drawer',
        Layout::TARGET_ID_CLOSE_BODY_BEFORE => 'close_body_before',
    ];

    /**
     * @return list<string> 既定と任意を合わせた全セクション
     */
    public static function allSections(): array
    {
        return [...self::DEFAULT_SECTIONS, ...self::OPTIONAL_SECTIONS];
    }

    public static function isSection(string $section): bool
    {
        return in_array($section, self::allSections(), true);
    }

    /**
     * セクションの yaml ファイル名.
     */
    public static function fileName(string $section): string
    {
        return $section.'.yaml';
    }

    public static function blockSectionName(int $section): ?string
    {
        return self::BLOCK_SECTIONS[$section] ?? null;
    }

    public static function blockSectionId(string $name): ?int
    {
        $id = array_search($name, self::BLOCK_SECTIONS, true);

        return false === $id ? null : $id;
    }

    /**
     * ページ・ブロック・メールテンプレートの鍵として妥当か.
     *
     * 取り込みはアーカイブのファイル名ではなく yaml の中身を鍵にするため, ここで検証する.
     * 実際の検証は後段の FormType (MainEditType / BlockType / MailType) も行うが,
     * 配置先の外を指す値をサービスへ渡さないよう手前で弾く. ドットを許さないので
     * ディレクトリを遡れない.
     */
    public static function isValidKey(string $key): bool
    {
        return '' !== $key && 1 === preg_match('/^[0-9a-zA-Z_\-\/]+$/', $key);
    }

    /**
     * 差分が安定するように書式を固定して yaml へ変換する.
     *
     * @param mixed $data
     */
    public static function dump(mixed $data): string
    {
        // インデント 2, 3 階層目からインライン. Yaml::DUMP_NULL_AS_TILDE で null を ~ に揃える
        return Yaml::dump($data, 3, 2, Yaml::DUMP_NULL_AS_TILDE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
    }
}
