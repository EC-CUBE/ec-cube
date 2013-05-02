<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     u<br>
 * Date:     Dec 28, 2010<br>
 * Purpose:  URL エンコードを行った後で、HTML エスケープを行う<br>
 * Example:  {$text|u}
 * @author   Seasoft 塚田将久
 * @param string $string
 * @return string
 */
function smarty_modifier_u($string) {
    return htmlspecialchars(rawurlencode($string), ENT_QUOTES);
}
