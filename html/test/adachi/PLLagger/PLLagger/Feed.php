<?php

require_once('XML/Feed/Parser.php');
 
class PLLagger_Feed extends XML_Feed_Parser {
    public static function entries2feed ($entries, $feed = null, $feed_info = array()) {
        $xml = '';
        return new PLLagger_Feed($xml);
    }
}

?>