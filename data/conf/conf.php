<?php

$CONF_PHP_PATH = realpath( dirname( __FILE__) );
require_once($CONF_PHP_PATH ."/../../html/install.inc");
require_once($CONF_PHP_PATH ."/core.php" );

//--------------------------------------------------------------------------------------------------------
/** ���åץǡ��ȴ����� **/
// ���åץǡ��ȴ����ѥե������Ǽ���
define("UPDATE_HTTP", "http://ec-cube.lockon.co.jp/share/");
// ���åץǡ��ȴ�����CSV1���դ�κ���ʸ����
define("UPDATE_CSV_LINE_MAX", 4096);
// ���åץǡ��ȴ�����CSV������
define("UPDATE_CSV_COL_MAX", 13);
//--------------------------------------------------------------------------------------------------------

define("CLOSE_DAY", 31);	// �������λ���(�����ξ��ϡ�31����ꤷ�Ƥ���������)

//���̥����ȥ��顼
define("FAVORITE_ERROR", 13);

//�������ᾦ�ʿ�
define ("RECOMMEND_PRODUCT_MAX", 4);

/** ����մ�Ϣ **/
	
define("LIB_DIR", ROOT_DIR . "data/lib/");						// �饤�֥��Υѥ�
define("TTF_DIR", ROOT_DIR . "data/fonts/");					// �ե���ȤΥѥ�
define("GRAPH_DIR", ROOT_DIR . "html/upload/graph_image/");		// ����ճ�Ǽ�ǥ��쥯�ȥ�
define("GRAPH_URL", "/upload/graph_image/");					// �����URL
define("GRAPH_PIE_MAX", 10);									// �ߥ���պ���ɽ����
define("GRAPH_LABEL_MAX", 40);									// ����դΥ�٥��ʸ����

/** �ѥ���Ϣ **/

define("PDF_DIR", ROOT_DIR . "data/pdf/");	// PDF��Ǽ�ǥ��쥯�ȥ�

/** ��夲���� **/

define("BAT_ORDER_AGE", 70);		// ���Фޤǽ��פ��оݤȤ��뤫
define("PRODUCTS_TOTAL_MAX", 15);	// ���ʽ��פǲ��̤ޤ�ɽ�����뤫

/** �ǥե������ **/
define("DEFAULT_PRODUCT_DISP", 2);	// 1:���� 2:�����

/** ���ץ�������� **/
define("DELIV_FREE_AMOUNT", 0);				// ����̵�������Ŀ���0�ξ��ϡ�������äƤ�̵���ˤʤ�ʤ�)
define("INPUT_DELIV_FEE", 1);				// ���������������ɽ��(ͭ��:1 ̵��:0)
define("OPTION_PRODUCT_DELIV_FEE", 0);		// ���ʤ��Ȥ���������(ͭ��:1 ̵��:0)
define("OPTION_DELIV_FEE", 1);				// �����ȼԤ��Ȥ���������û�����(ͭ��:1 ̵��:0)
define("OPTION_RECOMMEND", 1);		// �������ᾦ����Ͽ(ͭ��:1 ̵��:0)
define("OPTION_CLASS_REGIST", 1);	// ���ʵ�����Ͽ(ͭ��:1 ̵��:0)

define("TV_IMAGE_WIDTH",170);		//TVϢư���ʲ�����
define("TV_IMAGE_HEIGHT",95);		//TVϢư���ʲ�����
define("TV_PRODUCTS_MAX",10);		//TVϢư���ʺ�����Ͽ��

/** ���ץ�������� **/



//�����Ͽ�ѹ�(�ޥ��ڡ���)�ѥ������
define("DEFAULT_PASSWORD", "UAhgGR3L");
//�̤Τ��Ϥ��������Ͽ��
define("DELIV_ADDR_MAX", 20);
//�ޥ��ڡ������Ϥ���URL
define("MYPAGE_DELIVADDR_URL", "/mypage/delivery.php");
//����������¸��
define("CUSTOMER_READING_MAX",30);
//SSLURLȽ��
define("SSLURL_CHECK", 0);
//�������̥��ơ���������ɽ�����
define("ORDER_STATUS_MAX", 50);
//�ե��ȥ�ӥ塼�񤭹��ߺ����
define("REVIEW_REGIST_MAX", 5);

/*
 * ������������
 */
/* �����ƥ��Ϣ */
define ("LOGIN_FRAME", "login_frame.tpl");				// ��������̥ե졼��
define ("MAIN_FRAME", "main_frame.tpl");				// �������̥ե졼��
define ("SITE_FRAME", "site_frame.tpl");				// ���̥����Ȳ��̥ե졼��
define ("CERT_STRING", "7WDhcBTF");						// ǧ��ʸ����
define ("ADMIN_ID", "1");								// �����桼��ID(���ƥʥ���ɽ������ʤ���)
define ("DUMMY_PASS", "########");						// ���ߡ��ѥ����
define ("UNLIMITED", "++");								// �߸˿�����������̵�¤򼨤���
define ("BIRTH_YEAR", 1901);							// ��ǯ������Ͽ����ǯ
define ("RELEASE_YEAR", 2005);							// �ܥ����ƥ�β�Ư����ǯ
define ("CREDIT_ADD_YEAR", 10);							// ���쥸�åȥ����ɤδ��¡ܲ�ǯ
define ("PARENT_CAT_MAX", 12);							// �ƥ��ƥ���Υ��ƥ���ID�κ�����ʤ���ʲ��Ͽƥ��ƥ���Ȥ��롣)
define ("NUMBER_MAX", 1000000000);						// GET���ѹ��ʤɤΤ���������ɤ������������¤��ߤ��롣
define ("POINT_RULE", 2);								// �ݥ���Ȥη׻��롼��(1:�ͼθ�����2:�ڤ�Τơ�3:�ڤ�夲)
define ("POINT_VALUE", 1);								// 1�ݥ���������������(��)
define ("ADMIN_MODE", 0);								// �����⡼�� 1:ͭ����0:̵��(Ǽ�ʻ�)

define ("FORGOT_MAIL", 0);								// �ѥ����˺��γ�ǧ�᡼������դ��뤫�ݤ���(0:�������ʤ���1:��������)
define ("HTML_TEMPLATE_SUB_MAX", 12);					// ��Ͽ�Ǥ��륵�־��ʤο�
define ("LINE_LIMIT_SIZE", 60);							// ʸ������¿������Ȥ��˶������Ԥ��륵����(Ⱦ��)
define ("BIRTH_MONTH_POINT", 0);						// ��������ݥ����
define ("INPUT_DELIV_FEE", true);						// ���������������(true:���ꡢfalse:�ʤ�)

/* ���쥸�åȥ���(����ȥ��ե����ʥ�) */
define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredit");					// �ۡ��ॢ�ɥ쥹
define ("CF_STORECODE", "361901000000000");									// ����Ź������(�ϥ��ե�ʤ��ǡ�
// define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredittest");				// �ۡ��ॢ�ɥ쥹(�ƥ�����)
// define ("CF_STORECODE", "111111111111111");									// ����Ź������(�ƥ�����)

define ("CF_SIMULATE", "/simulate/simulate.cgi");							// ����ߥ졼�����ƤӽФ�
define ("CF_RETURNURL", SSL_URL . "shopping/loan.php");						// �����
define ("CF_CANCELURL", SSL_URL . "shopping/loan_cancel.php");				// �����
define ("CF_CONTINUE", "1");												// �ƤӽФ���ʬ(0:����ߥ졼�����Τߡ�1:����ߥ졼�����+����)
define ("CF_LABOR", "0");													// ��̵̳ͭ��ʬ(0:̵��1:ͭ)
define ("CF_RESULT", "1");													// ��̱���(1:��̤��ꡢ2:��̤ʤ�)

/* ���쥸�åȥ�����(�٥�ȥ��) */
define ("CGI_DIR", ROOT_DIR . "cgi-bin/");									// �⥸�塼���Ǽ�ǥ��쥯�ȥ�
define ("CGI_FILE", "mauthonly.cgi");										// ����CGI

// �롼�ȥ��ƥ���ID
define ("ROOT_CATEGORY_1", 2);
define ("ROOT_CATEGORY_2", 3);
define ("ROOT_CATEGORY_3", 4);
define ("ROOT_CATEGORY_4", 5);
define ("ROOT_CATEGORY_5", 6);
define ("ROOT_CATEGORY_6", 7);
define ("ROOT_CATEGORY_7", 8);

// ����ʧ����ˡ�ü�ID
define ("PAYMENT_DAIBIKI_ID",1);		// ������
define ("PAYMENT_GINFURI_ID", 2);		// ��Կ���
define ("PAYMENT_KAKITOME_ID", 3);		// �����α
define ("PAYMENT_CREDIT_ID",4);			// ���쥸�åȥ�����
define ("PAYMENT_LOAN_ID", 5);			// ����åԥ󥰥���
define ("PAYMENT_CONVENIENCE_ID", 6);	// ����ӥ˷��

define("LARGE_IMAGE_WIDTH",  500);						// ���������
define("LARGE_IMAGE_HEIGHT", 500);						// ���������
define("SMALL_IMAGE_WIDTH",  130);						// ����������
define("SMALL_IMAGE_HEIGHT", 130);						// ����������
define("NORMAL_IMAGE_WIDTH",  260);						// �̾������
define("NORMAL_IMAGE_HEIGHT", 260);						// �̾������
define("NORMAL_SUBIMAGE_WIDTH", 130);					// �̾掠�ֲ�����
define("NORMAL_SUBIMAGE_HEIGHT", 130);					// �̾掠�ֲ�����
define("LARGE_SUBIMAGE_WIDTH", 500);					// ���祵�ֲ�����
define("LARGE_SUBIMAGE_HEIGHT", 500);					// ���祵�ֲ�����
define("DISP_IMAGE_WIDTH",  65);						// ����ɽ��������
define("DISP_IMAGE_HEIGHT", 65);						// ����ɽ��������
define("OTHER_IMAGE1_WIDTH", 500);						// ����¾�β���1
define("OTHER_IMAGE1_HEIGHT", 500);						// ����¾�β���1
define("HTMLMAIL_IMAGE_WIDTH",  110);					// HTML�᡼��ƥ�ץ졼�ȥ᡼��ô��������
define("HTMLMAIL_IMAGE_HEIGHT", 120);					//  HTML�᡼��ƥ�ץ졼�ȥ᡼��ô��������

define("IMAGE_SIZE", 100);								// ��������������(KB)
define("CSV_SIZE", 2000);								// CSV����������(KB)
define("PDF_SIZE", 5000);								// PDF����������(KB):���ʾܺ٥ե�������
define("LEVEL_MAX", 3);									// ���ƥ���κ��糬��
define("CATEGORY_MAX", 1000);							// ���祫�ƥ�����Ͽ��

/* ɽ����Ϣ */
define ("ADMIN_TITLE", "EC�����ȴ����ڡ���");			// �����ڡ��������ȥ�
define ("SELECT_RGB", "#ffffdf");						// �Խ�����Ĵɽ����
define ("DISABLED_RGB", "#dddddd");						// ���Ϲ���̵������ɽ����
define ("ERR_COLOR", "#ffe8e8");						// ���顼��ɽ����
define ("CATEGORY_HEAD", "��");							// �ƥ��ƥ���ɽ��ʸ��
define ("START_BIRTH_YEAR", 1901);						// ��ǯ�������򳫻�ǯ

/* �����ƥ�ѥ� */
define ("LOG_PATH", ROOT_DIR . "data/logs/site.log");							// ���ե�����
define ("CUSTOMER_LOG_PATH", ROOT_DIR . "data/logs/customer.log");				// ��������� ���ե�����
define ("TEMPLATE_ADMIN_DIR", ROOT_DIR . "data/Smarty/templates/admin");		// SMARTY�ƥ�ץ졼��
define ("TEMPLATE_DIR", ROOT_DIR . "data/Smarty/templates");					// SMARTY�ƥ�ץ졼��
define ("COMPILE_ADMIN_DIR", ROOT_DIR . "data/Smarty/templates_c/admin");		// SMARTY����ѥ���
define ("COMPILE_DIR", ROOT_DIR . "data/Smarty/templates_c");					// SMARTY����ѥ���

define ("TEMPLATE_FTP_DIR", ROOT_DIR . "html/user_data/templates/");			// SMARTY�ƥ�ץ졼��(FTP����)
define ("COMPILE_FTP_DIR", ROOT_DIR . "data/Smarty/templates_c/user_data/");	// SMARTY����ѥ���

define ("IMAGE_TEMP_DIR", ROOT_DIR . "html/upload/temp_image/");				// ���������¸
define ("IMAGE_SAVE_DIR", ROOT_DIR . "html/upload/save_image/");				// ������¸��
define ("IMAGE_TEMP_URL", "/upload/temp_image/");								// ���������¸URL
define ("IMAGE_SAVE_URL", "/upload/save_image/");								// ������¸��URL
define ("CSV_TEMP_DIR", ROOT_DIR. "html/upload/csv/");							// ���󥳡���CSV�ΰ����¸��
define ("NO_IMAGE_URL", "/misc/dummy.gif");										// �������ʤ�����ɽ��

/* URL�ѥ� */
define ("URL_SYSTEM_TOP", "/admin/system/index.php");		// �����ƥ�����ȥå�
define ("URL_CLASS_REGIST", "/admin/products/class.php");	// ������Ͽ
define ("URL_INPUT_ZIP", "/input_zip.php");			// ͹���ֹ�����
define ("URL_DELIVERY_TOP", "/admin/basis/delivery.php");	// �����ȼ���Ͽ
define ("URL_PAYMENT_TOP", "/admin/basis/payment.php");		// ��ʧ����ˡ��Ͽ
define ("URL_HOME", "/admin/home.php");						// �ۡ���
define ("URL_LOGIN", "/admin/index.php");					// ������ڡ���
define ("URL_SEARCH_TOP", "/admin/products/index.php");		// ���ʸ����ڡ���
define ("URL_ORDER_EDIT", "/admin/order/edit.php");			// ��ʸ�Խ��ڡ���
define ("URL_SEARCH_ORDER", "/admin/order/index.php");		// ��ʸ�Խ��ڡ���
define ("URL_ORDER_MAIL", "/admin/order/mail.php");			// ��ʸ�Խ��ڡ���
define ("URL_LOGOUT", "/admin/logout.php");					// �������ȥڡ���
define ("URL_SYSTEM_CSV", "/admin/system/member_csv.php");	// �����ƥ����CSV���ϥڡ���
define ("URL_SYSTEM_TOP", "/admin/system/index.php");		// �����ƥ����TOP�ڡ���
define ("URL_ADMIN_CSS", "/admin/css/");					// �����ڡ�����CSS�ݴɥǥ��쥯�ȥ�

/* ǧ�ڥ��顼 */
define ("SUCCESS", 0);			// ������������
define ("LOGIN_ERROR", 1);		// ��������
define ("ACCESS_ERROR", 2);		// �����������ԡʥ����ॢ��������
define ("AUTH_ERROR", 3);		// �����������°�ȿ

/* ɽ�������� */
define ("PRODUCTS_LIST_MAX", 15);	// ���ʰ���ɽ����
define ("MEMBER_PMAX", 10);			// ���С������ڡ���ɽ���Կ�
define ("SEARCH_PMAX", 10);			// �����ڡ���ɽ���Կ�
define ("NAVI_PMAX", 5);			// �ڡ����ֹ�κ���ɽ���Ŀ�
define ("PRODUCTSUB_MAX", 5);		// ���ʥ��־�������
define ("DELIVTIME_MAX", 16);		// �������֤κ���ɽ����
define ("DELIVFEE_MAX", 47);		// ��������κ���ɽ����

/* ʸ�������� */
define ("STEXT_LEN", 50);		// û�����ܤ�ʸ������̾���ʤ�)
define ("SMTEXT_LEN", 100);
define ("MTEXT_LEN", 200);		// Ĺ�����ܤ�ʸ�����ʽ���ʤɡ�
define ("MLTEXT_LEN", 1000);	// Ĺ��ʸ��ʸ�������䤤��碌�ʤɡ�
define ("LTEXT_LEN", 3000);		// Ĺʸ��ʸ����
define ("LLTEXT_LEN", 99999);	// ĶĹʸ��ʸ�����ʥ��ޥ��ʤɡ�
define ("URL_LEN", 300);		// URL��ʸ��Ĺ
define("ID_MAX_LEN", 15);		// ID���ѥ���ɤ�ʸ��������
define("ID_MIN_LEN", 4);		// ID���ѥ���ɤ�ʸ��������
define("PRICE_LEN", 8);			// ��۷��
define("PERCENTAGE_LEN", 3);	// Ψ���
define("AMOUNT_LEN", 6);		// �߸˿����������¿�
define("ZIP01_LEN", 3);			// ͹���ֹ�1
define("ZIP02_LEN", 4);			// ͹���ֹ�2
define("TEL_ITEM_LEN", 6);		// �����ֹ�ƹ�������
define("TEL_LEN", 12);			// �����ֹ����
define("PASSWORD_LEN1", 4);		// �ѥ����1
define("PASSWORD_LEN2", 10);	// �ѥ����2
define("INT_LEN", 8);			// ���������ѷ��(INT)
define("CREDIT_NO_LEN", 4);		// ���쥸�åȥ����ɤ�ʸ����
define("SEARCH_CATEGORY_LEN", 18);	// �������ƥ������ɽ��ʸ����(byte)

/** �ե��ȥڡ��� **/

/* �����ƥ��Ϣ */
define ("SALE_LIMIT_MAX", 10);		// �������¤ʤ��ξ��κ�������Ŀ�
define ("SITE_TITLE", "�ţ�-�ãգ£�  �ƥ��ȥ�����");	// HTML�����ȥ�
define ("COOKIE_EXPIRE", 365);		// ���å����ݻ�����(��)
define ("FREE_DIAL", "0120-339337");

/* ���̥����ȥ��顼 */
define ("PRODUCT_NOT_FOUND", 1);	// ���꾦�ʥڡ������ʤ�
define ("CART_EMPTY", 2);			// �������⤬��
define ("PAGE_ERROR", 3);			// �ڡ�����ܥ��顼
define ("CART_ADD_ERROR", 4);		// ����������Υ����Ⱦ����ɲå��顼
define ("CANCEL_PURCHASE", 5);		// ¾�ˤ������³�����Ԥ�줿���
define ("CATEGORY_NOT_FOUND", 6);	// ���ꥫ�ƥ���ڡ������ʤ�
define ("SITE_LOGIN_ERROR", 7);		// ������˼���
define ("CUSTOMER_ERROR", 8);		// ������ѥڡ����ؤΥ����������顼
define ("SOLD_OUT", 9);				// ������������ڤ쥨�顼
define ("CART_NOT_FOUND", 10);		// �������⾦�ʤ��ɹ����顼
define ("LACK_POINT", 11);			// �ݥ���Ȥ���­
define ("TEMP_LOGIN_ERROR", 12);	// ����Ͽ�Ԥ�������˼���
define ("URL_ERROR", 13);			// URL���顼
define ("EXTRACT_ERROR", 14);		//�ե�������२�顼
define ("FTP_DOWNLOAD_ERROR", 15);	//FTP��������ɥ��顼
define ("FTP_LOGIN_ERROR", 16);		//FTP�����󥨥顼
define ("FTP_CONNECT_ERROR", 17);	//FTP��³���顼
define ("CREATE_DB_ERROR", 18);		//DB�������顼
define ("DB_IMPORT_ERROR", 19);		//DB����ݡ��ȥ��顼
define ("FILE_NOT_FOUND", 20);		//����ե�����¸�ߥ��顼
define ("WRITE_FILE_ERROR", 21);	//�ե�����񤭹��ߥ��顼

/* ɽ����Ϣ */
define ("SEPA_CATNAVI", " > ");	// ���ƥ�����ڤ�ʸ��
define ("SEPA_CATLIST", " | ");	// ���ƥ�����ڤ�ʸ��

/* URL */
define ("URL_SITE_TOP", "/index.php");					// �����ȥȥå�
define ("URL_CART_TOP", "/cart/index.php");				// �����ȥȥå�
define ("URL_SHOP_CONFIRM", "/shopping/confirm.php");	// ������ǧ�ڡ���
define ("URL_SHOP_PAYMENT", "/shopping/payment.php");	// ����ʧ����ˡ����ڡ���
define ("URL_SHOP_TOP", "/shopping/index.php");			// �����������
define ("URL_SHOP_COMPLETE", "/shopping/complete.php");	// ������λ����
define ("URL_SHOP_CREDIT", "/shopping/card.php");		// �����ɷ�Ѳ���
define ("URL_SHOP_LOAN", "/shopping/loan.php");			// �����Ѳ���
define ("URL_SHOP_CONVENIENCE", "/shopping/convenience.php");	// ����ӥ˷�Ѳ���
define ("URL_PRODUCTS_TOP","/products/top.php");		// ���ʥȥå�
define ("LIST_P_HTML", "/products/list-p");				// ���ʰ���(HTML����)
define ("LIST_C_HTML", "/products/list.php?mode=search&category_id=");				// ���ʰ���(HTML����)
define ("DETAIL_P_HTML", "/products/detail.php?product_id=");			// ���ʾܺ�(HTML����)

/*
 * ����������ѿ�
 */
 
// ������������
// 0:�����ԤΤߥ���������ǽ
// 1:���̰ʾ夬����������ǽ
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

// ���������Բĥڡ���
$arrDISABLE_LOGOUT = array(
	1 => "/shopping/deliv.php",
	2 => "/shopping/payment.php",
	3 => "/shopping/confirm.php",
	4 => "/shopping/card.php",
	5 => "/shopping/loan.php",
);

// ���С�����-����
$arrAUTHORITY[0] = "������";
// $arrAUTHORITY[1] = "����";
// $arrAUTHORITY[2] = "����";

// ���С�����-��Ư����
$arrWORK[0] = "���Ư";
$arrWORK[1] = "��Ư";

// ������Ͽ-ɽ��
$arrDISP[1] = "����";
$arrDISP[2] = "�����";

// ������Ͽ-����
$arrCLASS[1] = "����̵��";
$arrCLASS[2] = "����ͭ��";

// �������
$arrSRANK[1] = 1;
$arrSRANK[2] = 2;
$arrSRANK[3] = 3;
$arrSRANK[4] = 4;
$arrSRANK[5] = 5;

// ������Ͽ-���ơ�����
$arrSTATUS[1] = "NEW";
$arrSTATUS[2] = "�Ĥ�鷺��";
$arrSTATUS[3] = "�ݥ���ȣ���";
$arrSTATUS[4] = "��������";
$arrSTATUS[5] = "������";

// ������Ͽ-���ơ���������
$arrSTATUS_IMAGE[1] = "/img/right_product/icon01.gif";
$arrSTATUS_IMAGE[2] = "/img/right_product/icon02.gif";
$arrSTATUS_IMAGE[3] = "/img/right_product/icon03.gif";
$arrSTATUS_IMAGE[4] = "/img/right_product/icon04.gif";
$arrSTATUS_IMAGE[5] = "/img/right_product/icon05.gif";

// ���ϵ��Ĥ��륿��
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

// ���ڡ���ɽ���Կ�
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
	
// ���ޥ�����
$arrMagazineType["1"] = "HTML";
$arrMagazineType["2"] = "�ƥ�����";

$arrMagazineTypeAll = $arrMagazineType;
$arrMagazineTypeAll["3"] = "HTML�ƥ�ץ졼��";


/* ���ޥ����� */
$arrMAILMAGATYPE = array(
	1 => "HTML�᡼��",
	2 => "�ƥ����ȥ᡼��",
	3 => "��˾���ʤ�"
);

/* ���������٥� */
$arrRECOMMEND = array(
	5 => "����������",
	4 => "��������",
	3 => "������",
	2 => "����",
	1 => "��"
);

$arrTAXRULE = array(
	1 => "�ͼθ���",
	2 => "�ڤ�Τ�",
	3 => "�ڤ�夲"
);


// �᡼��ƥ�ץ졼�Ȥμ���
$arrMAILTEMPLATE = array(
	 1 => "��ʸ���ե᡼��"
	,2 => "��ʸ����󥻥���ե᡼��"
	,3 => "���󤻳�ǧ�᡼��"
);

// �ƥƥ�ץ졼�ȤΥѥ�
$arrMAILTPLPATH = array(
	1 => "mail_templates/order_mail.tpl",
	2 => "mail_templates/order_mail.tpl",
	3 => "mail_templates/order_mail.tpl",
	4 => "mail_templates/contact_mail.tpl",
);

// �����ơ������ѹ��κݤ˥ݥ��������û����륹�ơ������ֹ��ȯ���Ѥߡ�
define("ODERSTATUS_COMMIT", 5);

/* ��ƻ�ܸ����� */
$arrPref = array(
					1 => "�̳�ƻ",
					2 => "�Ŀ���",
					3 => "��긩",
					4 => "�ܾ븩",
					5 => "���ĸ�",
					6 => "������",
					7 => "ʡ�縩",
					8 => "��븩",
					9 => "���ڸ�",
					10 => "���ϸ�",
					11 => "��̸�",
					12 => "���ո�",
					13 => "�����",
					14 => "�����",
					15 => "���㸩",
					16 => "�ٻ���",
					17 => "���",
					18 => "ʡ�温",
					19 => "������",
					20 => "Ĺ�",
					21 => "���츩",
					22 => "�Ų���",
					23 => "���θ�",
					24 => "���Ÿ�",
					25 => "���츩",
					26 => "������",
					27 => "�����",
					28 => "ʼ�˸�",
					29 => "���ɸ�",
					30 => "�²λ���",
					31 => "Ļ�踩",
					32 => "�纬��",
					33 => "������",
					34 => "���縩",
					35 => "������",
					36 => "���縩",
					37 => "���",
					38 => "��ɲ��",
					39 => "���θ�",
					40 => "ʡ����",
					41 => "���츩",
					42 => "Ĺ�긩",
					43 => "���ܸ�",
					44 => "��ʬ��",
					45 => "�ܺ긩",
					46 => "�����縩",
					47 => "���츩"
				);
				
/* �������� */
$arrJob = array(
					1 => "��Ұ�",
					2 => "����",
					3 => "��̳��",
					4 => "����ȡ����ѿ�",
					5 => "�����ӥ���",
					6 => "���Ķ�",
					7 => "����",
					8 => "����",
					9 => "����¾"
				);

/* �ѥ���ɤ��������� */
$arrReminder = array(
						1 => "��Ƥε����ϡ�",
						2 => "����������Υޥ󥬤ϡ�",
						3 => "�繥���ʥڥåȤ�̾���ϡ�",
						4 => "�����οͤ�̾���ϡ�",
						5 => "���򤫤ä��ǲ�ϡ�",
						6 => "º�ɤ��Ƥ���������̾���ϡ�",
						7 => "�����ʿ���ʪ�ϡ�"
					);
/*����������*/
$arrSex = array(
					1 => "����",
					2 => "����"
				);

/*��1�Կ���*/		
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
		
/* �����ơ����� */
$arrORDERSTATUS = array(
	1 => "��������",
	2 => "�����Ԥ�",
	3 => "����󥻥�",
	4 => "������",
	5 => "ȯ���Ѥ�"
);

// �����ơ������ѹ��κݤ˥ݥ��������û����륹�ơ������ֹ��ȯ���Ѥߡ�
define("ODERSTATUS_COMMIT", 5);

/* ���ʼ��̤�ɽ���� */
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

// ����
$arrWDAY = array(
	0 => "��",
	1 => "��",
	2 => "��",
	3 => "��",
	4 => "��",
	5 => "��",
	6 => "��"
);			
		
/* �������������� */
define ("ADMIN_NEWS_STARTYEAR", 2005);	// ����ǯ(����)

/* �����Ͽ */
define("ENTRY_CUSTOMER_TEMP_SUBJECT", "�������Ͽ����λ�������ޤ�����");
define("ENTRY_CUSTOMER_REGIST_SUBJECT", "�ܲ����Ͽ����λ�������ޤ�����");
define("ENTRY_LIMIT_HOUR", 1);		//���������»��֡�ñ��: ����)

// �������ᾦ��ɽ����
define("RECOMMEND_NUM", 8);			// �������ᾦ��
define ("BEST_MAX", 5);				// �٥��Ⱦ��ʤκ�����Ͽ��
define ("BEST_MIN", 3);				// �٥��Ⱦ��ʤκǾ���Ͽ������Ͽ���������ʤ�����ɽ�����ʤ���)

//ȯ�����ܰ�
$arrDELIVERYDATE = array(
	1 => "¨��",
	2 => "1��2����",
	3 => "3��4����",
	4 => "1���ְʹ�",
	5 => "2���ְʹ�",
	6 => "3���ְʹ�",
	7 => "1����ʹ�",
	8 => "2����ʹ�",
	9 => "������(�������ٸ�)"
);

/* ��ã��ǽ�����հʹߤΥץ������ɽ���������� */
define("DELIV_DATE_END_MAX", 21);

/* ���������������Ͽ */
define("PURCHASE_CUSTOMER_REGIST", 0);	//1:ͭ����0:̵��

/* ���ʥꥹ��ɽ����� */
$arrPRODUCTLISTMAX = array(
	15 => '15��',
	30 => '30��',
	50 => '50��'
);

/* ���ξ��ʤ���ä��ͤϤ���ʾ��ʤ���äƤ��ޤ���ɽ����� */
define("RELATED_PRODUCTS_MAX", 3);

/*--------- ������ӥ˷���� ---------*/

//����ӥˤμ���
$arrCONVENIENCE = array(
	1 => '���֥󥤥�֥�',
	2 => '�ե��ߥ꡼�ޡ���',
	3 => '��������K���󥯥�',
	4 => '�����󡦥��������ޡ���',
	5 => '�ߥ˥��ȥåס��ǥ��꡼��ޥ�������ޥ����ǥ��꡼���ȥ�',
);

//�Ƽ拾��ӥ��ѥ�å�����
$arrCONVENIMESSAGE = array(
	1 => "�嵭URL���鿶��ɼ��������⤷���Ͽ���ɼ�ֹ���˹����ơ�����Υ��֥󥤥�֥�ˤƤ���ʧ������������",
	2 => "��ȥ����ɡ������ֹ���ʤɤ˹����ơ�����Υե��ߥ꡼�ޡ��Ȥˤ���ʧ������������",
	3 => "�嵭URL���鿶��ɼ��������⤷���ϥ�����������ֹ���ʤɤ˹����ơ�����Υ�������K���󥯥��ˤƤ���ʧ����������",
	4 => "����ɼ�ֹ���˹����ơ�����Υ�����ޤ��ϥ��������ޡ��ȤˤƤ���ʧ������������",
	5 => "�嵭URL���鿶��ɼ�������������Υߥ˥��ȥåס��ǥ��꡼��ޥ�������ޥ����ǥ��꡼���ȥ��ˤƤ���ʧ������������"
);

//��ʧ����
define("CV_PAYMENT_LIMIT", 14);

/*--------- ������ӥ˷���� ---------*/

//�����ڡ�����Ͽ�����
define("CAMPAIGN_REGIST_MAX", 20);

//DB�μ���
$arrDB = array(
	1 => 'PostgreSQL',
	2 => 'MySQL'
);

// �ƥ�ץ졼��
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

// �֥�å�����
$arrTarget = array(
	1 => "LeftNavi",
	2 => "MainHead",
	3 => "RightNavi",
	4 => "MainFoot",
	5 => "Unused"
);

?>