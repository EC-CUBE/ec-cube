<?php


class TestPlugin1 extends SC_Plugin_Ex {



    function enable(String $classname){
        return preg_match('/shopping|payment|products/',$classname)?
        !preg_match('/list/', $classname)
        :false
        ;
    }

    function init(){

    }


    function getVersion(){
        return "0.0.1";
    }

    function getName(){
        return "TestPlugin1";
    }

    function process(){

    }

    function mobileprocess(){

    }

    public function install(){
        $objQuery = new SC_Query();
        $arrPlugins = $objQuery->get("dtb_plugin", "plugin_id", "plugin_name = ?",array($name));
        $data = array(
          'plugin_name' => $objForm->getValue('plugin_name'),
          'path' => realpath(DATA_DIR.'/plugin/'.$objForm->getValue('plugin_name').'/'),
          'enable' => '1',
          'del_flg' => '0',
          'class_name' => $objForm->getValue('plugin_name'),
          'version' => $this->getVersion()
        );

        if(count($arrPlugins) == 0){
            $objQuery->insert("dtb_plugin", $data);
        }else{
            $objQuery->update('dtb_plugin',$data,'plugin_id = ?',array($arrPlugins[0]['plugin_id']));
        }

        
    }

    public function uninstall($plugin_id){
        $objQuery = new SC_Query();
        $objQuery->update('dtb_plugin', array('del_flg'=>1), $arrValIn, $arrRawSql, $arrRawSqlVal)
    }

    /**
     *
     * @return String インストール用のSQL
     */
    protected function getInstallSQL(){

    }


    protected function getUninstallSQL(){

    }

}