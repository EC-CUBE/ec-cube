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

namespace Eccube\Page\Guide;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Helper\KiyakuHelper;

/**
 * 利用規約について のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Kiyaku extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
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
        $this->lfGetKiyaku(intval($_GET['page']), $this);
    }

    /**
     * 利用規約を取得し、ページオブジェクトに格納する。
     *
     * @param integer $index 規約のインデックス
     * @param object &$objPage ページオブジェクト
     * @param LC_Page_Guide_Kiyaku $objPage
     * @return void
     */
    public function lfGetKiyaku($index, &$objPage)
    {
        /* @var $objKiyaku KiyakuHelper */
        $objKiyaku = Application::alias('eccube.helper.kiyaku');
        $arrKiyaku = $objKiyaku->getList();

        $number = count($arrKiyaku);
        if ($number > 0) {
            $last = $number - 1;
        } else {
            $last = 0;
        }

        if ($index < 0) {
            $index = 0;
        } elseif ($index > $last) {
            $index = $last;
        }

        $objPage->tpl_kiyaku_title = $arrKiyaku[$index]['kiyaku_title'];
        $objPage->tpl_kiyaku_text = $arrKiyaku[$index]['kiyaku_text'];
        $objPage->tpl_kiyaku_index = $index;
        $objPage->tpl_kiyaku_last_index = $last;
        $objPage->tpl_kiyaku_is_first = $index <= 0;
        $objPage->tpl_kiyaku_is_last = $index >= $last;
    }
}
