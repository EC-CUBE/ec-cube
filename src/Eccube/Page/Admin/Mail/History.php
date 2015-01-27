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

namespace Eccube\Page\Admin\Mail;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class History extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'mail/history.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subno = 'history';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = '配信履歴';
        $this->tpl_pager = 'pager.tpl';
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
        switch ($this->getMode()) {
            case 'delete':
                if (Utils::sfIsInt($_GET['send_id'])) {
                    // 削除時
                    $this->lfDeleteHistory($_GET['send_id']);

                    $this->objDisplay->reload(null, true);
                }
                break;
            default:
                break;
        }

        list($this->tpl_linemax, $this->arrDataList, $this->arrPagenavi) = $this->lfDoSearch($_POST['search_pageno']);
    }

    /**
     * 実行履歴の取得
     *
     * @param  integer $search_pageno 表示したいページ番号
     * @return array(  integer 全体件数, mixed メール配信データ一覧配列, mixed PageNaviオブジェクト)
     */
    public function lfDoSearch($search_pageno = 1)
    {
        // 引数の初期化
        if (Utils::sfIsInt($search_pageno)===false) {
            $search_pageno = 1;
        }
        //
        $objSelect = Application::alias('eccube.query');    // 一覧データ取得用
        $objQuery = Application::alias('eccube.query');    // 件数取得用

        // 該当全体件数の取得
        $linemax = $objQuery->count('dtb_send_history', 'del_flg = 0');

        // 一覧データの取得
        $objSelect->setOrder('start_date DESC, send_id DESC');

        $col = '*';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id) AS count_all';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 1) AS count_sent';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 2) AS count_error';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag IS NULL) AS count_unsent';

        // ページ送りの取得
        $offset = SEARCH_PMAX * ($search_pageno - 1);
        $objSelect->setLimitOffset(SEARCH_PMAX, $offset);
        $arrResult = $objSelect->select($col, 'dtb_send_history', ' del_flg = 0');

        /* @var $objNavi PageNavi */
        $objNavi = Application::alias(
            'eccube.page_navi',
            $search_pageno,
            $linemax,
            SEARCH_PMAX
        );

        return array($linemax, $arrResult, $objNavi->arrPagenavi);
    }

    /**
     * 送信履歴の削除
     * @return void
     */
    public function lfDeleteHistory($send_id)
    {
            $objQuery = Application::alias('eccube.query');
            $objQuery->update('dtb_send_history',
                              array('del_flg' =>1),
                              'send_id = ?',
                              array($send_id));
    }
}
