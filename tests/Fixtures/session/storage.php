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

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage();
$storage->setSaveHandler(new TestSessionHandler(new MockSessionHandler()));
$flash = new FlashBag();
$storage->registerBag($flash);
$storage->start();

$flash->add('foo', 'bar');

print_r($flash->get('foo'));
echo empty($_SESSION) ? '$_SESSION is empty' : '$_SESSION is not empty';
echo "\n";

$storage->save();

echo empty($_SESSION) ? '$_SESSION is empty' : '$_SESSION is not empty';

ob_start(function ($buffer) { return str_replace(session_id(), 'random_session_id', $buffer); });
