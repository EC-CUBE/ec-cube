<?php

class SC_Helper_Plugin{

    /**
     * enableかどうかを判別する
     * インスタンス化
     */
    public static function load(LC_Page $lcpage){
        //データベースからクラス名を読み込む
        $objQuery = new SC_Query();
        $col = "*";
        $table = "dtb_plugin";
        $where = "enable = 1 AND del_flg = 0";
        $arrRet = $objQuery->select($col, $table, $where);
        $arrEnablePlugins = array();
        $class_name = get_class($lcpage);

        // 実行されたぺーじ
        // 現在のページで使用するプラグインが存在するかどうかを検証する
        foreach ($arrRet as $key => $value){
            // プラグインを稼働させるクラス名のリストを取得する
            // プラグインのディレクトリ内の設定ファイルを参照する
            require_once DATA_PATH.'plugin/'.$value['class_name'].'/config.php';
            if( in_array($class_name,$arrPluginPageList) == true ){
                require_once DATA_PATH.'plugin/'.$value['class_name'].'/'.$value['class_name'].'.php';
                $arrPluginList[] = $value['class_name'];
            }
        }
        return $arrPluginList;
    }

     /* 読み込んだプラグインの実行用メソッド
     *
     */
    public static function process(LC_Page $lcpage){
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

}

