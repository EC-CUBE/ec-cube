<?php

require_once('LLReader/Plugin.php');

class LLReader_Plugin_Filter_SearchEntry2Feed extends LLReader_Plugin {
    public function execute ($llr) {
        $config = $this->get_config();
        $maches_entries = array();
        
        $feeds = $llr->get_feeds();
        foreach ( $feeds as $feed ) {
            
            $err_rep = ini_get('error_reporting');
            error_reporting(E_ALL ^ E_NOTICE);
            
            foreach ( $feed as $entry ) {
                
                $pattern = mb_convert_encoding($config['regex'], 'UTF-8', 'EUC-JP, SJIS, UTF-8');
                if ( preg_match_all($pattern , $entry->title, $maches) ) {
                    $maches_entries[] = $entry;
                }
                else {
                    unset($entry);
                } 
            }
            
            error_reporting($err_rep);
        }
        new LLReader_Feed($maches_entries[0]->__toString());
        $llr->p(mb_convert_encoding($maches_entries[0]->__toString(), 'SJIS', 'UTF-8'));
    }
    
    private function entry2feed ($llr, $feed, $entry) {
        
    }
}

?>