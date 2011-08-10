<?php
/** フロント表示関連 */
define('SAMPLE_ADDRESS1', "市区町村名 (例：千代田区神田神保町)");
/** フロント表示関連 */
define('SAMPLE_ADDRESS2', "番地・ビル名 (例：1-3-5)");
/** ユーザファイル保存先 */
define('USER_DIR', "user_data/");
/** ユーザファイル保存先 */
define('USER_REALDIR', HTML_REALDIR . USER_DIR);
/** ユーザインクルードファイル保存先 */
define('USER_INC_REALDIR', USER_REALDIR . "include/");
/** 郵便番号専用DB */
define('ZIP_DSN', DEFAULT_DSN);
/** ユーザー作成ページ等 */
define('USER_URL', HTTP_URL . USER_DIR);
/** 認証方式 */
define('AUTH_TYPE', "HMAC");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_DIR', "templates/");
/** テンプレートファイル保存先 */
define('USER_PACKAGE_DIR', "packages/");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_REALDIR', USER_REALDIR . USER_PACKAGE_DIR);
/** テンプレートファイル一時保存先 */
define('TEMPLATE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_template/");
/** ユーザー作成画面のデフォルトPHPファイル */
define('USER_DEF_PHP_REALFILE', USER_REALDIR . "__default.php");
/** その他画面のデフォルトページレイアウト */
define('DEF_LAYOUT', "products/list.php");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_DIR', "downloads/module/");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_REALDIR', DATA_REALDIR . MODULE_DIR);
/** DBセッションの有効期限(秒) */
define('MAX_LIFETIME', 7200);
/** マスターデータキャッシュディレクトリ */
define('MASTER_DATA_REALDIR', DATA_REALDIR . "cache/");
/** アップデート管理用ファイル格納場所 */
define('UPDATE_HTTP', "http://sv01.ec-cube.net/info/index.php");
/** アップデート管理用CSV1行辺りの最大文字数 */
define('UPDATE_CSV_LINE_MAX', 4096);
/** アップデート管理用CSVカラム数 */
define('UPDATE_CSV_COL_MAX', 13);
/** モジュール管理用CSVカラム数 */
define('MODULE_CSV_COL_MAX', 16);
/** 商品購入完了 */
define('AFF_SHOPPING_COMPLETE', 1);
/** ユーザ登録完了 */
define('AFF_ENTRY_COMPLETE', 2);
/** 文字コード */
define('CHAR_CODE', "UTF-8");
/** ロケール設定 */
define('LOCALE', "ja_JP.UTF-8");
/** 決済モジュール付与文言 */
define('ECCUBE_PAYMENT', "EC-CUBE");
/** PEAR::DBのデバッグモード */
define('PEAR_DB_DEBUG', 9);
/** PEAR::DBの持続的接続オプション */
define('PEAR_DB_PERSISTENT', false);
/** バッチを実行する最短の間隔(秒) */
define('LOAD_BATCH_PASS', 3600);
/** 締め日の指定(末日の場合は、31を指定してください。) */
define('CLOSE_DAY', 31);
/** 一般サイトエラー */
define('FAVORITE_ERROR', 13);
/** グラフ格納ディレクトリ */
define('GRAPH_REALDIR', HTML_REALDIR . "upload/graph_image/");
/** グラフURL */
define('GRAPH_URLPATH', ROOT_URLPATH . "upload/graph_image/");
/** 円グラフ最大表示数 */
define('GRAPH_PIE_MAX', 10);
/** グラフのラベルの文字数 */
define('GRAPH_LABEL_MAX', 40);
/** 何歳まで集計の対象とするか */
define('BAT_ORDER_AGE', 70);
/** 商品集計で何位まで表示するか */
define('PRODUCTS_TOTAL_MAX', 15);
/** 1:公開 2:非公開 */
define('DEFAULT_PRODUCT_DISP', 2);
/** 送料無料購入数量 (0の場合は、いくつ買っても無料にならない) */
define('DELIV_FREE_AMOUNT', 0);
/** 配送料の設定画面表示(有効:1 無効:0) */
define('INPUT_DELIV_FEE', 1);
/** 商品ごとの送料設定(有効:1 無効:0) */
define('OPTION_PRODUCT_DELIV_FEE', 0);
/** 配送業者ごとの配送料を加算する(有効:1 無効:0) */
define('OPTION_DELIV_FEE', 1);
/** おすすめ商品登録(有効:1 無効:0) */
define('OPTION_RECOMMEND', 1);
/** 商品規格登録(有効:1 無効:0) */
define('OPTION_CLASS_REGIST', 1);
/** 会員登録変更(マイページ)パスワード用 */
define('DEFAULT_PASSWORD', "********");
/** 別のお届け先最大登録数 */
define('DELIV_ADDR_MAX', 20);
/** 対応状況管理画面の一覧表示件数 */
define('ORDER_STATUS_MAX', 50);
/** フロントレビュー書き込み最大数 */
define('REVIEW_REGIST_MAX', 5);
/** デバッグモード(true：sfPrintRやDBのエラーメッセージを出力する、false：出力しない) */
define('DEBUG_MODE', false);
/** 管理ユーザID(メンテナンス用表示されない。) */
define('ADMIN_ID', "1");
/** 会員登録時に仮会員確認メールを送信するか (true:仮会員、false:本会員) */
define('CUSTOMER_CONFIRM_MAIL', false);
/** メルマガ配信(true:配信する、false:配信しない) */
define('MELMAGA_SEND', true);
/** メイルマガジンバッチモード(true:バッチで送信する ※要cron設定、false:リアルタイムで送信する) */
define('MELMAGA_BATCH_MODE', false);
/** ログイン画面フレーム */
define('LOGIN_FRAME', "login_frame.tpl");
/** 管理画面フレーム */
define('MAIN_FRAME', "main_frame.tpl");
/** 一般サイト画面フレーム */
define('SITE_FRAME', "site_frame.tpl");
/** 認証文字列 */
define('CERT_STRING', "7WDhcBTF");
/** 生年月日登録開始年 */
define('BIRTH_YEAR', 1901);
/** 本システムの稼働開始年 */
define('RELEASE_YEAR', 2005);
/** クレジットカードの期限＋何年 */
define('CREDIT_ADD_YEAR', 10);
/** 親カテゴリのカテゴリIDの最大数 (これ以下は親カテゴリとする。) */
define('PARENT_CAT_MAX', 12);
/** GET値変更などのいたずらを防ぐため最大数制限を設ける。 */
define('NUMBER_MAX', 1000000000);
/** ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ) */
define('POINT_RULE', 2);
/** 1ポイント当たりの値段(円) */
define('POINT_VALUE', 1);
/** 管理モード 1:有効　0:無効(納品時) */
define('ADMIN_MODE', 0);
/** 売上集計バッチモード(true:バッチで集計する ※要cron設定、false:リアルタイムで集計する) */
define('DAILY_BATCH_MODE', false);
/** ログファイル最大数(ログテーション) */
define('MAX_LOG_QUANTITY', 5);
/** 1つのログファイルに保存する最大容量(byte) */
define('MAX_LOG_SIZE', "1000000");
/** トランザクションID の名前 */
define('TRANSACTION_ID_NAME', "transactionid");
/** パスワード忘れの確認メールを送付するか否か。(0:送信しない、1:送信する) */
define('FORGOT_MAIL', 0);
/** 登録できるサブ商品の数 */
define('HTML_TEMPLATE_SUB_MAX', 12);
/** 文字数が多すぎるときに強制改行するサイズ(半角) */
define('LINE_LIMIT_SIZE', 60);
/** 誕生日月ポイント */
define('BIRTH_MONTH_POINT', 0);
/** 拡大画像横 */
define('LARGE_IMAGE_WIDTH', 500);
/** 拡大画像縦 */
define('LARGE_IMAGE_HEIGHT', 500);
/** 一覧画像横 */
define('SMALL_IMAGE_WIDTH', 130);
/** 一覧画像縦 */
define('SMALL_IMAGE_HEIGHT', 130);
/** 通常画像横 */
define('NORMAL_IMAGE_WIDTH', 260);
/** 通常画像縦 */
define('NORMAL_IMAGE_HEIGHT', 260);
/** 通常サブ画像横 */
define('NORMAL_SUBIMAGE_WIDTH', 200);
/** 通常サブ画像縦 */
define('NORMAL_SUBIMAGE_HEIGHT', 200);
/** 拡大サブ画像横 */
define('LARGE_SUBIMAGE_WIDTH', 500);
/** 拡大サブ画像縦 */
define('LARGE_SUBIMAGE_HEIGHT', 500);
/** 一覧表示画像横 */
define('DISP_IMAGE_WIDTH', 65);
/** 一覧表示画像縦 */
define('DISP_IMAGE_HEIGHT', 65);
/** その他の画像1 */
define('OTHER_IMAGE1_WIDTH', 500);
/** その他の画像1 */
define('OTHER_IMAGE1_HEIGHT', 500);
/** HTMLメールテンプレートメール担当画像横 */
define('HTMLMAIL_IMAGE_WIDTH', 110);
/** HTMLメールテンプレートメール担当画像縦 */
define('HTMLMAIL_IMAGE_HEIGHT', 120);
/** 画像サイズ制限(KB) */
define('IMAGE_SIZE', 1000);
/** CSVサイズ制限(KB) */
define('CSV_SIZE', 2000);
/** CSVアップロード1行あたりの最大文字数 */
define('CSV_LINE_MAX', 10000);
/** PDFサイズ制限(KB):商品詳細ファイル等 */
define('PDF_SIZE', 5000);
/** ファイル管理画面アップ制限(KB) */
define('FILE_SIZE', 10000);
/** アップできるテンプレートファイル制限(KB) */
define('TEMPLATE_SIZE', 10000);
/** カテゴリの最大階層 */
define('LEVEL_MAX', 5);
/** 最大カテゴリ登録数 */
define('CATEGORY_MAX', 1000);
/** 管理機能タイトル */
define('ADMIN_TITLE', "EC-CUBE 管理機能");
/** 編集時強調表示色 */
define('SELECT_RGB', "#ffffdf");
/** 入力項目無効時の表示色 */
define('DISABLED_RGB', "#C9C9C9");
/** エラー時表示色 */
define('ERR_COLOR', "#ffe8e8");
/** 親カテゴリ表示文字 */
define('CATEGORY_HEAD', ">");
/** 生年月日初期選択年 */
define('START_BIRTH_YEAR', 1970);
/** 価格名称 */
define('NORMAL_PRICE_TITLE', "通常価格");
/** 価格名称 */
define('SALE_PRICE_TITLE', "販売価格");
/** ログファイル */
define('LOG_REALFILE', DATA_REALDIR . "logs/site.log");
/** 会員ログイン ログファイル */
define('CUSTOMER_LOG_REALFILE', DATA_REALDIR . "logs/customer.log");
/** 画像一時保存 */
define('IMAGE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_image/");
/** 画像保存先 */
define('IMAGE_SAVE_REALDIR', HTML_REALDIR . "upload/save_image/");
/** 画像一時保存URL */
define('IMAGE_TEMP_URLPATH', ROOT_URLPATH . "upload/temp_image/");
/** 画像保存先URL */
define('IMAGE_SAVE_URLPATH', ROOT_URLPATH . "upload/save_image/");
/** RSS用画像一時保存URL */
define('IMAGE_TEMP_RSS_URL', HTTP_URL . "upload/temp_image/");
/** RSS用画像保存先URL */
define('IMAGE_SAVE_RSS_URL', HTTP_URL . "upload/save_image/");
/** エンコードCSVの一時保存先 */
define('CSV_TEMP_REALDIR', DATA_REALDIR . "upload/csv/");
/** 画像がない場合に表示 */
define('NO_IMAGE_REALDIR', USER_TEMPLATE_REALDIR . "img/picture/img_blank.gif");
/** システム管理トップ */
define('ADMIN_SYSTEM_URLPATH', ROOT_URLPATH . ADMIN_DIR . "system/" . DIR_INDEX_PATH);
/** 規格登録 */
define('ADMIN_CLASS_REGIST_URLPATH', ROOT_URLPATH . ADMIN_DIR . "products/class.php");
/** 郵便番号入力 */
define('INPUT_ZIP_URLPATH', ROOT_URLPATH . "input_zip.php");
/** 配送業者登録 */
define('ADMIN_DELIVERY_URLPATH', ROOT_URLPATH . ADMIN_DIR . "basis/delivery.php");
/** 支払い方法登録 */
define('ADMIN_PAYMENT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "basis/payment.php");
/** ホーム */
define('ADMIN_HOME_URLPATH', ROOT_URLPATH . ADMIN_DIR . "home.php");
/** ログインページ */
define('ADMIN_LOGIN_URLPATH', ROOT_URLPATH . ADMIN_DIR . DIR_INDEX_PATH);
/** 商品検索ページ */
define('ADMIN_PRODUCTS_URLPATH', ROOT_URLPATH . ADMIN_DIR . "products/" . DIR_INDEX_PATH);
/** 注文編集ページ */
define('ADMIN_ORDER_EDIT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/edit.php");
/** 注文編集ページ */
define('ADMIN_ORDER_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/" . DIR_INDEX_PATH);
/** 注文編集ページ */
define('ADMIN_ORDER_MAIL_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/mail.php");
/** ログアウトページ */
define('ADMIN_LOGOUT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "logout.php");
/** アクセス成功 */
define('SUCCESS', 0);
/** メンバー管理ページ表示行数 */
define('MEMBER_PMAX', 10);
/** 検索ページ表示行数 */
define('SEARCH_PMAX', 10);
/** ページ番号の最大表示数量 */
define('NAVI_PMAX', 4);
/** 商品サブ情報最大数 */
define('PRODUCTSUB_MAX', 5);
/** お届け時間の最大表示数 */
define('DELIVTIME_MAX', 16);
/** 配送料金の最大表示数 */
define('DELIVFEE_MAX', 47);
/** 短い項目の文字数 (名前など) */
define('STEXT_LEN', 50);
define('SMTEXT_LEN', 100);
/** 長い項目の文字数 (住所など) */
define('MTEXT_LEN', 200);
/** 長中文の文字数 (問い合わせなど) */
define('MLTEXT_LEN', 1000);
/** 長文の文字数 */
define('LTEXT_LEN', 3000);
/** 超長文の文字数 (メルマガなど) */
define('LLTEXT_LEN', 99999);
/** URLの文字長 */
define('URL_LEN', 1024);
/** 管理画面用：ID・パスワードの文字数制限 */
define('ID_MAX_LEN', STEXT_LEN);
/** 管理画面用：ID・パスワードの文字数制限 */
define('ID_MIN_LEN', 4);
/** 金額桁数 */
define('PRICE_LEN', 8);
/** 率桁数 */
define('PERCENTAGE_LEN', 3);
/** 在庫数、販売制限数 */
define('AMOUNT_LEN', 6);
/** 郵便番号1 */
define('ZIP01_LEN', 3);
/** 郵便番号2 */
define('ZIP02_LEN', 4);
/** 電話番号各項目制限 */
define('TEL_ITEM_LEN', 6);
/** 電話番号総数 */
define('TEL_LEN', 12);
/** フロント画面用：パスワードの最小文字数 */
define('PASSWORD_MIN_LEN', 4);
/** フロント画面用：パスワードの最大文字数 */
define('PASSWORD_MAX_LEN', STEXT_LEN);
/** 検査数値用桁数(INT) */
define('INT_LEN', 9);
/** クレジットカードの文字数 */
define('CREDIT_NO_LEN', 4);
/** 検索カテゴリ最大表示文字数(byte) */
define('SEARCH_CATEGORY_LEN', 18);
/** ファイル名表示文字数 */
define('FILE_NAME_LEN', 10);
/** クッキー保持期限(日) */
define('COOKIE_EXPIRE', 365);
/** カテゴリ区切り文字 */
define('SEPA_CATNAVI', " > ");
/** カテゴリ区切り文字 */
define('SEPA_CATLIST', " | ");
/** 会員情報入力 */
define('SHOPPING_URL', HTTPS_URL . "shopping/" . DIR_INDEX_PATH);
/** 会員登録ページTOP */
define('ENTRY_URL', HTTPS_URL . "entry/" . DIR_INDEX_PATH);
/** サイトトップ */
define('TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** カートトップ */
define('CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
/** お届け先設定 */
define('DELIV_URLPATH', ROOT_URLPATH . "shopping/deliv.php");
/** 複数お届け先設定 */
define('MULTIPLE_URLPATH', ROOT_URLPATH . "shopping/multiple.php");
/** Myページトップ */
define('URL_MYPAGE_TOP', HTTPS_URL . "mypage/login.php");
/** 購入確認ページ */
define('SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** 購入完了画面 */
define('SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** モジュール追加用画面 */
define('SHOPPING_MODULE_URLPATH', ROOT_URLPATH . "shopping/load_payment_module.php");
/** 商品詳細(HTML出力) */
define('P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** マイページお届け先URL */
define('MYPAGE_DELIVADDR_URLPATH', ROOT_URLPATH . "mypage/delivery.php");
/** メールアドレス種別 */
define('MAIL_TYPE_PC', 1);
/** メールアドレス種別 */
define('MAIL_TYPE_MOBILE', 2);
/** 新着情報管理画面 開始年(西暦) */
define('ADMIN_NEWS_STARTYEAR', 2005);
/** 会員登録 */
define('ENTRY_CUSTOMER_TEMP_SUBJECT', "会員仮登録が完了いたしました。");
/** 会員登録 */
define('ENTRY_CUSTOMER_REGIST_SUBJECT', "本会員登録が完了いたしました。");
/** 再入会制限時間 (単位: 時間) */
define('ENTRY_LIMIT_HOUR', 1);
/** 関連商品表示数 */
define('RECOMMEND_PRODUCT_MAX', 6);
/** おすすめ商品表示数 */
define('RECOMMEND_NUM', 8);
/** お届け可能日以降のプルダウン表示最大日数 */
define('DELIV_DATE_END_MAX', 21);
/** 購入時強制会員登録(1:有効　0:無効) */
define('PURCHASE_CUSTOMER_REGIST', 0);
/** 支払期限 */
define('CV_PAYMENT_LIMIT', 14);
/** 商品レビューでURL書き込みを許可するか否か */
define('REVIEW_ALLOW_URL', 0);
/** Pear::Mail バックエンド:mail|smtp|sendmail */
define('MAIL_BACKEND', "smtp");
/** SMTPサーバー */
define('SMTP_HOST', "127.0.0.1");
/** SMTPポート */
define('SMTP_PORT', "25");
/** アップデート時にサイト情報を送出するか */
define('UPDATE_SEND_SITE_INFO', false);
/** ポイントを利用するか(true:利用する、false:利用しない) (false は一部対応) */
define('USE_POINT', true);
/** 在庫無し商品の非表示(true:非表示、false:表示) */
define('NOSTOCK_HIDDEN', false);
/** モバイルサイトを利用するか(true:利用する、false:利用しない) (false は一部対応) */
define('USE_MOBILE', true);
/** デフォルトテンプレート名(PC) */
define('DEFAULT_TEMPLATE_NAME', "default");
/** デフォルトテンプレート名(モバイル) */
define('MOBILE_DEFAULT_TEMPLATE_NAME', "mobile");
/** デフォルトテンプレート名(スマートフォン) */
define('SMARTPHONE_DEFAULT_TEMPLATE_NAME', "sphone");
/** テンプレート名 */
define('TEMPLATE_NAME', "default");
/** モバイルテンプレート名 */
define('MOBILE_TEMPLATE_NAME', "mobile");
/** スマートフォンテンプレート名 */
define('SMARTPHONE_TEMPLATE_NAME', "sphone");
/** SMARTYテンプレート */
define('SMARTY_TEMPLATES_REALDIR',  DATA_REALDIR . "Smarty/templates/");
/** SMARTYテンプレート(PC) */
define('TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . TEMPLATE_NAME . "/");
/** SMARTYテンプレート(管理機能) */
define('TEMPLATE_ADMIN_REALDIR', SMARTY_TEMPLATES_REALDIR . "admin/");
/** SMARTYコンパイル */
define('COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . TEMPLATE_NAME . "/");
/** SMARTYコンパイル(管理機能) */
define('COMPILE_ADMIN_REALDIR', DATA_REALDIR . "Smarty/templates_c/admin/");
/** ブロックファイル保存先 */
define('BLOC_DIR', "frontparts/bloc/");
/** SMARTYテンプレート(mobile) */
define('MOBILE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(mobile) */
define('MOBILE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYテンプレート(smart phone) */
define('SMARTPHONE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . SMARTPHONE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(smartphone) */
define('SMARTPHONE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . SMARTPHONE_TEMPLATE_NAME . "/");
/** EメールアドレスチェックをRFC準拠にするか(true:準拠する、false:準拠しない) */
define('RFC_COMPLIANT_EMAIL_CHECK', false);
/** モバイルサイトのセッションの存続時間 (秒) */
define('MOBILE_SESSION_LIFETIME', 1800);
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_REALDIR', HTML_REALDIR . "upload/mobile_image/");
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_URLPATH', ROOT_URLPATH . "upload/mobile_image/");
/** モバイルURL */
define('MOBILE_TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** カートトップ */
define('MOBILE_CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
/** 会員情報入力 */
define('MOBILE_SHOPPING_URL', HTTPS_URL . "shopping/" . DIR_INDEX_PATH);
/** 購入確認ページ */
define('MOBILE_SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('MOBILE_SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** 商品詳細(HTML出力) */
define('MOBILE_P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** 購入完了画面 */
define('MOBILE_SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** モジュール追加用画面 */
define('MOBILE_SHOPPING_MODULE_URLPATH', ROOT_URLPATH . "shopping/load_payment_module.php");
/** セッション維持方法：useCookie|useRequest */
define('SESSION_KEEP_METHOD', "useCookie");
/** セッションの存続時間 (秒) */
define('SESSION_LIFETIME', 1800);
/** オーナーズストアURL */
define('OSTORE_URL', "http://store.ec-cube.net/");
/** オーナーズストアURL */
define('OSTORE_SSLURL', "https://store.ec-cube.net/");
/** オーナーズストアログパス */
define('OSTORE_LOG_REALFILE', DATA_REALDIR . "logs/ownersstore.log");
/** オーナーズストア通信ステータス */
define('OSTORE_STATUS_ERROR', "ERROR");
/** オーナーズストア通信ステータス */
define('OSTORE_STATUS_SUCCESS', "SUCCESS");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_UNKNOWN', "1000");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_INVALID_PARAM', "1001");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_CUSTOMER', "1002");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_WRONG_URL_PASS', "1003");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_PRODUCTS', "1004");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_DL_DATA', "1005");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_DL_DATA_OPEN', "1006");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_DLLOG_AUTH', "1007");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_ADMIN_AUTH', "2001");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_HTTP_REQ', "2002");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_HTTP_RESP', "2003");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_FAILED_JSON_PARSE', "2004");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_NO_KEY', "2005");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_INVALID_ACCESS', "2006");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_INVALID_PARAM', "2007");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_AUTOUP_DISABLE', "2008");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_PERMISSION', "2009");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_BATCH_ERR', "2010");
/** お気に入り商品登録(有効:1 無効:0) */
define('OPTION_FAVOFITE_PRODUCT', 1);
/** 画像リネーム設定 (商品画像のみ) (true:リネームする、false:リネームしない) */
define('IMAGE_RENAME', true);
/** プラグインディレクトリ */
define('PLUGIN_DIR', "plugins/");
/** プラグイン保存先 */
define('PLUGIN_REALDIR', USER_REALDIR . PLUGIN_DIR);
/** プラグイン URL */
define('PLUGIN_URL', USER_URL . PLUGIN_DIR);
/** 日数桁数 */
define('DOWNLOAD_DAYS_LEN', 3);
/** ダウンロードファイル登録可能拡張子(カンマ区切り)" */
define('DOWNLOAD_EXTENSION', "zip,lzh,jpg,jpeg,gif,png,mp3,pdf,csv");
/** ダウンロード販売ファイル用サイズ制限(KB) */
define('DOWN_SIZE', 50000);
/** 1:実商品 2:ダウンロード */
define('DEFAULT_PRODUCT_DOWN', 1);
/** ダウンロードファイル一時保存 */
define('DOWN_TEMP_REALDIR', DATA_REALDIR . "download/temp/");
/** ダウンロードファイル保存先 */
define('DOWN_SAVE_REALDIR', DATA_REALDIR . "download/save/");
/** ダウンロードファイル存在エラー */
define('DOWNFILE_NOT_FOUND', 22);
/** ダウンロード販売機能 ダウンロードファイル読み込みバイト(KB) */
define('DOWNLOAD_BLOCK', 1024);
/** 新規注文 */
define('ORDER_NEW', 1);
/** 入金待ち */
define('ORDER_PAY_WAIT', 2);
/** 入金済み */
define('ORDER_PRE_END', 6);
/** キャンセル */
define('ORDER_CANCEL', 3);
/** 取り寄せ中 */
define('ORDER_BACK_ORDER', 4);
/** 発送済み */
define('ORDER_DELIV', 5);
/** 決済処理中 */
define('ORDER_PENDING', 7);
/** 通常商品 */
define('PRODUCT_TYPE_NORMAL', 1);
/** ダウンロード商品 */
define('PRODUCT_TYPE_DOWNLOAD', 2);
/** SQLログを取得するフラグ(1:表示, 0:非表示) */
define('SQL_QUERY_LOG_MODE', 1);
/** SQLログを取得する時間設定(設定値以上かかった場合に取得) */
define('SQL_QUERY_LOG_MIN_EXEC_TIME', 2);
/** ページ表示時間のログを取得するフラグ(1:表示, 0:非表示) */
define('PAGE_DISPLAY_TIME_LOG_MODE', 1);
/** ページ表示時間のログを取得する時間設定(設定値以上かかった場合に取得) */
define('PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME', 2);
/** 端末種別: モバイル */
define('DEVICE_TYPE_MOBILE', 1);
/** 端末種別: スマートフォン */
define('DEVICE_TYPE_SMARTPHONE', 2);
/** 端末種別: PC */
define('DEVICE_TYPE_PC', 10);
/** 端末種別: 管理画面 */
define('DEVICE_TYPE_ADMIN', 99);
/** 配置ID: 未使用 */
define('TARGET_ID_UNUSED', 0);
/** 配置ID: LeftNavi */
define('TARGET_ID_LEFT', 1);
/** 配置ID: MainHead */
define('TARGET_ID_MAIN_HEAD', 2);
/** 配置ID: RightNavi */
define('TARGET_ID_RIGHT', 3);
/** 配置ID: MainFoot */
define('TARGET_ID_MAIN_FOOT', 4);
/** 配置ID: TopNavi */
define('TARGET_ID_TOP', 5);
/** 配置ID: BottomNavi */
define('TARGET_ID_BOTTOM', 6);
/** 配置ID: HeadNavi */
define('TARGET_ID_HEAD', 7);
/** 配置ID: HeadTopNavi */
define('TARGET_ID_HEAD_TOP', 8);
/** 配置ID: FooterBottomNavi */
define('TARGET_ID_FOOTER_BOTTOM', 9);
/** 配置ID: HeaderInternalNavi */
define('TARGET_ID_HEADER_INTERNAL', 10);
/** CSV入出力列設定有効無効フラグ: 有効 */
define('CSV_COLUMN_STATUS_FLG_ENABLE', 1);
/** CSV入出力列設定有効無効フラグ: 無効 */
define('CSV_COLUMN_STATUS_FLG_DISABLE', 2);
/** CSV入出力列設定読み書きフラグ: 読み書き可能 */
define('CSV_COLUMN_RW_FLG_READ_WRITE', 1);
/** CSV入出力列設定読み書きフラグ: 読み込みのみ可能 */
define('CSV_COLUMN_RW_FLG_READ_ONLY', 2);
/** CSV入出力列設定読み書きフラグ: キー列 */
define('CSV_COLUMN_RW_FLG_KEY_FIELD', 3);
/** 無制限フラグ： 無制限 */
define('UNLIMITED_FLG_UNLIMITED', "1");
/** 無制限フラグ： 制限有り */
define('UNLIMITED_FLG_LIMITED', "0");
/** EC-CUBE更新情報取得 (true:取得する false:取得しない) */
define('ECCUBE_INFO', true);
/** 外部サイトHTTP取得タイムアウト時間(秒) */
define('HTTP_REQUEST_TIMEOUT', "5");
/** プラグインの状態：アップロード済み */
define('PLUGIN_STATUS_UPLOADED', "1");
/** プラグインの状態：インストール済み */
define('PLUGIN_STATUS_INSTALLED', "2");
/** プラグイン有効/無効：有効 */
define('PLUGIN_ENABLE_TRUE', "1");
/** プラグイン有効/無効：無効 */
define('PLUGIN_ENABLE_FALSE', "2");
/** 郵便番号CSVのZIPアーカイブファイルの取得元 */
define('ZIP_DOWNLOAD_URL', "http://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip");
?>
