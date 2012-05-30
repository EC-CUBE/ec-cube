<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
//$_SERVER['REQUEST_METHOD'] = 'POST';
//$_POST['mode'] = 'products_list';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

// {{{ requires
require_once '../require.php';
require_once '../' . ADMIN_DIR . 'require.php';

// }}}
// {{{ generate page

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$objPage = lfPageFactory($mode);
$objPage->init();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->process($mode);


function lfPageFactory($mode) {
    $prefix = 'LC_Page_Upgrade_';
    $file   = CLASS_REALDIR . "pages/upgrade/${prefix}";
    $class  = $prefix;

    switch ($mode) {
        case 'products_list':
            $file  .= 'ProductsList.php';
            $class .= 'ProductsList';
            break;
        case 'patch_download':
        case 'download':
        case 'auto_update':
            $file  .= 'Download.php';
            $class .= 'Download';
            break;
        case 'site_check':
            $file  .= 'SiteCheck.php';
            $class .= 'SiteCheck';
            break;
        default:
            header('HTTP/1.1 400 Bad Request');
            GC_Util::gfPrintLog('modeの値が正しくありません。:'.$mode);
            exit();
            break;
    }

    require_once $file;
    return new $class;
}
