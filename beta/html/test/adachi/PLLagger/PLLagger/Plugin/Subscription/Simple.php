<?php

require_once('PLLagger/Plugin.php');
require_once('PLLagger/Feed.php');

class PLLagger_Plugin_Subscription_Simple extends PLLagger_Plugin {
    public function execute ($llr) {
        $config = $this->get_config();
        $urls   = array();
        
        if ( is_array($config['urls']) ) {
            $urls = $config['urls'];
        }
        else {
            $urls = array($config['urls']);
        }
        
        foreach ( $urls as $url ) {
            $xml = file_get_contents($url);
            
            try {
                $feed = new PLLagger_Feed($xml);
                $llr->add_feed($feed);
                $llr->log("[Subscription_Simple] get $url");
            }
            catch ( XML_Feed_Parser_Exception $e ) {
                $llr->log('[Warning] Feed invalid: ' . $e->getMessage());
            }
        }
    }
}

?>