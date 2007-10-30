<?php
class LC_Utils_Upgrade_Log {
    function LC_Utils_Upgrade_Log($mode) {
        $this->mode = $mode;
    }

    function start() {
        $mode = $this->mode;
        $message = "##### $mode start #####";
        $this->log($message);
    }

    function end() {
        $mode = $this->mode;
        $message = "##### $mode end #####";
        $this->log($message);
    }

    function log($message) {
        GC_Utils::gfPrintLog($message, OWNERSSTORE_LOG_PATH);
    }

    function errLog($code, $val = null) {
        $format = '* error! code:%s / debug:%s';
        $message = sprintf($format, $code, serialize($val));
        GC_Utils::gfPrintLog($message, OWNERSSTORE_LOG_PATH);
    }
}
?>
