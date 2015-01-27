<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
function smarty_modifier_u($string)
{
    return htmlspecialchars(rawurlencode($string), ENT_QUOTES);
}
