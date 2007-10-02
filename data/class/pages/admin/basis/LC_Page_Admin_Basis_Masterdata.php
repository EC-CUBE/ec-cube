<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * マスタデータ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Masterdata extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/masterdata.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $dbFactory = SC_DB_DBFactory::getInstance();
        $this->arrMasterDataName = $dbFactory->findTableNames("mtb_");
        $masterData = new SC_DB_MasterData_Ex();
        
        
        if (!isset($_POST["mode"])) $_POST["mode"] = "";
        
        switch ($_POST["mode"]) {
        	case "edit":
        		
        		$this->arrMasterData = $masterData->getDbMasterData($_POST["master_data_name"]);
        		break;
        	default:
        }
        
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
