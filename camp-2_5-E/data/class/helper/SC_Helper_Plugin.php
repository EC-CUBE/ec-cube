<?php
/**
 *
 */
class SC_Helper_Plugin{

    /**
     * enableかどうかを判別する
     * インスタンス化
     */
    public static function load(LC_Page $lcpage){
        //データベースからクラス名を読み込む
        $objQuery =& SC_Query::getSingletonInstance();
        $arrRet = $objQuery->select('*', 'dtb_plugin');

        // 実行されたぺーじ
        // 現在のページで使用するプラグインが存在するかどうかを検証する
        foreach ($arrRet as $key => $value){
            // プラグインを稼働させるクラス名のリストを取得する
            // プラグインのディレクトリ内の設定ファイルを参照する
            require_once DATA_PATH.'plugin/'.$value['class_name'].'/conf.php';
            if(in_array($value['class_name'], $arrPluginExecutePages)){
                require_once DATA_PATH.'plugin/'.$value['class_name'].$value['class_name'].'.php';
            }
        }

    }

    /**
     * 稼働中のプラグインを取得する。
     */
    public static function getEnablePlugin(){
        $objQuery =& SC_Query::getSingletonInstance();
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
        $objQuery =& SC_Query::getSingletonInstance();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'del_flg = 0';
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }


    /* プラグイン
     *
     */
    public static function process(LC_Page $lcpage,SC_View $view){       
        //プラグインの名前を判別してページ内で有効なプラグインがあれば実行する
        $view;
  }
}