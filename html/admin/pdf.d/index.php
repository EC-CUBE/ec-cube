<?php
/*
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * Ģɼ�����⥸�塼��.
 */

// �Ƽ�⥸�塼��ƤӽФ�
require('japanese.php');
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		global $arrPref;
		// �ǥե���Ȥ�����
		$this->pdf_download = 0;			// PDF�Υ�������ɷ�����0:ɽ����1:��������ɡ�
		$this->tpl_title = "����夲���ٽ�";		// �����ȥ�
		$this->tpl_char = "EUC-JP, UTF-8";		// ʸ��������	
		$this->tpl_pdf = "template_nouhin01.pdf";	// �ƥ�ץ졼�ȥե�����
		$this->tpl_dispmode = "real";			// ɽ���⡼��
		$this->arrPref = $arrPref;
		$this->width_cell = array(110.3,12,21.7,24.5);
		$label_cell[] = sjis_conv("����̾ / ���ʥ����� / [ ���� ]");
		$label_cell[] = sjis_conv("����");
		$label_cell[] = sjis_conv("ñ��");
		$label_cell[] = sjis_conv("���(�ǹ�)");
		$this->label_cell = $label_cell;
		$this->arrMessage = array(
			'���Τ��ӤϤ���夲�����������꤬�Ȥ��������ޤ���',
			'���������Ƥˤ�Ǽ�ʤ����Ƥ��������ޤ���',
			'����ǧ���������ޤ��褦�����ꤤ�������ޤ���'
		);
	}
}



// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

$pdf  = new PDF_Japanese();
$conn = new SC_DbConn();
$objPage = new LC_Page();
$objInfo = new SC_SiteInfo();
$arrInfo = $objInfo->data;
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();

// SJIS�ե����
$pdf->AddSJISFont();

//�ڡ����������
$pdf->AliasNbPages();

// �ޡ���������
$pdf->SetMargins(15, 20);

// PDF���ɤ߹���ǥڡ����������
$pageno = $pdf->setSourceFile($objPage->tpl_pdf);

// �ڡ����ֹ���ID�����
$tplidx = $pdf->ImportPage(1);

// �ڡ������ɲáʿ�����
$pdf->AddPage();

//ɽ����Ψ(100%)
$pdf->SetDisplayMode($objPage->tpl_dispmode);

if(sfIsInt($_POST['order_id'])) {
	$objPage->disp_mode = true;
	$order_id = $_POST['order_id'];
}
$objPage->tpl_order_id = $order_id;


// �����ȥ뤬���ꤵ��Ƥ������ѹ�
if($_POST['chohyo_title']) {
	$objPage->tpl_title = $_POST['chohyo_title'];
}

// �������������
if($_POST['download']) {
	$objPage->pdf_download = $_POST['download'];
}

// ��å�����
if($_POST['chohyo_msg1']) {
	$objPage->arrMessage[0] = $_POST['chohyo_msg1'];
}

if($_POST['chohyo_msg2']) {
	$objPage->arrMessage[1] = $_POST['chohyo_msg2'];
}

if($_POST['chohyo_msg3']) {
	$objPage->arrMessage[2] = $_POST['chohyo_msg3'];
}

// ����
if ($_POST['chohyo_etc1']) {
  if($_POST['chohyo_etc1']) {
	$objPage->arrEtc[0] = $_POST['chohyo_etc1'];
  }

  if($_POST['chohyo_etc2']) {
	$objPage->arrEtc[1] = $_POST['chohyo_etc2'];
  }

  if($_POST['chohyo_etc3']) {
	$objPage->arrEtc[2] = $_POST['chohyo_etc3'];
  }
}


// �ƥ�ץ졼�����Ƥΰ��֡�����Ĵ�� ��useTemplate�˰�����Ϳ���ʤ����100%ɽ�����ǥե����
$pdf->useTemplate($tplidx);

/**
 * PDF �񤭹��߳���
 *
 * PDF����� ��$pdf->Text(x��ɸ, y��ɸ, �ƥ�����);
 * �ե���ȤΥ��å� $pdf->SetFont('SJIS', '', 8); ��SJIS(MSPGothic)�ǥե���ȥ�����8
 */

// ����å׾���
$pdf->SetFont('SJIS', 'B', 8);
$pdf->Text(125, 60, sjis_conv($arrInfo['shop_name']));					//����å�̾
$pdf->SetFont('SJIS', '', 8);
$pdf->Text(125, 63, sjis_conv($arrInfo['law_url']));					//URL
$pdf->Text(125, 68, sjis_conv($arrInfo['law_company']));				//���̾
$pdf->Text(125, 71, sjis_conv("�� ".$arrInfo['zip01']." - ".$arrInfo['zip02']));	//͹���ֹ�
$pdf->Text(125, 74, sjis_conv($objPage->arrPref[$arrInfo['pref']].$arrInfo['addr01']));	//��ƻ�ܸ�+����1
$pdf->Text(125, 77, sjis_conv($arrInfo['addr02']));					//����2
$pdf->Text(125, 80, sjis_conv("TEL: ".$arrInfo['tel01']."-".$arrInfo['tel02']."-".$arrInfo['tel03']."��"."FAX: ".$arrInfo['fax01']."-".$arrInfo['fax02']."-".$arrInfo['fax03']));	//TEL��FAX
$pdf->Text(125, 83, sjis_conv("Email: ".$arrInfo['law_email']));			//Email


// ��å�����
$pdf->SetFont('SJIS', '', 8);
$pdf->Text(27, 70, sjis_conv($objPage->arrMessage[0]));  //��å�����1
$pdf->Text(27, 74, sjis_conv($objPage->arrMessage[1]));  //��å�����2
$pdf->Text(27, 78, sjis_conv($objPage->arrMessage[2]));  //��å�����3
$pdf->Text(158, 288, sjis_conv("������: ".$_POST['year']."ǯ".$_POST['month']."��".$_POST['day']."��"));  //������


// DB������������ɤ߹���
lfGetOrderData($order_id);

// �����Ծ���
$pdf->SetFont('SJIS', '', 10);
$pdf->Text(23, 43, sjis_conv("�� ".$objPage->arrDisp['order_zip01']." - ".$objPage->arrDisp['order_zip02']));           //������͹���ֹ�
$pdf->Text(27, 47, sjis_conv($objPage->arrPref[$objPage->arrDisp['order_pref']] . $objPage->arrDisp['order_addr01']));  //��������ƻ�ܸ�+����1
$pdf->Text(27, 51, sjis_conv($objPage->arrDisp['order_addr02']));  							//�����Խ���2
$pdf->SetFont('SJIS', '', 11);
$pdf->Text(27, 59, sjis_conv($objPage->arrDisp['order_name01']."��".$objPage->arrDisp['order_name02']."����"));		//�����Ի�̾

// ���Ϥ������
$pdf->SetFont('SJIS', '', 10);
$pdf->Text(22, 128, sjis_conv("�� ".$objPage->arrDisp['deliv_zip01']." - ".$objPage->arrDisp['deliv_zip02']));		//���Ϥ���͹���ֹ�
$pdf->Text(26, 132, sjis_conv($objPage->arrPref[$objPage->arrDisp['deliv_pref']] . $objPage->arrDisp['deliv_addr01'])); //���Ϥ�����ƻ�ܸ�+����1
$pdf->Text(26, 136, sjis_conv($objPage->arrDisp['deliv_addr02']));							//���Ϥ��轻��2
$pdf->Text(26, 140, sjis_conv($objPage->arrDisp['deliv_name01']."��".$objPage->arrDisp['deliv_name02']."����"));	//���Ϥ����̾

$pdf->Text(144, 121, sjis_conv($objPage->arrDisp['create_date']));    //����ʸ��
$pdf->Text(144, 131, sjis_conv($objPage->arrDisp['order_id']));  //��ʸ�ֹ�

$pdf->SetFont('SJIS', 'B', 15);
$pdf->Cell(0, 10, sjis_conv($objPage->tpl_title), 0, 2, 'C', 0, '');  //ʸ�񥿥��ȥ��Ǽ�ʽ�������
$pdf->Cell(0, 66, '', 0, 2, 'R', 0, '');
$pdf->Cell(5, 0, '', 0, 0, 'R', 0, '');
$pdf->Cell(67, 8, sjis_conv(number_format($objPage->arrDisp['payment_total'])." ��"), 0, 2, 'R', 0, '');
$pdf->Cell(0, 45, '', 0, 2, '', 0, '');

$pdf->SetFont('SJIS', '', 9);

//������
$pdf->Image('logo.png', 124, 46, 60);

$monetary_unit = sjis_conv("��");
$point_unit = sjis_conv("�Ύߎ��ݎ�");

// �������ʾ���
for ($i = 0; $i < count($objPage->arrDisp['quantity']); $i++) {

	// ��������
	$data[0] = $objPage->arrDisp['quantity'][$i];

	// �ǹ���ۡ�ñ����
	$data[1] = sfPreTax($objPage->arrDisp['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']);

	// ���סʾ������
	$data[2] = $data[0] * $data[1];

	$arrOrder[$i][0]  = sjis_conv($objPage->arrDisp['product_name'][$i]." / ");
	$arrOrder[$i][0] .= sjis_conv($objPage->arrDisp['product_code'][$i]." / ");
	if($objPage->arrDisp['classcategory_name1'][$i]) {
		$arrOrder[$i][0] .= sjis_conv(" [ ".$objPage->arrDisp['classcategory_name1'][$i]);
		if($objPage->arrDisp['classcategory_name2'][$i] == "") {
			$arrOrder[$i][0] .= " ]";
		} else {
			$arrOrder[$i][0] .= sjis_conv(" * ".$objPage->arrDisp['classcategory_name2'][$i]." ]");
		}
	}
	$arrOrder[$i][1]  = number_format($data[0]);
	$arrOrder[$i][2]  = number_format($data[1]).$monetary_unit;
	$arrOrder[$i][3]  = number_format($data[2]).$monetary_unit;

}

$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = "";
$arrOrder[$i][3] = "";

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("���ʹ��");
$arrOrder[$i][3] = number_format($objPage->arrDisp['subtotal']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("����");
$arrOrder[$i][3] = number_format($objPage->arrDisp['deliv_fee']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("�����");
$arrOrder[$i][3] = number_format($objPage->arrDisp['charge']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("�Ͱ���");
$arrOrder[$i][3] = "- ".number_format($objPage->arrDisp['use_point'] + $objPage->arrDisp['discount']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("������");
$arrOrder[$i][3] = number_format($objPage->arrDisp['payment_total']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = "";
$arrOrder[$i][3] = "";

// �ݥ����ɽ��
if ($_POST['disp_point'] && $objPage->arrDisp['customer_id']) {
  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("���юΎߎ��ݎ�");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['use_point']).$point_unit;

  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("�û��Ύߎ��ݎ�");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['add_point']).$point_unit;

  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("��ͭ�Ύߎ��ݎ�");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['point']).$point_unit;
}

$pdf->FancyTable($objPage->label_cell, $arrOrder, $objPage->width_cell);

if ($objPage->arrEtc[0]) {
  $pdf->Cell(0, 10, '', 0, 1, 'C', 0, '');
  $pdf->SetFont('SJIS', '', 9);
  $pdf->MultiCell(0, 6, sjis_conv("�� �� �� ��"), 'T', 2, 'L', 0, '');  //����
  $pdf->Ln();
  $pdf->SetFont('SJIS', '', 8);
  $pdf->MultiCell(0, 4, sjis_conv($objPage->arrEtc[0]."\n".$objPage->arrEtc[1]."\n".$objPage->arrEtc[2]), '', 2, 'L', 0, '');  //����
}

// PDF��֥饦��������
if($objPage->pdf_download == 1) {
	$pdf->Output(sjis_conv("nouhinsyo-No".$objPage->tpl_order_id.".pdf"), D);
} else {
	$pdf->Output();
}

// ���Ϥ���PDF�ե�������Ĥ���
$pdf->Close();


//-----------------------------------------------------------------------------------------------------------------------------------
// ʸ��������SJIS�Ѵ� -> japanese.php�ǻ��ѽ����ʸ�������ɤ�SJIS�Τ�
function sjis_conv($conv_str) {
	global $objPage;
	return (mb_convert_encoding($conv_str, "SJIS", $objPage->tpl_char));
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;

	// ���������
	$objFormParam->addParam("��̾��1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾��2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ֹ�1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));

	// �����ʾ���
	$objFormParam->addParam("�Ͱ���", "discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("����", "deliv_fee", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�����", "charge", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("���ѥݥ����", "use_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����ʧ����ˡ", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������ID", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�б�����", "status", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��ã��", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����ʧ��ˡ̾��", "payment_method");
	$objFormParam->addParam("��������", "deliv_time");
	
	// ����ܺپ���
	$objFormParam->addParam("ñ��", "price", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�Ŀ�", "quantity", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("����ID", "product_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate");
	$objFormParam->addParam("���ʥ�����", "product_code");
	$objFormParam->addParam("����̾", "product_name");
	$objFormParam->addParam("����1", "classcategory_id1");
	$objFormParam->addParam("����2", "classcategory_id2");
	$objFormParam->addParam("����̾1", "classcategory_name1");
	$objFormParam->addParam("����̾2", "classcategory_name2");
	$objFormParam->addParam("���", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));

	// DB�ɹ���
	$objFormParam->addParam("����", "subtotal");
	$objFormParam->addParam("���", "total");
	$objFormParam->addParam("��ʧ�����", "payment_total");
	$objFormParam->addParam("�û��ݥ����", "add_point");
	$objFormParam->addParam("���������ݥ����", "birth_point");
	$objFormParam->addParam("�����ǹ��", "tax");
	$objFormParam->addParam("�ǽ��ݻ��ݥ����", "total_point");
	$objFormParam->addParam("�ܵ�ID", "customer_id");
	$objFormParam->addParam("���ߤΥݥ����", "point");
}

// ����ǡ����μ���
function lfGetOrderData($order_id) {
	global $objFormParam;
	global $objPage;
	if(sfIsInt($order_id)) {

		// DB������������ɤ߹���
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
		$objFormParam->setParam($arrRet[0]);
		list($point, $total_point) = sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
		$objFormParam->setValue('total_point', $total_point);
		$objFormParam->setValue('point', $point);
		$arrRet[0]['total_point'] = $total_point;
		$arrRet[0]['point'] = $point;
		$objPage->arrDisp = $arrRet[0];

		// ����ܺ٥ǡ����μ���
		$arrRet = lfGetOrderDetail($order_id);
		$arrRet = sfSwapArray($arrRet);
		$objPage->arrDisp = array_merge($objPage->arrDisp, $arrRet);
		$objFormParam->setParam($arrRet);

		// ����¾��ʧ�������ɽ��
		if($objPage->arrDisp["memo02"] != "") $objPage->arrDisp["payment_info"] = unserialize($objPage->arrDisp["memo02"]);
		if($objPage->arrDisp["memo01"] == PAYMENT_CREDIT_ID){
			$objPage->arrDisp["payment_type"] = "���쥸�åȷ��";
		}elseif($objPage->arrDisp["memo01"] == PAYMENT_CONVENIENCE_ID){
			$objPage->arrDisp["payment_type"] = "����ӥ˷��";
		}else{
			$objPage->arrDisp["payment_type"] = "����ʧ��";
		}
	}
}

// ����ܺ٥ǡ����μ���
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}
?>

