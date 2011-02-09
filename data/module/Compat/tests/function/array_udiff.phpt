--TEST--
Function -- array_udiff
--SKIPIF--
<?php if (function_exists('array_udiff')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_udiff');

class cr {
    var $priv_member;
    function cr($val)
    {
        $this->priv_member = $val;
    }

    function comp_func_cr($a, $b)
    {
        if ($a->priv_member === $b->priv_member) return 0;
        return ($a->priv_member > $b->priv_member)? 1:-1;
    }
}

$a = array("0.1" => new cr(9), "0.5" => new cr(12), 0 => new cr(23), 1=> new cr(4), 2 => new cr(-15),);
$b = array("0.2" => new cr(9), "0.5" => new cr(22), 0 => new cr(3), 1=> new cr(4), 2 => new cr(-15),);

$result = array_udiff($a, $b, array("cr", "comp_func_cr"));
echo serialize($result);
?>
--EXPECT--
a:2:{s:3:"0.5";O:2:"cr":1:{s:11:"priv_member";i:12;}i:0;O:2:"cr":1:{s:11:"priv_member";i:23;}}