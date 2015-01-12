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

namespace Eccube\Page\Admin\Products;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\UploadFile;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CategoryHelper;
use Eccube\Framework\Helper\CsvHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * カテゴリ登録CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class UploadCSVCategory extends AbstractAdminPage
{
    /** エラー情報 **/
    public $arrErr;

    /** 表示用項目 **/
    public $arrTitle;

    /** 結果行情報 **/
    public $arrRowResult;

    /** エラー行情報 **/
    public $arrRowErr;

    /** TAGエラーチェックフィールド情報 */
    public $arrTagCheckItem;

    /** テーブルカラム情報 (登録処理用) **/
    public $arrRegistColumn;

    /** 登録フォームカラム情報 **/
    public $arrFormKeyList;
    /** @var string メインタイトル */
    public $tpl_maintitle;
    /** @var string サブタイトル */
    public $tpl_subtitle;
    /** @var string サブテンプレートナンバー */
    public $tpl_subno;

    /** @var array 許可タグ情報 */
    private $arrAllowedTag;
    /** @var int CSV ID */
    private $csv_id;
    /** @var  bool */
    private $tpl_is_format_default;
    /** @var  bool */
    private $tpl_is_update;
    /** @var  int */
    private $max_upload_csv_size;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/upload_csv_category.tpl';
        $this->tpl_mainno   = 'products';
        $this->tpl_subno    = 'upload_csv_category';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'カテゴリ登録CSV';
        $this->csv_id = '5';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrAllowedTag = $masterData->getMasterData('mtb_allowed_tag');
        $this->arrTagCheckItem = array();
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
        // CSV管理ヘルパー
        /* @var $objCSV CsvHelper */
        $objCSV = Application::alias('eccube.helper.csv');
        // CSV構造読み込み
        $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCSV->sfIsImportCSVFrame($arrCSVFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }
        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCSV->sfIsUpdateCSVFrame($arrCSVFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new UploadFile(CSV_TEMP_REALDIR, CSV_TEMP_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam, $arrCSVFrame);

        $this->max_upload_csv_size = Utils::getUnitDataSize(CSV_SIZE);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
            case 'csv_upload':
                $this->doUploadCsv($objFormParam, $objUpFile);
                break;
            default:
                break;
        }

    }

    /**
     * 登録/編集結果のメッセージをプロパティへ追加する
     *
     * @param  integer $line_count 行数
     * @param  string  $message    メッセージ
     * @return void
     */
    public function addRowResult($line_count, $message)
    {
        $this->arrRowResult[] = $line_count . '行目：' . $message;
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param  integer $line_count 行数
     * @param  string  $message    メッセージ
     * @return void
     */
    public function addRowErr($line_count, $message)
    {
        $this->arrRowErr[] = $line_count . '行目：' . $message;
    }

    /**
     * CSVアップロードを実行する
     *
     * @param  FormParam  $objFormParam
     * @param  UploadFile $objUpFile
     * @return void
     */
    public function doUploadCsv(&$objFormParam, &$objUpFile)
    {
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
        $arrErr = $objUpFile->checkExists();
        if (count($arrErr) > 0) {
            $this->arrErr = $arrErr;

            return;
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
        // CSVファイルの文字コード変換
        $enc_filepath = Utils::sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_REALDIR);
        // CSVファイルのオープン
        $fp = fopen($enc_filepath, 'r');
        // 失敗した場合はエラー表示
        if (!$fp) {
            Utils::sfDispError('');
        }

        // 登録先テーブル カラム情報の初期化
        $this->lfInitTableInfo();

        // 登録フォーム カラム情報
        $this->arrFormKeyList = $objFormParam->getKeyList();

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        $errFlag = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            // 行カウント
            $line_count++;
            // ヘッダ行はスキップ
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($line_count, '※ 項目数が' . $col_count . '個検出されました。項目数は' . $col_max_count . '個になります。');
                $errFlag = true;
                break;
            }
            // シーケンス配列を格納する。
            $objFormParam->setParam($arrCSV, true);
            // 入力値の変換
            $objFormParam->convParam();
            // <br>なしでエラー取得する。
            $arrCSVErr = $this->lfCheckError($objFormParam);

            // 入力エラーチェック
            if (count($arrCSVErr) > 0) {
                foreach ($arrCSVErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
                $errFlag = true;
                break;
            }

            $category_id = $this->lfRegisterCategory($line_count, $objFormParam);
            $this->addRowResult($line_count, 'カテゴリID：'.$category_id . ' / カテゴリ名：' . $objFormParam->getValue('category_name'));
        }

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/upload_csv_category_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();

            return;
        }

        $objQuery->commit();

        // カテゴリ件数を更新
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->countCategory($objQuery);

        return;
    }

    /**
     * ファイル情報の初期化を行う.
     *
     * @param UploadFile $objUpFile
     * @return void
     */
    public function lfInitFile(UploadFile &$objUpFile)
    {
        $objUpFile->addFile('CSVファイル', 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param FormParam $objFormParam
     * @param array $arrCSVFrame CSV構造設定配列
     * @return void
     */
    public function lfInitParam(FormParam &$objFormParam, &$arrCSVFrame)
    {
        // 固有の初期値調整
        $arrCSVFrame = $this->lfSetParamDefaultValue($arrCSVFrame);
        // CSV項目毎の処理
        foreach ($arrCSVFrame as $item) {
            if ($item['status'] == CSV_COLUMN_STATUS_FLG_DISABLE) continue;
            //サブクエリ構造の場合は AS名 を使用
            if (preg_match_all('/\(.+\) as (.+)$/i', $item['col'], $match, PREG_SET_ORDER)) {
                $col = $match[0][1];
            } else {
                $col = $item['col'];
            }
            // HTML_TAG_CHECKは別途実行なので除去し、別保存しておく
            if (strpos(strtoupper($item['error_check_types']), 'HTML_TAG_CHECK') !== FALSE) {
                $this->arrTagCheckItem[] = $item;
                $error_check_types = str_replace('HTML_TAG_CHECK', '', $item['error_check_types']);
            } else {
                $error_check_types = $item['error_check_types'];
            }
            $arrErrorCheckTypes = explode(',', $error_check_types);
            foreach ($arrErrorCheckTypes as $key => $val) {
                if (trim($val) == '') {
                    unset($arrErrorCheckTypes[$key]);
                } else {
                    $arrErrorCheckTypes[$key] = trim($val);
                }
            }
            // パラメーター登録
            $objFormParam->addParam(
                    $item['disp_name'],
                    $col,
                    constant($item['size_const_type']),
                    $item['mb_convert_kana_option'],
                    $arrErrorCheckTypes,
                    $item['default'],
                    $item['rw_flg'] != CSV_COLUMN_RW_FLG_READ_ONLY
                    );
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @param FormParam $objFormParam
     * @return array
     */
    public function lfCheckError(FormParam &$objFormParam)
    {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);
        // HTMLタグチェックの実行
        foreach ($this->arrTagCheckItem as $item) {
            $objErr->doFunc(array($item['disp_name'], $item['col'], $this->arrAllowedTag), array('HTML_TAG_CHECK'));
        }
        // このフォーム特有の複雑系のエラーチェックを行う
        if (count($objErr->arrErr) == 0) {
            $objErr->arrErr = $this->lfCheckErrorDetail($arrRet, $objErr->arrErr);
        }

        return $objErr->arrErr;
    }

    /**
     * 保存先テーブル情報の初期化を行う.
     *
     * @return void
     */
    public function lfInitTableInfo()
    {
        $objQuery = Application::alias('eccube.query');
        $this->arrRegistColumn = $objQuery->listTableFields('dtb_category');
    }

    /**
     * カテゴリ登録を行う.
     *
     * FIXME: 登録の実処理自体は、LC_Page_Admin_Products_Categoryと共通化して欲しい。
     *
     * @param  integer $line 処理中の行数
     * @param FormParam $objFormParam
     * @return integer        カテゴリID
     */
    public function lfRegisterCategory($line, FormParam &$objFormParam)
    {
        // 登録データ対象取得
        $arrList = $objFormParam->getDbArray();

        // 登録情報を生成する。
        // テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = Utils::sfArrayIntersectKeys($arrList, $this->arrRegistColumn);

        // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
        $sqlval = $this->lfSetCategoryDefaultData($sqlval);

        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');
        $category_id = $objCategory->save($sqlval);

        return $category_id;
    }

    /**
     * 初期値の設定
     *
     * @param  array $arrCSVFrame CSV構造配列
     * @return array $arrCSVFrame CSV構造配列
     */
    public function lfSetParamDefaultValue(&$arrCSVFrame)
    {
        foreach ($arrCSVFrame as $key => $val) {
            switch ($val['col']) {
                case 'parent_category_id':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                case 'del_flg':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                default:
                    break;
            }
        }

        return $arrCSVFrame;
    }

    /**
     * データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @return array $sqlval 登録情報配列
     */
    public function lfSetCategoryDefaultData(&$sqlval)
    {
        if ($sqlval['del_flg'] == '') {
            $sqlval['del_flg'] = '0'; //有効
        }
        if ($sqlval['creator_id'] == '') {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }
        if ($sqlval['parent_category_id'] == '') {
            $sqlval['parent_category_id'] = (string) '0';
        }

        return $sqlval;
    }

    /**
     * このフォーム特有の複雑な入力チェックを行う.
     *
     * @param array $item 確認対象データ
     * @param array $arrErr エラー配列
     * @return array エラー配列
     */
    public function lfCheckErrorDetail($item, $arrErr)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');
        // スタティック変数を初期化
        $objCategory->getTree(true);
        /*
        // カテゴリIDの存在チェック
        if (!$this->lfIsDbRecord('dtb_category', 'category_id', $item)) {
            $arrErr['category_id'] = '※ 指定のカテゴリIDは、登録されていません。';
        }
        */
        // 親カテゴリIDの存在チェック
        if (array_search('parent_category_id', $this->arrFormKeyList) !== FALSE
            && $item['parent_category_id'] != ''
            && $item['parent_category_id'] != '0'
            && !$objCategory->get($item['parent_category_id'])
        ) {
            $arrErr['parent_category_id'] = '※ 指定の親カテゴリID(' . $item['parent_category_id'] . ')は、存在しません。';
        }
        // 削除フラグのチェック
        if (array_search('del_flg', $this->arrFormKeyList) !== FALSE
            && $item['del_flg'] != ''
        ) {
            if (!($item['del_flg'] == '0' or $item['del_flg'] == '1')) {
                $arrErr['del_flg'] = '※ 削除フラグは「0」(有効)、「1」(削除)のみが有効な値です。';
            }
        }
        // 重複チェック 同じカテゴリ内に同名の存在は許可されない
        if (array_search('category_name', $this->arrFormKeyList) !== FALSE
            && $item['category_name'] != ''
        ) {
            $exists = false;
            $arrBrother = $objCategory->getTreeBranch($item['parent_category_id']);
            foreach ($arrBrother as $brother) {
                if ($brother['category_name'] == $item['category_name'] && $brother['category_id'] != $item['category_id']) {
                    $exists = true;
                }
            }
            if ($exists) {
                $arrErr['category_name'] = '※ 既に同名のカテゴリが存在します。';
            }
        }
        // 登録数上限チェック
        $count = count($objCategory->getList());
        if ($count >= CATEGORY_MAX) {
            $item['category_name'] = '※ カテゴリの登録最大数を超えました。';
        }

        if (array_search('parent_category_id', $this->arrFormKeyList) !== FALSE
                and $item['parent_category_id'] != '') {
            // 階層上限チェック
            $arrParent = $objCategory->get($item['parent_category_id']);
            if ($arrParent['level'] >= LEVEL_MAX) {
                $arrErr['parent_category_id'] = '※ ' . LEVEL_MAX . '階層以上の登録はできません。';
            }
            // 親カテゴリー論理チェック
            if (array_search('category_id', $this->arrFormKeyList) !== FALSE
                and $item['category_id'] != '') {
                $arrTrail = $objCategory->getTreeTrail($item['parent_category_id'], true);
                foreach ($arrTrail as $trailId) {
                    if ($trailId == $item['category_id']) {
                        $arrErr['parent_category_id'] = '※ 再帰的な親カテゴリーの指定はできません。';
                    }
                }
            }
        }


        return $arrErr;
    }
}
