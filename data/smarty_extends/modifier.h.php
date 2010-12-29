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
 * Name:     h<br>
 * Date:     Dec 28, 2010<br>
 * Purpose:  HTML エスケープを行う<br>
 * Example:  {$text|h}
 * @author   Seasoft 塚田将久
 * @param string $string
 * @return string
 */
function smarty_modifier_h($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}
