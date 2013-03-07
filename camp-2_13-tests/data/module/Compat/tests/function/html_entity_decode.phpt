--TEST--
Function -- html_entity_decode
--SKIPIF--
<?php if (function_exists('html_entity_decode')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('html_entity_decode');

$string = "I&#039;ll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now";
echo html_entity_decode($string), "\n";
echo html_entity_decode($string, ENT_COMPAT), "\n";
echo html_entity_decode($string, ENT_QUOTES), "\n";
echo html_entity_decode($string, ENT_NOQUOTES), "\n";
?>
--EXPECT--
I'll "walk" the <b>dog</b> now
I'll "walk" the <b>dog</b> now
I'll "walk" the <b>dog</b> now
I'll &quot;walk&quot; the <b>dog</b> now