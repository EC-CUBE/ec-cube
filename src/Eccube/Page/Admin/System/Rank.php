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
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Rank extends AbstractAdminPage
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
        // チェック後のデータを格納
        $arrClean = array();

        // $_GET['move'] が想定値かどうかチェック
        switch ($_GET['move']) {
            case 'up':
            case 'down':
                $arrClean['move'] = $_GET['move'];
                break;
            default:
                $arrClean['move'] = '';
                break;
        }

        // 正当な数値であればOK
        if (Utils::sfIsInt($_GET['id'])) {
            $arrClean['id'] = $_GET['id'];

            switch ($arrClean['move']) {
                case 'up':
                    $this->lfRunkUp($arrClean['id']);
                    break;

                case 'down':
                    $this->lfRunkDown($arrClean['id']);
                    break;

                default:
                    break;
            }
        // エラー処理
        } else {
            GcUtils::gfPrintLog('error id='.$_GET['id']);
        }

        // ページの表示
        Application::alias('eccube.response')->sendRedirect(ADMIN_SYSTEM_URLPATH);
    }

    // ランキングを上げる。
    public function lfRunkUp($id)
    {
        $objQuery = Application::alias('eccube.query');

        // 自身のランクを取得する。
        $rank = $objQuery->getOne('SELECT rank FROM dtb_member WHERE member_id = ?', array($id));

        // ランクの最大値を取得する。
        $maxno = $objQuery->getOne('SELECT max(rank) FROM dtb_member');
        // ランクが最大値よりも小さい場合に実行する。
        if ($rank < $maxno) {
            // ランクがひとつ上のIDを取得する。
            $sqlse = 'SELECT member_id FROM dtb_member WHERE rank = ?';
            $up_id = $objQuery->getOne($sqlse, $rank + 1);

            // Updateする値を作成する.
            $sqlVal1 = array();
            $sqlVal2 = array();
            $sqlVal1['rank'] = $rank + 1;
            $sqlVal2['rank'] = $rank;
            $where = 'member_id = ?';

            // ランク入れ替えの実行
            $objQuery->begin();
            $objQuery->update('dtb_member', $sqlVal1, $where, array($id));
            $objQuery->update('dtb_member', $sqlVal2, $where, array($up_id));
            $objQuery->commit();
        }
    }

    // ランキングを下げる。
    public function lfRunkDown($id)
    {
        $objQuery = Application::alias('eccube.query');

        // 自身のランクを取得する。
        $rank = $objQuery->getOne('SELECT rank FROM dtb_member WHERE member_id = ?', array($id));
        // ランクの最小値を取得する。
        $minno = $objQuery->getOne('SELECT min(rank) FROM dtb_member');
        // ランクが最大値よりも大きい場合に実行する。
        if ($rank > $minno) {
            // ランクがひとつ下のIDを取得する。
            $sqlse = 'SELECT member_id FROM dtb_member WHERE rank = ?';
            $down_id = $objQuery->getOne($sqlse, $rank - 1);

            // Updateする値を作成する.
            $sqlVal1 = array();
            $sqlVal2 = array();
            $sqlVal1['rank'] = $rank - 1;
            $sqlVal2['rank'] = $rank;
            $where = 'member_id = ?';

            // ランク入れ替えの実行
            $objQuery->begin();
            $objQuery->update('dtb_member', $sqlVal1, $where, array($id));
            $objQuery->update('dtb_member', $sqlVal2, $where, array($down_id));
            $objQuery->commit();
        }
    }
}
