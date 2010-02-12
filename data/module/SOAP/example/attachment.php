<?php
require_once("SOAP/Client.php");
require_once("SOAP/test/test.utility.php");
require_once("SOAP/Value.php");

$filename = 'attachment.php';
$v =  new SOAP_Attachment('test','text/plain',$filename);
$methodValue = new SOAP_Value('testattach', 'Struct', array($v));

$client = new SOAP_Client('mailto:user@domain.com');
# calling with mime
$resp = $client->call('echoMimeAttachment',array($v),
                array('attachments'=>'Mime',
                'namespace'=>'http://soapinterop.org/',
                'from'=>'user@domain.com',
                'host'=>'smtp.domain.com'));
print $client->wire."\n\n\n";
print_r($resp);

# calling with DIME
$resp = $client->call('echoMimeAttachment',array($v));
# DIME has null spaces, change them so we can see the wire
$wire = str_replace("\0",'*',$client->wire);
print $wire."\n\n\n";
print_r($resp);
?>