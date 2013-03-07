--TEST--
Constant -- Tokenizer constants
--FILE--
<?php
require_once ('PHP/Compat.php');
PHP_Compat::loadConstant('T');

$constants = array(
    'T_ML_COMMENT',
    'T_ML_COMMENT',
    'T_DOC_COMMENT',
    'T_OLD_FUNCTION',
    'T_ABSTRACT',
    'T_CATCH',
    'T_FINAL',
    'T_INSTANCEOF',
    'T_PRIVATE',
    'T_PROTECTED',
    'T_PUBLIC',
    'T_THROW',
    'T_TRY',
    'T_CLONE');

foreach ($constants as $constant) {
    if (defined($constant)) {
        echo 'true';
    } else {
        echo 'false';
    }
    echo "\n";
}

?>
--EXPECT--
true
true
true
true
true
true
true
true
true
true
true
true
true
true