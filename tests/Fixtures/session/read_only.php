<?php

require __DIR__.'/common.php';

session_set_save_handler(new TestSessionHandler(new MockSessionHandler('abc|i:123;')), false);
session_start();

echo $_SESSION['abc'];
