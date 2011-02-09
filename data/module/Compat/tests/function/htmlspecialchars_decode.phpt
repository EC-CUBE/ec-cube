--TEST--
Function -- htmlspecialchars_decode
--SKIPIF--
<?php if (function_exists('htmlspecialchars_decode')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('htmlspecialchars_decode');

$text = 'Text &amp; &quot; &#039; &lt; &gt; End Text';
echo $text, "\n";
echo htmlspecialchars_decode($text), "\n";
echo htmlspecialchars_decode($text, ENT_COMPAT), "\n";
echo htmlspecialchars_decode($text, ENT_QUOTES), "\n";
echo htmlspecialchars_decode($text, ENT_NOQUOTES), "\n";

?>
--EXPECT--
Text &amp; &quot; &#039; &lt; &gt; End Text
Text & &quot; &#039; < > End Text
Text & " ' < > End Text
Text & " ' < > End Text
Text & &quot; &#039; < > End Text