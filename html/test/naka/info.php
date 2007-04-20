<?php
    require_once("../../require.php");
    $objQuery = new SC_Query();
    $arrRet = $objQuery->select("*", "dtb_products");
    sfPrintR($arrRet);
?>