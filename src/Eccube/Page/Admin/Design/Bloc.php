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

namespace Eccube\Page\Admin\Design;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\BlocHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * ブロック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Bloc extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'design/bloc.tpl';
        $this->tpl_subno_edit = 'bloc';
        $this->text_row = 13;
        $this->tpl_subno = 'bloc';
        $this->tpl_mainno = 'design';
        $this->tpl_maintitle = 'デザイン管理';
        $this->tpl_subtitle = 'ブロック設定';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
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
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $this->arrErr = $objFormParam->checkError();
        $is_error = (!Utils::isBlank($this->arrErr));

        $this->bloc_id = $objFormParam->getValue('bloc_id');
        $this->device_type_id = $objFormParam->getValue('device_type_id', DEVICE_TYPE_PC);

        /* @var $objBloc BlocHelper */
        $objBloc = Application::alias('eccube.helper.bloc', $this->device_type_id);

        switch ($this->getMode()) {
            // 登録/更新
            case 'confirm':
                if (!$is_error) {
                    $this->arrErr = $this->lfCheckError($objFormParam, $this->arrErr, $objBloc);
                    if (Utils::isBlank($this->arrErr)) {
                        $result = $this->doRegister($objFormParam, $objBloc);
                        if ($result !== false) {
                            $arrPram = array(
                                'bloc_id' => $result,
                                'device_type_id' => $this->device_type_id,
                                'msg' => 'on',
                            );

                            Application::alias('eccube.response')->reload($arrPram, true);
                            Application::alias('eccube.response')->actionExit();
                        }
                    }
                }
                break;

            // 削除
            case 'delete':
                if (!$is_error) {
                    if ($this->doDelete($objFormParam, $objBloc)) {
                        $arrPram = array(
                            'device_type_id' => $this->device_type_id,
                            'msg' => 'on',
                        );

                        Application::alias('eccube.response')->reload($arrPram, true);
                        Application::alias('eccube.response')->actionExit();
                    }
                }
                break;

            default:
                if (isset($_GET['msg']) && $_GET['msg'] == 'on') {
                    // 完了メッセージ
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
        }

        if (!$is_error) {
            // ブロック一覧を取得
            $this->arrBlocList = $objBloc->getList();
            // bloc_id が指定されている場合にはブロックデータの取得
            if (!Utils::isBlank($this->bloc_id)) {
                $arrBloc = $this->getBlocTemplate($this->bloc_id, $objBloc);
                $objFormParam->setParam($arrBloc);
            }
        } else {
            // 画面にエラー表示しないため, ログ出力
            GcUtils::gfPrintLog('Error: ' . print_r($this->arrErr, true));
        }
        $this->tpl_subtitle = $this->arrDeviceType[$this->device_type_id] . '＞' . $this->tpl_subtitle;
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('ブロックID', 'bloc_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('端末種別ID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ブロック名', 'bloc_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ファイル名', 'filename', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ブロックデータ', 'bloc_html');
    }

    /**
     * ブロックのテンプレートを取得する.
     *
     * @param  integer           $bloc_id ブロックID
     * @param  BlocHelper $objBloc BlocHelper インスタンス
     * @return array             ブロック情報の配列
     */
    public function getBlocTemplate($bloc_id, BlocHelper &$objBloc)
    {
        $arrBloc = $objBloc->getBloc($bloc_id);

        return $arrBloc;
    }

    /**
     * 登録を実行する.
     *
     * ファイルの作成に失敗した場合は, エラーメッセージを出力し,
     * データベースをロールバックする.
     *
     * @param  FormParam    $objFormParam FormParam インスタンス
     * @param  BlocHelper  $objBloc      BlocHelper インスタンス
     * @return integer|boolean 登録が成功した場合, 登録したブロックID;
     *                         失敗した場合 false
     */
    public function doRegister(&$objFormParam, BlocHelper &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        $result = $objBloc->save($arrParams);

        if (!$result) {
            $this->arrErr['err'] = '※ ブロックの書き込みに失敗しました<br />';
        }

        return $result;
    }

    /**
     * 削除を実行する.
     *
     * @param  FormParam   $objFormParam FormParam インスタンス
     * @param  BlocHelper $objBloc      BlocHelper インスタンス
     * @return boolean        登録が成功した場合 true; 失敗した場合 false
     */
    public function doDelete(&$objFormParam, BlocHelper &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        $result = $objBloc->delete($arrParams['bloc_id']);

        if (!$result) {
            $this->arrErr['err'] = '※ ブロックの削除に失敗しました<br />';
        }

        return $result;
    }

    /**
     * エラーチェックを行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        エラーメッセージの配列
     */
    public function lfCheckError(&$objFormParam, &$arrErr, BlocHelper &$objBloc)
    {
        $arrParams = $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrParams);
        $objErr->arrErr =& $arrErr;
        $objErr->doFunc(array('ブロック名', 'bloc_name', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('ファイル名', 'filename', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK','FILE_NAME_CHECK_BY_NOUPLOAD'));

        $where = 'filename = ?';
        $arrValues = array($arrParams['filename']);

        // 変更の場合は自ブロックを除外
        if (!Utils::isBlank($arrParams['bloc_id'])) {
            $where .= ' AND bloc_id <> ?';
            $arrValues[] = $arrParams['bloc_id'];
        }
        $arrBloc = $objBloc->getWhere($where, $arrValues);
        if (!Utils::isBlank($arrBloc)) {
            $objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別のファイル名を入力してください。<br />';
        }

        return $objErr->arrErr;
    }
}
