<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 会員規約設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Kiyaku extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/kiyaku.tpl';
        $this->tpl_subno = 'kiyaku';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '会員規約設定';
        $this->tpl_mainno = 'basis';
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
        $objKiyaku = new SC_Helper_Kiyaku_Ex();

        $mode = $this->getMode();
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($mode, $objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $kiyaku_id = $objFormParam->getValue('kiyaku_id');

        // 要求判定
        switch ($mode) {
            // 編集処理
            case 'confirm':
                // エラーチェック
                $this->arrErr = $this->lfCheckError($objFormParam, $objKiyaku);
                if (!SC_Utils_Ex::isBlank($this->arrErr['kiyaku_id'])) {
                    trigger_error('', E_USER_ERROR);

                    return;
                }

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // POST値の引き継ぎ
                    $arrParam = $objFormParam->getHashArray();
                    // 登録実行
                    $res_kiyaku_id = $this->doRegist($kiyaku_id, $arrParam, $objKiyaku);
                    if ($res_kiyaku_id !== FALSE) {
                        // 完了メッセージ
                        $kiyaku_id = $res_kiyaku_id;
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }

                // 編集中の規約IDを渡す
                $this->tpl_kiyaku_id = $kiyaku_id;
                break;
            // 削除
            case 'delete':
                $objKiyaku->deleteKiyaku($kiyaku_id);
                break;

            // 編集前処理
            case 'pre_edit':
                // 編集項目を取得する。
                $arrKiyakuData = $objKiyaku->getKiyaku($kiyaku_id);
                $objFormParam->setParam($arrKiyakuData);

                // 編集中の規約IDを渡す
                $this->tpl_kiyaku_id = $kiyaku_id;
                break;

            case 'down':
                $objKiyaku->rankDown($kiyaku_id);

                // 再表示
                $this->objDisplay->reload();
                break;

            case 'up':
                $objKiyaku->rankUp($kiyaku_id);

                // 再表示
                $this->objDisplay->reload();
                break;

            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

        // 規約一覧を取得
        $this->arrKiyaku = $objKiyaku->getList();
    }

    /**
     * 登録処理を実行.
     *
     * @param  integer  $kiyaku_id
     * @param  array    $sqlval
     * @param  object   $objKiyaku
     * @return multiple
     */
    public function doRegist($kiyaku_id, $sqlval, SC_Helper_Kiyaku_Ex &$objKiyaku)
    {
        $sqlval['kiyaku_id'] = $kiyaku_id;
        $sqlval['creator_id'] = $_SESSION['member_id'];

        return $objKiyaku->saveKiyaku($sqlval);
    }

    public function lfInitParam($mode, &$objFormParam)
    {
        switch ($mode) {
            case 'confirm':
            case 'pre_edit':
                $objFormParam->addParam('規約タイトル', 'kiyaku_title', SMTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('規約内容', 'kiyaku_text', MLTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('規約ID', 'kiyaku_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            case 'delete':
            case 'down':
            case 'up':
            default:
                $objFormParam->addParam('規約ID', 'kiyaku_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
        }
    }

    /**
     * 入力エラーチェック
     *
     * @param  string $mode
     * @param  object $objKiyaku
     * @return array
     */
    public function lfCheckError($objFormParam, SC_Helper_Kiyaku_Ex &$objKiyaku)
    {
        $arrErr = $objFormParam->checkError();
        $arrForm = $objFormParam->getHashArray();

        $isTitleExist = $objKiyaku->isTitleExist($arrForm['kiyaku_title'], $arrForm['kiyaku_id']);
        // 編集中のレコード以外に同じ名称が存在する場合
        if ($isTitleExist) {
            $arrErr['name'] = '※ 既に同じ内容の登録が存在します。<br />';
        }

        return $arrErr;
    }
}
