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
use Eccube\Framework\PageNavi;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->list_data    = '';  // テーブルデータ取得用
        $this->tpl_disppage = '';  // 表示中のページ番号
        $this->tpl_strnavi  = '';
        $this->tpl_mainpage = 'system/index.tpl';
        $this->tpl_mainno   = 'system';
        $this->tpl_subno    = 'index';
        $this->tpl_onload   = 'eccube.getRadioChecked();';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'メンバー管理';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');
        $this->arrWORK = $masterData->getMasterData('mtb_work');
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
        // ADMIN_ID以外の管理者件数を取得
        $linemax = $this->getMemberCount('del_flg <> 1 AND member_id <> ' . ADMIN_ID);

        // ADMIN_ID以外で稼動中の管理者件数を取得
        $this->workmax
            = $this->getMemberCount('work = 1 AND del_flg <> 1 AND member_id <> ' . ADMIN_ID);

        // ページ送りの処理 $_GET['pageno']が信頼しうる値かどうかチェックする。
        $pageno = $this->lfCheckPageNo($_GET['pageno']);

        /* @var $objNavi PageNavi */
        $objNavi = Application::alias('eccube.page_navi', $pageno, $linemax, MEMBER_PMAX, 'eccube.moveMemberPage', NAVI_PMAX);
        $this->tpl_strnavi  = $objNavi->strnavi;
        $this->tpl_disppage = $objNavi->now_page;
        $this->tpl_pagemax  = $objNavi->max_page;

        // 取得範囲を指定(開始行番号、行数のセット)して管理者データを取得
        $this->list_data = $this->getMemberData($objNavi->start_row);

        $this->tpl_last_admin = $this->checkLastAdministrator($this->list_data);
    }

    /**
     * dtb_memberからWHERE句に該当する件数を取得する.
     *
     * @access private
     * @param  string  $where WHERE句
     * @return integer 件数
     */
    public function getMemberCount($where)
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_member';

        return $objQuery->count($table, $where);
    }

    /**
     * 開始行番号, 行数を指定して管理者データを取得する.
     *
     * @access private
     * @param  integer $startno 開始行番号
     * @return array   管理者データの連想配列
     */
    public function getMemberData($startno)
    {
        $col = 'member_id,name,department,login_id,authority,rank,work';
        $from = 'dtb_member';
        $where = 'del_flg <> 1 AND member_id <> ?';
        $objQuery = Application::alias('eccube.query');
        $objQuery->setOrder('rank DESC');
        $objQuery->setLimitOffset(MEMBER_PMAX, $startno);
        $arrMemberData = $objQuery->select($col, $from, $where, array(ADMIN_ID));

        return $arrMemberData;
    }

    /**
     * 登録されている管理者権限が1つであるかチェックする.
     *
     * @access private
     * @param  array   $arrMemberData 管理者データの連想配列
     * @return boolean 管理者権限が1つであることを示すフラグ
     */
    public function checkLastAdministrator($arrMemberData)
    {
        $numberOfAdministrator = 0;
        foreach ($arrMemberData as $member) {
            if ($member['authority'] == 0) {
                $numberOfAdministrator++;  
                if ($numberOfAdministrator > 1) {
                    break;
                }
            }
        }        
        return $numberOfAdministrator == 1 ? 1 : 0;
    }


    /**
     * ページ番号が信頼しうる値かチェックする.
     *
     * @access private
     * @param  integer $pageno ページの番号（$_GETから入ってきた値）
     * @return integer $clean_pageno チェック後のページの番号
     */
    public function lfCheckPageNo($pageno)
    {
        $clean_pageno = '';

        // $pagenoが0以上の整数かチェック
        if (Utils::sfIsInt($pageno) && $pageno > 0) {
            $clean_pageno = $pageno;
        // 例外は全て1とする
        } else {
            $clean_pageno = 1;
        }

        return $clean_pageno;
    }
}
