--TEST--
Constant -- Upload error constants
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('UPLOAD_ERR');

echo UPLOAD_ERR_OK, "\n";
echo UPLOAD_ERR_INI_SIZE, "\n";
echo UPLOAD_ERR_FORM_SIZE, "\n";
echo UPLOAD_ERR_PARTIAL, "\n";
echo UPLOAD_ERR_NO_FILE;

?>
--EXPECT--
0
1
2
3
4