<?php 
$I = new AcceptanceTester($scenario);
AdminLoginPage::of($I)->login('admin', 'password');
$I->amOnPage('/admin/customer/new');
$I->see('会員管理');
?>