<?php
require_once("../require.php");
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

function createInsert($tableName, $arr) {

    $sql = "";
    $i = 0;
    foreach ($arr as $key => $val) {
        $sql .= sprintf("INSERT INTO %s VALUES (", $tableName);
        $sql .= "'" . $key . "', "
             .  "'" . $val . "',"
             .        $i   . ");\n";
        $i++;
    }
    return $sql;
}

// アクセス権限
// 0:管理者のみアクセス可能
// 1:一般以上がアクセス可能
$arrPERMISSION[URL_SYSTEM_TOP] = 0;
$arrPERMISSION["/admin/system/delete.php"] = 0;
$arrPERMISSION["/admin/system/index.php"] = 0;
$arrPERMISSION["/admin/system/input.php"] = 0;
$arrPERMISSION["/admin/system/master.php"] = 0;
$arrPERMISSION["/admin/system/master_delete.php"] = 0;
$arrPERMISSION["/admin/system/master_rank.php"] = 0;
$arrPERMISSION["/admin/system/mastercsv.php"] = 0;
$arrPERMISSION["/admin/system/rank.php"] = 0;
$arrPERMISSION["/admin/entry/index.php"] = 1;
$arrPERMISSION["/admin/entry/delete.php"] = 1;
$arrPERMISSION["/admin/entry/inputzip.php"] = 1;
$arrPERMISSION["/admin/search/delete_note.php"] = 1;

print(createInsert("mtb_permission", $arrPERMISSION));

// ログアウト不可ページ
$arrDISABLE_LOGOUT = array(
    1 => "/shopping/deliv.php",
    2 => "/shopping/payment.php",
    3 => "/shopping/confirm.php",
    4 => "/shopping/card.php",
    5 => "/shopping/loan.php",
);

print(createInsert("mtb_disable_logout", $arrDISABLE_LOGOUT));

// メンバー管理-権限
$arrAUTHORITY[0] = "管理者";
//$arrAUTHORITY[1] = "一般";
// $arrAUTHORITY[2] = "閲覧";

print(createInsert("mtb_authority", $arrAUTHORITY));

// メンバー管理-稼働状況
$arrWORK[0] = "非稼働";
$arrWORK[1] = "稼働";

print(createInsert("mtb_work", $arrWORK));

// 商品登録-表示
$arrDISP[1] = "公開";
$arrDISP[2] = "非公開";

print(createInsert("mtb_disp", $arrDISP));

// 商品登録-規格
$arrCLASS[1] = "規格無し";
$arrCLASS[2] = "規格有り";

print(createInsert("mtb_class", $arrCLASS));

// 検索ランク
$arrSRANK[1] = 1;
$arrSRANK[2] = 2;
$arrSRANK[3] = 3;
$arrSRANK[4] = 4;
$arrSRANK[5] = 5;

print(createInsert("mtb_srank", $arrSRANK));

// 商品登録-ステータス
$arrSTATUS[1] = "NEW";
$arrSTATUS[2] = "残りわずか";
$arrSTATUS[3] = "ポイント２倍";
$arrSTATUS[4] = "オススメ";
$arrSTATUS[5] = "限定品";

print(createInsert("mtb_status", $arrSTATUS));

// 商品登録-ステータス画像
$arrSTATUS_IMAGE[1] = URL_DIR . "img/right_product/icon01.gif";
$arrSTATUS_IMAGE[2] = URL_DIR . "img/right_product/icon02.gif";
$arrSTATUS_IMAGE[3] = URL_DIR . "img/right_product/icon03.gif";
$arrSTATUS_IMAGE[4] = URL_DIR . "img/right_product/icon04.gif";
$arrSTATUS_IMAGE[5] = URL_DIR . "img/right_product/icon05.gif";

print(createInsert("mtb_status_image", $arrSTATUS_IMAGE));

// 入力許可するタグ
$arrAllowedTag = array(
    "table",
    "tr",
    "td",
    "a",
    "b",
    "blink",
    "br",
    "center",
    "font",
    "h",
    "hr",
    "img",
    "li",
    "strong",
    "p",
    "div",
    "i",
    "u",
    "s",
    "/table",
    "/tr",
    "/td",
    "/a",
    "/b",
    "/blink",
    "/br",
    "/center",
    "/font",
    "/h",
    "/hr",
    "/img",
    "/li",
    "/strong",
    "/p",
    "/div",
    "/i",
    "/u",
    "/s"
);

print(createInsert("mtb_allowed_tag", $arrAllowedTag));

// １ページ表示行数
$arrPageMax = array(
    10 => "10",
    20 => "20",
    30 => "30",
    40 => "40",
    50 => "50",
    60 => "60",
    70 => "70",
    80 => "80",
    90 => "90",
    100 => "100",
);

print(createInsert("mtb_page_max", $arrPageMax));

// メルマガ種別
$arrMagazineType["1"] = "HTML";
$arrMagazineType["2"] = "テキスト";

$arrMagazineTypeAll = $arrMagazineType;
$arrMagazineTypeAll["3"] = "HTMLテンプレート";

print(createInsert("mtb_magazine_type", $arrMagazineTypeAll));

/* メルマガ種別 */
$arrMAILMAGATYPE = array(
    1 => "HTMLメール",
    2 => "テキストメール",
    3 => "希望しない"
);

print(createInsert("mtb_mail_magazine_type", $arrMAILMAGATYPE));

/* おすすめレベル */
$arrRECOMMEND = array(
    5 => "★★★★★",
    4 => "★★★★",
    3 => "★★★",
    2 => "★★",
    1 => "★"
);

print(createInsert("mtb_recommend", $arrRECOMMEND));

$arrTAXRULE = array(
    1 => "四捨五入",
    2 => "切り捨て",
    3 => "切り上げ"
);

print(createInsert("mtb_taxrule", $arrTAXRULE));

// メールテンプレートの種類
$arrMAILTEMPLATE = array(
     1 => "注文受付メール"
    ,2 => "注文キャンセル受付メール"
    ,3 => "取り寄せ確認メール"
);

print(createInsert("mtb_mail_template", $arrMAILTEMPLATE));

// 各テンプレートのパス
$arrMAILTPLPATH = array(
    1 => "mail_templates/order_mail.tpl",
    2 => "mail_templates/order_mail.tpl",
    3 => "mail_templates/order_mail.tpl",
    4 => "mail_templates/contact_mail.tpl",
);

print(createInsert("mtb_mail_tpl_path", $arrMAILTPLPATH));

/* 都道府県配列 */
$arrPref = array(
                    1 => "北海道",
                    2 => "青森県",
                    3 => "岩手県",
                    4 => "宮城県",
                    5 => "秋田県",
                    6 => "山形県",
                    7 => "福島県",
                    8 => "茨城県",
                    9 => "栃木県",
                    10 => "群馬県",
                    11 => "埼玉県",
                    12 => "千葉県",
                    13 => "東京都",
                    14 => "神奈川県",
                    15 => "新潟県",
                    16 => "富山県",
                    17 => "石川県",
                    18 => "福井県",
                    19 => "山梨県",
                    20 => "長野県",
                    21 => "岐阜県",
                    22 => "静岡県",
                    23 => "愛知県",
                    24 => "三重県",
                    25 => "滋賀県",
                    26 => "京都府",
                    27 => "大阪府",
                    28 => "兵庫県",
                    29 => "奈良県",
                    30 => "和歌山県",
                    31 => "鳥取県",
                    32 => "島根県",
                    33 => "岡山県",
                    34 => "広島県",
                    35 => "山口県",
                    36 => "徳島県",
                    37 => "香川県",
                    38 => "愛媛県",
                    39 => "高知県",
                    40 => "福岡県",
                    41 => "佐賀県",
                    42 => "長崎県",
                    43 => "熊本県",
                    44 => "大分県",
                    45 => "宮崎県",
                    46 => "鹿児島県",
                    47 => "沖縄県"
                );

/* 職業配列 */
$arrJob = array(
                    1 => "公務員",
                    2 => "コンサルタント",
                    3 => "コンピュータ関連技術職",
                    4 => "コンピュータ関連以外の技術職",
                    5 => "金融関係",
                    6 => "医師",
                    7 => "弁護士",
                    8 => "総務・人事・事務",
                    9 => "営業・販売",
                    10 => "研究・開発",
                    11 => "広報・宣伝",
                    12 => "企画・マーケティング",
                    13 => "デザイン関係",
                    14 => "会社経営・役員",
                    15 => "出版・マスコミ関係",
                    16 => "学生・フリーター",
                    17 => "主婦",
                    18 => "その他"
                );

print(createInsert("mtb_job", $arrJob));

/* パスワードの答え配列 */
$arrReminder = array(
                        1 => "母親の旧姓は？",
                        2 => "お気に入りのマンガは？",
                        3 => "大好きなペットの名前は？",
                        4 => "初恋の人の名前は？",
                        5 => "面白かった映画は？",
                        6 => "尊敬していた先生の名前は？",
                        7 => "好きな食べ物は？"
                    );

print(createInsert("mtb_reminder", $arrReminder));

/*　性別配列　*/
$arrSex = array(
                    1 => "男性",
                    2 => "女性"
                );

print(createInsert("mtb_sex", $arrSex));

/*　メールアドレス種別　*/
define ("MAIL_TYPE_PC",1);
define ("MAIL_TYPE_MOBILE",2);
$arrMailType = array(
                    MAIL_TYPE_PC => "パソコン用アドレス",
                    MAIL_TYPE_MOBILE => "携帯用アドレス",
                );

print(createInsert("mtb_mail_type", $arrMailType));

/*　1行数　*/
$arrPageRows = array(
                        10 => 10,
                        20 => 20,
                        30 => 30,
                        40 => 40,
                        50 => 50,
                        60 => 60,
                        70 => 70,
                        80 => 80,
                        90 => 90,
                        100 => 100,
                    );

print(createInsert("mtb_page_rows", $arrPageRows));

/* 受注ステータス */
define ("ORDER_NEW",1);	 		// 新規注文
define ("ORDER_PAY_WAIT",2);	// 入金待ち
define ("ORDER_PRE_END",6);		// 入金済み
define ("ORDER_CANCEL",3);		// キャンセル
define ("ORDER_BACK_ORDER",4);	// 取り寄せ中
define ("ORDER_DELIV",5);		// 発送済み

/* 受注ステータス */
$arrORDERSTATUS = array(
    ORDER_NEW        => "新規受付",
    ORDER_PAY_WAIT   => "入金待ち",
    ORDER_PRE_END    => "入金済み",
    ORDER_CANCEL     => "キャンセル",
    ORDER_BACK_ORDER => "取り寄せ中",
    ORDER_DELIV      => "発送済み"
);

// 受注ステータス変更の際にポイント等を加算するステータス番号（発送済み）
define("ODERSTATUS_COMMIT", ORDER_DELIV);

print(createInsert("mtb_order_status", $arrORDERSTATUS));

/* 商品種別の表示色 */
$arrPRODUCTSTATUS_COLOR = array(
    1 => "#FFFFFF",
    2 => "#C9C9C9",
    3 => "#DDE6F2"
);

print(createInsert("mtb_order_status_color", $arrORDERSTATUS));

$arrORDERSTATUS_COLOR = array(
    1 => "#FFFFFF",
    2 => "#FFDE9B",
    3 => "#C9C9C9",
    4 => "#FFD9D9",
    5 => "#BFDFFF",
    6 => "#FFFFAB"
);

print(createInsert("mtb_order_status_color", $arrORDERSTATUS_COLOR));

// 曜日
$arrWDAY = array(
    0 => "日",
    1 => "月",
    2 => "火",
    3 => "水",
    4 => "木",
    5 => "金",
    6 => "土"
);

print(createInsert("mtb_wday", $arrWDAY));

/* 新着情報管理画面 */
define ("ADMIN_NEWS_STARTYEAR", 2005);	// 開始年(西暦)

/* 会員登録 */
define("ENTRY_CUSTOMER_TEMP_SUBJECT", "会員仮登録が完了いたしました。");
define("ENTRY_CUSTOMER_REGIST_SUBJECT", "本会員登録が完了いたしました。");
define("ENTRY_LIMIT_HOUR", 1);		//再入会制限時間（単位: 時間)

// オススメ商品表示数
define("RECOMMEND_NUM", 8);			// オススメ商品
define ("BEST_MAX", 5);				// ベスト商品の最大登録数
define ("BEST_MIN", 3);				// ベスト商品の最小登録数（登録数が満たない場合は表示しない。)

//発送日目安
$arrDELIVERYDATE = array(
    1 => "即日",
    2 => "1〜2日後",
    3 => "3〜4日後",
    4 => "1週間以降",
    5 => "2週間以降",
    6 => "3週間以降",
    7 => "1ヶ月以降",
    8 => "2ヶ月以降",
    9 => "お取り寄せ(商品入荷後)"
);

print(createInsert("mtb_delivery_date", $arrDELIVERYDATE));

/* 配達可能な日付以降のプルダウン表示最大日数 */
define("DELIV_DATE_END_MAX", 21);

/* 購入時強制会員登録 */
define("PURCHASE_CUSTOMER_REGIST", 0);	//1:有効　0:無効

/* 商品リスト表示件数 */
$arrPRODUCTLISTMAX = array(
    15 => '15件',
    30 => '30件',
    50 => '50件'
);

print(createInsert("mtb_product_list_max", $arrPRODUCTLISTMAX));

/* この商品を買った人はこんな商品も買っています　表示件数 */
define("RELATED_PRODUCTS_MAX", 3);

/*--------- ▼コンビニ決済用 ---------*/

//コンビニの種類
$arrCONVENIENCE = array(
    1 => 'セブンイレブン',
    2 => 'ファミリーマート',
    3 => 'サークルKサンクス',
    4 => 'ローソン・セイコーマート',
    5 => 'ミニストップ・デイリーヤマザキ・ヤマザキデイリーストア',
);

print(createInsert("mtb_convenience", $arrCONVENIENCE));

//各種コンビニ用メッセージ
$arrCONVENIMESSAGE = array(
    1 => "上記URLから振込票を印刷、もしくは振込票番号を紙に控えて、全国のセブンイレブンにてお支払いください。",
    2 => "企業コード、受付番号を紙などに控えて、全国のファミリーマートにお支払いください。",
    3 => "上記URLから振込票を印刷、もしくはケータイ決済番号を紙などに控えて、全国のサークルKサンクスにてお支払ください。",
    4 => "振込票番号を紙に控えて、全国のローソンまたはセイコーマートにてお支払いください。",
    5 => "上記URLから振込票を印刷し、全国のミニストップ・デイリーヤマザキ・ヤマザキデイリーストアにてお支払いください。"
);

print(createInsert("mtb_conveni_message", $arrCONVENIMESSAGE));

//支払期限
define("CV_PAYMENT_LIMIT", 14);

/*--------- ▲コンビニ決済用 ---------*/

//キャンペーン登録最大数
define("CAMPAIGN_REGIST_MAX", 20);

//DBの種類
$arrDB = array(
    1 => 'PostgreSQL',
    2 => 'MySQL'
);

print(createInsert("mtb_db", $arrDB));

// ブロック配置
$arrTarget = array(
    1 => "LeftNavi",
    2 => "MainHead",
    3 => "RightNavi",
    4 => "MainFoot",
    5 => "Unused"
);

print(createInsert("mtb_target", $arrTarget));

/*--------- ▲商品レビュー用 ---------*/
// 商品レビューでURL書き込みを許可するか否か
define ('REVIEW_ALLOW_URL', false);

// 書き込み不可のURL文字列
$arrReviewDenyURL = array(
    'http://',
    'https://',
    'ttp://',
    'ttps://',
);

print(createInsert("mtb_review_deny_url", $arrReviewDenyURL));

/*--------- ▲トラックバック用 ---------*/

define ("TRACKBACK_STATUS_VIEW", 1);		// 表示
define ("TRACKBACK_STATUS_NOT_VIEW", 2);	// 非表示
define ("TRACKBACK_STATUS_SPAM", 3);		// スパム

define ("TRACKBACK_VIEW_MAX", 10);			// フロント最大表示数
define ("TRACKBACK_TO_URL", SITE_URL . "tb/index.php?pid=");	// トラックバック先URL

// 状態
$arrTrackBackStatus = array(
    1 => "表示",
    2 => "非表示",
    3 => "スパム"
);

print(createInsert("mtb_track_back_status", $arrTrackBackStatus));

/*--------- ▲サイト管理用 ---------*/

define ("SITE_CONTROL_TRACKBACK", 1);		// トラックバック
define ("SITE_CONTROL_AFFILIATE", 2);		// アフィリエイト

// トラックバック
$arrSiteControlTrackBack = array(
    1 => "有効",
    2 => "無効"
);

print(createInsert("mtb_site_control_track_back", $arrSiteControlTrackBack));

// アフィリエイト
$arrSiteControlAffiliate = array(
    1 => "有効",
    2 => "無効"
);

print(createInsert("mtb_site_control_affiliate", $arrSiteControlAffiliate));


?>
