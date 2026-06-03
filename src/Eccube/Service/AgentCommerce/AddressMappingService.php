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

namespace Eccube\Service\AgentCommerce;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;

/**
 * EC-CUBE の住所系エンティティ (Customer / CustomerAddress / Shipping) を
 * ACP / UCP の住所 DTO へ写すためのマッピングサービス。
 *
 * - 国コードは Country.id (ISO 3166-1 numeric) を alpha-2 へ変換する。
 *   mtb_country.csv に登録されている全 id を ISO 3166-1 標準どおりにマップする。
 * - region は Pref 名 (例: 東京都) をそのまま返す。
 * - app/Customize で変換テーブル/ロジックを差し替え可能にする意図のため、
 *   NUMERIC_TO_ALPHA2 への参照は必ずメソッド (getAlpha2FromCountryId) 経由で行う。
 */
class AddressMappingService
{
    /**
     * ISO 3166-1 numeric (mtb_country.id) => alpha-2。
     * mtb_country.csv に出現する全 id を網羅する。未知 id は null を返す設計。
     *
     * @var array<int, string>
     */
    private const NUMERIC_TO_ALPHA2 = [
        352 => 'IS', // アイスランド
        372 => 'IE', // アイルランド
        31 => 'AZ',  // アゼルバイジャン
        4 => 'AF',   // アフガニスタン
        840 => 'US', // アメリカ合衆国
        850 => 'VI', // アメリカ領ヴァージン諸島
        16 => 'AS',  // アメリカ領サモア
        784 => 'AE', // アラブ首長国連邦
        12 => 'DZ',  // アルジェリア
        32 => 'AR',  // アルゼンチン
        533 => 'AW', // アルバ
        8 => 'AL',   // アルバニア
        51 => 'AM',  // アルメニア
        660 => 'AI', // アンギラ
        24 => 'AO',  // アンゴラ
        28 => 'AG',  // アンティグア・バーブーダ
        20 => 'AD',  // アンドラ
        887 => 'YE', // イエメン
        826 => 'GB', // イギリス
        86 => 'IO',  // イギリス領インド洋地域
        92 => 'VG',  // イギリス領ヴァージン諸島
        376 => 'IL', // イスラエル
        380 => 'IT', // イタリア
        368 => 'IQ', // イラク
        364 => 'IR', // イラン
        356 => 'IN', // インド
        360 => 'ID', // インドネシア
        876 => 'WF', // ウォリス・フツナ
        800 => 'UG', // ウガンダ
        804 => 'UA', // ウクライナ
        860 => 'UZ', // ウズベキスタン
        858 => 'UY', // ウルグアイ
        218 => 'EC', // エクアドル
        818 => 'EG', // エジプト
        233 => 'EE', // エストニア
        231 => 'ET', // エチオピア
        232 => 'ER', // エリトリア
        222 => 'SV', // エルサルバドル
        36 => 'AU',  // オーストラリア
        40 => 'AT',  // オーストリア
        248 => 'AX', // オーランド諸島
        512 => 'OM', // オマーン
        528 => 'NL', // オランダ
        288 => 'GH', // ガーナ
        132 => 'CV', // カーボベルデ
        831 => 'GG', // ガーンジー
        328 => 'GY', // ガイアナ
        398 => 'KZ', // カザフスタン
        634 => 'QA', // カタール
        581 => 'UM', // 合衆国領有小離島
        124 => 'CA', // カナダ
        266 => 'GA', // ガボン
        120 => 'CM', // カメルーン
        270 => 'GM', // ガンビア
        116 => 'KH', // カンボジア
        580 => 'MP', // 北マリアナ諸島
        324 => 'GN', // ギニア
        624 => 'GW', // ギニアビサウ
        196 => 'CY', // キプロス
        192 => 'CU', // キューバ
        531 => 'CW', // キュラソー島
        300 => 'GR', // ギリシャ
        296 => 'KI', // キリバス
        417 => 'KG', // キルギス
        320 => 'GT', // グアテマラ
        312 => 'GP', // グアドループ
        316 => 'GU', // グアム
        414 => 'KW', // クウェート
        184 => 'CK', // クック諸島
        304 => 'GL', // グリーンランド
        162 => 'CX', // クリスマス島
        268 => 'GE', // グルジア
        308 => 'GD', // グレナダ
        191 => 'HR', // クロアチア
        136 => 'KY', // ケイマン諸島
        404 => 'KE', // ケニア
        384 => 'CI', // コートジボワール
        166 => 'CC', // ココス諸島
        188 => 'CR', // コスタリカ
        174 => 'KM', // コモロ
        170 => 'CO', // コロンビア
        178 => 'CG', // コンゴ共和国
        180 => 'CD', // コンゴ民主共和国
        682 => 'SA', // サウジアラビア
        239 => 'GS', // サウスジョージア・サウスサンドウィッチ諸島
        882 => 'WS', // サモア
        678 => 'ST', // サントメ・プリンシペ
        652 => 'BL', // サン・バルテルミー島
        894 => 'ZM', // ザンビア
        666 => 'PM', // サンピエール島・ミクロン島
        674 => 'SM', // サンマリノ
        663 => 'MF', // サン・マルタン (フランス領)
        694 => 'SL', // シエラレオネ
        262 => 'DJ', // ジブチ
        292 => 'GI', // ジブラルタル
        832 => 'JE', // ジャージー
        388 => 'JM', // ジャマイカ
        760 => 'SY', // シリア
        702 => 'SG', // シンガポール
        534 => 'SX', // シント・マールテン
        716 => 'ZW', // ジンバブエ
        756 => 'CH', // スイス
        752 => 'SE', // スウェーデン
        729 => 'SD', // スーダン
        744 => 'SJ', // スヴァールバル諸島およびヤンマイエン島
        724 => 'ES', // スペイン
        740 => 'SR', // スリナム
        144 => 'LK', // スリランカ
        703 => 'SK', // スロバキア
        705 => 'SI', // スロベニア
        748 => 'SZ', // スワジランド
        690 => 'SC', // セーシェル
        226 => 'GQ', // 赤道ギニア
        686 => 'SN', // セネガル
        688 => 'RS', // セルビア
        659 => 'KN', // セントクリストファー・ネイビス
        670 => 'VC', // セントビンセント・グレナディーン
        426 => 'LS', // レソト
        654 => 'SH', // セントヘレナ・アセンションおよびトリスタンダクーニャ
        662 => 'LC', // セントルシア
        706 => 'SO', // ソマリア
        90 => 'SB',  // ソロモン諸島
        796 => 'TC', // タークス・カイコス諸島
        764 => 'TH', // タイ王国
        410 => 'KR', // 大韓民国
        158 => 'TW', // 台湾
        762 => 'TJ', // タジキスタン
        834 => 'TZ', // タンザニア
        203 => 'CZ', // チェコ
        148 => 'TD', // チャド
        140 => 'CF', // 中央アフリカ共和国
        156 => 'CN', // 中華人民共和国
        788 => 'TN', // チュニジア
        408 => 'KP', // 朝鮮民主主義人民共和国
        152 => 'CL', // チリ
        798 => 'TV', // ツバル
        208 => 'DK', // デンマーク
        276 => 'DE', // ドイツ
        768 => 'TG', // トーゴ
        772 => 'TK', // トケラウ
        214 => 'DO', // ドミニカ共和国
        212 => 'DM', // ドミニカ国
        780 => 'TT', // トリニダード・トバゴ
        795 => 'TM', // トルクメニスタン
        792 => 'TR', // トルコ
        776 => 'TO', // トンガ
        566 => 'NG', // ナイジェリア
        520 => 'NR', // ナウル
        516 => 'NA', // ナミビア
        10 => 'AQ',  // 南極
        570 => 'NU', // ニウエ
        558 => 'NI', // ニカラグア
        562 => 'NE', // ニジェール
        392 => 'JP', // 日本
        732 => 'EH', // 西サハラ
        540 => 'NC', // ニューカレドニア
        554 => 'NZ', // ニュージーランド
        524 => 'NP', // ネパール
        574 => 'NF', // ノーフォーク島
        578 => 'NO', // ノルウェー
        334 => 'HM', // ハード島とマクドナルド諸島
        48 => 'BH',  // バーレーン
        332 => 'HT', // ハイチ
        586 => 'PK', // パキスタン
        336 => 'VA', // バチカン
        591 => 'PA', // パナマ
        548 => 'VU', // バヌアツ
        44 => 'BS',  // バハマ
        598 => 'PG', // パプアニューギニア
        60 => 'BM',  // バミューダ諸島
        585 => 'PW', // パラオ
        600 => 'PY', // パラグアイ
        52 => 'BB',  // バルバドス
        275 => 'PS', // パレスチナ
        348 => 'HU', // ハンガリー
        50 => 'BD',  // バングラデシュ
        626 => 'TL', // 東ティモール
        612 => 'PN', // ピトケアン諸島
        242 => 'FJ', // フィジー
        608 => 'PH', // フィリピン
        246 => 'FI', // フィンランド
        64 => 'BT',  // ブータン
        74 => 'BV',  // ブーベ島
        630 => 'PR', // プエルトリコ
        234 => 'FO', // フェロー諸島
        238 => 'FK', // フォークランド諸島
        76 => 'BR',  // ブラジル
        250 => 'FR', // フランス
        254 => 'GF', // フランス領ギアナ
        258 => 'PF', // フランス領ポリネシア
        260 => 'TF', // フランス領南方・南極地域
        100 => 'BG', // ブルガリア
        854 => 'BF', // ブルキナファソ
        96 => 'BN',  // ブルネイ
        108 => 'BI', // ブルンジ
        704 => 'VN', // ベトナム
        204 => 'BJ', // ベナン
        862 => 'VE', // ベネズエラ
        112 => 'BY', // ベラルーシ
        84 => 'BZ',  // ベリーズ
        604 => 'PE', // ペルー
        56 => 'BE',  // ベルギー
        616 => 'PL', // ポーランド
        70 => 'BA',  // ボスニア・ヘルツェゴビナ
        72 => 'BW',  // ボツワナ
        535 => 'BQ', // BES諸島
        68 => 'BO',  // ボリビア
        620 => 'PT', // ポルトガル
        344 => 'HK', // 香港
        340 => 'HN', // ホンジュラス
        584 => 'MH', // マーシャル諸島
        446 => 'MO', // マカオ
        807 => 'MK', // マケドニア共和国
        450 => 'MG', // マダガスカル
        175 => 'YT', // マヨット
        454 => 'MW', // マラウイ
        466 => 'ML', // マリ共和国
        470 => 'MT', // マルタ
        474 => 'MQ', // マルティニーク
        458 => 'MY', // マレーシア
        833 => 'IM', // マン島
        583 => 'FM', // ミクロネシア連邦
        710 => 'ZA', // 南アフリカ共和国
        728 => 'SS', // 南スーダン
        104 => 'MM', // ミャンマー
        484 => 'MX', // メキシコ
        480 => 'MU', // モーリシャス
        478 => 'MR', // モーリタニア
        508 => 'MZ', // モザンビーク
        492 => 'MC', // モナコ
        462 => 'MV', // モルディブ
        498 => 'MD', // モルドバ
        504 => 'MA', // モロッコ
        496 => 'MN', // モンゴル国
        499 => 'ME', // モンテネグロ
        500 => 'MS', // モントセラト
        400 => 'JO', // ヨルダン
        418 => 'LA', // ラオス
        428 => 'LV', // ラトビア
        440 => 'LT', // リトアニア
        434 => 'LY', // リビア
        438 => 'LI', // リヒテンシュタイン
        430 => 'LR', // リベリア
        642 => 'RO', // ルーマニア
        442 => 'LU', // ルクセンブルク
        646 => 'RW', // ルワンダ
        422 => 'LB', // レバノン
        638 => 'RE', // レユニオン
        643 => 'RU', // ロシア
    ];

    /**
     * ISO 3166-1 numeric (Country.id) を alpha-2 へ変換する。
     * 未知の id / null は null を返す。
     */
    public function getAlpha2FromCountryId(?int $numericCountryId): ?string
    {
        if ($numericCountryId === null) {
            return null;
        }

        return self::NUMERIC_TO_ALPHA2[$numericCountryId] ?? null;
    }

    /**
     * Pref から region 文字列 (都道府県名) を返す。null は null を返す。
     */
    public function getRegionFromPref(?Pref $pref): ?string
    {
        if ($pref === null) {
            return null;
        }

        return $pref->getName();
    }

    /**
     * 住所系エンティティを ACP / UCP の住所 DTO 相当の配列へ写す。
     *
     * @return array{
     *     family_name: ?string,
     *     given_name: ?string,
     *     family_name_kana: ?string,
     *     given_name_kana: ?string,
     *     company: ?string,
     *     postal_code: ?string,
     *     region: ?string,
     *     address1: ?string,
     *     address2: ?string,
     *     country: ?string,
     *     phone: ?string
     * }
     */
    public function toAddressArray(Customer|CustomerAddress|Shipping $source): array
    {
        $country = $this->extractCountry($source);
        $countryId = $country !== null ? $country->getId() : null;

        return [
            'family_name' => $this->callIfExists($source, 'getName01'),
            'given_name' => $this->callIfExists($source, 'getName02'),
            'family_name_kana' => $this->callIfExists($source, 'getKana01'),
            'given_name_kana' => $this->callIfExists($source, 'getKana02'),
            'company' => $this->callIfExists($source, 'getCompanyName'),
            'postal_code' => $this->callIfExists($source, 'getPostalCode'),
            'region' => $this->getRegionFromPref($this->extractPref($source)),
            'address1' => $this->callIfExists($source, 'getAddr01'),
            'address2' => $this->callIfExists($source, 'getAddr02'),
            'country' => $this->getAlpha2FromCountryId($countryId !== null ? (int) $countryId : null),
            'phone' => $this->extractPhoneNumber($source),
        ];
    }

    private function extractCountry(Customer|CustomerAddress|Shipping $source): ?Country
    {
        // Customer / CustomerAddress / Shipping いずれも getCountry() を持つ。
        return $source->getCountry();
    }

    private function extractPref(Customer|CustomerAddress|Shipping $source): ?Pref
    {
        // Customer / CustomerAddress / Shipping いずれも getPref() を持つ。
        return $source->getPref();
    }

    /**
     * 電話番号を取得する。
     * Customer / CustomerAddress / Shipping いずれも getPhoneNumber() を持つ。
     */
    private function extractPhoneNumber(Customer|CustomerAddress|Shipping $source): ?string
    {
        return $source->getPhoneNumber();
    }

    /**
     * 指定 getter が存在すれば呼び出して文字列(or null)を返す。存在しなければ null。
     */
    private function callIfExists(Customer|CustomerAddress|Shipping $source, string $method): ?string
    {
        if (!method_exists($source, $method)) {
            return null;
        }

        try {
            $value = $source->$method();
        } catch (\TypeError) {
            // 一部エンティティ (Shipping の getName01/getKana01 等) は getter の戻り型が
            // 非 null の string だが、値が未設定だと TypeError になる。null 扱いにする。
            return null;
        }

        return $value === null ? null : (string) $value;
    }
}
