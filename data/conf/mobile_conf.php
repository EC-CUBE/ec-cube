<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 *
 * ��Х��륵���ȶ�ͭ����ե�����
 */

//--------------------------------------------------------------------------------------------------------

define('MOBILE_TEMPLATE_DIR', DATA_PATH . 'Smarty/templates/mobile');	// SMARTY�ƥ�ץ졼��
define('MOBILE_COMPILE_DIR', DATA_PATH . 'Smarty/templates_c/mobile');	// SMARTY����ѥ���

/**
 * ��Х��륵���ȤǤ��뤳�Ȥ�ɽ�����
 */
define('MOBILE_SITE', true);

/**
 * ���å�����¸³���� (��)
 */
define('MOBILE_SESSION_LIFETIME', 1800);

/**
 * ���᡼�뵡ǽ����Ѥ��뤫�ɤ���
 */
define('MOBILE_USE_KARA_MAIL', false);

/**
 * ���᡼������դ����ɥ쥹�Υ桼����̾��ʬ
 */
define('MOBILE_KARA_MAIL_ADDRESS_USER', 'eccube');

/**
 * ���᡼������դ����ɥ쥹�Υ桼����̾�ȥ��ޥ�ɤδ֤ζ��ڤ�ʸ��
 * qmail �ξ��� '-'
 */
define('MOBILE_KARA_MAIL_ADDRESS_DELIMITER', '+');

/**
 * ���᡼������դ����ɥ쥹�Υɥᥤ����ʬ
 */
define('MOBILE_KARA_MAIL_ADDRESS_DOMAIN', 'mobile.ec-cube.net');

/**
 * ���ӤΥ᡼�륢�ɥ쥹�ǤϤʤ��������Ӥ��Ȥߤʤ��ɥᥤ��Υꥹ��
 * Ǥ�դο��Ρ�,�ס� �פǶ��ڤ롣
 */
define('MOBILE_ADDITIONAL_MAIL_DOMAINS', 'rebelt.co.jp, lockon.co.jp');

/**
 * �������ø����Ѵ�������¸�ǥ��쥯�ȥ�
 */
define('MOBILE_IMAGE_DIR', HTML_PATH . 'upload/mobile_image');
define('MOBILE_IMAGE_URL', URL_DIR . 'upload/mobile_image');

/* URL */
define ('MOBILE_URL_SITE_TOP', MOBILE_URL_DIR . 'index.php');								// �����ȥȥå�
define ("MOBILE_URL_CART_TOP", MOBILE_URL_DIR . "cart/index.php");							// �����ȥȥå�
define ("MOBILE_URL_SHOP_TOP", MOBILE_SSL_URL . "shopping/index.php");						// �����������
define ("MOBILE_URL_SHOP_CONFIRM", MOBILE_URL_DIR . "shopping/confirm.php");				// ������ǧ�ڡ���
define ("MOBILE_URL_SHOP_PAYMENT", MOBILE_URL_DIR . "shopping/payment.php");				// ����ʧ����ˡ����ڡ���
define ("MOBILE_DETAIL_P_HTML", MOBILE_URL_DIR . "products/detail.php?product_id=");		// ���ʾܺ�(HTML����)
define ("MOBILE_URL_SHOP_COMPLETE", MOBILE_URL_DIR . "shopping/complete.php");				// ������λ����
define ("MOBILE_URL_SHOP_MODULE", MOBILE_URL_DIR . "shopping/load_payment_module.php");		// �⥸�塼���ɲ��Ѳ���

//--------------------------------------------------------------------------------------------------------

?>