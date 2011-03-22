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
// $Id: php_strip_whitespace.php,v 1.10 2005/01/26 04:55:13 aidan Exp $


/**
 * Replace php_strip_whitespace()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.php_strip_whitespace
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.10 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error) + Tokenizer extension
 */
if (!function_exists('php_strip_whitespace')) {
    function php_strip_whitespace($file)
    {
        // Sanity check
        if (!is_scalar($file)) {
            user_error('php_strip_whitespace() expects parameter 1 to be string, ' .
                gettype($file) . ' given', E_USER_WARNING);
            return;
        }

        // Load file / tokens
        $source = implode('', file($file));
        $tokens = token_get_all($source);

        // Init
        $source = '';
        $was_ws = false;

        // Process
        foreach ($tokens as $token) {
            if (is_string($token)) {
                // Single character tokens
                $source .= $token;
            } else {
                list($id, $text) = $token;
                
                switch ($id) {
                    // Skip all comments
                    case T_COMMENT:
                    case T_ML_COMMENT:
                    case T_DOC_COMMENT:
                        break;

                    // Remove whitespace
                    case T_WHITESPACE:
                        // We don't want more than one whitespace in a row replaced
                        if ($was_ws !== true) {
                            $source .= ' ';
                        }
                        $was_ws = true;
                        break;

                    default:
                        $was_ws = false;
                        $source .= $text;
                        break;
                }
            }
        }

        return $source;
    }
}

?>
