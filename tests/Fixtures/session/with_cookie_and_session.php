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

require __DIR__.'/common.php';

setcookie('abc', 'def');

session_set_save_handler(new TestSessionHandler(new MockSessionHandler('abc|i:123;')), false);
session_start();
session_write_close();
session_start();

$_SESSION['abc'] = 234;
unset($_SESSION['abc']);
