<?php

$CONF_PHP_PATH = realpath( dirname( __FILE__) );
require_once($CONF_PHP_PATH ."/../../html/install.inc");
require_once($CONF_PHP_PATH ."/core.php" );

//--------------------------------------------------------------------------------------------------------
/** アップデート管理用 **/
// アップデート管理用ファイル格納場所
define("UPDATE_HTTP", "http://ec-cube.lockon.co.jp/share/");
// アップデート管理用CSV1行辺りの最大文字数
define("UPDATE_CSV_LINE_MAX", 4096);
// アップデート管理用CSVカラム数
define("UPDATE_CSV_COL_MAX", 13);
//--------------------------------------------------------------------------------------------------------

//バッチを実行する最短の間隔(秒)
define("LOAD_BATCH_PASS", 10);

define("CLOSE_DAY", 31);	// 締め日の指定(末日の場合は、31を指定してください。)

//一般サイトエラー
define("FAVORITE_ERROR", 13);

//おすすめ商品数
define ("RECOMMEND_PRODUCT_MAX", 4);

/** グラフ関連 **/
	
define("LIB_DIR", ROOT_DIR . "data/lib/");						// ライブラリのパス
define("TTF_DIR", ROOT_DIR . "data/fonts/");					// フォントのパス
define("GRAPH_DIR", ROOT_DIR . "html/upload/graph_image/");		// グラフ格納ディレクトリ
define("GRAPH_URL", "/upload/graph_image/");					// グラフURL
define("GRAPH_PIE_MAX", 10);									// 円グラフ最大表示数
define("GRAPH_LABEL_MAX", 40);									// グラフのラベルの文字数

/** パス関連 **/

define("PDF_DIR", ROOT_DIR . "data/pdf/");	// PDF格納ディレクトリ

/** 売上げ集計 **/

define("BAT_ORDER_AGE", 70);		// 何歳まで集計の対象とするか
define("PRODUCTS_TOTAL_MAX", 15);	// 商品集計で何位まで表示するか

/** デフォルト値 **/
define("DEFAULT_PRODUCT_DISP", 2);	// 1:公開 2:非公開

/** オプション設定 **/
define("DELIV_FREE_AMOUNT", 0);				// 送料無料購入個数（0の場合は、何個買っても無料にならない)
define("INPUT_DELIV_FEE", 1);				// 配送料の設定画面表示(有効:1 無効:0)
define("OPTION_PRODUCT_DELIV_FEE", 0);		// 商品ごとの送料設定(有効:1 無効:0)
define("OPTION_DELIV_FEE", 1);				// 配送業者ごとの配送料を加算する(有効:1 無効:0)
define("OPTION_RECOMMEND", 1);		// おすすめ商品登録(有効:1 無効:0)
define("OPTION_CLASS_REGIST", 1);	// 商品規格登録(有効:1 無効:0)

define("TV_IMAGE_WIDTH",170);		//TV連動商品画像横
define("TV_IMAGE_HEIGHT",95);		//TV連動商品画像縦
define("TV_PRODUCTS_MAX",10);		//TV連動商品最大登録数

/** オプション設定 **/



//会員登録変更(マイページ)パスワード用
define("DEFAULT_PASSWORD", "UAhgGR3L");
//別のお届け先最大登録数
define("DELIV_ADDR_MAX", 20);
//マイページお届け先URL
define("MYPAGE_DELIVADDR_URL", "/mypage/delivery.php");
//閲覧履歴保存数
define("CUSTOMER_READING_MAX",30);
//SSLURL判定
define("SSLURL_CHECK", 0);
//管理画面ステータス一覧表示件数
define("ORDER_STATUS_MAX", 50);
//フロントレビュー書き込み最大数
define("REVIEW_REGIST_MAX", 5);

/*
 * サイト定義定数
 */
/* システム関連 */
define ("LOGIN_FRAME", "login_frame.tpl");				// ログイン画面フレーム
define ("MAIN_FRAME", "main_frame.tpl");				// 管理画面フレーム
define ("SITE_FRAME", "site_frame.tpl");				// 一般サイト画面フレーム
define ("CERT_STRING", "7WDhcBTF");						// 認証文字列
define ("ADMIN_ID", "1");								// 管理ユーザID(メンテナンス用表示されない。)
define ("DUMMY_PASS", "########");						// ダミーパスワード
define ("UNLIMITED", "++");								// 在庫数、販売制限無限を示す。
define ("BIRTH_YEAR", 1901);							// 生年月日登録開始年
define ("RELEASE_YEAR", 2005);							// 本システムの稼働開始年
define ("CREDIT_ADD_YEAR", 10);							// クレジットカードの期限＋何年
define ("PARENT_CAT_MAX", 12);							// 親カテゴリのカテゴリIDの最大数（これ以下は親カテゴリとする。)
define ("NUMBER_MAX", 1000000000);						// GET値変更などのいたずらを防ぐため最大数制限を設ける。
define ("POINT_RULE", 2);								// ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ)
define ("POINT_VALUE", 1);								// 1ポイント当たりの値段(円)
define ("ADMIN_MODE", 0);								// 管理モード 1:有効　0:無効(納品時)

define ("FORGOT_MAIL", 0);								// パスワード忘れの確認メールを送付するか否か。(0:送信しない、1:送信する)
define ("HTML_TEMPLATE_SUB_MAX", 12);					// 登録できるサブ商品の数
define ("LINE_LIMIT_SIZE", 60);							// 文字数が多すぎるときに強制改行するサイズ(半角)
define ("BIRTH_MONTH_POINT", 0);						// 誕生日月ポイント
define ("INPUT_DELIV_FEE", true);						// 配送料の設定画面(true:あり、false:なし)

/* クレジットローン(セントラルファイナンス) */
define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredit");					// ホームアドレス
define ("CF_STORECODE", "361901000000000");									// 加盟店コード(ハイフンなしで）
// define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredittest");				// ホームアドレス(テスト用)
// define ("CF_STORECODE", "111111111111111");									// 加盟店コード(テスト用)

define ("CF_SIMULATE", "/simulate/simulate.cgi");							// シュミレーション呼び出し
// define ("CF_RETURNURL", SSL_URL . "shopping/loan.php");						// 戻り先 ショッピングローンは次期開発
// define ("CF_CANCELURL", SSL_URL . "shopping/loan_cancel.php");				// 戻り先 ショッピングローンは次期開発
define ("CF_CONTINUE", "1");												// 呼び出し区分(0:シュミレーションのみ、1:シュミレーション+申込)
define ("CF_LABOR", "0");													// 役務有無区分(0:無、1:有)
define ("CF_RESULT", "1");													// 結果応答(1:結果あり、2:結果なし)

/* クレジットカード(ベリトランス) */
define ("CGI_DIR", ROOT_DIR . "cgi-bin/");									// モジュール格納ディレクトリ
define ("CGI_FILE", "mauthonly.cgi");										// コアCGI

// ルートカテゴリID
define ("ROOT_CATEGORY_1", 2);
define ("ROOT_CATEGORY_2", 3);
define ("ROOT_CATEGORY_3", 4);
define ("ROOT_CATEGORY_4", 5);
define ("ROOT_CATEGORY_5", 6);
define ("ROOT_CATEGORY_6", 7);
define ("ROOT_CATEGORY_7", 8);

// お支払い方法特殊ID
define ("PAYMENT_DAIBIKI_ID",1);		// 代金引換
define ("PAYMENT_GINFURI_ID", 2);		// 銀行振込
define ("PAYMENT_KAKITOME_ID", 3);		// 現金書留
define ("PAYMENT_CREDIT_ID",4);			// クレジットカード
define ("PAYMENT_LOAN_ID", 5);			// ショッピングローン
define ("PAYMENT_CONVENIENCE_ID", 6);	// コンビニ決済

define("LARGE_IMAGE_WIDTH",  500);						// 拡大画像横
define("LARGE_IMAGE_HEIGHT", 500);						// 拡大画像縦
define("SMALL_IMAGE_WIDTH",  130);						// 一覧画像横
define("SMALL_IMAGE_HEIGHT", 130);						// 一覧画像縦
define("NORMAL_IMAGE_WIDTH",  260);						// 通常画像横
define("NORMAL_IMAGE_HEIGHT", 260);						// 通常画像縦
define("NORMAL_SUBIMAGE_WIDTH", 130);					// 通常サブ画像横
define("NORMAL_SUBIMAGE_HEIGHT", 130);					// 通常サブ画像縦
define("LARGE_SUBIMAGE_WIDTH", 500);					// 拡大サブ画像横
define("LARGE_SUBIMAGE_HEIGHT", 500);					// 拡大サブ画像縦
define("DISP_IMAGE_WIDTH",  65);						// 一覧表示画像横
define("DISP_IMAGE_HEIGHT", 65);						// 一覧表示画像縦
define("OTHER_IMAGE1_WIDTH", 500);						// その他の画像1
define("OTHER_IMAGE1_HEIGHT", 500);						// その他の画像1
define("HTMLMAIL_IMAGE_WIDTH",  110);					// HTMLメールテンプレートメール担当画像横
define("HTMLMAIL_IMAGE_HEIGHT", 120);					//  HTMLメールテンプレートメール担当画像縦

define("IMAGE_SIZE", 100);								// 画像サイズ制限(KB)
define("CSV_SIZE", 2000);								// CSVサイズ制限(KB)
define("PDF_SIZE", 5000);								// PDFサイズ制限(KB):商品詳細ファイル等
define("LEVEL_MAX", 3);									// カテゴリの最大階層
define("CATEGORY_MAX", 1000);							// 最大カテゴリ登録数

/* 表示関連 */
define ("ADMIN_TITLE", "ECサイト管理ページ");			// 管理ページタイトル
define ("SELECT_RGB", "#ffffdf");						// 編集時強調表示色
define ("DISABLED_RGB", "#dddddd");						// 入力項目無効時の表示色
define ("ERR_COLOR", "#ffe8e8");						// エラー時表示色
define ("CATEGORY_HEAD", "■");							// 親カテゴリ表示文字
define ("START_BIRTH_YEAR", 1901);						// 生年月日選択開始年

/* システムパス */
define ("LOG_PATH", ROOT_DIR . "data/logs/site.log");							// ログファイル
define ("CUSTOMER_LOG_PATH", ROOT_DIR . "data/logs/customer.log");				// 会員ログイン ログファイル
define ("TEMPLATE_ADMIN_DIR", ROOT_DIR . "data/Smarty/templates/admin");		// SMARTYテンプレート
define ("TEMPLATE_DIR", ROOT_DIR . "data/Smarty/templates");					// SMARTYテンプレート
define ("COMPILE_ADMIN_DIR", ROOT_DIR . "data/Smarty/templates_c/admin");		// SMARTYコンパイル
define ("COMPILE_DIR", ROOT_DIR . "data/Smarty/templates_c");					// SMARTYコンパイル

define ("TEMPLATE_FTP_DIR", ROOT_DIR . "html/user_data/templates/");			// SMARTYテンプレート(FTP許可)
define ("COMPILE_FTP_DIR", ROOT_DIR . "data/Smarty/templates_c/user_data/");	// SMARTYコンパイル

define ("IMAGE_TEMP_DIR", ROOT_DIR . "html/upload/temp_image/");				// 画像一時保存
define ("IMAGE_SAVE_DIR", ROOT_DIR . "html/upload/save_image/");				// 画像保存先
define ("IMAGE_TEMP_URL", "/upload/temp_image/");								// 画像一時保存URL
define ("IMAGE_SAVE_URL", "/upload/save_image/");								// 画像保存先URL
define ("CSV_TEMP_DIR", ROOT_DIR. "html/upload/csv/");							// エンコードCSVの一時保存先
define ("NO_IMAGE_URL", "/misc/dummy.gif");										// 画像がない場合に表示

/* URLパス */
define ("URL_SYSTEM_TOP", "/admin/system/index.php");		// システム管理トップ
define ("URL_CLASS_REGIST", "/admin/products/class.php");	// 規格登録
define ("URL_INPUT_ZIP", "/input_zip.php");			// 郵便番号入力
define ("URL_DELIVERY_TOP", "/admin/basis/delivery.php");	// 配送業者登録
define ("URL_PAYMENT_TOP", "/admin/basis/payment.php");		// 支払い方法登録
define ("URL_HOME", "/admin/home.php");						// ホーム
define ("URL_LOGIN", "/admin/index.php");					// ログインページ
define ("URL_SEARCH_TOP", "/admin/products/index.php");		// 商品検索ページ
define ("URL_ORDER_EDIT", "/admin/order/edit.php");			// 注文編集ページ
define ("URL_SEARCH_ORDER", "/admin/order/index.php");		// 注文編集ページ
define ("URL_ORDER_MAIL", "/admin/order/mail.php");			// 注文編集ページ
define ("URL_LOGOUT", "/admin/logout.php");					// ログアウトページ
define ("URL_SYSTEM_CSV", "/admin/system/member_csv.php");	// システム管理CSV出力ページ
define ("URL_SYSTEM_TOP", "/admin/system/index.php");		// システム管理TOPページ
define ("URL_ADMIN_CSS", "/admin/css/");					// 管理ページ用CSS保管ディレクトリ

/* 認証エラー */
define ("SUCCESS", 0);			// アクセス成功
define ("LOGIN_ERROR", 1);		// ログイン失敗
define ("ACCESS_ERROR", 2);		// アクセス失敗（タイムアウト等）
define ("AUTH_ERROR", 3);		// アクセス権限違反

/* 表示数制限 */
define ("PRODUCTS_LIST_MAX", 15);	// 商品一覧表示数
define ("MEMBER_PMAX", 10);			// メンバー管理ページ表示行数
define ("SEARCH_PMAX", 10);			// 検索ページ表示行数
define ("NAVI_PMAX", 5);			// ページ番号の最大表示個数
define ("PRODUCTSUB_MAX", 5);		// 商品サブ情報最大数
define ("DELIVTIME_MAX", 16);		// 配送時間の最大表示数
define ("DELIVFEE_MAX", 47);		// 配送料金の最大表示数

/* 文字数制限 */
define ("STEXT_LEN", 50);		// 短い項目の文字数（名前など)
define ("SMTEXT_LEN", 100);
define ("MTEXT_LEN", 200);		// 長い項目の文字数（住所など）
define ("MLTEXT_LEN", 1000);	// 長中文の文字数（問い合わせなど）
define ("LTEXT_LEN", 3000);		// 長文の文字数
define ("LLTEXT_LEN", 99999);	// 超長文の文字数（メルマガなど）
define ("URL_LEN", 300);		// URLの文字長
define("ID_MAX_LEN", 15);		// ID・パスワードの文字数制限
define("ID_MIN_LEN", 4);		// ID・パスワードの文字数制限
define("PRICE_LEN", 8);			// 金額桁数
define("PERCENTAGE_LEN", 3);	// 率桁数
define("AMOUNT_LEN", 6);		// 在庫数、販売制限数
define("ZIP01_LEN", 3);			// 郵便番号1
define("ZIP02_LEN", 4);			// 郵便番号2
define("TEL_ITEM_LEN", 6);		// 電話番号各項目制限
define("TEL_LEN", 12);			// 電話番号総数
define("PASSWORD_LEN1", 4);		// パスワード1
define("PASSWORD_LEN2", 10);	// パスワード2
define("INT_LEN", 8);			// 検査数値用桁数(INT)
define("CREDIT_NO_LEN", 4);		// クレジットカードの文字数
define("SEARCH_CATEGORY_LEN", 18);	// 検索カテゴリ最大表示文字数(byte)

/** フロントページ **/

/* システム関連 */
define ("SALE_LIMIT_MAX", 10);		// 購入制限なしの場合の最大購入個数
define ("SITE_TITLE", "ＥＣ-ＣＵＢＥ  テストサイト");	// HTMLタイトル
define ("COOKIE_EXPIRE", 365);		// クッキー保持期限(日)
define ("FREE_DIAL", "0120-339337");

/* 一般サイトエラー */
define ("PRODUCT_NOT_FOUND", 1);	// 指定商品ページがない
define ("CART_EMPTY", 2);			// カート内が空
define ("PAGE_ERROR", 3);			// ページ推移エラー
define ("CART_ADD_ERROR", 4);		// 購入処理中のカート商品追加エラー
define ("CANCEL_PURCHASE", 5);		// 他にも購入手続きが行われた場合
define ("CATEGORY_NOT_FOUND", 6);	// 指定カテゴリページがない
define ("SITE_LOGIN_ERROR", 7);		// ログインに失敗
define ("CUSTOMER_ERROR", 8);		// 会員専用ページへのアクセスエラー
define ("SOLD_OUT", 9);				// 購入時の売り切れエラー
define ("CART_NOT_FOUND", 10);		// カート内商品の読込エラー
define ("LACK_POINT", 11);			// ポイントの不足
define ("TEMP_LOGIN_ERROR", 12);	// 仮登録者がログインに失敗
define ("URL_ERROR", 13);			// URLエラー
define ("EXTRACT_ERROR", 14);		//ファイル解凍エラー
define ("FTP_DOWNLOAD_ERROR", 15);	//FTPダウンロードエラー
define ("FTP_LOGIN_ERROR", 16);		//FTPログインエラー
define ("FTP_CONNECT_ERROR", 17);	//FTP接続エラー
define ("CREATE_DB_ERROR", 18);		//DB作成エラー
define ("DB_IMPORT_ERROR", 19);		//DBインポートエラー
define ("FILE_NOT_FOUND", 20);		//設定ファイル存在エラー
define ("WRITE_FILE_ERROR", 21);	//ファイル書き込みエラー

/* 表示関連 */
define ("SEPA_CATNAVI", " > ");	// カテゴリ区切り文字
define ("SEPA_CATLIST", " | ");	// カテゴリ区切り文字

/* URL */
define ("URL_SITE_TOP", "/index.php");					// サイトトップ
define ("URL_CART_TOP", "/cart/index.php");				// カートトップ
define ("URL_SHOP_CONFIRM", "/shopping/confirm.php");	// 購入確認ページ
define ("URL_SHOP_PAYMENT", "/shopping/payment.php");	// お支払い方法選択ページ
define ("URL_SHOP_TOP", "/shopping/index.php");			// 会員情報入力
define ("URL_SHOP_COMPLETE", "/shopping/complete.php");	// 購入完了画面
define ("URL_SHOP_CREDIT", "/shopping/card.php");		// カード決済画面
define ("URL_SHOP_LOAN", "/shopping/loan.php");			// ローン決済画面
define ("URL_SHOP_CONVENIENCE", "/shopping/convenience.php");	// コンビニ決済画面
define ("URL_PRODUCTS_TOP","/products/top.php");		// 商品トップ
define ("LIST_P_HTML", "/products/list-p");				// 商品一覧(HTML出力)
define ("LIST_C_HTML", "/products/list.php?mode=search&category_id=");				// 商品一覧(HTML出力)
define ("DETAIL_P_HTML", "/products/detail.php?product_id=");			// 商品詳細(HTML出力)

/*
 * サイト定義変数
 */
 
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

// ログアウト不可ページ
$arrDISABLE_LOGOUT = array(
	1 => "/shopping/deliv.php",
	2 => "/shopping/payment.php",
	3 => "/shopping/confirm.php",
	4 => "/shopping/card.php",
	5 => "/shopping/loan.php",
);

// メンバー管理-権限
$arrAUTHORITY[0] = "管理者";
// $arrAUTHORITY[1] = "一般";
// $arrAUTHORITY[2] = "閲覧";

// メンバー管理-稼働状況
$arrWORK[0] = "非稼働";
$arrWORK[1] = "稼働";

// 商品登録-表示
$arrDISP[1] = "公開";
$arrDISP[2] = "非公開";

// 商品登録-規格
$arrCLASS[1] = "規格無し";
$arrCLASS[2] = "規格有り";

// 検索ランク
$arrSRANK[1] = 1;
$arrSRANK[2] = 2;
$arrSRANK[3] = 3;
$arrSRANK[4] = 4;
$arrSRANK[5] = 5;

// 商品登録-ステータス
$arrSTATUS[1] = "NEW";
$arrSTATUS[2] = "残りわずか";
$arrSTATUS[3] = "ポイント２倍";
$arrSTATUS[4] = "オススメ";
$arrSTATUS[5] = "限定品";

// 商品登録-ステータス画像
$arrSTATUS_IMAGE[1] = "/img/right_product/icon01.gif";
$arrSTATUS_IMAGE[2] = "/img/right_product/icon02.gif";
$arrSTATUS_IMAGE[3] = "/img/right_product/icon03.gif";
$arrSTATUS_IMAGE[4] = "/img/right_product/icon04.gif";
$arrSTATUS_IMAGE[5] = "/img/right_product/icon05.gif";

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
	
// メルマガ種別
$arrMagazineType["1"] = "HTML";
$arrMagazineType["2"] = "テキスト";

$arrMagazineTypeAll = $arrMagazineType;
$arrMagazineTypeAll["3"] = "HTMLテンプレート";


/* メルマガ種別 */
$arrMAILMAGATYPE = array(
	1 => "HTMLメール",
	2 => "テキストメール",
	3 => "希望しない"
);

/* おすすめレベル */
$arrRECOMMEND = array(
	5 => "★★★★★",
	4 => "★★★★",
	3 => "★★★",
	2 => "★★",
	1 => "★"
);

$arrTAXRULE = array(
	1 => "四捨五入",
	2 => "切り捨て",
	3 => "切り上げ"
);


// メールテンプレートの種類
$arrMAILTEMPLATE = array(
	 1 => "注文受付メール"
	,2 => "注文キャンセル受付メール"
	,3 => "取り寄せ確認メール"
);

// 各テンプレートのパス
$arrMAILTPLPATH = array(
	1 => "mail_templates/order_mail.tpl",
	2 => "mail_templates/order_mail.tpl",
	3 => "mail_templates/order_mail.tpl",
	4 => "mail_templates/contact_mail.tpl",
);

// 受注ステータス変更の際にポイント等を加算するステータス番号（発送済み）
define("ODERSTATUS_COMMIT", 5);

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
					1 => "会社員",
					2 => "教職",
					3 => "公務員",
					4 => "専門業・技術職",
					5 => "サービス業",
					6 => "自営業",
					7 => "学生",
					8 => "主婦",
					9 => "その他"
				);

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
/*　性別配列　*/
$arrSex = array(
					1 => "男性",
					2 => "女性"
				);

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
		
/* 受注ステータス */
$arrORDERSTATUS = array(
	1 => "新規受付",
	2 => "入金待ち",
	3 => "キャンセル",
	4 => "取り寄せ中",
	5 => "発送済み"
);

// 受注ステータス変更の際にポイント等を加算するステータス番号（発送済み）
define("ODERSTATUS_COMMIT", 5);

/* 商品種別の表示色 */
$arrPRODUCTSTATUS_COLOR = array(
	1 => "#FFFFFF",
	2 => "#DDDDDD",
	3 => "#DDE6F2"
);

$arrORDERSTATUS_COLOR = array(
	1 => "#FFFFFF",
	2 => "#FFFFB3",
	3 => "#AAAAAA",
	4 => "#EEC1FD",
	5 => "#DBFDE3"
);

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

//各種コンビニ用メッセージ
$arrCONVENIMESSAGE = array(
	1 => "上記URLから振込票を印刷、もしくは振込票番号を紙に控えて、全国のセブンイレブンにてお支払いください。",
	2 => "企業コード、受付番号を紙などに控えて、全国のファミリーマートにお支払いください。",
	3 => "上記URLから振込票を印刷、もしくはケータイ決済番号を紙などに控えて、全国のサークルKサンクスにてお支払ください。",
	4 => "振込票番号を紙に控えて、全国のローソンまたはセイコーマートにてお支払いください。",
	5 => "上記URLから振込票を印刷し、全国のミニストップ・デイリーヤマザキ・ヤマザキデイリーストアにてお支払いください。"
);

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

// テンプレート
$arrTemplate = array(
	1 => array(
			"TopImage" 		 	=> "/img/template/top_l1.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top1.tpl",
			"ProdImage" 		=> "/img/template/prod_l1.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product1.tpl",
			"DetailImage" 		=> "/img/template/detail_l1.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail1.tpl",
			"MypageImage" 		=> "/img/template/mypage_l1.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage1.tpl"
		),
	2 => array(
			"TopImage" 			=> "/img/template/top_l2.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top2.tpl",
			"ProdImage" 		=> "/img/template/prod_l2.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product2.tpl",
			"DetailImage"  		=> "/img/template/detail_l2.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail2.tpl",
			"MypageImage" 		=> "/img/template/mypage_l2.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage2.tpl"
		),
	3 => array(
			"TopImage" 			=> "/img/template/top_l3.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top3.tpl",
			"ProdImage" 		=> "/img/template/prod_l3.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product3.tpl",
			"DetailImage" 		=> "/img/template/detail_l3.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail3.tpl",
			"MypageImage" 		=> "/img/template/mypage_l3.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage3.tpl"
		),
	4 => array(
			"TopImage" 			=> "/img/template/top_l4.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top4.tpl",
			"ProdImage" 		=> "/img/template/prod_l4.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product4.tpl",
			"DetailImage" 		=> "/img/template/detail_l4.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail4.tpl",
			"MypageImage" 		=> "/img/template/mypage_l4.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage4.tpl"
		),
	5 => array(
			"TopImage" 			=> "/img/template/top_l5.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top5.tpl",
			"ProdImage" 		=> "/img/template/prod_l5.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product5.tpl",
			"DetailImage" 		=> "/img/template/detail_l5.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail5.tpl",
			"MypageImage" 		=> "/img/template/mypage_l5.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage5.tpl"
		),
	6 => array(
			"TopImage" 			=> "/img/template/top_l6.gif",
			"TopTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/top6.tpl",
			"ProdImage" 		=> "/img/template/prod_l6.gif",
			"ProdTemplate" 		=> ROOT_DIR."data/Smarty/templates/sample/product6.tpl",
			"DetailImage" 		=> "/img/template/detail_l6.gif",
			"DetailTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/detail6.tpl",
			"MypageImage" 		=> "/img/template/mypage_l6.gif",
			"MypageTemplate" 	=> ROOT_DIR."data/Smarty/templates/sample/mypage6.tpl"
		)
);

// ブロック配置
$arrTarget = array(
	1 => "LeftNavi",
	2 => "MainHead",
	3 => "RightNavi",
	4 => "MainFoot",
	5 => "Unused"
);

?>