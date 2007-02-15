<?
/*
 * ���򤫤���ʸ���������Ͽ���������Ȥ����ܤ��롣
 */
require_once("../require.php");

$objCustomer = new SC_Customer();
$objCartSess = new SC_CartSession();

//����ܺ٥ǡ����μ���
$arrDisp = lfGetOrderDetail($_POST['order_id']);

//�����󤷤Ƥ��ʤ����ޤ���DB�˾���̵�����
if (!$objCustomer->isLoginSuccess() or count($arrDisp) == 0){
	sfDispSiteError(CUSTOMER_ERROR);
}

for($num = 0; $num < count($arrDisp); $num++) {
	$product_id = $arrDisp[$num]['product_id'];
	$cate_id1 = $arrDisp[$num]['classcategory_id1'];
	$cate_id2 = $arrDisp[$num]['classcategory_id2'];
	$quantity = $arrDisp[$num]['quantity'];

	$objCartSess->addProduct(array($product_id, $cate_id1, $cate_id2), $quantity);
}

header("Location: " . gfAddSessionId(URL_CART_TOP));


//-----------------------------------------------------------------------------------------------------------------------------------
// ����ܺ٥ǡ����μ���
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, quantity";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

?>
