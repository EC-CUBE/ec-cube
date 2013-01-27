<?php
/** Related to front display */
define('SAMPLE_ADDRESS1', "Municipality name (Example: Sunnyvale, CA 94085 USA)");
/** Related to front display */
define('SAMPLE_ADDRESS2', "House number/building name (Example: 440 North Wolfe Road)");
/** User file saving destination */
define('USER_DIR', "user_data/");
/** User file saving destination */
define('USER_REALDIR', HTML_REALDIR . USER_DIR);
/** User creation page, etc. */
define('USER_URL', HTTP_URL . USER_DIR);
/** Authentication method */
define('AUTH_TYPE', "HMAC");
/** Template file saving destination */
define('USER_PACKAGE_DIR', "packages/");
/** Template file saving destination */
define('USER_TEMPLATE_REALDIR', USER_REALDIR . USER_PACKAGE_DIR);
/** Temporary saving of template file */
define('TEMPLATE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_template/");
/** Default PHP file for user creation screen */
define('USER_DEF_PHP_REALFILE', USER_REALDIR . "__default.php");
/** Downloaded module storage directory */
define('MODULE_DIR', "downloads/module/");
/** Downloaded module storage directory */
define('MODULE_REALDIR', DATA_REALDIR . MODULE_DIR);
/** Validity period of DB session (seconds) */
define('MAX_LIFETIME', 7200);
/** Master data cache directory */
define('MASTER_DATA_REALDIR', DATA_REALDIR . "cache/");
/** Update management file storage location */
define('UPDATE_HTTP', "");
/** Language code */
define('LANG_CODE', "en-US");
/** Text code */
define('CHAR_CODE', "UTF-8");
/** Locale settings */
define('LOCALE', "en_US.UTF-8");
/** Phrase granted to payment module */
define('ECCUBE_PAYMENT', "EC-CUBE");
/** PEAR::DB debug mode */
define('PEAR_DB_DEBUG', 0);
/** PEAR::DB persistent option */
define('PEAR_DB_PERSISTENT', false);
/** Designation of cutoff date (if last day of the month, specify 31.) */
define('CLOSE_DAY', 31);
/** General site error */
define('FAVORITE_ERROR', 13);
/** Graph storage directory */
define('GRAPH_REALDIR', HTML_REALDIR . "upload/graph_image/");
/** Graph URL */
define('GRAPH_URLPATH', ROOT_URLPATH . "upload/graph_image/");
/** Maximum display count in pie chart */
define('GRAPH_PIE_MAX', 10);
/** Character count of label for graph */
define('GRAPH_LABEL_MAX', 40);
/** Up to how many do you want to display in product tabulation? */
define('PRODUCTS_TOTAL_MAX', 15);
/** 1: Disclosed 2: Not disclosed */
define('DEFAULT_PRODUCT_DISP', 2);
/** Quantity of products purchased with free shipping (if 0, shipping is not free regardless of the quantity purchased) */
define('DELIV_FREE_AMOUNT', 0);
/** Delivery charge settings screen display (active: 1 inactive: 0) */
define('INPUT_DELIV_FEE', 1);
/** Shipping cost settings for each product (active: 1 inactive: 0) */
define('OPTION_PRODUCT_DELIV_FEE', 0);
/** Add delivery charges for each delivery company (active: 1 inactive: 0) */
define('OPTION_DELIV_FEE', 1);
/** Recommended product registration (active: 1 inactive: 0) */
define('OPTION_RECOMMEND', 1);
/** Product specification registration (active: 1 inactive: 0) */
define('OPTION_CLASS_REGIST', 1);
/** Revision of member registration (MY page) for password */
define('DEFAULT_PASSWORD', "******");
/** Maximum number of separate shipping destinations registered */
define('DELIV_ADDR_MAX', 20);
/** Response status management screen list display quantity */
define('ORDER_STATUS_MAX', 50);
/** Maximum number of front review writings */
define('REVIEW_REGIST_MAX', 5);
/** Debug mode (true: sfPrintR and DB error message, the log level outputs a Debug log, false: not output) */
define('DEBUG_MODE', false);
/** Do you want to make the log wordy? (true: Use, false: Do not use) */
define('USE_VERBOSE_LOG', DEBUG_MODE);
/** Management user ID (not displayed for maintenance.) */
define('ADMIN_ID', "1");
/** Do you want to send a temporary member confirmation e-mail when registering as a member? (true: Temporary member, false: Full member) */
define('CUSTOMER_CONFIRM_MAIL', false);
/** Login screen frame */
define('LOGIN_FRAME', "login_frame.tpl");
/** Management screen frame */
define('MAIN_FRAME', "main_frame.tpl");
/** General site screen frame */
define('SITE_FRAME', "site_frame.tpl");
/** Authentication character example */
define('CERT_STRING', "7WDhcBTF");
/** Date of birth Registration start year */
define('BIRTH_YEAR', 1901);
/** Year in which this system started operating */
define('RELEASE_YEAR', 2005);
/** Credit card expiration + years */
define('CREDIT_ADD_YEAR', 10);
/** Point calculation rule (1: Round off, 2: Truncated, 3: Round up) */
define('POINT_RULE', 2);
/** Price per point ($) */
define('POINT_VALUE', 1);
/** Management mode 1: Active 0: Inactive (during delivery) */
define('ADMIN_MODE', 0);
/** Maximum number of log files (Log rotation) */
define('MAX_LOG_QUANTITY', 5);
/** Maximum capacity stored in a single log file (byte) */
define('MAX_LOG_SIZE', "1000000");
/** Transaction ID name */
define('TRANSACTION_ID_NAME', "transactionid");
/** Do you want a confirmation e-mail regarding your forgotten password sent to you? (0: Do not send, 1: Send) */
define('FORGOT_MAIL', 0);
/** Points for birthday month */
define('BIRTH_MONTH_POINT', 0);
/** Enlarged image horizontal */
define('LARGE_IMAGE_WIDTH', 500);
/** Enlarged image vertical */
define('LARGE_IMAGE_HEIGHT', 500);
/** List image horizontal */
define('SMALL_IMAGE_WIDTH', 130);
/** List image vertical */
define('SMALL_IMAGE_HEIGHT', 130);
/** Normal image length */
define('NORMAL_IMAGE_WIDTH', 260);
/** Normal image height */
define('NORMAL_IMAGE_HEIGHT', 260);
/** Normal subimage length */
define('NORMAL_SUBIMAGE_WIDTH', 200);
/** Normal subimage height */
define('NORMAL_SUBIMAGE_HEIGHT', 200);
/** Enlarge sub image horizontal */
define('LARGE_SUBIMAGE_WIDTH', 500);
/** Enlarge sub image vertical */
define('LARGE_SUBIMAGE_HEIGHT', 500);
/** Image key restriction (KB) */
define('IMAGE_SIZE', 1000);
/** CSV size restriction (KB) */
define('CSV_SIZE', 2000);
/** Maximum number of characters per line for CSV upload */
define('CSV_LINE_MAX', 10000);
/** File management screen upload restrictions (KB) */
define('FILE_SIZE', 10000);
/** Restrictions for template files that can be uploaded (KB) */
define('TEMPLATE_SIZE', 10000);
/** Maximum hierarchy for category */
define('LEVEL_MAX', 5);
/** Maximum number of categories that can be registered */
define('CATEGORY_MAX', 1000);
/** Management function title */
define('ADMIN_TITLE', "EC-CUBE management function");
/** Emphasized display color during editing */
define('SELECT_RGB', "#ffffdf");
/** Display color when input items are inactive */
define('DISABLED_RGB', "#C9C9C9");
/** Displayed color during an error */
define('ERR_COLOR', "#ffe8e8");
/** New category display characters */
define('CATEGORY_HEAD', ">");
/** Date of birth Initially selected year */
define('START_BIRTH_YEAR', 1970);
/** Price name */
define('NORMAL_PRICE_TITLE', "Normal price");
/** Price name */
define('SALE_PRICE_TITLE', "Sales price");
/** Standard log file */
define('LOG_REALFILE', DATA_REALDIR . "logs/site.log");
/** Member login log file */
define('CUSTOMER_LOG_REALFILE', DATA_REALDIR . "logs/customer.log");
/** Management function log file */
define('ADMIN_LOG_REALFILE', DATA_REALDIR . "logs/admin.log");
/** Debug log file (not input: Standard log file/control screen log file) */
define('DEBUG_LOG_REALFILE', "");
/** Error log file (not input: standard log file/management screen log file) */
define('ERROR_LOG_REALFILE', DATA_REALDIR . "logs/error.log");
/** DB log file */
define('DB_LOG_REALFILE', DATA_REALDIR . "logs/db.log");
/** Temporary saving of image */
define('IMAGE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_image/");
/** Image saving destination */
define('IMAGE_SAVE_REALDIR', HTML_REALDIR . "upload/save_image/");
/** URL for temporary saving of image */
define('IMAGE_TEMP_URLPATH', ROOT_URLPATH . "upload/temp_image/");
/** URL for image saving destination */
define('IMAGE_SAVE_URLPATH', ROOT_URLPATH . "upload/save_image/");
/** RSS image temporary storage URL */
define('IMAGE_TEMP_RSS_URL', HTTP_URL . "upload/temp_image/");
/** RSS image saving destination URL */
define('IMAGE_SAVE_RSS_URL', HTTP_URL . "upload/save_image/");
/** Temporary saving destination of encoded CSV */
define('CSV_TEMP_REALDIR', DATA_REALDIR . "upload/csv/");
/** Displayed where there is no image */
define('NO_IMAGE_REALFILE', USER_TEMPLATE_REALDIR . "default_en/img/picture/img_blank.gif");
/** System management top */
define('ADMIN_SYSTEM_URLPATH', ROOT_URLPATH . ADMIN_DIR . "system/" . DIR_INDEX_PATH);
/** Postal code input */
define('INPUT_ZIP_URLPATH', ROOT_URLPATH . "input_zip.php");
/** Home */
define('ADMIN_HOME_URLPATH', ROOT_URLPATH . ADMIN_DIR . "home.php");
/** Login page */
define('ADMIN_LOGIN_URLPATH', ROOT_URLPATH . ADMIN_DIR . DIR_INDEX_PATH);
/** Product search page */
define('ADMIN_PRODUCTS_URLPATH', ROOT_URLPATH . ADMIN_DIR . "products/" . DIR_INDEX_PATH);
/** Order editing page */
define('ADMIN_ORDER_EDIT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/edit.php");
/** Order editing page */
define('ADMIN_ORDER_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/" . DIR_INDEX_PATH);
/** Order editing page */
define('ADMIN_ORDER_MAIL_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/mail.php");
/** Logout page */
define('ADMIN_LOGOUT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "logout.php");
/** Number of lines displayed on member management page */
define('MEMBER_PMAX', 10);
/** Number of lines for search page display */
define('SEARCH_PMAX', 10);
/** Maximum display quantity for page number */
define('NAVI_PMAX', 4);
/** Maximum number of product subinformation */
define('PRODUCTSUB_MAX', 5);
/** Maximum number of delivery times displayed */
define('DELIVTIME_MAX', 16);
/** Maximum display count for delivery charge */
define('DELIVFEE_MAX', 47);
/** Character count of short items (names, etc.) */
define('STEXT_LEN', 50);
define('SMTEXT_LEN', 100);
/** Character count of long items (addresses, etc.) */
define('MTEXT_LEN', 200);
/** Character count of long and medium-length text (inquiries, etc.) */
define('MLTEXT_LEN', 1000);
/** Character count of long text */
define('LTEXT_LEN', 3000);
/** Character count of ultralong text (mail magazines, etc.) */
define('LLTEXT_LEN', 99999);
/** URL character length */
define('URL_LEN', 1024);
/** For management screen: Maximum character count for ID/password */
define('ID_MAX_LEN', STEXT_LEN);
/** For management screen: Minimum number of characters for ID and password */
define('ID_MIN_LEN', 4);
/** Number of digits for amount */
define('PRICE_LEN', 8);
/** the number of digits following the point */
define('PERCENTAGE_LEN', 3);
/** Inventory count, Number of sales restrictions */
define('AMOUNT_LEN', 6);
/** Postal code 1 */
define('ZIP01_LEN', 3);
/** Postal code 2 */
define('ZIP02_LEN', 4);
/** Various item restrictions for telephone numbers */
define('TEL_ITEM_LEN', 6);
/** Total number of telephone numbers */
define('TEL_LEN', 12);
/** Front screen: Minimum character count for password */
define('PASSWORD_MIN_LEN', 4);
/** Front screen: Maximum character count for password */
define('PASSWORD_MAX_LEN', STEXT_LEN);
/** Number of digits for test values (INT) */
define('INT_LEN', 9);
/** Character count for credit card (*Used in module) */
define('CREDIT_NO_LEN', 4);
/** Search category maximum display character count (byte) */
define('SEARCH_CATEGORY_LEN', 18);
/** File name display character count */
define('FILE_NAME_LEN', 10);
/** zipcode character count */
define('ZIPCODE_LEN', 10);
/** Cookie retention time (days) */
define('COOKIE_EXPIRE', 365);
/** Category delimiter */
define('SEPA_CATNAVI', " > ");
/** Member information input */
define('SHOPPING_URL', HTTPS_URL . "shopping/" . DIR_INDEX_PATH);
/** Top of member registration page */
define('ENTRY_URL', HTTPS_URL . "entry/" . DIR_INDEX_PATH);
/** Site top */
define('TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** Cart top */
define('CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
/** Delivery destination settings */
define('DELIV_URLPATH', ROOT_URLPATH . "shopping/deliv.php");
/** Settings for multiple delivery destinations */
define('MULTIPLE_URLPATH', ROOT_URLPATH . "shopping/multiple.php");
/** Purchase confirmation page */
define('SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** Payment method selection page */
define('SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** Purchase completion screen */
define('SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** Screen for module addition */
define('SHOPPING_MODULE_URLPATH', ROOT_URLPATH . "shopping/load_payment_module.php");
/** Product details (HTML output) */
define('P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** My page delivery destination URL */
define('MYPAGE_DELIVADDR_URLPATH', ROOT_URLPATH . "mypage/delivery.php");
/** New information management screen Start year (A.D.) */
define('ADMIN_NEWS_STARTYEAR', 2005);
/** Reinitiation restriction time (units: hours) */
define('ENTRY_LIMIT_HOUR', 1);
/** Related product display number */
define('RECOMMEND_PRODUCT_MAX', 6);
/** Recommended product display number */
define('RECOMMEND_NUM', 8);
/** Maximum number of days displayed on pull-down menu after date on which delivery is possible */
define('DELIV_DATE_END_MAX', 21);
/** Payment deadline (*Used in module) */
define('CV_PAYMENT_LIMIT', 14);
/** Allow or not allow writing of URLs in product reviews */
define('REVIEW_ALLOW_URL', 0);
/** Will site information be transmitted when updating? */
define('UPDATE_SEND_SITE_INFO', false);
/** Do you want to use points? (true: Use, false: Do not use) (false is partially supported) */
define('USE_POINT', true);
/** Non-display of products with no inventory (true: Not displayed, false: Displayed) */
define('NOSTOCK_HIDDEN', false);
/** Do you want to use the mobile site? (true: Use, false: Do not use) (false is partially supported) (*Used in module) */
define('USE_MOBILE', true);
/** Do you want to use the multiple shipping destination designation function? (true: Use, false: Do not use) */
define('USE_MULTIPLE_SHIPPING', true);
/** Character count of short text */
define('SLTEXT_LEN', 500);
/** Default template name (PC) */
define('DEFAULT_TEMPLATE_NAME', "default_en-US");
/** Default template name (mobile) */
define('MOBILE_DEFAULT_TEMPLATE_NAME', "mobile");
/** Default template name (smartphone) */
define('SMARTPHONE_DEFAULT_TEMPLATE_NAME', "sphone_en-US");
/** Template name */
define('TEMPLATE_NAME', "default_en-US");
/** Mobile template name */
define('MOBILE_TEMPLATE_NAME', "mobile");
/** Smartphone template name */
define('SMARTPHONE_TEMPLATE_NAME', "sphone_en-US");
/** SMARTY template */
define('SMARTY_TEMPLATES_REALDIR',  DATA_REALDIR . "Smarty/templates/");
/** SMARTY template (PC) */
define('TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . TEMPLATE_NAME . "/");
/** SMARTY template (management function) */
define('TEMPLATE_ADMIN_REALDIR', SMARTY_TEMPLATES_REALDIR . "admin/");
/** SMARTY compile */
define('COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . TEMPLATE_NAME . "/");
/** SMARTY compile (management function) */
define('COMPILE_ADMIN_REALDIR', DATA_REALDIR . "Smarty/templates_c/admin/");
/** Block file saving destination */
define('BLOC_DIR', "frontparts/bloc/");
/** SMARTY template (mobile) */
define('MOBILE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . MOBILE_TEMPLATE_NAME . "/");
/** SMARTY compile (mobile) */
define('MOBILE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . MOBILE_TEMPLATE_NAME . "/");
/** SMARTY template (smart phone) */
define('SMARTPHONE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . SMARTPHONE_TEMPLATE_NAME . "/");
/** SMARTY compile (smartphone) */
define('SMARTPHONE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . SMARTPHONE_TEMPLATE_NAME . "/");
/** Does the e-mail address check comply with RFC? (true: complies, false: does not comply) */
define('RFC_COMPLIANT_EMAIL_CHECK', false);
/** Mobile site session continuation time (seconds) */
define('MOBILE_SESSION_LIFETIME', 1800);
/** Directory for saving converted images for mobile phones */
define('MOBILE_IMAGE_REALDIR', HTML_REALDIR . "upload/mobile_image/");
/** Directory for saving converted images for mobile phones */
define('MOBILE_IMAGE_URLPATH', ROOT_URLPATH . "upload/mobile_image/");
/** Mobile URL */
define('MOBILE_TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** Cart top */
define('MOBILE_CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
/** Purchase confirmation page */
define('MOBILE_SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** Payment method selection page */
define('MOBILE_SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** Product details (HTML output) */
define('MOBILE_P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** Purchase completion screen (*Used in the module) */
define('MOBILE_SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** Session maintenance method: "useCookie"|"useRequest" */
define('SESSION_KEEP_METHOD', "useCookie");
/** Session continuation time (seconds) */
define('SESSION_LIFETIME', 1800);
/** Owners Store URL */
define('OSTORE_URL', "http://www.ec-cube.net/");
/** Owners Store URL */
define('OSTORE_SSLURL', "");
/** Owners Store log path */
define('OSTORE_LOG_REALFILE', DATA_REALDIR . "logs/ownersstore.log");
/** Favorite product registration (active: 1 inactive: 0) */
define('OPTION_FAVORITE_PRODUCT', 1);
/** Image rename settings (product images only) (true: Rename, false: Do not rename) */
define('IMAGE_RENAME', true);
/** (For 2.11) Plug-in directory (Used in module) */
define('PLUGIN_DIR', "plugins/");
/** (For 2.11) Plug-in saving destination (Used in module) */
define('PLUGIN_REALDIR', USER_REALDIR . PLUGIN_DIR);
/** Plug-in saving destination directory */
define('PLUGIN_UPLOAD_REALDIR', DATA_REALDIR . "downloads/plugin/");
/** Plug-in saving destination directory (html) */
define('PLUGIN_HTML_REALDIR', HTML_REALDIR . "plugin/");
/** Temporary saving destination of plug-in file */
define('PLUGIN_TEMP_REALDIR', HTML_REALDIR . "upload/temp_plugin/");
/** Extensions possible for registration of plug-in file (comma-delimited) */
define('PLUGIN_EXTENSION', "tar,tar.gz");
/** Temporary decompression directory for plug-in (for updating) */
define('DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR', DATA_REALDIR . "downloads/tmp/plugin_update/");
/** Temporary decompression directory for plug-in (for installing) */
define('DOWNLOADS_TEMP_PLUGIN_INSTALL_DIR', DATA_REALDIR . "downloads/tmp/plugin_install/");
/** Plug-in URL */
define('PLUGIN_HTML_URLPATH', ROOT_URLPATH . "plugin/");
/** Number of days Number of digits */
define('DOWNLOAD_DAYS_LEN', 3);
/** Extensions possible for registration of downloaded files (comma-delimited) */
define('DOWNLOAD_EXTENSION', "zip,lzh,jpg,jpeg,gif,png,mp3,pdf,csv");
/** Size limitation for download sales file (KB) */
define('DOWN_SIZE', 50000);
/** 1: Actual product 2: Download */
define('DEFAULT_PRODUCT_DOWN', 1);
/** Temporary saving of downloaded file */
define('DOWN_TEMP_REALDIR', DATA_REALDIR . "download/temp/");
/** Saving destination of downloaded file */
define('DOWN_SAVE_REALDIR', DATA_REALDIR . "download/save/");
/** Download sales function   Downloaded file reading bytes (KB) */
define('DOWNLOAD_BLOCK', 1024);
/** New order */
define('ORDER_NEW', 1);
/** Waiting for deposit */
define('ORDER_PAY_WAIT', 2);
/** Deposited */
define('ORDER_PRE_END', 6);
/** Cancel */
define('ORDER_CANCEL', 3);
/** Being backordered */
define('ORDER_BACK_ORDER', 4);
/** Shipped */
define('ORDER_DELIV', 5);
/** Payment being processed */
define('ORDER_PENDING', 7);
/** Normal product */
define('PRODUCT_TYPE_NORMAL', 1);
/** Downloaded product */
define('PRODUCT_TYPE_DOWNLOAD', 2);
/** DB log recording mode (0: No recording, 1: Recording only during delays, 2: Constant recording) */
define('SQL_QUERY_LOG_MODE', 1);
/** Execution time deemed as being a delay in the DB log (seconds) */
define('SQL_QUERY_LOG_MIN_EXEC_TIME', 2);
/** Flag for retrieving page display time log (1: Display, 0: Do not display) */
define('PAGE_DISPLAY_TIME_LOG_MODE', 1);
/** Time settings for retrieving page display time log (retrieved when it takes longer than the set value) */
define('PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME', 2);
/** Terminal type: Mobile */
define('DEVICE_TYPE_MOBILE', 1);
/** Terminal type: Smartphone */
define('DEVICE_TYPE_SMARTPHONE', 2);
/** Terminal type: PC */
define('DEVICE_TYPE_PC', 10);
/** Terminal type: Management screen */
define('DEVICE_TYPE_ADMIN', 99);
/** EC-CUBE update information retrieval (true: retrieve false: do not retrieve) */
define('ECCUBE_INFO', true);
/** External site HTTP retrieval timeout time (seconds) */
define('HTTP_REQUEST_TIMEOUT', "5");
/** Postal code CSV ZIP archive file retrieval source */
define('ZIP_DOWNLOAD_URL', "http://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip");
/** Hook point (preprocess) */
define('HOOK_POINT_PREPROCESS', "LC_Page_preProcess");
/** Hook point (process) */
define('HOOK_POINT_PROCESS', "LC_Page_process");
/** Load/not load flag for plug) */
define('PLUGIN_ACTIVATE_FLAG', true);
/** SMARTY compile mode */
define('SMARTY_FORCE_COMPILE_MODE', false);
/** Delay time when login fails (seconds) (measure against brute force attack) */
define('LOGIN_RETRY_INTERVAL', 0);
/** MY page: Order status display flag */
define('MYPAGE_ORDER_STATUS_DISP_FLAG', true);
/** Mail character code */
define('MAIL_CHARACTER_CODE', 'UTF-8');
/** Mail header: contents type */
define('MAIL_HEADER_CONTENT_TYPE', 'US-ASCII');
/** Time zone  */
define('TIMEZONE', 'Etc/GMT+0');