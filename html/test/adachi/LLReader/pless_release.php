<?php

ini_set('include_path', ini_get('include_path') . ';./;./Lib');

require_once('LLReader.php');

$config = array(
    'plugins' => array(
        'Subscription_Simple' => array(
            'urls' => array(
                '',
                ''
            ),
        )/*,
        'Publish_Mail'=> array(
            'to' => array(
                '',
                '',
            ),
            'from' => ''
        )*/
    )
);
 
$LLR = new LLReader($config);
$LLR->run();

?>