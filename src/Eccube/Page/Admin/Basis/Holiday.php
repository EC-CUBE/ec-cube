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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Helper\HolidayHelper;
use Eccube\Framework\Util\Utils;

/**
 * 定休日管理のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Holiday extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/holiday.tpl';
        $this->tpl_subno = 'holiday';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '定休日管理';
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
        /* @var $objHoliday HolidayHelper */
        $objHoliday = Application::alias('eccube.helper.holiday');

        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date');
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        $mode = $this->getMode();

        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($mode, $objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $holiday_id = $objFormParam->getValue('holiday_id');

        // 要求判定
        switch ($mode) {
            // 編集処理
            case 'edit':
                $this->arrErr = $this->lfCheckError($objFormParam, $objHoliday);
                if (!Utils::isBlank($this->arrErr['holiday_id'])) {
                    trigger_error('', E_USER_ERROR);

                    return;
                }

                if (count($this->arrErr) <= 0) {
                    // POST値の引き継ぎ
                    $arrParam = $objFormParam->getHashArray();
                    // 登録実行
                    $res_holiday_id = $this->doRegist($holiday_id, $arrParam, $objHoliday);
                    if ($res_holiday_id !== FALSE) {
                        // 完了メッセージ
                        $holiday_id = $res_holiday_id;
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                // POSTデータを引き継ぐ
                $this->tpl_holiday_id = $holiday_id;

                break;
            // 削除
            case 'delete':
                $objHoliday->delete($holiday_id);
                break;
            // 編集前処理
            case 'pre_edit':
                // 編集項目を取得する。
                $arrHolidayData = $objHoliday->get($holiday_id);
                $objFormParam->setParam($arrHolidayData);

                // POSTデータを引き継ぐ
                $this->tpl_holiday_id = $holiday_id;
                break;
            case 'down':
                $objHoliday->rankDown($holiday_id);

                // 再表示
                $this->objDisplay->reload();
                break;
            case 'up':
                $objHoliday->rankUp($holiday_id);

                // 再表示
                $this->objDisplay->reload();
                break;
            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

        $this->arrHoliday = $objHoliday->getList();
    }

    /**
     * 登録処理を実行.
     *
     * @param  integer  $holiday_id
     * @param  array    $sqlval
     * @param  HolidayHelper   $objHoliday
     * @return multiple
     */
    public function doRegist($holiday_id, $sqlval, HolidayHelper $objHoliday)
    {
        $sqlval['holiday_id'] = $holiday_id;
        $sqlval['creator_id'] = $_SESSION['member_id'];

        return $objHoliday->save($sqlval);
    }

    /**
     * @param string|null $mode
     * @param FormParam $objFormParam
     */
    public function lfInitParam($mode, &$objFormParam)
    {
        switch ($mode) {
            case 'edit':
            case 'pre_edit':
                $objFormParam->addParam('タイトル', 'title', STEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('月', 'month', INT_LEN, 'n', array('SELECT_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('日', 'day', INT_LEN, 'n', array('SELECT_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('定休日ID', 'holiday_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            case 'delete':
            case 'down':
            case 'up':
            default:
                $objFormParam->addParam('定休日ID', 'holiday_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
        }
    }

    /**
     * 入力エラーチェック
     *
     * @param  FormParam $objFormParam
     * @param  HolidayHelper $objHoliday
     * @return array
     */
    public function lfCheckError(&$objFormParam, HolidayHelper &$objHoliday)
    {
        $arrErr = $objFormParam->checkError();
        $arrForm = $objFormParam->getHashArray();

        // 日付の妥当性チェック
        // 閏年への対応.
        if ($arrForm['month'] == 2 && $arrForm['day'] == 29) {
            $valid_date = true;
        } else {
            $valid_date = checkdate($arrForm['month'], $arrForm['day'], date('Y'));
        }
        if (!$valid_date) {
            $arrErr['date'] = '※ 妥当な日付ではありません。<br />';
        }

        // 編集中のレコード以外に同じ日付が存在する場合
        if ($objHoliday->isDateExist($arrForm['month'], $arrForm['day'], $arrForm['holiday_id'])) {
            $arrErr['date'] = '※ 既に同じ日付の登録が存在します。<br />';
        }

        return $arrErr;
    }
}
