<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */

$CONF_PHP_PATH = realpath( dirname( __FILE__) );
require_once($CONF_PHP_PATH ."/../install.php");
require_once($CONF_PHP_PATH ."/core.php" );

//--------------------------------------------------------------------------------------------------------
/** ���顼��٥�����
/*
 *	'E_ERROR'             => ��ʼ¹Ի����顼������ϡ�������ݤ˴ؤ�������Τ褦�������� ���ʤ����顼�򼨤��ޤ���������ץȤμ¹Ԥ����Ǥ���ޤ��� 
 *	'E_WARNING'           => �¹Ի��ηٹ� (��̿Ū�ʥ��顼�ǤϤʤ�)��������ץȤμ¹Ԥ����Ǥ� ��ޤ���
 *	'E_PARSE'             => ����ѥ�����Υѡ������顼���ѡ������顼�ϥѡ����ǤΤ���������� ���� 
 *	'E_NOTICE'            => �¹Ի��ηٹ𡣥��顼��ȯ����������������������Ȥ򼨤��� �������̾�Υ�����ץȼ¹Ԥξ��ˤ⤳�ηٹ��ȯ���뤳�Ȥ����ꤦ�롣
 *	'E_CORE_ERROR'        => PHP�ν����ư�����Ǥ���̿Ū�ʥ��顼��E_ERROR�� ���Ƥ��뤬PHP�Υ����ˤ�ä�ȯ�Ԥ���������㤦�� 
 *	'E_CORE_WARNING'      => ����̿Ū�ǤϤʤ��˷ٹ�PHP�ν����ư����ȯ�����롣 E_WARNING�˻��Ƥ��뤬PHP�Υ����ˤ�ä�ȯ�Ԥ���� �����㤦�� 
 *	'E_COMPILE_ERROR'     => ����ѥ��������̿Ū�ʥ��顼��E_ERROR�� ���Ƥ��뤬Zend������ץƥ��󥰥��󥸥�ˤ�ä�ȯ�Ԥ���������㤦�� 
 *	'E_COMPILE_WARNING'   => ����ѥ�����ηٹ����̿Ū�ǤϤʤ��ˡ�E_WARNING�� ���Ƥ��뤬Zend������ץƥ��󥰥��󥸥�ˤ�ä�ȯ�Ԥ���������㤦�� 
 *	'E_USER_ERROR'        => �桼�����ˤ�ä�ȯ�Ԥ���륨�顼��å�������E_ERROR �˻��Ƥ��뤬PHP�����ɾ��trigger_error()�ؿ��� ���Ѥ�������ȯ�Ԥ���������㤦�� 
 *	'E_USER_WARNING'      => �桼�����ˤ�ä�ȯ�Ԥ����ٹ��å�������E_WARNING �˻��Ƥ��뤬PHP�����ɾ��trigger_error()�ؿ��� ���Ѥ�������ȯ�Ԥ���������㤦�� 
 *	'E_USER_NOTICE'       => �桼�����ˤ�ä�ȯ�Ԥ������ե�å�������E_NOTICE�� �˻��Ƥ��뤬PHP�����ɾ��trigger_error()�ؿ��� ���Ѥ�������ȯ�Ԥ���������㤦�� 
 *	'E_ALL'               => ���ݡ��Ȥ�������ƤΥ��顼�ȷٹ�PHP < 6 �Ǥ� E_STRICT ��٥�Υ��顼�Ͻ����� 
 *	'E_STRICT'            => ��PHP5���饵�ݡ��� �¹Ի�����ա������ɤ���߱�������ߴ�����ݻ����뤿��� PHP �������ɤ��ѹ�����Ƥ��롣
 *	'E_RECOVERABLE_ERROR' => ��PHP5���饵�ݡ��� ����å��Ǥ�����̿Ū�ʥ��顼�����ʥ��顼��ȯ���������� ���󥸥��԰���ʾ��֤ˤʤ�ۤɤǤϤʤ����Ȥ�ɽ���� �桼������Υϥ�ɥ�ǥ��顼������å�����ʤ��ä���� (set_error_handler() �⻲�Ȥ�������) �ϡ� E_ERROR �Ȥ��ư۾ｪλ���롣 
 */
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);

//--------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------
/** �ե���ɽ����Ϣ **/
define("SAMPLE_ADDRESS1", "�Զ�Į¼̾���㡧����������Ķ���Ŀ���Į��");
define("SAMPLE_ADDRESS2", "���ϡ��ӥ�̾���㡧1-3-5��");
//--------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------
/** �ѥ���Ϣ **/
// �桼���ե�������¸��
define("USER_DIR", "user_data/");
define("USER_PATH", HTML_PATH . USER_DIR);

// �桼�����󥯥롼�ɥե�������¸��
define("USER_INC_PATH", USER_PATH . "include/");

// �֥�å��ե�������¸��
define("BLOC_DIR", "include/bloc/");
define("BLOC_PATH", USER_PATH . BLOC_DIR);

// �����ڡ���ե�������¸��
define("CAMPAIGN_DIR", "cp/");
define("CAMPAIGN_URL", URL_DIR . CAMPAIGN_DIR);
define("CAMPAIGN_PATH", HTML_PATH . CAMPAIGN_DIR);
define("CAMPAIGN_TEMPLATE_DIR", "include/campaign/");
define("CAMPAIGN_TEMPLATE_PATH", USER_PATH . CAMPAIGN_TEMPLATE_DIR);
define("CAMPAIGN_BLOC_DIR", "bloc/");
define("CAMPAIGN_BLOC_PATH", CAMPAIGN_TEMPLATE_PATH . CAMPAIGN_BLOC_DIR);
define("CAMPAIGN_TEMPLATE_ACTIVE", "active/");
define("CAMPAIGN_TEMPLATE_END", "end/");

// �ƥ�ץ졼�ȥե�������¸��
define("USER_TEMPLATE_DIR", "templates/");
define("USER_TEMPLATE_PATH", USER_PATH . USER_TEMPLATE_DIR);
// �ƥ�ץ졼�ȥե���������¸��
define("TEMPLATE_TEMP_DIR", HTML_PATH . "upload/temp_template/");

// �桼�����������̤Υǥե����PHP�ե�����
define("USER_DEF_PHP", HTML_PATH . "__default.php");

// ����¾���̤Υǥե���ȥڡ����쥤������
define("DEF_LAYOUT", "products/list.php");

// ��������ɥ⥸�塼����¸�ǥ��쥯�ȥ�
define("MODULE_DIR", "downloads/module/");
define("MODULE_PATH", DATA_PATH . MODULE_DIR);

// HotFix��¸�ǥ��쥯�ȥ�
define("UPDATE_DIR", "downloads/update/");
define("UPDATE_PATH", DATA_PATH . UPDATE_DIR);
//--------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------
/** ���åץǡ��ȴ����� **/
// ���åץǡ��ȴ����ѥե������Ǽ��ꡡ
define("UPDATE_HTTP", "http://www.lockon.co.jp/share/");
// ���åץǡ��ȴ�����CSV1���դ�κ���ʸ����
define("UPDATE_CSV_LINE_MAX", 4096);
// ���åץǡ��ȴ�����CSV������
define("UPDATE_CSV_COL_MAX", 13);
// �⥸�塼�������CSV������
define("MODULE_CSV_COL_MAX", 16);
// �⥸�塼�������CSV�ե�����
define("MODULE_CSV", "module.txt");
//--------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------
/** �⥸�塼������� **/
// ���ӥ�������ǽ�Υ⥸�塼��ID
define("EBIS_TAG_MID", 1);
// ���ե��ꥨ���ȥ�����ǽ�Υ⥸�塼��ID
define("AFF_TAG_MID", 3);
// ���ʹ�����λ
define("AFF_SHOPPING_COMPLETE", 1);
// �桼����Ͽ��λ
define("AFF_ENTRY_COMPLETE", 2);
// ��Ѽ�����URL
define("CREDIT_HTTP_DOMAIN", "http://rcv.ec-cube.net/");
define("CREDIT_HTTP_ANALYZE_PROGRAM", "rcv_credit.php");
define("CREDIT_HTTP_ANALYZE_URL", CREDIT_HTTP_DOMAIN . CREDIT_HTTP_ANALYZE_PROGRAM);
//--------------------------------------------------------------------------------------------------------

// ʸ��������
define("CHAR_CODE", "EUC-JP");

// EC-CUBE�С���������
define("ECCUBE_VERSION", "1.4.0a-beta");

// ��ѥ⥸�塼����Ϳʸ��
define("ECCUBE_PAYMENT", "EC-CUBE");

// PEAR::DB�ΥǥХå��⡼��
define('PEAR_DB_DEBUG', 9);

//�Хå���¹Ԥ����û�δֳ�(��)
define("LOAD_BATCH_PASS", 3600);

define("CLOSE_DAY", 31);	// �������λ���(�����ξ��ϡ�31����ꤷ�Ƥ���������)

//���̥����ȥ��顼
define("FAVORITE_ERROR", 13);

/** ����մ�Ϣ **/
	
define("LIB_DIR", DATA_PATH . "lib/");						// �饤�֥��Υѥ�
define("TTF_DIR", DATA_PATH . "fonts/");					// �ե���ȤΥѥ�
define("GRAPH_DIR", HTML_PATH . "upload/graph_image/");		// ����ճ�Ǽ�ǥ��쥯�ȥ�
define("GRAPH_URL", URL_DIR . "upload/graph_image/");		// �����URL
define("GRAPH_PIE_MAX", 10);								// �ߥ���պ���ɽ����
define("GRAPH_LABEL_MAX", 40);								// ����դΥ�٥��ʸ����

/** �ѥ���Ϣ **/

define("PDF_DIR", DATA_PATH . "pdf/");	// PDF��Ǽ�ǥ��쥯�ȥ�

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
//�������ᾦ�ʿ�
define ("RECOMMEND_PRODUCT_MAX", 5);

//�̤Τ��Ϥ��������Ͽ��
define("DELIV_ADDR_MAX", 20);
//����������¸��
define("CUSTOMER_READING_MAX",30);
//SSLURLȽ��
define("SSLURL_CHECK", 0);
//�������̥��ơ���������ɽ�����
define("ORDER_STATUS_MAX", 50);
//�ե��ȥ�ӥ塼�񤭹��ߺ����
define("REVIEW_REGIST_MAX", 5);

/*F
 * ������������
 */
/* �����ƥ��Ϣ */
define ('DEBUG_MODE', false);                          // �ǥХå��⡼��(true��sfPrintR��DB�Υ��顼��å���������Ϥ��롢false�����Ϥ��ʤ�)
define ("ADMIN_ID", "1");								// �����桼��ID(���ƥʥ���ɽ������ʤ���)
define ("CUSTOMER_CONFIRM_MAIL", false);				// �����Ͽ���˲������ǧ�᡼����������뤫��true:�������false:�ܲ����
define ("MELMAGA_SEND", true);							// ���ޥ��ۿ�����(false:OFF��true:ON)
define ("MELMAGA_BATCH_MODE", false);					// �ᥤ��ޥ�����Хå��⡼��(true:�Хå����������� ����cron���ꡢfalse:�ꥢ�륿�������������)
define ("LOGIN_FRAME", "login_frame.tpl");				// ��������̥ե졼��
define ("MAIN_FRAME", "main_frame.tpl");				// �������̥ե졼��
define ("SITE_FRAME", "site_frame.tpl");				// ���̥����Ȳ��̥ե졼��
define ("CERT_STRING", "7WDhcBTF");						// ǧ��ʸ����
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
define ("DAILY_BATCH_MODE", false);						// ��彸�ץХå��⡼��(true:�Хå��ǽ��פ��� ����cron���ꡢfalse:�ꥢ�륿����ǽ��פ���)
define ("MAX_LOG_QUANTITY", 5);							// ���ե���������(���ơ������)
define ("MAX_LOG_SIZE", "1000000");						// 1�ĤΥ��ե��������¸�����������(byte)

define ("FORGOT_MAIL", 0);								// �ѥ����˺��γ�ǧ�᡼������դ��뤫�ݤ���(0:�������ʤ���1:��������)
define ("HTML_TEMPLATE_SUB_MAX", 12);					// ��Ͽ�Ǥ��륵�־��ʤο�
define ("LINE_LIMIT_SIZE", 60);							// ʸ������¿������Ȥ��˶������Ԥ��륵����(Ⱦ��)
define ("BIRTH_MONTH_POINT", 0);						// ��������ݥ����

/* ���쥸�åȥ���(����ȥ��ե����ʥ�) */
define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredit");					// �ۡ��ॢ�ɥ쥹
define ("CF_STORECODE", "");												// ����Ź������(�ϥ��ե�ʤ��ǡ�
// define ("CF_HOMEADDR", "https://cf.ufit.ne.jp/dotcredittest");				// �ۡ��ॢ�ɥ쥹(�ƥ�����)
// define ("CF_STORECODE", "111111111111111");									// ����Ź������(�ƥ�����)

define ("CF_SIMULATE", "/simulate/simulate.cgi");							// ����ߥ졼�����ƤӽФ�
// define ("CF_RETURNURL", SSL_URL . "shopping/loan.php");					// ����� ����åԥ󥰥���ϼ�����ȯ
// define ("CF_CANCELURL", SSL_URL . "shopping/loan_cancel.php");			// ����� ����åԥ󥰥���ϼ�����ȯ
define ("CF_CONTINUE", "1");												// �ƤӽФ���ʬ(0:����ߥ졼�����Τߡ�1:����ߥ졼�����+����)
define ("CF_LABOR", "0");													// ��̵̳ͭ��ʬ(0:̵��1:ͭ)
define ("CF_RESULT", "1");													// ��̱���(1:��̤��ꡢ2:��̤ʤ�)

/* ���쥸�åȥ�����(�٥�ȥ��) */
define ("CGI_DIR", HTML_PATH . "../cgi-bin/");								// �⥸�塼���Ǽ�ǥ��쥯�ȥ�
define ("CGI_FILE", "mauthonly.cgi");										// ����CGI

/* �֥쥤��SMTP���� */
// define ("SMTP_HOST_BLAYN", "210.188.254.83");					// �֥쥤��SMTP������
define ("SMTP_PORT_BLAYN", 25);									// �֥쥤��SMTP�ݡ����ֹ�

// �롼�ȥ��ƥ���ID
define ("ROOT_CATEGORY_1", 2);
define ("ROOT_CATEGORY_2", 3);
define ("ROOT_CATEGORY_3", 4);
define ("ROOT_CATEGORY_4", 5);
define ("ROOT_CATEGORY_5", 6);
define ("ROOT_CATEGORY_6", 7);
define ("ROOT_CATEGORY_7", 8);

// ����ʧ����ˡ�ü�ID
//define ("PAYMENT_DAIBIKI_ID",1);		// ������
//define ("PAYMENT_GINFURI_ID", 2);		// ��Կ���
//define ("PAYMENT_KAKITOME_ID", 3);		// �����α
//define ("PAYMENT_LOAN_ID", 5);			// ����åԥ󥰥���
define ("PAYMENT_CREDIT_ID",1);			// ���쥸�åȥ�����
define ("PAYMENT_CONVENIENCE_ID", 2);	// ����ӥ˷��

define("LARGE_IMAGE_WIDTH",  500);						// ���������
define("LARGE_IMAGE_HEIGHT", 500);						// ���������
define("SMALL_IMAGE_WIDTH",  130);						// ����������
define("SMALL_IMAGE_HEIGHT", 130);						// ����������
define("NORMAL_IMAGE_WIDTH",  260);						// �̾������
define("NORMAL_IMAGE_HEIGHT", 260);						// �̾������
define("NORMAL_SUBIMAGE_WIDTH", 200);					// �̾掠�ֲ�����
define("NORMAL_SUBIMAGE_HEIGHT", 200);					// �̾掠�ֲ�����
define("LARGE_SUBIMAGE_WIDTH", 500);					// ���祵�ֲ�����
define("LARGE_SUBIMAGE_HEIGHT", 500);					// ���祵�ֲ�����
define("DISP_IMAGE_WIDTH",  65);						// ����ɽ��������
define("DISP_IMAGE_HEIGHT", 65);						// ����ɽ��������
define("OTHER_IMAGE1_WIDTH", 500);						// ����¾�β���1
define("OTHER_IMAGE1_HEIGHT", 500);						// ����¾�β���1
define("HTMLMAIL_IMAGE_WIDTH",  110);					// HTML�᡼��ƥ�ץ졼�ȥ᡼��ô��������
define("HTMLMAIL_IMAGE_HEIGHT", 120);					// HTML�᡼��ƥ�ץ졼�ȥ᡼��ô��������

define("IMAGE_SIZE", 1000);								// ��������������(KB)
define("CSV_SIZE", 2000);								// CSV����������(KB)
define("CSV_LINE_MAX", 10000);							// CSV���åץ���1�Ԥ�����κ���ʸ����
define("PDF_SIZE", 5000);								// PDF����������(KB):���ʾܺ٥ե�������
define("FILE_SIZE", 10000);								// �ե�����������̥��å�����(KB)
define("TEMPLATE_SIZE", 10000);							// ���åפǤ���ƥ�ץ졼�ȥե���������(KB)
define("LEVEL_MAX", 5);									// ���ƥ���κ��糬��
define("CATEGORY_MAX", 1000);							// ���祫�ƥ�����Ͽ��

/* ɽ����Ϣ */
define ("ADMIN_TITLE", "EC�����ȴ����ڡ���");			// �����ڡ��������ȥ�
define ("SELECT_RGB", "#ffffdf");						// �Խ�����Ĵɽ����
define ("DISABLED_RGB", "#C9C9C9");						// ���Ϲ���̵������ɽ����
define ("ERR_COLOR", "#ffe8e8");						// ���顼��ɽ����
define ("CATEGORY_HEAD", ">");							// �ƥ��ƥ���ɽ��ʸ��
define ("START_BIRTH_YEAR", 1901);						// ��ǯ�������򳫻�ǯ
// ����̾��
define("NORMAL_PRICE_TITLE","�̾����");
define("SALE_PRICE_TITLE","�������");

/* �����ƥ�ѥ� */
define ("LOG_PATH", DATA_PATH . "logs/site.log");							// ���ե�����
define ("DB_ERR_LOG_PATH", DATA_PATH . "logs/db_err.log");                  // ���ե�����(DB���顼��)
define ("DEBUG_LOG_PATH", DATA_PATH . "logs/debug.log");                    // ���ե�����(DEBUG��)
define ("CUSTOMER_LOG_PATH", DATA_PATH . "logs/customer.log");				// ��������� ���ե�����
define ("TEMPLATE_ADMIN_DIR", DATA_PATH . "Smarty/templates/admin");		// SMARTY�ƥ�ץ졼��
define ("TEMPLATE_DIR", DATA_PATH . "Smarty/templates");					// SMARTY�ƥ�ץ졼��
define ("COMPILE_ADMIN_DIR", DATA_PATH . "Smarty/templates_c/admin");		// SMARTY����ѥ���
define ("COMPILE_DIR", DATA_PATH . "Smarty/templates_c");					// SMARTY����ѥ���

define ("TEMPLATE_FTP_DIR", USER_PATH . "templates/");                      // SMARTY�ƥ�ץ졼��(FTP����)
define ("COMPILE_FTP_DIR", DATA_PATH . "Smarty/templates_c/user_data/");	// SMARTY����ѥ���

define ("IMAGE_TEMP_DIR", HTML_PATH . "upload/temp_image/");				// ���������¸
define ("IMAGE_SAVE_DIR", HTML_PATH . "upload/save_image/");				// ������¸��
define ("IMAGE_TEMP_URL", URL_DIR . "upload/temp_image/");					// ���������¸URL
define ("IMAGE_SAVE_URL", URL_DIR . "upload/save_image/");					// ������¸��URL
define ("IMAGE_TEMP_URL_RSS", SITE_URL . "upload/temp_image/");				// RSS�Ѳ��������¸URL
define ("IMAGE_SAVE_URL_RSS", SITE_URL . "upload/save_image/");				// RSS�Ѳ�����¸��URL
define ("FTP_IMAGE_SAVE_DIR", "./html" . URL_DIR . "upload/save_image/");   // FTP������¸(���Хѥ�)
define ("FTP_IMAGE_TEMP_DIR", "./html" . URL_DIR . "upload/temp_image/");   // FTP���������¸(���Хѥ�)
define ("CSV_TEMP_DIR", HTML_PATH . "upload/csv/");							// ���󥳡���CSV�ΰ����¸��
define ("NO_IMAGE_URL", URL_DIR . "misc/blank.gif");						// �������ʤ�����ɽ��
define ("NO_IMAGE_DIR", HTML_PATH . "misc/blank.gif");						// �������ʤ�����ɽ��

/* URL�ѥ� */
define ("URL_SYSTEM_TOP", URL_DIR . "admin/system/index.php");			// �����ƥ�����ȥå�
define ("URL_CLASS_REGIST", URL_DIR . "admin/products/class.php");		// ������Ͽ
define ("URL_INPUT_ZIP", URL_DIR . "input_zip.php");					// ͹���ֹ�����
define ("URL_DELIVERY_TOP", URL_DIR . "admin/basis/delivery.php");		// �����ȼ���Ͽ
define ("URL_PAYMENT_TOP", URL_DIR . "admin/basis/payment.php");		// ��ʧ����ˡ��Ͽ
define ("URL_CONTROL_TOP", URL_DIR . "admin/basis/control.php");		// �����ȴ���������Ͽ
define ("URL_HOME", URL_DIR . "admin/home.php");						// �ۡ���
define ("URL_LOGIN", URL_DIR . "admin/index.php");						// ������ڡ���
define ("URL_SEARCH_TOP", URL_DIR . "admin/products/index.php");		// ���ʸ����ڡ���
define ("URL_ORDER_EDIT", URL_DIR . "admin/order/edit.php");			// ��ʸ�Խ��ڡ���
define ("URL_SEARCH_ORDER", URL_DIR . "admin/order/index.php");			// ��ʸ�Խ��ڡ���
define ("URL_ORDER_MAIL", URL_DIR . "admin/order/mail.php");			// ��ʸ�Խ��ڡ���
define ("URL_LOGOUT", URL_DIR . "admin/logout.php");					// �������ȥڡ���
define ("URL_SYSTEM_CSV", URL_DIR . "admin/system/member_csv.php");		// �����ƥ����CSV���ϥڡ���
define ("URL_ADMIN_CSS", URL_DIR . "admin/css/");						// �����ڡ�����CSS�ݴɥǥ��쥯�ȥ�
define ("URL_CAMPAIGN_TOP", URL_DIR . "admin/contents/campaign.php");	// �����ڡ�����Ͽ�ڡ���
define ("URL_CAMPAIGN_DESIGN", URL_DIR . "admin/contents/campaign_design.php");		// �����ڡ���ǥ���������ڡ���

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
define ("ID_MAX_LEN", 15);		// ID���ѥ���ɤ�ʸ��������
define ("ID_MIN_LEN", 4);		// ID���ѥ���ɤ�ʸ��������
define ("PRICE_LEN", 8);		// ��۷��
define ("PERCENTAGE_LEN", 3);	// Ψ���
define ("AMOUNT_LEN", 6);		// �߸˿����������¿�
define ("ZIP01_LEN", 3);		// ͹���ֹ�1
define ("ZIP02_LEN", 4);		// ͹���ֹ�2
define ("TEL_ITEM_LEN", 6);		// �����ֹ�ƹ�������
define ("TEL_LEN", 12);			// �����ֹ����
define ("PASSWORD_LEN1", 4);	// �ѥ����1
define ("PASSWORD_LEN2", 10);	// �ѥ����2
define ("INT_LEN", 8);			// ���������ѷ��(INT)
define ("CREDIT_NO_LEN", 4);		// ���쥸�åȥ����ɤ�ʸ����
define ("SEARCH_CATEGORY_LEN", 18);	// �������ƥ������ɽ��ʸ����(byte)
define ("FILE_NAME_LEN", 10);		// �ե�����̾ɽ��ʸ����

/** �ե��ȥڡ��� **/

/* �����ƥ��Ϣ */
define ("SALE_LIMIT_MAX", 10);		// �������¤ʤ��ξ��κ�������Ŀ�
define ("SITE_TITLE", "�ţ�-�ãգ£�  �ƥ��ȥ�����");	// HTML�����ȥ�
define ("COOKIE_EXPIRE", 365);		// ���å����ݻ�����(��)
define ("FREE_DIAL", "");

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
define ("EXTRACT_ERROR", 14);		// �ե�������२�顼
define ("FTP_DOWNLOAD_ERROR", 15);	// FTP��������ɥ��顼
define ("FTP_LOGIN_ERROR", 16);		// FTP�����󥨥顼
define ("FTP_CONNECT_ERROR", 17);	// FTP��³���顼
define ("CREATE_DB_ERROR", 18);		// DB�������顼
define ("DB_IMPORT_ERROR", 19);		// DB����ݡ��ȥ��顼
define ("FILE_NOT_FOUND", 20);		// ����ե�����¸�ߥ��顼
define ("WRITE_FILE_ERROR", 21);	// �񤭹��ߥ��顼
define ("FREE_ERROR_MSG", 999);		// �ե꡼��å�����

/* ɽ����Ϣ */
define ("SEPA_CATNAVI", " > ");	// ���ƥ�����ڤ�ʸ��
define ("SEPA_CATLIST", " | ");	// ���ƥ�����ڤ�ʸ��

/* URL */
define ("URL_SHOP_TOP", SSL_URL . "shopping/index.php");						// �����������
define ("URL_ENTRY_TOP", SSL_URL . "entry/index.php");	 						// �����Ͽ�ڡ���TOP
define ("URL_SITE_TOP", URL_DIR . "index.php");									// �����ȥȥå�
define ("URL_CART_TOP", URL_DIR . "cart/index.php");							// �����ȥȥå�
define ("URL_DELIV_TOP", URL_DIR . "shopping/deliv.php");						// ������������ 
define ("URL_MYPAGE_TOP", SSL_URL . "mypage/login.php");						// My�ڡ����ȥå�
define ("URL_SHOP_CONFIRM", URL_DIR . "shopping/confirm.php");					// ������ǧ�ڡ���
define ("URL_SHOP_PAYMENT", URL_DIR . "shopping/payment.php");					// ����ʧ����ˡ����ڡ���
define ("URL_SHOP_COMPLETE", URL_DIR . "shopping/complete.php");				// ������λ����
define ("URL_SHOP_CREDIT", URL_DIR . "shopping/card.php");						// �����ɷ�Ѳ���
define ("URL_SHOP_LOAN", URL_DIR . "shopping/loan.php");						// �����Ѳ���
define ("URL_SHOP_CONVENIENCE", URL_DIR . "shopping/convenience.php");			// ����ӥ˷�Ѳ���
define ("URL_SHOP_MODULE", URL_DIR . "shopping/load_payment_module.php");		// �⥸�塼���ɲ��Ѳ���
define ("URL_PRODUCTS_TOP", URL_DIR . "products/top.php");						// ���ʥȥå�
define ("LIST_P_HTML", URL_DIR . "products/list-p");							// ���ʰ���(HTML����)
define ("LIST_C_HTML", URL_DIR . "products/list.php?mode=search&category_id=");	// ���ʰ���(HTML����)
define ("DETAIL_P_HTML", URL_DIR . "products/detail.php?product_id=");			// ���ʾܺ�(HTML����)
define ("MYPAGE_DELIVADDR_URL", URL_DIR . "mypage/delivery.php");				// �ޥ��ڡ������Ϥ���URL

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
//$arrAUTHORITY[1] = "����";
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
$arrSTATUS_IMAGE[1] = URL_DIR . "img/right_product/icon01.gif";
$arrSTATUS_IMAGE[2] = URL_DIR . "img/right_product/icon02.gif";
$arrSTATUS_IMAGE[3] = URL_DIR . "img/right_product/icon03.gif";
$arrSTATUS_IMAGE[4] = URL_DIR . "img/right_product/icon04.gif";
$arrSTATUS_IMAGE[5] = URL_DIR . "img/right_product/icon05.gif";

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
	 1 => "PC����ʸ���ե᡼��"
	,2 => "PC����ʸ����󥻥���ե᡼��"
	,3 => "PC�����󤻳�ǧ�᡼��"
	,4 => "���ӡ���ʸ���ե᡼��"
	,5 => "���ӡ���ʸ����󥻥���ե᡼��"
	,6 => "���ӡ����󤻳�ǧ�᡼��"
);

// �ƥƥ�ץ졼�ȤΥѥ�
$arrMAILTPLPATH = array(
	0 => "mail_templates/order_mail.tpl",
    1 => "mobile/mail_templates/order_mail.tpl"
);

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
					1 => "��̳��",
					2 => "���󥵥륿���",
					3 => "����ԥ塼����Ϣ���ѿ�",
					4 => "����ԥ塼����Ϣ�ʳ��ε��ѿ�",
					5 => "��ͻ�ط�",
					6 => "���",
					7 => "�۸��",
					8 => "��̳���ͻ�����̳",
					9 => "�Ķȡ�����",
					10 => "���桦��ȯ",
					11 => "��������",
					12 => "��衦�ޡ����ƥ���",
					13 => "�ǥ�����ط�",
					14 => "��ҷбġ����",
					15 => "���ǡ��ޥ����ߴط�",
					16 => "�������ե꡼����",
					17 => "����",
					18 => "����¾"
				);

/* �ѥ���ɤ��������� */
$arrReminder = array(
						1 => "�Ƥε����ϡ�",
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

/*���᡼�륢�ɥ쥹���̡�*/
define ("MAIL_TYPE_PC",1);
define ("MAIL_TYPE_MOBILE",2);
$arrMailType = array(
					MAIL_TYPE_PC => "�ѥ������ѥ᡼�륢�ɥ쥹",
					MAIL_TYPE_MOBILE => "�����ѥ᡼�륢�ɥ쥹"
				);	

/*  ���ӥɥᥤ����ꡡ*/
$arrDOMAIN = array(
                        1 => "PC�ɥᥤ��",
                        2 => "���ӥɥᥤ��"
                    );
				

$arrDomainType = array(
                      1 => "@docomo.ne.jp",
                      2 => "@ezweb.ne.jp",
                      3 => "@softbank.ne.jp",
                      4 => "@t.vodafone.ne.jp",
                      5 => "@d.vodafone.ne.jp",
                      6 => "@h.vodafone.ne.jp",
                      7 => "@c.vodafone.ne.jp",
                      8 => "@k.vodafone.ne.jp",
                      9 => "@r.vodafone.ne.jp",
                      10 => "@n.vodafone.ne.jp",
                      11 => "@s.vodafone.ne.jp",
                      12 => "@q.vodafone.ne.jp",
                      13 => "@pdx.ne.jp",
                      14 => "@di.pdx.ne.jp",
                      15 => "@dj.pdx.ne.jp",
                      16 => "@dk.pdx.ne.jp",
                      17 => "@wm.pdx.ne.jp"
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
define ("ORDER_NEW",1);	 		// ������ʸ
define ("ORDER_PAY_WAIT",2);	// �����Ԥ�
define ("ORDER_PRE_END",6);		// ����Ѥ�
define ("ORDER_CANCEL",3);		// ����󥻥�
define ("ORDER_BACK_ORDER",4);	// ������
define ("ORDER_DELIV",5);		// ȯ���Ѥ�

/* �����ơ����� */
$arrORDERSTATUS = array(
	ORDER_NEW        => "��������",
	ORDER_PAY_WAIT   => "�����Ԥ�",
	ORDER_PRE_END    => "����Ѥ�",
	ORDER_CANCEL     => "����󥻥�",
	ORDER_BACK_ORDER => "������",
	ORDER_DELIV      => "ȯ���Ѥ�"
);

// �����ơ������ѹ��κݤ˥ݥ��������û����륹�ơ������ֹ��ȯ���Ѥߡ�
define("ODERSTATUS_COMMIT", ORDER_DELIV);

/* ���ʼ��̤�ɽ���� */
$arrPRODUCTSTATUS_COLOR = array(
	1 => "#FFFFFF",
	2 => "#C9C9C9",
	3 => "#DDE6F2"
);

$arrORDERSTATUS_COLOR = array(
	1 => "#FFFFFF",
	2 => "#FFDE9B",
	3 => "#C9C9C9",
	4 => "#FFD9D9",
	5 => "#BFDFFF",
	6 => "#FFFFAB"
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

// �֥�å�����
$arrTarget = array(
	1 => "LeftNavi",
	2 => "MainHead",
	3 => "RightNavi",
	4 => "MainFoot",
	5 => "Unused"
);

/*--------- ���ȥ�å��Хå��� ---------*/

define ("TRACKBACK_STATUS_VIEW", 1);		// ɽ��
define ("TRACKBACK_STATUS_NOT_VIEW", 2);	// ��ɽ��
define ("TRACKBACK_STATUS_SPAM", 3);		// ���ѥ�

define ("TRACKBACK_VIEW_MAX", 10);			// �ե��Ⱥ���ɽ����
define ("TRACKBACK_TO_URL", SITE_URL . "tb/index.php?pid=");	// �ȥ�å��Хå���URL

// ����
$arrTrackBackStatus = array(
	1 => "ɽ��",
	2 => "��ɽ��",
	3 => "���ѥ�"
);

/*--------- �������ȴ����� ---------*/

define ("SITE_CONTROL_TRACKBACK", 1);		// �ȥ�å��Хå�
define ("SITE_CONTROL_AFFILIATE", 2);		// ���ե��ꥨ����

// �ȥ�å��Хå�
$arrSiteControlTrackBack = array(
	1 => "ͭ��",
	2 => "̵��"
);

// ���ե��ꥨ����
$arrSiteControlAffiliate = array(
	1 => "ͭ��",
	2 => "̵��"
);

/*--------- ��View������ ---------*/

// View��Where���ִ���
$arrViewWhere = array(
	"&&crscls_where&&" => "",
	"&&crsprdcls_where&&" =>"",
	"&&noncls_where&&" => "",
	"&&allcls_where&&" => "",
	"&&allclsdtl_where&&" => "",
	"&&prdcls_where&&" => "",
	"&&catcnt_where&&" => ""
);

// View�Ѵ���(MySQL�б�)

$arrView = array(
	"vw_cross_class" => '
		(SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
		FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ) ',

	"vw_cross_products_class" =>'
		(SELECT T1.class_id1, T1.class_id2, T1.classcategory_id1, T1.classcategory_id2, T2.product_id,
		T1.name1, T1.name2, T2.product_code, T2.stock, T2.price01, T2.price02, T1.rank1, T1.rank2
		FROM (SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
		FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ) AS T1 LEFT JOIN dtb_products_class AS T2 
		ON T1.classcategory_id1 = T2.classcategory_id1 AND T1.classcategory_id2 = T2.classcategory_id2) ',

	"vw_products_allclass" => '
		(SELECT
        product_id,
        product_code_min,
        product_code_max,
        price01_min,
        price01_max,
        price02_min,
        price02_max,
        stock_min,
        stock_max,
        stock_unlimited_min,
        stock_unlimited_max,
        del_flg,
        status,
        name,
        comment1,
        comment2,
        comment3,
        rank,
        main_list_comment,
        main_image,
        main_list_image,
        product_flag,
        deliv_date_id,
        sale_limit,
        point_rate,
        sale_unlimited,
        create_date,
        deliv_fee
        ,(SELECT rank AS category_rank FROM dtb_category AS T4 WHERE T1.category_id = T4.category_id) as category_rank
        ,(SELECT category_id AS sub_category_id FROM dtb_category T4 WHERE T1.category_id = T4.category_id) as category_id
    FROM
        dtb_products AS T1 RIGHT JOIN (SELECT product_id AS product_id_sub, MIN(product_code) AS product_code_min, MAX(product_code) AS product_code_max, MIN(price01) AS price01_min, MAX(price01) AS price01_max, MIN(price02) AS price02_min, MAX(price02) AS price02_max, MIN(stock) AS stock_min, MAX(stock) AS stock_max, MIN(stock_unlimited) AS stock_unlimited_min, MAX(stock_unlimited) AS stock_unlimited_max FROM dtb_products_class GROUP BY product_id) AS T2 ON T1.product_id = T2.product_id_sub
    ) ',

	"vw_products_allclass_detail" => '
		(SELECT product_id,price01_min,price01_max,price02_min,price02_max,stock_min,stock_max,stock_unlimited_min,stock_unlimited_max,
		del_flg,status,name,comment1,comment2,comment3,deliv_fee,main_comment,main_image,main_large_image,
		sub_title1,sub_comment1,sub_image1,sub_large_image1,
		sub_title2,sub_comment2,sub_image2,sub_large_image2,
		sub_title3,sub_comment3,sub_image3,sub_large_image3,
		sub_title4,sub_comment4,sub_image4,sub_large_image4,
		sub_title5,sub_comment5,sub_image5,sub_large_image5,
		product_flag,deliv_date_id,sale_limit,point_rate,sale_unlimited,file1,file2,category_id
		FROM ( SELECT * FROM (dtb_products AS T1 RIGHT JOIN 
		(SELECT 
		product_id AS product_id_sub,
		MIN(price01) AS price01_min,
		MAX(price01) AS price01_max,
		MIN(price02) AS price02_min,
		MAX(price02) AS price02_max,
		MIN(stock) AS stock_min,
		MAX(stock) AS stock_max,
		MIN(stock_unlimited) AS stock_unlimited_min,
		MAX(stock_unlimited) AS stock_unlimited_max
		FROM dtb_products_class GROUP BY product_id) AS T2
		ON T1.product_id = T2.product_id_sub ) ) AS T3 LEFT JOIN (SELECT rank AS category_rank, category_id AS sub_category_id FROM dtb_category) AS T4
		ON T3.category_id = T4.sub_category_id) ',

	"vw_product_class" => '
		(SELECT * FROM 
		(SELECT T3.product_class_id, T3.product_id AS product_id_sub, classcategory_id1, classcategory_id2, 
		T3.rank AS rank1, T4.rank AS rank2, T3.class_id AS class_id1, T4.class_id AS class_id2,
		stock, price01, price02, stock_unlimited, product_code
		FROM ( SELECT 
		        T1.product_class_id,
		        T1.product_id,
		        classcategory_id1,
		        classcategory_id2,
		        T2.rank,
		        T2.class_id,
		        stock,
		        price01,
		        price02,
		        stock_unlimited,
		        product_code		
		 FROM (dtb_products_class AS T1 LEFT JOIN dtb_classcategory AS T2
		ON T1.classcategory_id1 = T2.classcategory_id))
		AS T3 LEFT JOIN dtb_classcategory AS T4
		ON T3.classcategory_id2 = T4.classcategory_id) AS T5 LEFT JOIN dtb_products AS T6
		ON product_id_sub = T6.product_id) ',

	"vw_category_count" => '
		(SELECT T1.category_id, T1.category_name, T1.parent_category_id, T1.level, T1.rank, T2.product_count
		FROM dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2
		ON T1.category_id = T2.category_id) '
);

$vw_products_nonclass = "
		(SELECT 
		    T1.product_id,
		    T1.name,
		    T1.deliv_fee,
		    T1.sale_limit,
		    T1.sale_unlimited,
		    T1.category_id,
		    T1.rank,
		    T1.status,
		    T1.product_flag,
		    T1.point_rate,
		    T1.comment1,
		    T1.comment2,
		    T1.comment3,
		    T1.comment4,
		    T1.comment5,
		    T1.comment6,
		    T1.file1,
		    T1.file2,
		    T1.file3,
		    T1.file4,
		    T1.file5,
		    T1.file6,
		    T1.main_list_comment,
		    T1.main_list_image,
		    T1.main_comment,
		    T1.main_image,
		    T1.main_large_image,";
            
for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
            $vw_products_nonclass.= "
		    T1.sub_title$cnt,
		    T1.sub_comment$cnt,
		    T1.sub_image$cnt,
            T1.sub_large_image$cnt,";
}

$vw_products_nonclass.= "
		    T1.del_flg,
		    T1.creator_id,
		    T1.create_date,
		    T1.update_date,
		    T1.deliv_date_id,
		    T2.product_id_sub,
		    T2.product_code,
		    T2.price01,
		    T2.price02,
		    T2.stock,
		    T2.stock_unlimited,
		    T2.classcategory_id1,
		    T2.classcategory_id2
		FROM (SELECT * FROM dtb_products &&noncls_where&&) AS T1 LEFT JOIN 
		(SELECT
		product_id AS product_id_sub,
		product_code,
		price01,
		price02,
		stock,
		stock_unlimited,
		classcategory_id1,
		classcategory_id2
		FROM dtb_products_class WHERE classcategory_id1 = 0 AND classcategory_id2 = 0) 
		AS T2
		ON T1.product_id = T2.product_id_sub) ";

$arrView['vw_products_nonclass'] = $vw_products_nonclass;
?>