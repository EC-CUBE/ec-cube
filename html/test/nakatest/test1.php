<?php

require_once("../../require.php");

$arrRet = sfGetChildrenArray("dtb_category","parent_category_id", "category_id", 1);

sfPrintR($arrRet);


?>