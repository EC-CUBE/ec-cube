--TEST--
Function -- hash_algos
--FILE--
<?php
require_once 'PHP/Compat/Function/hash_algos.php';

var_dump(php_compat_hash_algos());
?>
--EXPECT--
array(3) {
  [0]=>
  string(3) "md5"
  [1]=>
  string(4) "sha1"
  [2]=>
  string(6) "sha256"
}
