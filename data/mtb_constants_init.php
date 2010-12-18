<?php
/** フロント表示関連 */
define('SAMPLE_ADDRESS1', "市区町村名 (例：千代田区神田神保町)");
/** フロント表示関連 */
define('SAMPLE_ADDRESS2', "番地・ビル名 (例：1-3-5)");
/** ユーザファイル保存先 */
define('USER_DIR', "user_data/");
/** ユーザファイル保存先 */
define('USER_PATH', HTML_PATH . USER_DIR);
/** ユーザインクルードファイル保存先 */
define('USER_INC_PATH', USER_PATH . "include/");
/** 郵便番号専用DB */
define('ZIP_DSN', DEFAULT_DSN);
/** ユーザー作成ページ等 */
define('USER_URL', SITE_URL . USER_DIR);
/** 認証用 magic */
define('AUTH_MAGIC', "31eafcbd7a81d7b401a7fdc12bba047c02d1fae6");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_DIR', "templates/");
/** テンプレートファイル保存先 */
define('USER_PACKAGE_DIR', "packages/");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_PATH', USER_PATH . USER_PACKAGE_DIR);
/** テンプレートファイル一時保存先 */
define('TEMPLATE_TEMP_DIR', HTML_PATH . "upload/temp_template/");
/** ユーザー作成画面のデフォルトPHPファイル */
define('USER_DEF_PHP', HTML_PATH . "__default.php");
/** その他画面のデフォルトページレイアウト */
define('DEF_LAYOUT', "products/list.php");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_DIR', "downloads/module/");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_PATH', DATA_PATH . MODULE_DIR);
/** DBセッションの有効期限(秒) */
define('MAX_LIFETIME', 7200);
/** マスタデータキャッシュディレクトリ */
define('MASTER_DATA_DIR', DATA_PATH . "cache/");
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
/** フォントのパス */
define('TTF_DIR', DATA_PATH . "fonts/");
/** グラフ格納ディレクトリ */
define('GRAPH_DIR', HTML_PATH . "upload/graph_image/");
/** グラフURL */
define('GRAPH_URL', URL_DIR . "upload/graph_image/");
/** 円グラフ最大表示数 */
define('GRAPH_PIE_MAX', 10);
/** グラフのラベルの文字数 */
define('GRAPH_LABEL_MAX', 40);
/** PDF格納ディレクトリ */
define('PDF_DIR', DATA_PATH . "pdf/");
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
define('DEFAULT_PASSWORD', "UAhgGR3L");
/** 別のお届け先最大登録数 */
define('DELIV_ADDR_MAX', 20);
/** 管理画面ステータス一覧表示件数 */
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
/** ダミーパスワード */
define('DUMMY_PASS', "########");
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
/** ルートカテゴリID */
define('ROOT_CATEGORY_1', 2);
/** ルートカテゴリID */
define('ROOT_CATEGORY_2', 3);
/** ルートカテゴリID */
define('ROOT_CATEGORY_3', 4);
/** ルートカテゴリID */
define('ROOT_CATEGORY_4', 5);
/** ルートカテゴリID */
define('ROOT_CATEGORY_5', 6);
/** ルートカテゴリID */
define('ROOT_CATEGORY_6', 7);
/** ルートカテゴリID */
define('ROOT_CATEGORY_7', 8);
/** クレジットカード */
define('PAYMENT_CREDIT_ID', 1);
/** コンビニ決済 */
define('PAYMENT_CONVENIENCE_ID', 2);
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
define('ADMIN_TITLE', "管理機能");
/** 編集時強調表示色 */
define('SELECT_RGB', "#ffffdf");
/** 入力項目無効時の表示色 */
define('DISABLED_RGB', "#C9C9C9");
/** エラー時表示色 */
define('ERR_COLOR', "#ffe8e8");
/** 親カテゴリ表示文字 */
define('CATEGORY_HEAD', ">");
/** 生年月日選択開始年 */
define('START_BIRTH_YEAR', 1901);
/** 価格名称 */
define('NORMAL_PRICE_TITLE', "通常価格");
/** 価格名称 */
define('SALE_PRICE_TITLE', "販売価格");
/** ログファイル */
define('LOG_PATH', DATA_PATH . "logs/site.log");
/** 会員ログイン ログファイル */
define('CUSTOMER_LOG_PATH', DATA_PATH . "logs/customer.log");
/** 画像一時保存 */
define('IMAGE_TEMP_DIR', HTML_PATH . "upload/temp_image/");
/** 画像保存先 */
define('IMAGE_SAVE_DIR', HTML_PATH . "upload/save_image/");
/** 画像一時保存URL */
define('IMAGE_TEMP_URL', URL_DIR . "upload/temp_image/");
/** 画像保存先URL */
define('IMAGE_SAVE_URL', URL_DIR . "upload/save_image/");
/** RSS用画像一時保存URL */
define('IMAGE_TEMP_URL_RSS', SITE_URL . "upload/temp_image/");
/** RSS用画像保存先URL */
define('IMAGE_SAVE_URL_RSS', SITE_URL . "upload/save_image/");
/** エンコードCSVの一時保存先 */
define('CSV_TEMP_DIR', DATA_PATH . "upload/csv/");
/** 画像がない場合に表示 */
define('NO_IMAGE_URL', URL_DIR . "misc/blank.gif");
/** 画像がない場合に表示 */
define('NO_IMAGE_DIR', HTML_PATH . "misc/blank.gif");
/** システム管理トップ */
define('URL_SYSTEM_TOP', URL_DIR . ADMIN_DIR . "system/" . DIR_INDEX_URL);
/** 規格登録 */
define('URL_CLASS_REGIST', URL_DIR . ADMIN_DIR . "products/class.php");
/** 郵便番号入力 */
define('URL_INPUT_ZIP', URL_DIR . "input_zip.php");
/** 配送業者登録 */
define('URL_DELIVERY_TOP', URL_DIR . ADMIN_DIR . "basis/delivery.php");
/** 支払い方法登録 */
define('URL_PAYMENT_TOP', URL_DIR . ADMIN_DIR . "basis/payment.php");
/** サイト管理情報登録 */
define('URL_CONTROL_TOP', URL_DIR . ADMIN_DIR . "basis/control.php");
/** ホーム */
define('URL_HOME', URL_DIR . ADMIN_DIR . "home.php");
/** ログインページ */
define('URL_LOGIN', URL_DIR . ADMIN_DIR . "" . DIR_INDEX_URL);
/** 商品検索ページ */
define('URL_SEARCH_TOP', URL_DIR . ADMIN_DIR . "products/" . DIR_INDEX_URL);
/** 注文編集ページ */
define('URL_ORDER_EDIT', URL_DIR . ADMIN_DIR . "order/edit.php");
/** 注文編集ページ */
define('URL_SEARCH_ORDER', URL_DIR . ADMIN_DIR . "order/" . DIR_INDEX_URL);
/** 注文編集ページ */
define('URL_ORDER_MAIL', URL_DIR . ADMIN_DIR . "order/mail.php");
/** ログアウトページ */
define('URL_LOGOUT', URL_DIR . ADMIN_DIR . "logout.php");
/** システム管理CSV出力ページ */
define('URL_SYSTEM_CSV', URL_DIR . ADMIN_DIR . "system/member_csv.php");
/** 管理機能用CSS保管ディレクトリ */
define('URL_ADMIN_CSS', URL_DIR . ADMIN_DIR . "css/");
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
define('ID_MAX_LEN', 15);
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
define('PASSWORD_LEN1', 4);
/** フロント画面用：パスワードの最大文字数 */
define('PASSWORD_LEN2', 10);
/** 検査数値用桁数(INT) */
define('INT_LEN', 9);
/** クレジットカードの文字数 */
define('CREDIT_NO_LEN', 4);
/** 検索カテゴリ最大表示文字数(byte) */
define('SEARCH_CATEGORY_LEN', 18);
/** ファイル名表示文字数 */
define('FILE_NAME_LEN', 10);
/** 購入制限なしの場合の最大購入数量 */
define('SALE_LIMIT_MAX', 10);
/** クッキー保持期限(日) */
define('COOKIE_EXPIRE', 365);
/** カテゴリ区切り文字 */
define('SEPA_CATNAVI', " > ");
/** カテゴリ区切り文字 */
define('SEPA_CATLIST', " | ");
/** 会員情報入力 */
define('URL_SHOP_TOP', SSL_URL . "shopping/" . DIR_INDEX_URL);
/** 会員登録ページTOP */
define('URL_ENTRY_TOP', SSL_URL . "entry/" . DIR_INDEX_URL);
/** サイトトップ */
define('URL_SITE_TOP', URL_DIR . DIR_INDEX_URL);
/** カートトップ */
define('URL_CART_TOP', URL_DIR . "cart/" . DIR_INDEX_URL);
/** お届け先設定 */
define('URL_DELIV_TOP', URL_DIR . "shopping/deliv.php");
/** Myページトップ */
define('URL_MYPAGE_TOP', SSL_URL . "mypage/login.php");
/** 購入確認ページ */
define('URL_SHOP_CONFIRM', URL_DIR . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('URL_SHOP_PAYMENT', URL_DIR . "shopping/payment.php");
/** 購入完了画面 */
define('URL_SHOP_COMPLETE', URL_DIR . "shopping/complete.php");
/** カード決済画面 */
define('URL_SHOP_CREDIT', URL_DIR . "shopping/card.php");
/** ローン決済画面 */
define('URL_SHOP_LOAN', URL_DIR . "shopping/loan.php");
/** コンビニ決済画面 */
define('URL_SHOP_CONVENIENCE', URL_DIR . "shopping/convenience.php");
/** モジュール追加用画面 */
define('URL_SHOP_MODULE', URL_DIR . "shopping/load_payment_module.php");
/** 商品詳細(HTML出力) */
define('DETAIL_P_HTML', URL_DIR . "products/detail.php?product_id=");
/** マイページお届け先URL */
define('MYPAGE_DELIVADDR_URL', URL_DIR . "mypage/delivery.php");
/** メールアドレス種別 */
define('MAIL_TYPE_PC', 1);
/** メールアドレス種別 */
define('MAIL_TYPE_MOBILE', 2);
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
/** 受注ステータス変更の際にポイント等を加算するステータス番号 (発送済み) */
define('ODERSTATUS_COMMIT', ORDER_DELIV);
/** 新着情報管理画面 開始年(西暦)  */
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
/** トラックバック 表示 */
define('TRACKBACK_STATUS_VIEW', 1);
/** トラックバック 非表示 */
define('TRACKBACK_STATUS_NOT_VIEW', 2);
/** トラックバック スパム */
define('TRACKBACK_STATUS_SPAM', 3);
/** フロント最大表示数 */
define('TRACKBACK_VIEW_MAX', 10);
/** トラックバック先URL */
define('TRACKBACK_TO_URL', SITE_URL . "tb/" . DIR_INDEX_URL . "?pid=");
/** サイト管理 トラックバック */
define('SITE_CONTROL_TRACKBACK', 1);
/** サイト管理 アフィリエイト */
define('SITE_CONTROL_AFFILIATE', 2);
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
/** デフォルトテンプレート名 */
define('DEFAULT_TEMPLATE_NAME', "default");
/** テンプレート名 */
define('TEMPLATE_NAME', "default");
/** モバイルテンプレート名 */
define('MOBILE_TEMPLATE_NAME', "mobile");
/** スマートフォンテンプレート名 */
define('SMARTPHONE_TEMPLATE_NAME', "sphone");
/** SMARTYテンプレート */
define('SMARTY_TEMPLATES_DIR',  DATA_PATH . "Smarty/templates/");
/** SMARTYテンプレート */
define('TPL_DIR', URL_DIR . USER_DIR . USER_PACKAGE_DIR . TEMPLATE_NAME . "/");
/** SMARTYテンプレート */
define('TEMPLATE_DIR', SMARTY_TEMPLATES_DIR . TEMPLATE_NAME . "/");
/** SMARTYテンプレート(管理機能) */
define('TEMPLATE_ADMIN_DIR', SMARTY_TEMPLATES_DIR . "admin/");
/** SMARTYコンパイル */
define('COMPILE_DIR', DATA_PATH . "Smarty/templates_c/" . TEMPLATE_NAME . "/");
/** SMARTYコンパイル(管理機能) */
define('COMPILE_ADMIN_DIR', DATA_PATH . "Smarty/templates_c/admin/");
/** SMARTYテンプレート(FTP許可) */
define('TEMPLATE_FTP_DIR', USER_PATH . USER_PACKAGE_DIR . TEMPLATE_NAME . "/");
/** SMARTYコンパイル(FTP許可) */
define('COMPILE_FTP_DIR', COMPILE_DIR . USER_DIR);
/** ブロックファイル保存先 */
define('BLOC_DIR', "bloc/");
/** ブロックファイル保存先 */
define('BLOC_PATH', TEMPLATE_DIR . BLOC_DIR);
/** EメールアドレスチェックをRFC準拠にするか(true:準拠する、false:準拠しない) */
define('RFC_COMPLIANT_EMAIL_CHECK', false);
/** SMARTYテンプレート(mobile) */
define('MOBILE_TEMPLATE_DIR', SMARTY_TEMPLATES_DIR . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(mobile) */
define('MOBILE_COMPILE_DIR', DATA_PATH . "Smarty/templates_c/" . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYテンプレート(smart phone) */
define('SMARTPHONE_TEMPLATE_DIR', SMARTY_TEMPLATES_DIR . SMARTPHONE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(smart phone) */
define('SMARTPHONE_COMPILE_DIR', DATA_PATH . "Smarty/templates_c/" . SMARTPHONE_TEMPLATE_NAME . "/");
/** モバイルサイトのセッションの存続時間 (秒) */
define('MOBILE_SESSION_LIFETIME', 1800);
/** 空メール機能を使用するかどうか(true:送信する、false:送信しない) */
define('MOBILE_USE_KARA_MAIL', false);
/** 空メール受け付けアドレスのユーザー名部分 */
define('MOBILE_KARA_MAIL_ADDRESS_USER', "eccube");
/** 空メール受け付けアドレスのユーザー名とコマンドの間の区切り文字 qmail の場合は - */
define('MOBILE_KARA_MAIL_ADDRESS_DELIMITER', "+");
/** 空メール受け付けアドレスのドメイン部分 */
define('MOBILE_KARA_MAIL_ADDRESS_DOMAIN', "");
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_DIR', HTML_PATH . "upload/mobile_image");
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_URL', URL_DIR . "upload/mobile_image");
/** モバイルURL */
define('MOBILE_URL_SITE_TOP', MOBILE_URL_DIR . DIR_INDEX_URL);
/** カートトップ */
define('MOBILE_URL_CART_TOP', MOBILE_URL_DIR . "cart/" . DIR_INDEX_URL);
/** 会員情報入力 */
define('MOBILE_URL_SHOP_TOP', MOBILE_SSL_URL . "shopping/" . DIR_INDEX_URL);
/** 購入確認ページ */
define('MOBILE_URL_SHOP_CONFIRM', MOBILE_URL_DIR . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('MOBILE_URL_SHOP_PAYMENT', MOBILE_URL_DIR . "shopping/payment.php");
/** 商品詳細(HTML出力) */
define('MOBILE_DETAIL_P_HTML', MOBILE_URL_DIR . "products/detail.php?product_id=");
/** 購入完了画面 */
define('MOBILE_URL_SHOP_COMPLETE', MOBILE_URL_DIR . "shopping/complete.php");
/** モジュール追加用画面 */
define('MOBILE_URL_SHOP_MODULE', MOBILE_URL_DIR . "shopping/load_payment_module.php");
/** セッション維持方法：useCookie|useRequest */
define('SESSION_KEEP_METHOD', "useCookie");
/** セッションの存続時間 (秒) */
define('SESSION_LIFETIME', 1800);
/** オーナーズストアURL */
define('OSTORE_URL', "http://store.ec-cube.net/");
/** オーナーズストアURL */
define('OSTORE_SSLURL', "https://store.ec-cube.net/");
/** オーナーズストアログパス */
define('OSTORE_LOG_PATH', DATA_PATH . "logs/ownersstore.log");
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
define('PLUGIN_PATH', USER_PATH . PLUGIN_DIR);
/** プラグイン URL */
define('PLUGIN_URL', USER_URL . PLUGIN_DIR);
/** 1:実商品 2:ダウンロード */
define('DEFAULT_PRODUCT_DOWN', 1);
/** ダウンロード販売機能 ファイルサイズ制限(KB) */
define('DOWN_SIZE', 50000);
/** ダウンロード販売機能 ファイル一時保存 */
define('DOWN_TEMP_DIR', DATA_PATH . "download/temp/");
/** ダウンロード販売機能 ファイル保存先 */
define('DOWN_SAVE_DIR', DATA_PATH . "download/save/");
/** ダウンロード販売機能 ファイル存在エラー */
define('DOWNFILE_NOT_FOUND', 22);
/** ダウンロード販売機能 ダウンロード可能日数桁数 */
define('DOWNLOAD_DAYS_LEN', 3);
/** ダウンロード販売機能 ダウンロードファイル読み込みバイト(KB) */
define('DOWNLOAD_BLOCK', 1024);
/** ダウンロードファイル登録可能拡張子(カンマ区切り) */
define('DOWNLOAD_EXTENSION', "zip,lzh,jpg,jpeg,gif,png,mp3,pdf,csv");
/** ダウンロード販売機能用オンライン決済payment_id(カンマ区切り) */
define('ONLINE_PAYMENT', "1");
/** 通常商品 */
define('PRODUCT_TYPE_NORMAL', 1);
/** ダウンロード商品 */
define('PRODUCT_TYPE_DOWNLOAD', 2);
/** 端末種別: モバイル */
define('DEVICE_TYPE_MOBILE', 1);
/** 端末種別: スマートフォン */
define('DEVICE_TYPE_SMARTPHONE', 2);
/** 端末種別: PC */
define('DEVICE_TYPE_PC', 10);
/** 端末種別: 管理画面 */
define('DEVICE_TYPE_ADMIN', 99);
/** 配置ID 未使用 */
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
?>
