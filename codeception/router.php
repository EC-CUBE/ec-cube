<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (preg_match('/\.xml$/', $_SERVER['REQUEST_URI'])) {
    return require 'index.php';
}

// multipart POST (ファイルアップロード) は PHP built-in server の
// fallback 経由だと $_FILES が空になる場合があるため、明示的に index.php へ転送する
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES)) {
    return require 'index.php';
}

return false;
