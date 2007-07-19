<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.30 2007/07/18 04:02:40 adachi Exp $
 * @link		http://www.lockon.co.jp/
 *
 */

// ǧ�ڳ�ǧ
$objSess = new SC_Session();
sfIsSuccess($objSess);

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage  = MODULE_PATH . 'mdl_ebis_tag/ebis_tag.tpl';
		$this->tpl_subtitle  = 'AD EBiS���������ߵ�ǽ';
        $this->tpl_uniqid    = '';
        
        global $arrEBiSTagCustomerId;
        $this->arrEBiSTagCustomerId = $arrEBiSTagCustomerId;
        global $arrEBiSTagPayment;
        $this->arrEBiSTagPayment = $arrEBiSTagPayment;
        global $arrEBiSTagOptions;
        $this->arrEBiSTagOptions = $arrEBiSTagOptions;
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST�ͤμ���
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'edit':
    // �������ܤ������������å�
    //if (sfIsValidTransition($objSess) == false) {
    //    sfDispError(INVALID_MOVE_ERRORR);
    //}
    
	// ���ϥ��顼Ƚ��
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$arrRet = $objFormParam->getHashArray();
		$sqlval['sub_data'] = serialize($arrRet);
		$objQuery = new SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(EBIS_TAG_MID));
		// javascript�¹�
		$objPage->tpl_onload = "window.close();";
	}
	break;
case 'csv':
    // �������ܤ������������å�
    //if (sfIsValidTransition($objSess) == false) {
    //    sfDispError(INVALID_MOVE_ERRORR);
    //}
    $csv = lgGetCsvData();
    sfCSVDownload($csv, 'ebis_tag_');
    exit;
default:
	$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(EBIS_TAG_MID));
	$arrSubData = unserialize($arrRet[0]['sub_data']);
	$objFormParam->setParam($arrSubData);
    
    // ��ˡ���ID��������
    // $objPage->tpl_uniqid = $objSess->getUniqId();
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display($objPage->tpl_mainpage);		//�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam($objFormParam) {
    $objFormParam->addParam("������URL", "login_url", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
    $objFormParam->addParam("EBiS����", "cid", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    
    $objFormParam->addParam("�ܵ�ID", "m1id", INT_LEN, "", array("MAX_LENGTH_CHECK", 'NUM_CHECK'));
    $objFormParam->addParam("�������", "a1id", INT_LEN, "", array("MAX_LENGTH_CHECK", 'NUM_CHECK'));
    
    for ($i = 1; $i <= EBiS_TAG_OPTIONS_MAX; $i++) {
        $title = "Ǥ�չ���$i";
        $name  = 'o' . $i . 'id';
        $objFormParam->addParam(
            $title, $name, INT_LEN, "",
            array("MAX_LENGTH_CHECK", "NUM_CHECK")
        );
    }
	return $objFormParam;
}

/**
 * ���ƥ���ʸ��������(���ƥ���䥫�ƥ���䥫�ƥ����)
 * 
 * @param int $category_id ���ƥ���ID
 * @param str $sep ���ƥ���ζ��ڤ�ʸ��
 * return ���ƥ���ʸ����(���ƥ���䥫�ƥ���䥫�ƥ����)
 */ 
function lfGetCategoriesStr($category_id, $sep = ' > ') {
    $tbl_category = 'dtb_category';
    
    // �ƥ��ƥ���ID������
    $arrParentsCatId = sfGetParents(null, $tbl_category, 'parent_category_id', 'category_id', $category_id);
    
    // WHERE�����
    $where = str_repeat('category_id = ? OR ' , count($arrParentsCatId));
    $where = preg_replace('/OR $/', '', $where);

    // ���ƥ���̾�����
    $objQuery = new SC_Query();
    $arrRet   = $objQuery->select('category_name', $tbl_category, $where, $arrParentsCatId);
    
    // ���ƥ���䥫�ƥ���䥫�ƥ���...����
    $categories_str = '';
    foreach($arrRet as $category) {
        $categories_str .= $category['category_name'] . $sep;
    }
    
    return $categories_str;
}

/** CSV���� **/
function lgGetCsvData() {
    $csv  = '"�ڡ���ID","�����ȥ�","URL"' . "\n";
    $csv .= lfGetDetailPageCSV();
    $csv .= lfGetListPageCSV();
    $csv .= lfGetFrontPageCSV();
    return $csv;
}

/** ���ʾܺ٥ڡ�����CSV����� **/
function lfGetDetailPageCSV() {
    $table    = 'dtb_products';
    $colmuns  = 'product_id, name, category_id';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $index => $product) {
        $id  = $product['product_id'];
        $url = SITE_URL . 'products/detail.php?product_id=';
        $title = lfGetCategoriesStr($product['category_id']) . $product['name'];
        
        $arrCSV[$index]['page_id']    = 'detail-p' . $id;
        $arrCSV[$index]['page_title'] = $title;
        $arrCSV[$index]['url']        = $url . $id;
    }
    
    return lfCreateCSV($arrCSV);
}

/** ���ʰ����ڡ�����CSV����� **/
function lfGetListPageCSV() {
    $table    = 'dtb_category';
    $colmuns  = 'category_id, category_name';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $index => $category) {
        $id  = $category['category_id'];
        $url = SITE_URL . 'products/list.php?category_id=';
        $title = $category['category_name'];
        
        $arrCSV[$index]['page_id']    = 'list-c' . $id;
        $arrCSV[$index]['page_title'] = $title;
        $arrCSV[$index]['url']        = $url . $id;
    }
    return lfCreateCSV($arrCSV);
}

function lfCreateCSV ($arrCSV) {
    $csv_str = '';
    $max = count($arrCSV);
    for ($i=0; $i < $max; $i++) {
        foreach (array('page_id', 'page_title', 'url') as $key) {
            $arrCSV[$i][$key] = sprintf(
                '"%s"',
                str_replace('"', '""', $arrCSV[$i][$key])
            );
        }
        $csv_str .= join(',', $arrCSV[$i]) . "\n";
    }
    
    return $csv_str;
}

/** ����¾�ڡ�����CSV����� **/
function lfGetFrontPageCSV() {
    // �����ɲäκݤϲ���Ϣ��������ɲá�
    // page_title,url��Ǥ�ա��ʤ�����page_id���鼫ư���������
    // 'page_id' => 'top', 'page_title' => '�ȥå�' , 'url' => 'index.php'
    $arrList = array(
        array('page_id' => 'top', 'page_title' => '�ȥå�' , 'url' => 'index.php'),
        array('page_id' => 'abouts_index', 'page_title' => '�������ȤˤĤ���'),
        array('page_id' => 'cart_index', 'page_title' => '�㤤ʪ�����ʥȥåס�'),
        array('page_id' => 'contact_index', 'page_title' => '���䤤��碌�����ϡ�'),
        array('page_id' => 'contact_confirm', 'page_title' => '���䤤��碌�ʳ�ǧ��', 'url' => 'contact/index.php'),
        array('page_id' => 'contact_complete', 'page_title' => '���䤤��碌�ʴ�λ��'),
        array('page_id' => 'order_index', 'page_title' => '���������ϡ�'),
        array('page_id' => 'entry_kiyaku', 'page_title' => '�����ѵ���'),
        array('page_id' => 'entry_index', 'page_title' => '�����Ͽ������'),
        array('page_id' => 'entry_confirm', 'page_title' => '�����Ͽ�ʳ�ǧ��', 'url' => 'entry/index.php'),
        array('page_id' => 'regist_complete', 'page_title' => '�����Ͽ�ʴ�λ��', 'url' => 'entry/complete.php'),
        array('page_id' => 'shopping_deliv', 'page_title' => '�����ʤ��Ϥ�������'),
        array('page_id' => 'shopping_payment', 'page_title' => '�����ʤ���ʧ����ˡ�����'),
        array('page_id' => 'shopping_confirm', 'page_title' => '�����ʳ�ǧ��'),
        array('page_id' => 'thanks', 'page_title' => '�����ʴ�λ)', 'url' => 'shopping/complete.php'),
        array('page_id' => 'mypage_index', 'page_title' => 'MY�ڡ����ʥȥåס�'),
        array('page_id' => 'mypage_change', 'page_title' => 'MY�ڡ��� > �����Ͽ�����ѹ������ϡ�'),
        array('page_id' => 'mypage_change_confirm', 'page_title' => 'MY�ڡ��� > �����Ͽ�����ѹ��ʳ�ǧ��', 'url' => 'mypage/change.php'),
        array('page_id' => 'mypage_change_complete', 'page_title' => 'MY�ڡ��� > �����Ͽ�����ѹ��ʴ�λ��', 'url' => 'mypage/change_complete.php'),
    );
    
    foreach ($arrList as $key => $list) {
        if ( empty($arrList[$key]['page_title']) ) {
            $arrList[$key]['page_title'] = $arrList[$key]['page_id'];
        }
        
        if ( empty($arrList[$key]['url']) ) {
            $url = SITE_URL . str_replace('_', '/', $arrList[$key]['page_id']);
            $arrList[$key]['url'] = $url . '.php';
        } else {
            $arrList[$key]['url'] = SITE_URL . $arrList[$key]['url'];
        }
    }
    
    return lfCreateCSV($arrList);
}

?>