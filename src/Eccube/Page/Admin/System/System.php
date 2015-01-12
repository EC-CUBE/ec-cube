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

namespace Eccube\Page\Admin\System;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\DBFactory;

/**
 * システム情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class System extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'system/system.tpl';
        $this->tpl_subno    = 'system';
        $this->tpl_mainno   = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'システム情報';
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
        $objFormParam = Application::alias('eccube.form_param');

        $this->initForm($objFormParam, $_GET);
        switch ($this->getMode()) {
            // PHP INFOを表示
            case 'info':
                phpinfo();
                Application::alias('eccube.response')->actionExit();
                break;

            default:
                break;
        }

        $this->arrSystemInfo = $this->getSystemInfo();
    }

    /**
     * フォームパラメーター初期化.
     *
     * @param  FormParam $objFormParam
     * @param  array  $arrParams    $_GET値
     * @return void
     */
    public function initForm(&$objFormParam, &$arrParams)
    {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrParams);
    }

    /**
     * システム情報を取得する.
     *
     * @return array システム情報
     */
    public function getSystemInfo()
    {
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');

        $arrSystemInfo = array(
            array('title' => 'EC-CUBE',     'value' => ECCUBE_VERSION),
            array('title' => 'サーバーOS',    'value' => php_uname()),
            array('title' => 'DBサーバー',    'value' => $dbFactory->sfGetDBVersion()),
            array('title' => 'WEBサーバー',   'value' => $_SERVER['SERVER_SOFTWARE']),
        );

        $value = phpversion() . ' (' . implode(', ', get_loaded_extensions()) . ')';
        $arrSystemInfo[] = array('title' => 'PHP', 'value' => $value);

        if (extension_loaded('GD') || extension_loaded('gd')) {
            $arrValue = array();
            foreach (gd_info() as $key => $val) {
                $arrValue[] = "$key => $val";
            }
            $value = '有効 (' . implode(', ', $arrValue) . ')';
        } else {
            $value = '無効';
        }
        $arrSystemInfo[] = array('title' => 'GD', 'value' => $value);
        $arrSystemInfo[] = array('title' => 'HTTPユーザーエージェント', 'value' => $_SERVER['HTTP_USER_AGENT']);

        return $arrSystemInfo;
    }
}
