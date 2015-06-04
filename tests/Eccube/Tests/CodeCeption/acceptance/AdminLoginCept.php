<?php 
$I = new AcceptanceTester($scenario);
$I->amOnPage('/admin');
$I->fillField('ID', 'admin');
$I->fillField('PASSWORD', 'password');
$csrf_token = $I->grabValueFrom("Form#form1 input[name='_csrf_token']");
$I->sendAjaxPostRequest('/admin/login_check',
    array('login_id' => 'admin',
          'password' => 'password',
          '_csrf_token' => $csrf_token));
$I->see('システム情報');

$I->amOnPage('/admin/customer/new');
$I->see('会員管理');
?>