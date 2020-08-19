<?php

require __DIR__.'/common.php';

session_set_save_handler(new TestSessionHandler(new MockSessionHandler('abc|i:123;')), false);
session_start();

session_regenerate_id(true);

ob_start(function ($buffer) { return str_replace(session_id(), 'random_session_id', $buffer); });
