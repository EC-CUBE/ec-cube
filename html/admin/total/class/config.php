<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *	���� 
*/
// TTF�ե���ȥե�����
define("FONT_PATH", DATA_PATH . "fonts/wlmaru20044.ttf");
define("FONT_SIZE", 8);			// �ե���ȥ�����
define("TITLE_FONT_SIZE", 11);	// �����ȥ�ե���ȥ�����
define("BG_WIDTH", 720);		// �ط���
define("BG_HEIGHT", 400);		// �طʹ⤵
define("LINE_PAD", 5);			// �Դ�
define("TEXT_RATE", 0.75);		// �ե����������(�ºݤ�������/�ե���ȥ�����)

/*
	�ߥ����
*/
define("PIE_LEFT", 200);			// �ߥ���հ���
define("PIE_TOP", 150);				// �ߥ���հ���
define("PIE_WIDTH", 230);			// �ߥ������
define("PIE_HEIGHT", 100);			// �ߥ���չ⤵
define("PIE_THICK", 30);			// �ߥ��������
define("PIE_LABEL_UP", 20);			// �ߥ���դΥ�٥���֤��ˤ�����
define("PIE_SHADE_IMPACT", 0.1);	// �ͤ��礭���ۤɱƤ�Ĺ���ʤ�

/*
	�ޤ��������
*/
define("LINE_Y_SCALE", 10);			// Y�����������
define("LINE_X_SCALE", 10);			// X�����������
define("LINE_LEFT", 60);			// ������հ���
define("LINE_TOP", 50);				// ������հ���
define("LINE_AREA_WIDTH", 600);		// ��������طʤΥ�����
define("LINE_AREA_HEIGHT", 300);	// ��������طʤΥ�����
define("LINE_MARK_SIZE", 6);		// ������եޡ����Υ�����
define("LINE_SCALE_SIZE", 6);		// ��������
define("LINE_XLABEL_MAX", 30);		// X���Υ�٥��ɽ�����¿�
define("LINE_XTITLE_PAD", -5);		// X���Υ����ȥ�ȼ��δֳ�
define("LINE_YTITLE_PAD", 15);		// Y���Υ����ȥ�ȼ��δֳ�

/* 
	�������
*/
define("BAR_PAD", 6);				// ����դ�������δֳ�

/*
	�����ȥ��٥�
*/
define("TITLE_TOP", 10);	// �ط��ȤȤξ���

/*
	����
*/
define("LEGEND_TOP", 10); 	// �ط��ȤȤξ���
define("LEGEND_RIGHT", 10); // �ط��ȤȤα���


/*
	ɽ����
*/
// �����ط�
$ARR_LEGENDBG_COLOR = array(245,245,245);
// ��٥��ط�
$ARR_LABELBG_COLOR = array(255,255,255);
// ����ե��顼
$ARR_GRAPH_RGB = array(
	array(200,50,50),
	array(50,50,200),
	array(50,200,50),
	array(255,255,255),
	array(244,200,200),
	array(200,200,255),
	array(50,200,50),
	array(255,255,255),
	array(244,244,244),
);
// �Ƥο�
$ARR_SHADE_COLOR = array(100,100,100);
// ��ο�
$ARR_FLAME_COLOR = array(0, 0, 0);
// ʸ����
$ARR_TEXT_COLOR = array(0, 0, 0);
// �طʥ��顼
$ARR_BG_COLOR = array(255,255,255);	
// �����ȥ�ʸ����
$ARR_TITLE_COLOR = array(0, 0, 0);
// ����å�����
$ARR_GRID_COLOR = array(200, 200, 200);
// �ޡ����ο�
$ARR_MARK_COLOR = array(130, 130, 255);


?>