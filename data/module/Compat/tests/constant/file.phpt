--TEST--
Constant -- File System Constants
--SKIPIF--
<?php if (defined('FILE_USE_INCLUDE_PATH')) { echo 'skip'; } ?>
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('FILE');

echo FILE_USE_INCLUDE_PATH, "\n";
echo FILE_IGNORE_NEW_LINES, "\n";
echo FILE_SKIP_EMPTY_LINES, "\n";
echo FILE_APPEND, "\n";
echo FILE_NO_DEFAULT_CONTEXT
?>
--EXPECT--
1
2
4
8
16