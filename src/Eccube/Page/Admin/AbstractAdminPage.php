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

namespace Eccube\Page\Admin;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Display;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * 管理者ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
abstract class AbstractAdminPage extends AbstractPage
{
    public $tpl_subno;
    public $tpl_maintitle;
    public $tpl_subtitle;

    /** @var Display */
    public $objDisplay;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->template = MAIN_FRAME;

        //IP制限チェック
        $allow_hosts = unserialize(ADMIN_ALLOW_HOSTS);
        if (is_array($allow_hosts) && count($allow_hosts) > 0) {
            if (array_search($_SERVER['REMOTE_ADDR'], $allow_hosts) === FALSE) {
                Utils::sfDispError(AUTH_ERROR);
            }
        }

        //SSL制限チェック
        if (ADMIN_FORCE_SSL == TRUE) {
            if (Utils::sfIsHTTPS() === false) {
                Application::alias('eccube.response')->sendRedirect($_SERVER['REQUEST_URI'], $_GET, FALSE, TRUE);
            }
        }

        $this->tpl_authority = $_SESSION['authority'];

        // ディスプレイクラス生成
        $this->objDisplay = Application::alias('eccube.display');

        // スーパーフックポイントを実行.
        $objPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_preProcess', array($this));

        // トランザクショントークンの検証と生成
        $this->doValidToken(true);
        $this->setTokenTo();

        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        $objPlugin->doAction($parent_class_name . '_action_before', array($this));
        $class_name = get_class($this);
        if ($class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_before', array($this));
        }
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    public function sendResponse()
    {
        $objPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        $objPlugin->doAction($parent_class_name . '_action_after', array($this));
        $class_name = get_class($this);
        if ($class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_after', array($this));
        }

        // HeadNaviにpluginテンプレートを追加する.
        $objPlugin->setHeadNaviBlocs($this->arrPageLayout['HeadNavi']);

        // スーパーフックポイントを実行.
        $objPlugin->doAction('LC_Page_process', array($this));

        $this->objDisplay->prepare($this, true);
        $this->objDisplay->response->write();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::gfPrintLog を使用すること
     */
    public function log($mess, $log_level='Info')
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug' && DEBUG_MODE === false) {
            return;
        }

        // ログ出力
        GcUtils::gfPrintLog($mess, '');
    }
}
