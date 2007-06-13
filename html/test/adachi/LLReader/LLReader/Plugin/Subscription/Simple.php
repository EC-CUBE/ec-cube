<?php

require_once('LLReader/Plugin.php');

class LLReader_Plugin_Subscription_Simple extends LLReader_Plugin {
    function execute ($llr) {
        $config = $this->get_config();
        $llr->p($config);
    }
}

?>