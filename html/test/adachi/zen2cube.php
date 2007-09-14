<?php
/**
 *  zencart����ec-cube�ؤξ��ʥǡ����ܹԥ�����ץ�(����)
 */

require_once("../../require.php");

// zencart��DSN������
$zen_db_dsn = 'mysql://root:password@localhost/zencart_db';

lfConvertProducts();

function lfGetObjQuery($dsn = ''){
    global $zen_db_dsn;
    if (strtolower($dsn) == 'zen') {
        $dsn = $zen_db_dsn;
    }
    return new SC_Query($dsn, true, true);
}

function lfConvertProducts(){
    $arrError = 0;
    
    $ZenQuery  = lfGetObjQuery('zen');
    $CubeQuery = lfGetObjQuery();
    
    $z_tbl_products = 'products';
    $z_tbl_customers_info = 'customers_info';
    $z_tbl_customers_wishlist = 'customers_wishlist';
    
    $arrRet = $CubeQuery->select('*', 'vw_products_allclass_detail');
    sfprintr($arrRet);
}
