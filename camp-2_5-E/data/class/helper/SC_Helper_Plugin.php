<?php

class SC_Helper_Plugin{

    /**
     * enableかどうかを判別する
     * インスタンス化
     */
    public static function load(LC_Page &$lcpage){
        //データベースからクラス名を読み込む
        $objQuery = new SC_Query();
        $col = "*";
        $table = "dtb_plugin";
        $where = "enable = 1 AND del_flg = 0";
        $arrRet = $objQuery->select($col, $table, $where);
        $class_name = get_class($lcpage);

        // 実行されたぺーじ
        // 現在のページで使用するプラグインが存在するかどうかを検証する
        foreach ($arrRet as $plugins){
            // プラグインを稼働させるクラス名のリストを取得する
            // プラグインのディレクトリ内の設定ファイルを参照する
            $plugin_name = $plugins['name'];
            $plugin_class_name = $plugins['class_name'];
            require_once DATA_PATH."plugin/{$plugin_name}/{$plugin_class_name}.php";

            $code_str = "\$is_enable = {$plugin_class_name}::is_enable(\$class_name);";
            eval($code_str);
            if ($is_enable) {
                $arrPluginList[] = $plugin_class_name;
            }
        }
        return $arrPluginList;
    }

    public static function preProcess(LC_Page &$lcpage){
        //プラグインの名前を判別してページ内で有効なプラグインがあれば実行する
        $arrPluginList = SC_Helper_Plugin::load($lcpage);
       if(count($arrPluginList) > 0){
            foreach ($arrPluginList as $key => $value){
                $instance = new $value;
                $instance->preProcess($lcpage);
            }
        }
        return $lcpage;
    }

    /* 読み込んだプラグインの実行用メソッド
     *
     */
    public static function process(LC_Page &$lcpage){
        //プラグインの名前を判別してページ内で有効なプラグインがあれば実行する
        $arrPluginList = SC_Helper_Plugin::load($lcpage);
        if(count($arrPluginList) > 0){
            foreach ($arrPluginList as $key => $value){
                $instance = new $value;
                $instance->process($lcpage);
            }
        }
        return $lcpage;
    }

    /**
     * 稼働中のプラグインを取得する。
     */
    public static function getEnablePlugin(){
        $objQuery = new SC_Query();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'enable = 1 AND del_flg = 0';
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }

    /**
     * インストールされているプラグインを取得する。
     */
    public static function getAllPlugin(){
        $objQuery = new SC_Query();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'del_flg = 0';
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }


    public static function getFilesystemPlugins(){
        $plugin_dir = DATA_PATH."/plugin/";
        if($dh = opendir($plugin_dir)){
            while(($file = readdir($dh) !== false)){
                if(is_dir($plugin_dir."/".$file)){
                     
                }
            }
        }
    }
}

