<?php

require_once('PLLagger/Plugin.php');

class PLLagger_Plugin_Publish_Debug extends PLLagger_Plugin {
    public function execute ($llr) {
        $config = $this->get_config();
        //$llr->p($llr->get_feeds());
    }
}

?>