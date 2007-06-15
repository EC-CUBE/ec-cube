<?php

class PLLagger_Util {
    public static function log ($corrent_phase, $msg) {
        echo "[$corrent_phase] " . mb_convert_encoding($msg, 'EUC-JP', 'UTF-8') . "\n";
    }

    public static function p ($var, $var_dump = true) {
        echo "++++++++ debug start ++++++++\n";

        if ($var_dump) {
            var_dump($var);
        }
        else {
            print_r($var);
        }

        echo "++++++++ debug end ++++++++\n";
    }

}

?>