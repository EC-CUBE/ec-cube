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
// $Id: T.php,v 1.4 2004/11/14 16:43:40 aidan Exp $


/**
 * Replace tokenizer constants
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/ref.tokenizer
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.4 $
 * @since       PHP 5
 */
if (!defined('T_ML_COMMENT')) {
   define('T_ML_COMMENT', T_COMMENT);
}
if (!defined('T_DOC_COMMENT')) {
    define('T_DOC_COMMENT', T_ML_COMMENT);
}

if (!defined('T_OLD_FUNCTION')) {
    define('T_OLD_FUNCTION', -1);
}
if (!defined('T_ABSTRACT')) {
    define('T_ABSTRACT', -1);
}
if (!defined('T_CATCH')) {
    define('T_CATCH', -1);
}
if (!defined('T_FINAL')) {
    define('T_FINAL', -1);
}
if (!defined('T_INSTANCEOF')) {
    define('T_INSTANCEOF', -1);
}
if (!defined('T_PRIVATE')) {
    define('T_PRIVATE', -1);
}
if (!defined('T_PROTECTED')) {
    define('T_PROTECTED', -1);
}
if (!defined('T_PUBLIC')) {
    define('T_PUBLIC', -1);
}
if (!defined('T_THROW')) {
    define('T_THROW', -1);
}
if (!defined('T_TRY')) {
    define('T_TRY', -1);
}
if (!defined('T_CLONE')) {
    define('T_CLONE', -1);
}

?>