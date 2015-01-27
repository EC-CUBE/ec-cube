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
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Input extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'system/input.tpl';

        // マスターデータから権限配列を取得
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');
        $this->arrWORK = $masterData->getMasterData('mtb_work');

        $this->tpl_subtitle = 'メンバー登録/編集';
        $this->httpCacheControl('nocache');
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

        // ページ送りの処理 $_REQUEST['pageno']が信頼しうる値かどうかチェックする。
        $this->tpl_pageno = $this->lfCheckPageNo($_REQUEST['pageno']);

        $arrErr = array();
        $arrForm = array();

        switch ($this->getMode()) {
            case 'new':
                // パラメーターの初期化
                $this->initForm($objFormParam, $_POST);

                // エラーチェック
                $arrErr = $this->validateData($objFormParam, $_POST, $this->getMode());
                $this->arrForm = $objFormParam->getHashArray();

                if (Utils::isBlank($arrErr)) {
                    $this->insertMemberData($this->arrForm);
                    // 親ウィンドウを更新後、自ウィンドウを閉じる。
                    $url = ADMIN_SYSTEM_URLPATH . '?pageno=' . $this->arrForm['pageno'];
                    $this->tpl_onload = "eccube.changeParentUrl('".$url."'); window.close();";
                } else {
                    // 入力された値を保持する
                    $this->tpl_mode      = $this->getMode();
                    $this->tpl_member_id = '';
                    $this->tpl_old_login_id = '';

                    // パスワードは保持しない
                    $this->arrForm['password'] = '';
                    $this->arrForm['password02'] = '';
                    // エラー情報をセットする
                    $this->arrErr = $arrErr;
                }
                break;

            case 'edit':
                // パラメーターの初期化
                $this->initForm($objFormParam, $_POST, $this->getMode());

                // エラーチェック
                $arrErr = $this->validateData($objFormParam, $_POST, $this->getMode());
                $this->arrForm = $objFormParam->getHashArray();

                if (Utils::isBlank($arrErr)) {
                    $this->updateMemberData($this->arrForm['member_id'], $this->arrForm);
                    // 親ウィンドウを更新後、自ウィンドウを閉じる。
                    $url = ADMIN_SYSTEM_URLPATH . '?pageno=' . $this->arrForm['pageno'];
                    $this->tpl_onload = "eccube.changeParentUrl('".$url."'); window.close();";
                } else {
                    // 入力された値を保持する
                    $this->tpl_mode      = $this->getMode();
                    $this->tpl_member_id = $this->arrForm['member_id'];
                    $this->tpl_old_login_id = $this->arrForm['old_login_id'];

                    // パスワードは保持しない
                    $this->arrForm['password'] = '';
                    $this->arrForm['password02'] = '';
                    // エラー情報をセットする
                    $this->arrErr = $arrErr;
                }
                break;

            default:

                // $_GET['id']（member_id）が登録済みのものかチェック。
                // 登録されていない場合は不正なものとして、新規扱いとする。
                $clean_id = '';
                $clean_mode_flg = 'new';

                // idが0より大きい数字で整数の場合
                if (isset($_GET['id']) && Utils::sfIsInt($_GET['id']) && $_GET['id'] > 0) {
                    if ($this->memberDataExists('member_id = ? AND del_flg = 0', $_GET['id'])) {
                        $clean_id = $_GET['id'];
                        $clean_mode_flg = 'edit';
                    }
                }

                switch ($clean_mode_flg) {
                    case 'edit':
                        $this->tpl_mode      = $clean_mode_flg;
                        $this->tpl_member_id = $clean_id;
                        $this->tpl_onfocus   = 'eccube.clearValue(this.name);';
                        $this->arrForm       = $this->getMemberData($clean_id);
                        $this->arrForm['password'] = DEFAULT_PASSWORD;
                        $this->arrForm['password02'] = DEFAULT_PASSWORD;
                        $this->tpl_old_login_id    = $this->arrForm['login_id'];
                        break;

                    case 'new':
                    default:
                        $this->tpl_mode = $clean_mode_flg;
                        $this->arrForm['authority'] = -1;
                        break;
                }
                break;
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * フォームパラメーター初期化
     *
     * @param  FormParam $objFormParam
     * @param  array  $arrParams    $_POST値
     * @param  string $mode         editの時は指定
     * @return void
     */
    public function initForm(&$objFormParam, &$arrParams, $mode = '')
    {
        $objFormParam->addParam('メンバーID', 'member_id', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('名前', 'name', STEXT_LEN, 'KV', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('所属', 'department', STEXT_LEN, 'KV', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ログインID', 'login_id', '', '', array('EXIST_CHECK', 'ALNUM_CHECK'));
        $objFormParam->addParam('変更前ログインID', 'old_login_id', '', '', array('ALNUM_CHECK'));
        if ($mode == 'edit' && $arrParams['password'] == DEFAULT_PASSWORD) {
            $objFormParam->addParam('パスワード', 'password', '', '', array('EXIST_CHECK'));
            $objFormParam->addParam('パスワード(確認)', 'password02', '', '', array('EXIST_CHECK'));
        } else {
            $objFormParam->addParam('パスワード', 'password', '', '', array('EXIST_CHECK', 'GRAPH_CHECK'));
            $objFormParam->addParam('パスワード(確認)', 'password02', '', '', array('EXIST_CHECK', 'GRAPH_CHECK'));
        }
        $objFormParam->addParam('権限', 'authority', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('稼働/非稼働', 'work', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページ', 'pageno', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

        $objFormParam->setParam($arrParams);
        $objFormParam->convParam();
    }

    /**
     * パラメーターの妥当性検証を行う.
     *
     * @param void
     * @param string|null $mode
     * @param FormParam $objFormParam
     * @return array エラー情報の連想配列
     */
    public function validateData(&$objFormParam, &$arrParams, $mode)
    {
        $arrErr = $objFormParam->checkError();
        if (isset($arrErr) && count($arrErr) > 0) return $arrErr;

        // ログインID・パスワードの文字数チェック
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error');
        if ($mode == 'new') {
            $objErr->doFunc(array('パスワード', 'password', ID_MIN_LEN, ID_MAX_LEN), array('NUM_RANGE_CHECK'));
            $objErr->doFunc(array('ログインID', 'login_id', ID_MIN_LEN, ID_MAX_LEN), array('NUM_RANGE_CHECK'));
        } elseif ($mode == 'edit') {
            $objErr->doFunc(array('パスワード', 'password', ID_MIN_LEN, ID_MAX_LEN), array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));
            $objErr->doFunc(array('ログインID', 'login_id', ID_MIN_LEN, ID_MAX_LEN), array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));
        }
        $objErr->doFunc(array('パスワード', 'パスワード(確認)', 'password', 'password02'), array('EQUAL_CHECK'));

        $arrErr = $objErr->arrErr;

        switch ($mode) {
            case 'new':
                // 管理者名が登録済みでないか
                if ($this->memberDataExists('name = ? AND del_flg = 0', $arrParams['name'])) {
                    $arrErr['name'] = '既に登録されている名前なので利用できません。<br>';
                }
                // ログインIDが登録済みでないか
                if ($this->memberDataExists('login_id = ? AND del_flg = 0', $arrParams['login_id'])) {
                    $arrErr['login_id'] = '既に登録されているIDなので利用できません。<br>';
                }
                break;
            case 'edit':
                // ログインIDが変更されている場合はチェックする。
                if ($arrParams['login_id'] != $arrParams['old_login_id']) {
                    // ログインIDが登録済みでないか
                    if ($this->memberDataExists('login_id = ? AND del_flg = 0', $arrParams['login_id'])) {
                        $arrErr['login_id'] = '既に登録されているIDなので利用できません。<br>';
                    }
                }
                break;
        }

        return $arrErr;
    }

    /**
     * DBからmember_idに対応する管理者データを取得する
     *
     * @param  integer $id メンバーID
     * @return array   管理者データの連想配列, 無い場合は空の配列を返す
     */
    public function getMemberData($id)
    {
        $table   = 'dtb_member';
        $columns = 'name,department,login_id,authority, work';
        $where   = 'member_id = ?';

        $objQuery = Application::alias('eccube.query');

        return $objQuery->getRow($columns, $table, $where, array($id));
    }

    /**
     *  値が登録済みかどうかを調べる
     *
     * @param  string  $where WHERE句
     * @param  string  $val   検索したい値
     * @return boolean 登録済みならtrue, 未登録ならfalse
     */
    public function memberDataExists($where, $val)
    {
        $objQuery = Application::alias('eccube.query');

        $table = 'dtb_member';

        $exists = $objQuery->exists($table, $where, array($val));

        return $exists;
    }

    /**
     * ページ番号が信頼しうる値かチェックする.
     *
     * @access private
     * @param  integer $pageno ページの番号
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

    /**
     * 入力された管理者データをInsertする.
     *
     * @param array 管理者データの連想配列
     * @return void
     */
    public function insertMemberData($arrMemberData)
    {
        $objQuery = Application::alias('eccube.query');

        // INSERTする値を作成する.
        $salt                  = Utils::sfGetRandomString(10);
        $sqlVal = array();
        $sqlVal['name']        = $arrMemberData['name'];
        $sqlVal['department']  = $arrMemberData['department'];
        $sqlVal['login_id']    = $arrMemberData['login_id'];
        $sqlVal['password']    = Utils::sfGetHashString($arrMemberData['password'], $salt);
        $sqlVal['salt']        = $salt;
        $sqlVal['authority']   = $arrMemberData['authority'];
        $sqlVal['rank']        = $objQuery->max('rank', 'dtb_member') + 1;
        $sqlVal['work']        = $arrMemberData['work'];
        $sqlVal['del_flg']     = '0'; // 削除フラグをOFFに設定
        $sqlVal['creator_id']  = $_SESSION['member_id'];
        $sqlVal['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlVal['update_date'] = 'CURRENT_TIMESTAMP';

        // INSERTの実行
        $sqlVal['member_id'] = $objQuery->nextVal('dtb_member_member_id');
        $objQuery->insert('dtb_member', $sqlVal);
    }

    /**
     * 管理者データをUpdateする.
     *
     * @param array 管理者データの連想配列
     * @return void
     */
    public function updateMemberData($member_id, $arrMemberData)
    {
        $objQuery = Application::alias('eccube.query');

        // Updateする値を作成する.
        $sqlVal = array();
        $sqlVal['name']        = $arrMemberData['name'];
        $sqlVal['department']  = $arrMemberData['department'];
        $sqlVal['login_id']    = $arrMemberData['login_id'];
        $sqlVal['authority']   = $arrMemberData['authority'];
        $sqlVal['work']   = $arrMemberData['work'];
        $sqlVal['update_date'] = 'CURRENT_TIMESTAMP';
        if ($arrMemberData['password'] != DEFAULT_PASSWORD) {
            $salt = Utils::sfGetRandomString(10);
            $sqlVal['salt']     = $salt;
            $sqlVal['password'] = Utils::sfGetHashString($arrMemberData['password'], $salt);
        }

        $where = 'member_id = ?';

        // UPDATEの実行
        $objQuery->update('dtb_member', $sqlVal, $where, array($member_id));
    }
}
