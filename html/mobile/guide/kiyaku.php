<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * ��Х��륵����/�����ѵ���
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'guide/kiyaku.tpl';	// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '�����ѵ���';
	}
}

$objPage = new LC_Page();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// ���ѵ����������롣
lfGetKiyaku(intval(@$_GET['page']), $objPage);

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/**
 * ���ѵ������������ڡ������֥������Ȥ˳�Ǽ���롣
 *
 * @param integer $index ����Υ���ǥå���
 * @param object &$objPage �ڡ������֥�������
 * @return void
 */
function lfGetKiyaku($index, &$objPage)
{
	$objQuery = new SC_Query();
	$objQuery->setorder('rank DESC');
	$arrRet = $objQuery->select('kiyaku_title, kiyaku_text', 'dtb_kiyaku', 'del_flg <> 1');

	$number = count($arrRet);
	if ($number > 0) {
		$last = $number - 1;
	} else {
		$last = 0;
	}

	if ($index < 0) {
		$index = 0;
	} elseif ($index > $last) {
		$index = $last;
	}

	$objPage->tpl_kiyaku_title = @$arrRet[$index]['kiyaku_title'];
	$objPage->tpl_kiyaku_text = @$arrRet[$index]['kiyaku_text'];
	$objPage->tpl_kiyaku_index = $index;
	$objPage->tpl_kiyaku_last_index = $last;
	$objPage->tpl_kiyaku_is_first = $index <= 0;
	$objPage->tpl_kiyaku_is_last = $index >= $last;
}
?>
