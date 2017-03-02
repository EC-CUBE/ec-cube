<?php

return array(
    'request' => 'GetResponseEvent',
    'controller' => 'FilterControllerEvent',
    'response' => 'FilterResponseEvent',
    'exception' => 'GetResponseForExceptionEvent',
    'terminate' => 'PostResponseEvent',
    'eccube.event.render' => 'FilterResponseEvent',
);
