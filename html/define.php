<?php
/** HTMLディレクトリからのDATAディレクトリの相対パス */
define('HTML2DATA_DIR', '../data/');

/** data/module 以下の PEAR ライブラリを優先的に使用する */
set_include_path(realpath(dirname(__FILE__) . '/' . HTML2DATA_DIR . 'module') . PATH_SEPARATOR . get_include_path());

/**
 * DIR_INDEX_FILE にアクセスするときにファイル名を使用するか
 *
 * true: 使用する, false: 使用しない, null: 自動(IIS は true、それ以外は false)
 * ※ IIS は、POST 時にファイル名を使用しないと不具合が発生する。(http://support.microsoft.com/kb/247536/ja)
 */
define('USE_FILENAME_DIR_INDEX', null);

/*
 * Local variables:
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
