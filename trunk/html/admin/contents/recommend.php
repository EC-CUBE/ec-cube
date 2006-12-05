<?
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 require_once("../require.php");

class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/recomend.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "recommend";
		$this->tpl_subtitle = '�����������';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$arrRegistColumn = array(
 							 array(  "column" => "product_id", "convert" => "n" ),
							 array(  "column" => "category_id", "convert" => "n" ),
							 array(  "column" => "rank", "convert" => "n" ),
							 array(  "column" => "title", "convert" => "aKV" ),
							 array(  "column" => "comment", "convert" => "aKV" ),
						);

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

//������Ͽ����ɽ��
$objPage->tpl_disp_max = RECOMMEND_NUM;

// ��Ͽ��
if ( $_POST['mode'] == 'regist' ){
		
	// ����ʸ���ζ����Ѵ�
	$objPage->arrForm = $_POST;	
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	// ���顼�����å�
	$objPage->arrErr[$objPage->arrForm['rank']] = lfErrorCheck();
	if ( ! $objPage->arrErr[$objPage->arrForm['rank']]) {
		// �Ť��Τ�ä�
		$sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
		$conn->query($sql, array($objPage->arrForm['category_id'] ,$objPage->arrForm['rank']));
	
		// �ģ���Ͽ
		$objPage->arrForm['creator_id'] = $_SESSION['member_id'];
		$objPage->arrForm['update_date'] = "NOW()";
		$objPage->arrForm['create_date'] = "NOW()";
		
		$objQuery = new SC_Query();
		$objQuery->insert("dtb_best_products", $objPage->arrForm );
//		$conn->autoExecute("dtb_best_products", $objPage->arrForm );
	}	

} elseif ( $_POST['mode'] == 'delete' ){
// �����

	$sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
	$conn->query($sql, array($_POST['category_id'] ,$_POST['rank']));
	
}

// ���ƥ���ID���� ̵���Ȥ��ϥȥåץڡ���
if ( sfCheckNumLength($_POST['category_id']) ){
	$objPage->category_id = $_POST['category_id'];
} else {
	$objPage->category_id = 0;
}

// ������Ͽ����Ƥ������Ƥ��������
$sql = "SELECT B.name, B.main_list_image, A.* FROM dtb_best_products as A INNER JOIN dtb_products as B USING (product_id)
		 WHERE A.del_flg = 0 ORDER BY rank";
$arrItems = $conn->getAll($sql);
foreach( $arrItems as $data ){
	$objPage->arrItems[$data['rank']] = $data;
}

// �����ѹ����ϡ����򤵤줿���ʤ˰��Ū���֤�������
if ( $_POST['mode'] == 'set_item'){
	$sql = "SELECT product_id, name, main_list_image FROM dtb_products WHERE product_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($_POST['product_id']));
	if ( $result ){
		$data = $result[0];
		foreach( $data as $key=>$val){
			$objPage->arrItems[$_POST['rank']][$key] = $val;
		}
		$objPage->arrItems[$_POST['rank']]['rank'] = $_POST['rank'];
	}
	$objPage->checkRank = $_POST['rank'];
}

//�ƥڡ�������
$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;

// ���ƥ������
$objPage->arrCatList = sfGetCategoryList("level = 1");

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {

	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// ʸ���Ѵ�
	$new_array = array();
	foreach ($arrConvList as $key => $val) {
		$new_array[$key] = $array[$key];
		if( strlen($val) > 0) {
			$new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
		}
	}
	return $new_array;
	
}

/* ���ϥ��顼�����å� */
function lfErrorCheck() {
	$objQuery = new SC_Query;
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("���Ф�������", "title", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ᥳ����", "comment", LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

?>