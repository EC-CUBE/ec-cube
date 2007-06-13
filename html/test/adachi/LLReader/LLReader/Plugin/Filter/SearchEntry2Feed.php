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
    
    private function entry2feed ($llr, $feed, $entries) {
        $output = "<?xml version=\"1.0\"?>
                    <rss version=\"2.0\">
                        <channel>
                            <title>{$feed->title}</title>
                            <link>http://www.tracypeterson.com/RSS/RSS.php</link>
                            <description>$feed->description</description>
                            <language>$feed->language</language>
                            <pubDate>$feed->date</pubDate>
                            <lastBuildDate>$feed->lastBuildDate</lastBuildDate>
                            <docs>http://someurl.com</docs>
                            <managingEditor>you@youremail.com</managingEditor>
                            <webMaster>you@youremail.com</webMaster>
                    ";
                    
        foreach ($entries as $entry)
        {
            $output .= "<item><title>" . $entry->title . "</title>
                            <link>" . $entry->link . "</link>
                            
        <description>".htmlentities(strip_tags($line['description']))."</description>
                        </item>";
        }
        
        return new LLReader_Feed($output);
    }
}

?>