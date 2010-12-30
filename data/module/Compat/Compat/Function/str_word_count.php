<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: str_word_count.php,v 1.9 2005/02/28 11:45:28 aidan Exp $


/**
 * Replace str_word_count()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.str_word_count
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.9 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('str_word_count')) {
    function str_word_count($string, $format = null)
    {
        if ($format !== 1 && $format !== 2 && $format !== null) {
            user_error('str_word_count() The specified format parameter, "' . $format . '" is invalid',
                E_USER_WARNING);
            return false;
        }

        $word_string = preg_replace('/[0-9]+/', '', $string);
        $word_array  = preg_split('/[^A-Za-z0-9_\']+/', $word_string, -1, PREG_SPLIT_NO_EMPTY);

        switch ($format) {
            case null:
                $result = count($word_array);
                break;

            case 1:
                $result = $word_array;
                break;

            case 2:
                $lastmatch = 0;
                $word_assoc = array();
                foreach ($word_array as $word) {
                    $word_assoc[$lastmatch = strpos($string, $word, $lastmatch)] = $word;
                    $lastmatch += strlen($word);
                }
                $result = $word_assoc;
                break;
        }

        return $result;
    }
}

?>