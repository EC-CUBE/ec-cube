<?php

require_once('LLReader/Plugin.php');
require_once('LLReader/Feed.php');

class LLReader_Plugin_Subscription_Simple extends LLReader_Plugin {
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
                $feed = new LLReader_Feed($xml);
                $llr->add_feed($feed);
            }
            catch ( XML_Feed_Parser_Exception $e ) {
                $llr->log('[Warning] Feed invalid: ' . $e->getMessage());
            }
        }
    }
}

?>