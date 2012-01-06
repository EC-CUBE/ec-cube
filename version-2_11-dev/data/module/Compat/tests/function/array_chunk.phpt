--TEST--
Function -- array_chunk
--SKIPIF--
<?php if (function_exists('array_chunk')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('array_chunk');

$input_array = array(2 => 'a', 3 => 'b', 4 => 'c', 5 => 'd', 6 => 'e');
print_r(array_chunk($input_array, 2));
print_r(array_chunk($input_array, 2, true));
print_r(array_chunk($input_array, 3));
print_r(array_chunk($input_array, 3, true));
print_r(array_chunk($input_array, 4));
print_r(array_chunk($input_array, 4, true));
print_r(array_chunk($input_array, 5));
print_r(array_chunk($input_array, 5, true));
print_r(array_chunk($input_array, 6));
print_r(array_chunk($input_array, 6, true));
?>
--EXPECT--
Array
(
    [0] => Array
        (
            [0] => a
            [1] => b
        )

    [1] => Array
        (
            [0] => c
            [1] => d
        )

    [2] => Array
        (
            [0] => e
        )

)
Array
(
    [0] => Array
        (
            [2] => a
            [3] => b
        )

    [1] => Array
        (
            [4] => c
            [5] => d
        )

    [2] => Array
        (
            [6] => e
        )

)
Array
(
    [0] => Array
        (
            [0] => a
            [1] => b
            [2] => c
        )

    [1] => Array
        (
            [0] => d
            [1] => e
        )

)
Array
(
    [0] => Array
        (
            [2] => a
            [3] => b
            [4] => c
        )

    [1] => Array
        (
            [5] => d
            [6] => e
        )

)
Array
(
    [0] => Array
        (
            [0] => a
            [1] => b
            [2] => c
            [3] => d
        )

    [1] => Array
        (
            [0] => e
        )

)
Array
(
    [0] => Array
        (
            [2] => a
            [3] => b
            [4] => c
            [5] => d
        )

    [1] => Array
        (
            [6] => e
        )

)
Array
(
    [0] => Array
        (
            [0] => a
            [1] => b
            [2] => c
            [3] => d
            [4] => e
        )

)
Array
(
    [0] => Array
        (
            [2] => a
            [3] => b
            [4] => c
            [5] => d
            [6] => e
        )

)
Array
(
    [0] => Array
        (
            [0] => a
            [1] => b
            [2] => c
            [3] => d
            [4] => e
        )

)
Array
(
    [0] => Array
        (
            [2] => a
            [3] => b
            [4] => c
            [5] => d
            [6] => e
        )

)