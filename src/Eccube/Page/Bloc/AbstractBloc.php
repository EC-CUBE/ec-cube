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

namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Display;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Util\Utils;

/**
 * ブロック の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
abstract class AbstractBloc extends AbstractPage
{

    /** @var Display */
    public $objDisplay;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        // 開始時刻を設定する。
        $this->timeStart = microtime(true);

        $this->tpl_authority = $_SESSION['authority'];

        // ディスプレイクラス生成
        $this->objDisplay = Application::alias('eccube.display');

        $this->setTplMainpage($this->blocItems['tpl_path']);

        // トランザクショントークンの検証と生成
        $this->setTokenTo();

        // ローカルフックポイントを実行.
        $objPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        $this->doLocalHookpointBefore($objPlugin);
    }

    /**
     * ブロックファイルに応じて tpl_mainpage を設定する
     *
     * @param  string $bloc_file ブロックファイル名
     * @return void
     */
    public function setTplMainpage($bloc_file)
    {
        if (Utils::isAbsoluteRealPath($bloc_file)) {
            $this->tpl_mainpage = $bloc_file;
        } else {
            $this->tpl_mainpage = Application::alias('eccube.helper.page_layout')->getTemplatePath($this->objDisplay->detectDevice()) . BLOC_DIR . $bloc_file;
        }

        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ
     *
     * @return void
     */
    public function __destruct()
    {
        // 親がリクエスト単位を意図した処理なので、断絶する。
    }
}
