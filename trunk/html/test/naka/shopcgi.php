<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$entry_url = "http://mod-i.ccsware.net/ohayou/EntryTran.php";
$exec_url = "https://mod-i.ccsware.net/ohayou/ExecTran.php";
// �����ֹ�μ���
$order_id = sfGetUniqRandomId();

// Ź�޾��������
$arrData = array(
	'OrderId' => $order_id,		// Ź�ޤ��Ȥ˰�դ���ʸID���������롣
	'TdTenantName' => '',		// 3Dǧ�ڻ�ɽ����Ź��̾
	'TdFlag' => '',				// 3D�ե饰
	'ShopId' => 'test000003087',// ����å�ID
	'ShopPass' => 'lockon',		// ����åץѥ����
	'Currency' => 'JPN',		// �̲ߥ�����
	'Amount' => '1000',			// ���
	'Tax' => '50',				// ������
	'JobCd' => 'CHECK',			// ������ʬ
	'TenantNo' => '111111111',	// cgi-4�Ǻ�������Ź��ID���������롣
);

$req = new HTTP_Request($entry_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();
$arrRet = lfGetPostArray($response);

sfPrintR($arrRet);

// ��Ѿ��������
$arrData = array(
	'AccessId' => $arrRet['ACCESS_ID'],
	'AccessPass' => $arrRet['ACCESS_PASS'],
	'OrderId' => $order_id,
	'RetURL' => 'http://test.ec-cube.net/ec-cube/test/naka/recv.php',
	// �ץ�ѡ������ɤ򰷤�ʤ�����VISA�����OK
	'CardType' => 'VISA, 11111, 111111111111111111111111111111111111, 1111111111',
	// ��ʧ����ˡ
	/*
		1:���
		2:ʬ��
		3:�ܡ��ʥ����
		4:�ܡ��ʥ�ʬ��
		5:���ʧ��
	 */
	'Method' => '2',
	// ��ʧ���
	'PayTimes' => '4',
	'CardNo1' => '4444',
	'CardNo2' => '4444',
	'CardNo3' => '4444',
	'CardNo4' => '5780',
    'ExpireMM' => '06',
    'ExpireYY' => '07',
	// ����Ź��ͳ�����ֵѥե饰
    'ClientFieldFlag' => '1',
    'ClientField1' => 'f1',
    'ClientField2' => 'f2',
    'ClientField3' => 'f3',
	// ������쥯�ȥڡ����Ǥα�����������ʤ�
	'ModiFlag' => '1',	
);

$req = new HTTP_Request($exec_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);

$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

$arrRet = lfGetPostArray($response);

sfPrintR($arrRet);

//---------------------------------------------------------------------------------------------------------------------------------
function lfGetPostArray($text) {
	if($text != "") {
		$text = ereg_replace("[\n\r]", "", $text);
		$arrTemp = split("&", $text);
		foreach($arrTemp as $ret) {
			list($key, $val) = split("=", $ret);
			$arrRet[$key] = $val;
		}
	}
	return $arrRet;
}
?>