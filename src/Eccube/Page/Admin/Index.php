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
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Session;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * 管理者ログイン のページクラス.
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
        $this->tpl_mainpage = 'login.tpl';
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
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');

        switch ($this->getMode()) {
            case 'login':
                //ログイン処理
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (Utils::isBlank($this->arrErr)) {
                    $this->lfDoLogin($objFormParam->getValue('login_id'));

                    Application::alias('eccube.response')->sendRedirect(ADMIN_HOME_URLPATH);
                } else {
                    // ブルートフォースアタック対策
                    // ログイン失敗時に遅延させる
                    sleep(LOGIN_RETRY_INTERVAL);

                    Utils::sfDispError(LOGIN_ERROR);
                }
                break;
            default:
                break;
        }

        // 管理者ログインテンプレートフレームの設定
        $this->setTemplate(LOGIN_FRAME);
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('ID', 'login_id', ID_MAX_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('PASSWORD', 'password', ID_MAX_LEN, '', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * パラメーターのエラーチェック
     *
     * TODO: ブルートフォースアタック対策チェックの実装
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array $arrErr エラー配列
     */
    public function lfCheckError(&$objFormParam)
    {
        // 書式チェック
        $arrErr = $objFormParam->checkError();
        if (Utils::isBlank($arrErr)) {
            $arrForm = $objFormParam->getHashArray();
            // ログインチェック
            if (!$this->lfIsLoginMember($arrForm['login_id'], $arrForm['password'])) {
                $arrErr['password'] = 'ログイン出来ません。';
                $this->lfSetIncorrectData($arrForm['login_id']);
            }
        }

        return $arrErr;
    }

    /**
     * 有効な管理者ID/PASSかどうかチェックする
     *
     * @param  string  $login_id ログインID文字列
     * @param  string  $pass     ログインパスワード文字列
     * @return boolean ログイン情報が有効な場合 true
     */
    public function lfIsLoginMember($login_id, $pass)
    {
        $objQuery = Application::alias('eccube.query');
        //パスワード、saltの取得
        $cols = 'password, salt';
        $table = 'dtb_member';
        $where = 'login_id = ? AND del_flg <> 1 AND work = 1';
        $arrData = $objQuery->getRow($cols, $table, $where, array($login_id));
        if (Utils::isBlank($arrData)) {
            return false;
        }
        // ユーザー入力パスワードの判定
        if (Utils::sfIsMatchHashPassword($pass, $arrData['password'], $arrData['salt'])) {
            return true;
        }

        return false;
    }

    /**
     * 管理者ログイン設定処理
     *
     * @param  string $login_id ログインID文字列
     * @return void
     */
    public function lfDoLogin($login_id)
    {
        $objQuery = Application::alias('eccube.query');
        //メンバー情報取得
        $cols = 'member_id, authority, login_date, name';
        $table = 'dtb_member';
        $where = 'login_id = ?';
        $arrData = $objQuery->getRow($cols, $table, $where, array($login_id));
        // セッション登録
        $sid = $this->lfSetLoginSession($arrData['member_id'], $login_id, $arrData['authority'], $arrData['name'], $arrData['login_date']);
        // ログイン情報記録
        $this->lfSetLoginData($sid, $arrData['member_id'], $login_id, $arrData['authority'], $arrData['login_date']);
    }

    /**
     * ログイン情報セッション登録
     *
     * @param  integer $member_id  メンバーID
     * @param  string  $login_id   ログインID文字列
     * @param  integer $authority  権限ID
     * @param  string  $login_name ログイン表示名
     * @param  string  $last_login 最終ログイン日時(YYYY/MM/DD HH:ii:ss形式) またはNULL
     * @return string  $sid 設定したセッションのセッションID
     */
    public function lfSetLoginSession($member_id, $login_id, $authority, $login_name, $last_login)
    {
        // Session Fixation対策
        SessionHelper::regenerateSID();

        $objSess = new Session();
        // 認証済みの設定
        $objSess->SetSession('cert', CERT_STRING);
        $objSess->SetSession('member_id', $member_id);
        $objSess->SetSession('login_id', $login_id);
        $objSess->SetSession('authority', $authority);
        $objSess->SetSession('login_name', $login_name);
        $objSess->SetSession('uniqid', $objSess->getUniqId());
        if (Utils::isBlank($last_login)) {
            $objSess->SetSession('last_login', date('Y-m-d H:i:s'));
        } else {
            $objSess->SetSession('last_login', $last_login);
        }

        return $objSess->GetSID();
    }

    /**
     * ログイン情報の記録
     *
     * @param  string   $sid        セッションID
     * @param  integer $member_id  メンバーID
     * @param  string  $login_id   ログインID文字列
     * @param  integer $authority  権限ID
     * @param  string  $last_login 最終ログイン日時(YYYY/MM/DD HH:ii:ss形式) またはNULL
     * @return void
     */
    public function lfSetLoginData($sid, $member_id, $login_id, $authority, $last_login)
    {
        // ログイン記録ログ出力
        $str_log = "login: user=$login_id($member_id) auth=$authority "
                    . "lastlogin=$last_login sid=$sid";
        GcUtils::gfPrintLog($str_log);

        // 最終ログイン日時更新
        $objQuery = Application::alias('eccube.query');
        $sqlval = array();
        $sqlval['login_date'] = date('Y-m-d H:i:s');
        $table = 'dtb_member';
        $where = 'member_id = ?';
        $objQuery->update($table, $sqlval, $where, array($member_id));
    }

    /**
     * ログイン失敗情報の記録
     *
     * TODO: ブルートフォースアタック対策の実装
     *
     * @param  string $error_login_id ログイン失敗時に投入されたlogin_id文字列
     * @return void
     */
    public function lfSetIncorrectData($error_login_id)
    {
        GcUtils::gfPrintLog($error_login_id . ' password incorrect.');
    }
}
