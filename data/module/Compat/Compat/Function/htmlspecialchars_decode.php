<?PHP
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
// | Authors: Stephan Schmidt <schst@php.net>                             |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: htmlspecialchars_decode.php,v 1.3 2005/06/18 14:02:09 aidan Exp $


/**
 * Replace function htmlspecialchars_decode()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.htmlspecialchars_decode
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.3 $
 * @since       PHP 5.1.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('htmlspecialchars_decode')) {
    function htmlspecialchars_decode($string, $quote_style = null)
    {
        // Sanity check
        if (!is_scalar($string)) {
            user_error('htmlspecialchars_decode() expects parameter 1 to be string, ' .
                gettype($string) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_int($quote_style) && $quote_style !== null) {
            user_error('htmlspecialchars_decode() expects parameter 2 to be integer, ' .
                gettype($quote_style) . ' given', E_USER_WARNING);
            return;
        }

        // Init
        $from   = array('&amp;', '&lt;', '&gt;');
        $to     = array('&', '<', '>');
        
        // The function does not behave as documented
        // This matches the actual behaviour of the function
        if ($quote_style & ENT_COMPAT || $quote_style & ENT_QUOTES) {
            $from[] = '&quot;';
            $to[]   = '"';
            
            $from[] = '&#039;';
            $to[]   = "'";
        }

        return str_replace($from, $to, $string);
    }
}

?>
