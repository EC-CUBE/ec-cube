<?php

require_once('PLLagger/Plugin.php');

class PLLagger_Plugin_Filter_SearchEntry2Feed extends PLLagger_Plugin {
    public function execute ($pll) {
        $config = $this->get_config();
        
        $feeds = $pll->get_feeds();
        foreach ( $feeds as $feed ) {
            $hits = array();
            
            // because a lot of 'Notice' occured...
            $err_rep = ini_get('error_reporting');
            error_reporting(E_ALL ^ E_NOTICE);
            
            foreach ( $feed as $entry ) {
                
                $pattern = mb_convert_encoding($config['regex'], 'UTF-8', 'EUC-JP, SJIS, UTF-8');
                if ( preg_match_all($pattern , $entry->title, $matches) ) {
                    $hits[] = $entry;
                    //$pll->log(mb_convert_encoding($feed->title,  'EUC-JP', 'UTF-8')
                    //         . ':' . mb_convert_encoding($entry->title, 'EUC-JP', 'UTF-8'));
                }
            }
            
            error_reporting($err_rep);
            
            $count = count($hits);
            $pll->log('[Filter_SearchEntry2Feed] ' . $feed->title . " : $count entries hit");
            
            if ( $count > 0 ) {
                $pll->update_feed($this->create_feed($pll, $feed, $hits));
            }
        }
    }
    
    private function create_feed ($pll, $feed, $entries) {
        return ;
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
        
        return new PLLagger_Feed($output);
    }
}

?>