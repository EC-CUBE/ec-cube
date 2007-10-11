<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

// {{{ requires
require_once '../require.php';
require_once '../admin/require.php';
require_once DATA_PATH  . 'module/Services/JSON.php';

// }}}
// {{{ generate page

$objPage = lfPageFactory();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));

function lfPageFactory() {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

    $prefix = 'LC_Page_Upgrade_';
    $file   = CLASS_PATH . "pages/upgrade/${prefix}";
    $class  = $prefix;

    switch ($mode) {
    case 'echo_key':
        $file  .= 'EchoKey.php';
        $class .= 'EchoKey';
        break;
    case 'products_list':
        $file  .= 'ProductsList.php';
        $class .= 'ProductsList';
        break;
    default:
        header("HTTP/1.1 400 Bad Request");
        exit();
        break;
    }

    require_once $file;
    return new $class;
}
?>

