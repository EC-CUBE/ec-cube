<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Admin\OwnersStore;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Plugin\PluginUtil;
use Eccube\Framework\Util\Utils;

/**
 * オーナーズストア：プラグイン管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class PluginHookPointList extends AbstractAdminPage
{

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'ownersstore/plugin_hookpoint_list.tpl';
        $this->tpl_subno    = 'index';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = 'プラグインフックポイント管理';

        $this->arrUse = array();
        $this->arrUse[1] = 'ON';
        $this->arrUse[0] = 'OFF';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        $this->initParam($objFormParam);
        $objFormParam->setParam($_POST);

        $mode = $this->getMode();
        switch ($mode) {
            // ON/OFF
            case 'update_use':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if (!(count($this->arrErr) > 0)) {
                    $arrPluginHookpointUse = $objFormParam->getValue('plugin_hookpoint_use');
                    $plugin_hookpoint_id = $objFormParam->getValue('plugin_hookpoint_id');
                    $use_flg = ($arrPluginHookpointUse[$plugin_hookpoint_id] == 1) ? 1 : 0;
                    PluginUtil::setPluginHookPointChangeUse($plugin_hookpoint_id, $use_flg);
                    // Smartyコンパイルファイルをクリア
                    Utils::clearCompliedTemplate();
                }
                break;
            default:
                break;
        }
        // DBからプラグイン情報を取得
        $arrRet = PluginUtil::getPluginHookPointList();
        // 競合チェック
        $this->arrConflict = PluginUtil::checkConflictPlugin();
        $arrHookPoint = array();
        foreach ($arrRet AS $key => $val) {
            $arrHookPoint[$val['hook_point']][$val['plugin_id']] = $val;
        }
        $this->arrHookPoint = $arrHookPoint;
    }

    /**
     * パラメーター初期化.
     *
     * @param  FormParam $objFormParam
     * @return void
     */
    public function initParam(&$objFormParam)
    {
        $objFormParam->addParam('モード', 'mode', STEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ON/OFFフラグ', 'plugin_hookpoint_use', INT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('プラグインフックポイントID', 'plugin_hookpoint_id', INT_LEN, '', array('NUM_CHECK', 'EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

}
