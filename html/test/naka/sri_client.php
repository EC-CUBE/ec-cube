<?php
require_once('../../require.php');
ini_set('include_path', DATA_PATH . 'module' . PATH_SEPARATOR . ini_get('include_path'));
require_once('SOAP/Client.php');

$client = new SOAP_Client('http://testwallet.sri.jp/index.php?module=CreditV&wsdl');

$authonlyData = array(
    // 加盟店ID
    'spid' => 'd45f9d00f62721e7',
    // 注文ID
    'orderid' => '3',
    // カード番号
    'cardno' => '5555-5555-5555-4444',
    // 有効期限
    'expire' => '0808',
    // 金額
    'amount'=> '1000',
    // 支払方法
    'method' => '10'
);

$input = array(
    'authonlyData' => $authonlyData
);

$result = $client->call('CreditVService.authonly', $input);
print_r($result); 



?>
