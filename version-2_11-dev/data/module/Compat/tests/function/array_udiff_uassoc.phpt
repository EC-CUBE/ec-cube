--TEST--
Function -- array_udiff_uassoc
--SKIPIF--
<?php if (function_exists('array_udiff_uassoc')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_udiff_uassoc');

class cr
{
    var $val;

    function cr($val)
    {
        $this->val = $val;
    }

    function comp_func_cr($a, $b)
    {
        if ($a->val === $b->val) return 0;
        return ($a->val > $b->val) ? 1 : -1;
    }
   
    function comp_func_key($a, $b)
    {
        if ($a === $b) return 0;
        return ($a > $b) ? 1 : -1;
    }
}

$a = array('0.1' => new cr(9), '0.5' => new cr(12), 0 => new cr(23), 1 => new cr(4), 2 => new cr(-15));
$b = array('0.2' => new cr(9), '0.5' => new cr(22), 0 => new cr(3), 1 => new cr(4), 2 => new cr(-15));

$result = array_udiff_uassoc($a, $b, array('cr', 'comp_func_cr'), array('cr', 'comp_func_key'));
print_r($result);
?>
--EXPECT--
Array
(
    [0.1] => cr Object
        (
            [val] => 9
        )

    [0.5] => cr Object
        (
            [val] => 12
        )

    [0] => cr Object
        (
            [val] => 23
        )

)