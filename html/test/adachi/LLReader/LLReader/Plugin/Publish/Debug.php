<?php

require_once('LLReader/Plugin.php');

class LLReader_Plugin_Publish_Debug extends LLReader_Plugin {
    public function execute ($llr) {
        $config = $this->get_config();
        //$llr->p($llr->get_feeds());
    }
}

?>