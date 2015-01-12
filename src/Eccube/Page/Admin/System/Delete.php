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
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * メンバー削除 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Delete extends AbstractAdminPage
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
        $objFormParam = Application::alias('eccube.form_param');

        // パラメーターの初期化
        $this->initParam($objFormParam, $_GET);

        // パラメーターの検証
        if ($objFormParam->checkError()
            || !Utils::sfIsInt($id = $objFormParam->getValue('id'))) {
            GcUtils::gfPrintLog("error id=$id");
            Utils::sfDispError(INVALID_MOVE_ERRORR);
        }

        $id = $objFormParam->getValue('id');

        // レコードの削除
        $this->deleteMember($id);

        // リダイレクト
        $url = $this->getLocation(ADMIN_SYSTEM_URLPATH)
             . '?pageno=' . $objFormParam->getValue('pageno');

        Application::alias('eccube.response')->sendRedirect($url);
    }

    /**
     * パラメーター初期化.
     *
     * @param  FormParam $objFormParam
     * @param  array  $arrParams    $_GET値
     * @return void
     */
    public function initParam(&$objFormParam, &$arrParams)
    {
        $objFormParam->addParam('pageno', 'pageno', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('id', 'id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrParams);
    }

    /**
     * メンバー情報削除の為の制御.
     *
     * @param  integer $id 削除対象のmember_id
     * @return void
     */
    public function deleteMember($id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        $this->renumberRank($objQuery, $id);
        $this->deleteRecode($objQuery, $id);

        $objQuery->commit();
    }

    /**
     * ランキングの振り直し.
     *
     * @param  Query      $objQuery
     * @param  integer     $id       削除対象のmember_id
     * @return void|UPDATE の結果フラグ
     */
    public function renumberRank(&$objQuery, $id)
    {
        // ランクの取得
        $where1 = 'member_id = ?';
        $rank = $objQuery->get('rank', 'dtb_member', $where1, array($id));

        // Updateする値を作成する.
        $where2 = 'rank > ? AND del_flg <> 1';

        // UPDATEの実行 - 削除したレコードより上のランキングを下げてRANKの空きを埋める。
        return $objQuery->update('dtb_member', array(), $where2, array($rank), array('rank' => 'rank-1'));
    }

    /**
     * レコードの削除(削除フラグをONにする).
     *
     * @param  Query      $objQuery
     * @param  integer     $id       削除対象のmember_id
     * @return void|UPDATE の結果フラグ
     */
    public function deleteRecode(&$objQuery, $id)
    {
        // Updateする値を作成する.
        $sqlVal = array();
        $sqlVal['rank'] = 0;
        $sqlVal['del_flg'] = 1;
        $where = 'member_id = ?';

        // UPDATEの実行 - ランクを最下位にする、DELフラグON
        return $objQuery->update('dtb_member', $sqlVal, $where, array($id));
    }
}
