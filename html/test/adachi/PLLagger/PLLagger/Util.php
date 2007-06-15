<?php

class PLLagger_Util {
    public static function log ($corrent_phase, $msg) {
        echo "[$corrent_phase] " . $msg . "\n";
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