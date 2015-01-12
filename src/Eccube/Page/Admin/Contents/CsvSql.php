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

namespace Eccube\Page\Admin\Contents;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\CsvHelper;
use Eccube\Framework\Util\Utils;

/**
 * CSV 出力項目設定(高度な設定)のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class CsvSql extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'contents/csv_sql.tpl';
        $this->tpl_subno = 'csv';
        $this->tpl_subno_csv = 'csv_sql';
        $this->tpl_mainno = 'contents';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'CSV出力設定';
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
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->setParam($_GET);
        $objFormParam->convParam();
        $this->arrForm = $objFormParam->getHashArray();
        switch ($this->getMode()) {
            // データの登録
            case 'confirm':
                $this->arrErr = $this->lfCheckConfirmError($objFormParam);
                if (Utils::isBlank($this->arrErr)) {
                    // データの更新
                    $this->arrForm['sql_id'] = $this->lfUpdData($objFormParam->getValue('sql_id'), $objFormParam->getDbArray());
                    // 完了メッセージ表示
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
            // 確認画面
            case 'preview':
                $this->arrErr = $this->lfCheckPreviewError($objFormParam);
                if (Utils::isBlank($this->arrErr)) {
                    $this->sqlerr = $this->lfCheckSQL($objFormParam->getValue('csv_sql'));
                }
                $this->setTemplate('contents/csv_sql_view.tpl');

                return;

            // 新規作成
            case 'new_page':
                // リロード
                Application::alias('eccube.response')->reload();
                break;
            // データ削除
            case 'delete':
                $this->arrErr = $this->lfCheckDeleteError($objFormParam);
                if (Utils::isBlank($this->arrErr)) {
                    $this->lfDelData($objFormParam->getValue('sql_id'));
                    Application::alias('eccube.response')->reload();
                    Application::alias('eccube.response')->actionExit();
                }
                break;
            // CSV出力
            case 'csv_output':
                $this->arrErr = $this->lfCheckOutputError($objFormParam);
                if (Utils::isBlank($this->arrErr)) {
                    $this->lfDoCsvOutput($objFormParam->getValue('csv_output_id'));
                    Application::alias('eccube.response')->actionExit();
                }
                break;
            default:
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    // 設定内容を取得する
                    $this->arrForm = $this->lfGetSqlData($objFormParam);
                    // カラム一覧を取得する
                    $this->arrColList = $this->lfGetColList($objFormParam->getValue('select_table'));
                }
                break;
        }

        // 登録済みSQL一覧取得
        $this->arrSqlList = $this->lfGetSqlList();
        // テーブル一覧を取得する
        $this->arrTableList = $this->lfGetTableList();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('SQL ID', 'sql_id', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('CSV出力対象SQL ID', 'csv_output_id', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('選択テーブル', 'select_table', STEXT_LEN, 'KVa', array('GRAPH_CHECK','MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('名称', 'sql_name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK','SPTAB_CHECK'));
        $objFormParam->addParam('SQL文', 'csv_sql', '30000', 'KVa', array('MAX_LENGTH_CHECK','SPTAB_CHECK'));
    }

    /**
     * SQL登録エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckConfirmError(&$objFormParam)
    {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->doFunc(array('名称', 'sql_name'), array('EXIST_CHECK'));
        $objErr->doFunc(array('SQL文', 'csv_sql', '30000'), array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('SQL文には読み込み関係以外のSQLコマンドおよび";"記号', 'csv_sql', $this->lfGetSqlDenyList()), array('PROHIBITED_STR_CHECK'));
        if (!Utils::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }
        // SQL文自体の確認、エラーが無い時のみ実行
        if (Utils::isBlank($arrErr)) {
            $sql_error = $this->lfCheckSQL($objFormParam->getValue('csv_sql'));
            if (!Utils::isBlank($sql_error)) {
                $arrErr['csv_sql'] = '※ SQL文が不正です。SQL文を見直してください';
            }
        }

        return $arrErr;
    }

    /**
     * SQL確認エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckPreviewError(&$objFormParam)
    {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->doFunc(array('SQL文', 'csv_sql', '30000'), array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('SQL文には読み込み関係以外のSQLコマンドおよび";"記号', 'csv_sql', $this->lfGetSqlDenyList()), array('PROHIBITED_STR_CHECK'));
        if (!Utils::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }

        return $arrErr;
    }

    /**
     * SQL設定 削除エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckDeleteError(&$objFormParam)
    {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->doFunc(array('SQL ID', 'sql_id'), array('EXIST_CHECK'));
        if (!Utils::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }

        return $arrErr;
    }

    /**
     * SQL設定 CSV出力エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckOutputError(&$objFormParam)
    {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->doFunc(array('CSV出力対象SQL ID', 'csv_output_id'), array('EXIST_CHECK'));
        if (!Utils::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }

        return $arrErr;
    }

    /**
     * テーブル一覧を取得する.
     *
     * @return array テーブル名一覧
     */
    public function lfGetTableList()
    {
        $objQuery = Application::alias('eccube.query');
        // 実テーブル上のカラム設定を見に行く仕様に変更 ref #476
        $arrTable = $objQuery->listTables();
        if (Utils::isBlank($arrTable)) {
            return array();
        }
        $arrRet = array();
        foreach ($arrTable as $table) {
            if (substr($table, 0, 4) == 'dtb_') {
                $arrRet[ $table ] = 'データテーブル: ' . $table;
            } elseif (substr($table, 0, 4) == 'mtb_') {
                $arrRet[ $table ] = 'マスターテーブル: ' . $table;
            }
        }

        return $arrRet;
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @return array  カラム一覧の配列
     */
    public function lfGetColList($table)
    {
        if (Utils::isBlank($table)) {
            return array();
        }
        $objQuery = Application::alias('eccube.query');
        // 実テーブル上のカラム設定を見に行く仕様に変更 ref #476
        $arrColList = $objQuery->listTableFields($table);
        $arrColList= Utils::sfArrCombine($arrColList, $arrColList);

        return $arrColList;
    }

    /**
     * 登録済みSQL一覧を取得する.
     *
     * @param  string $where  Where句
     * @param  array  $arrVal 絞り込みデータ
     * @return array  取得結果の配列
     */
    public function lfGetSqlList($where = '' , $arrVal = array())
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_csv_sql';
        $objQuery->setOrder('sql_id');

        return $objQuery->select('*', $table, $where, $arrVal);
    }

    /**
     * 入力されたSQL文が正しく実行出来るかのチェックを行う.
     *
     * @param string SQL文データ(頭にSELECTは入れない)
     * @return string エラー内容
     */
    public function lfCheckSQL($sql)
    {
        // force_run
        $objQuery = Application::alias('eccube.query', '', true);
        $err = '';
        $sql = 'SELECT ' . $sql . ' ';
        $objErrMsg = $objQuery->query($sql);
        if (\PEAR::isError($objErrMsg)) {
            $err = $objErrMsg->message . "\n" . $objErrMsg->userinfo;
        }

        return $err;
    }

    /**
     * SQL詳細設定情報呼び出し (編集中データがある場合はそれを保持する）
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return mixed 表示用パラメーター
     */
    public function lfGetSqlData(&$objFormParam)
    {
        // 編集中データがある場合
        if (!Utils::isBlank($objFormParam->getValue('sql_name'))
            || !Utils::isBlank($objFormParam->getValue('csv_sql'))
        ) {
            return $objFormParam->getHashArray();
        }
        $sql_id = $objFormParam->getValue('sql_id');
        if (!Utils::isBlank($sql_id)) {
            $arrData = $this->lfGetSqlList('sql_id = ?', array($sql_id));

            return $arrData[0];
        }

        return array();
    }

    /**
     * DBにデータを保存する.
     *
     * @param  integer $sql_id 出力するデータのSQL_ID
     * @return void
     */
    public function lfDoCsvOutput($sql_id)
    {
        /* @var $objCSV CsvHelper */
        $objCSV = Application::alias('eccube.helper.csv');

        $arrData = $this->lfGetSqlList('sql_id = ?', array($sql_id));
        $sql = 'SELECT ' . $arrData[0]['csv_sql'];

        $objCSV->sfDownloadCsvFromSql($sql, array(), 'contents', null, true);
        Application::alias('eccube.response')->actionExit();
    }

    /**
     * DBにデータを保存する.
     *
     * @param  integer $sql_id    更新するデータのSQL_ID
     * @param  array   $arrSqlVal 更新データの配列
     * @return integer $sql_id SQL_IDを返す
     */
    public function lfUpdData($sql_id, $arrSqlVal)
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_csv_sql';
        $arrSqlVal['update_date'] = 'CURRENT_TIMESTAMP';
        if (Utils::sfIsInt($sql_id)) {
            //データ更新
            $where = 'sql_id = ?';
            $objQuery->update($table, $arrSqlVal, $where, array($sql_id));
        } else {
            //新規作成
            $sql_id = $objQuery->nextVal('dtb_csv_sql_sql_id');
            $arrSqlVal['sql_id'] = $sql_id;
            $arrSqlVal['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrSqlVal);
        }

        return $sql_id;
    }

    /**
     * 登録済みデータを削除する.
     *
     * @param  integer $sql_id 削除するデータのSQL_ID
     * @return boolean 実行結果 true：成功
     */
    public function lfDelData($sql_id)
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_csv_sql';
        $where = 'sql_id = ?';
        if (Utils::sfIsInt($sql_id)) {
            $objQuery->delete($table, $where, array($sql_id));

            return true;
        }

        return false;
    }

    /**
     * SQL文に含めることを許可しないSQLキーワード
     * 基本的にEC-CUBEのデータを取得するために必要なコマンドしか許可しない。複数クエリも不可
     *
     * FIXME: キーワードの精査。危険な部分なのでプログラム埋め込みで実装しました。mtb化の有無判断必要。
     *
     * @return string[] 不許可ワード配列
     */
    public function lfGetSqlDenyList()
    {
        $arrList = array(
            ';',
            'CREATE\s',
            'INSERT\s',
            'UPDATE\s',
            'DELETE\s',
            'ALTER\s',
            'ABORT\s',
            'ANALYZE\s',
            'CLUSTER\s',
            'COMMENT\s',
            'COPY\s',
            'DECLARE\s',
            'DISCARD\s',
            'DO\s',
            'DROP\s',
            'EXECUTE\s',
            'EXPLAIN\s',
            'GRANT\s',
            'LISTEN\s',
            'LOAD\s',
            'LOCK\s',
            'NOTIFY\s',
            'PREPARE\s',
            'REASSIGN\s',
//            'REINDEX\s', // REINDEXは許可で良いかなと
            'RELEASE\sSAVEPOINT',
            'RENAME\s',
            'REST\s',
            'REVOKE\s',
            'SAVEPOINT\s',
            '\sSET\s', // OFFSETを誤検知しないように先頭・末尾に\sを指定
            'SHOW\s',
            'START\sTRANSACTION',
            'TRUNCATE\s',
            'UNLISTEN\s',
            'VACCUM\s',
            'HANDLER\s',
            'LOAD\sDATA\s',
            'LOAD\sXML\s',
            'REPLACE\s',
            'OPTIMIZE\s',
            'REPAIR\s',
            'INSTALL\sPLUGIN\s',
            'UNINSTALL\sPLUGIN\s',
            'BINLOG\s',
            'KILL\s',
            'RESET\s',
            'PURGE\s',
            'CHANGE\sMASTER',
            'START\sSLAVE',
            'STOP\sSLAVE',
            'MASTER\sPOS\sWAIT',
            'SIGNAL\s',
            'RESIGNAL\s',
            'RETURN\s',
            'USE\s',
            'HELP\s',
        );

        return $arrList;
    }
}
