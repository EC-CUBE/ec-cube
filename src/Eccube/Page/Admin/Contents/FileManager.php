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
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\UploadFile;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Util\Utils;

/**
 * ファイル管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class FileManager extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'contents/file_manager.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = 'file';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'ファイル管理';
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
        // フォーム操作クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($this->createSetParam($_POST));
        $objFormParam->convParam();

        // ファイル管理クラス
        $objUpFile = new UploadFile($objFormParam->getValue('now_dir'), $objFormParam->getValue('now_dir'));
        // ファイル情報の初期化
        $this->lfInitFile($objUpFile);

        // ファイル操作クラス
        /* @var $objFileManager FileManagerHelper */
        $objFileManager = Application::alias('eccube.helper.file_manager');

        switch ($this->getMode()) {
            // フォルダ移動
            case 'move':
                $objFormParam = Application::alias('eccube.form_param');
                $this->lfInitParamModeMove($objFormParam);
                $objFormParam->setParam($this->createSetParam($_POST));
                $objFormParam->convParam();

                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    $now_dir = $this->lfCheckSelectDir($objFormParam, $objFormParam->getValue('tree_select_file'));
                    $objFormParam->setValue('now_dir', $now_dir);
                }
                break;

            // ファイル表示
            case 'view':
                $objFormParam = Application::alias('eccube.form_param');
                $this->lfInitParamModeView($objFormParam);
                $objFormParam->setParam($this->createSetParam($_POST));
                $objFormParam->convParam();

                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    if ($this->tryView($objFormParam)) {
                        $pattern = '/' . preg_quote($objFormParam->getValue('top_dir'), '/') . '/';
                        $file_url = htmlspecialchars(preg_replace($pattern, '', $objFormParam->getValue('select_file')));
                        $tpl_onload = "eccube.openWindow('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
                        $this->setTplOnLoad($tpl_onload);
                    }
                }
                break;

            // ファイルダウンロード
            case 'download':
                $objFormParam = Application::alias('eccube.form_param');
                $this->lfInitParamModeView($objFormParam);
                $objFormParam->setParam($this->createSetParam($_POST));
                $objFormParam->convParam();

                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    if (is_dir($objFormParam->getValue('select_file'))) {
                        $disp_error = '※ ディレクトリをダウンロードすることは出来ません。<br/>';
                        $this->setDispError('select_file', $disp_error);
                    } else {
                        $path_exists = Utils::checkFileExistsWithInBasePath($objFormParam->getValue('select_file'), USER_REALDIR);
                        if ($path_exists) {
                            // ファイルダウンロード
                            $objFileManager->sfDownloadFile($objFormParam->getValue('select_file'));
                            Application::alias('eccube.response')->actionExit();
                        }
                    }
                }
                break;
            // ファイル削除
            case 'delete':
                $objFormParam = Application::alias('eccube.form_param');
                $this->lfInitParamModeView($objFormParam);
                $objFormParam->setParam($this->createSetParam($_POST));
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError();
                $path_exists = Utils::checkFileExistsWithInBasePath($objFormParam->getValue('select_file'), USER_REALDIR);
                if (Utils::isBlank($this->arrErr) && ($path_exists)) {
                    Application::alias('eccube.helper.file_manager')->deleteFile($objFormParam->getValue('select_file'));
                }
                break;
            // ファイル作成
            case 'create':
                $objFormParam = Application::alias('eccube.form_param');
                $this->lfInitParamModeCreate($objFormParam);
                $objFormParam->setParam($this->createSetParam($_POST));
                $objFormParam->convParam();

                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    if (!$this->tryCreateDir($objFileManager, $objFormParam)) {
                        $disp_error = '※ '.htmlspecialchars($objFormParam->getValue('create_file'), ENT_QUOTES).'の作成に失敗しました。<br/>';
                        $this->setDispError('create_file', $disp_error);
                    } else {
                        $tpl_onload = "alert('フォルダを作成しました。');";
                        $this->setTplOnLoad($tpl_onload);
                    }
                }
                break;
            // ファイルアップロード
            case 'upload':
                // 画像保存処理
                $ret = $objUpFile->makeTempFile('upload_file', false);
                if (Utils::isBlank($ret)) {
                    $tpl_onload = "alert('ファイルをアップロードしました。');";
                    $this->setTplOnLoad($tpl_onload);
                } else {
                    $this->setDispError('upload_file', $ret);
                }
                break;
            // 初期表示
            default:
                break;
        }

        // 値をテンプレートに渡す
        $this->arrParam = $objFormParam->getHashArray();
        // 現在の階層がルートディレクトリかどうかテンプレートに渡す
        $this->setIsTopDir($objFormParam);
        // 現在の階層より一つ上の階層をテンプレートに渡す
        $this->setParentDir($objFormParam);
        // 現在いる階層(表示用)をテンプレートに渡す
        $this->setDispPath($objFormParam);
        // 現在のディレクトリ配下のファイル一覧を取得
        $this->arrFileList = $objFileManager->sfGetFileList($objFormParam->getValue('now_dir'));
        // 現在の階層のディレクトリをテンプレートに渡す
        $this->setDispParam('tpl_now_file', $objFormParam->getValue('now_dir'));
        // ディレクトリツリー表示
        $this->setDispTree($objFileManager, $objFormParam);
    }

    /**
     * 初期化を行う.
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        // 共通定義
        $this->lfInitParamCommon($objFormParam);
    }

    /**
     * ディレクトリ移動時、パラメーター定義
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParamModeMove(&$objFormParam)
    {
        // 共通定義
        $this->lfInitParamCommon($objFormParam);
        $objFormParam->addParam('選択ファイル', 'select_file', MTEXT_LEN, 'a', array());
    }

    /**
     * ファイル表示時、パラメーター定義
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParamModeView(&$objFormParam)
    {
        // 共通定義
        $this->lfInitParamCommon($objFormParam);
        $objFormParam->addParam('選択ファイル', 'select_file', MTEXT_LEN, 'a', array('SELECT_CHECK'));
    }

    /**
     * ファイル表示時、パラメーター定義
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParamModeCreate(&$objFormParam)
    {
        // 共通定義
        $this->lfInitParamCommon($objFormParam);
        $objFormParam->addParam('選択ファイル', 'select_file', MTEXT_LEN, 'a', array());
        $objFormParam->addParam('作成ファイル名', 'create_file', MTEXT_LEN, 'a', array('EXIST_CHECK', 'FILE_NAME_CHECK_BY_NOUPLOAD'));
    }

    /**
     * ファイル表示時、パラメーター定義
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParamCommon(&$objFormParam)
    {
        $objFormParam->addParam('ルートディレクトリ', 'top_dir', MTEXT_LEN, 'a', array());
        $objFormParam->addParam('現在の階層ディレクトリ', 'now_dir', MTEXT_LEN, 'a', array());
        $objFormParam->addParam('現在の階層ファイル', 'now_file', MTEXT_LEN, 'a', array());
        $objFormParam->addParam('ツリー選択状態', 'tree_status', MTEXT_LEN, 'a', array());
        $objFormParam->addParam('ツリー選択ディレクトリ', 'tree_select_file', MTEXT_LEN, 'a', array());
    }

    /*
     * ファイル情報の初期化
     *
     * @param  object $objUpFile UploadFileインスタンス
     * @return void
     */
    public function lfInitFile(&$objUpFile)
    {
        $objUpFile->addFile('ファイル', 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
    }

    /**
     * テンプレートに渡す値を整形する
     *
     * @param  array $arrVal $_POST
     * @return array $setParam テンプレートに渡す値
     */
    public function createSetParam($arrVal)
    {
        $setParam = $arrVal;
        // Windowsの場合は, ディレクトリの区切り文字を\から/に変換する
        $setParam['top_dir'] = (strpos(PHP_OS, 'WIN') === false) ? USER_REALDIR : str_replace('\\', '/', USER_REALDIR);
        // 初期表示はルートディレクトリ(user_data/)を表示
        if (Utils::isBlank($this->getMode())) {
            $setParam['now_dir'] = $setParam['top_dir'];
        }

        return $setParam;
    }

    /**
     * テンプレートに値を渡す
     *
     * @param  string $key キー名
     * @param  string $val 値
     * @return void
     */
    public function setDispParam($key, $val)
    {
        $this->$key = $val;
    }

    /**
     * ディレクトリを作成
     *
     * @param  FileManagerHelper       $objFileManager FileManagerHelperインスタンス
     * @param  FormParam $objFormParam   FormParamインスタンス
     * @return boolean      ディレクトリ作成できたかどうか
     */
    public function tryCreateDir($objFileManager, $objFormParam)
    {
        $create_dir_flg = false;
        $create_dir = rtrim($objFormParam->getValue('now_dir'), '/');
        // ファイル作成
        if ($objFileManager->sfCreateFile($create_dir.'/'.$objFormParam->getValue('create_file'), 0755)) {
            $create_dir_flg = true;
        }

        return $create_dir_flg;
    }

    /**
     * ファイル表示を行う
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return boolean      ファイル表示するかどうか
     */
    public function tryView(&$objFormParam)
    {
        $view_flg = false;
        $now_dir = $this->lfCheckSelectDir($objFormParam, dirname($objFormParam->getValue('select_file')));
        $objFormParam->setValue('now_dir', $now_dir);
        if (!strpos($objFormParam->getValue('select_file'), $objFormParam->getValue('top_dir'))) {
            $view_flg = true;
        }

        return $view_flg;
    }

    /**
     * 現在の階層の一つ上の階層のディレクトリをテンプレートに渡す
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function setParentDir($objFormParam)
    {
        $parent_dir = $this->lfGetParentDir($objFormParam->getValue('now_dir'));
        $this->setDispParam('tpl_parent_dir', $parent_dir);
    }

    /**
     * 現在の階層のパスをテンプレートに渡す
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function setDispPath($objFormParam)
    {
        // Windows 環境で DIRECTORY_SEPARATOR が JavaScript に渡るとエスケープ文字と勘違いするので置換
        $html_realdir = str_replace(DIRECTORY_SEPARATOR, '/', HTML_REALDIR);
        $arrNowDir = preg_split('/\//', str_replace($html_realdir, '', $objFormParam->getValue('now_dir')));
        $this->setDispParam('tpl_now_dir', Utils::jsonEncode($arrNowDir));
        $this->setDispParam('tpl_file_path', $html_realdir);
    }

    /**
     * エラーを表示用の配列に格納
     *
     * @param  string $key   キー名
     * @param  string $value エラー内容
     * @return void
     */
    public function setDispError($key, $value)
    {
        // 既にエラーがある場合は、処理しない
        if (Utils::isBlank($this->arrErr[$key])) {
            $this->arrErr[$key] = $value;
        }
    }

    /**
     * javascriptをテンプレートに渡す
     *
     * @param  string $tpl_onload javascript
     * @return void
     */
    public function setTplOnLoad($tpl_onload)
    {
        $this->tpl_onload .= $tpl_onload;
    }

    /*
     * 選択ディレクトリがUSER_REALDIR以下かチェック
     *
     * @param  object $objFormParam FormParamインスタンス
     * @param  string $dir          ディレクトリ
     * @return string $select_dir 選択ディレクトリ
     */
    public function lfCheckSelectDir($objFormParam, $dir)
    {
        $select_dir = '';
        $top_dir = $objFormParam->getValue('top_dir');
        // USER_REALDIR以下の場合
        if (preg_match("@^\Q". $top_dir. "\E@", $dir) > 0) {
            // 相対パスがある場合、USER_REALDIRを返す.
            if (preg_match("@\Q..\E@", $dir) > 0) {
                $select_dir = $top_dir;
            // 相対パスがない場合、そのままディレクトリパスを返す.
            } else {
                $select_dir= $dir;
            }
        // USER_REALDIR以下でない場合、USER_REALDIRを返す.
        } else {
            $select_dir = $top_dir;
        }

        return $select_dir;
    }

    /**
     * 親ディレクトリ取得
     *
     * @param  string $dir 現在いるディレクトリ
     * @return string $parent_dir 親ディレクトリ
     */
    public function lfGetParentDir($dir)
    {
        $parent_dir = '';
        $dir = rtrim($dir, '/');
        $arrDir = explode('/', $dir);
        array_pop($arrDir);
        foreach ($arrDir as $val) {
            $parent_dir .= "$val/";
        }
        $parent_dir = rtrim($parent_dir, '/');

        return $parent_dir;
    }

    /**
     * ディレクトリツリー生成
     *
     * @param  FileManagerHelper       $objFileManager FileManagerHelperインスタンス
     * @param  FormParam $objFormParam   FormParamインスタンス
     * @return void
     */
    public function setDispTree($objFileManager, $objFormParam)
    {
        $tpl_onload = '';
        // ツリーを表示する divタグid, ツリー配列変数名, 現在ディレクトリ, 選択ツリーhidden名, ツリー状態hidden名, mode hidden名
        $now_dir = $objFormParam->getValue('now_dir');
        $treeView = "eccube.fileManager.viewFileTree('tree', arrTree, '$now_dir', 'tree_select_file', 'tree_status', 'move');";
        if (!empty($this->tpl_onload)) {
            $tpl_onload .= $treeView;
        } else {
            $tpl_onload = $treeView;
        }
        $this->setTplOnLoad($tpl_onload);

        $tpl_javascript = '';
        $arrTree = $objFileManager->sfGetFileTree($objFormParam->getValue('top_dir'), $objFormParam->getValue('tree_status'));
        $tpl_javascript .= "arrTree = new Array();\n";
        foreach ($arrTree as $arrVal) {
            $tpl_javascript .= 'arrTree['.$arrVal['count'].'] = new Array('.$arrVal['count'].", '".$arrVal['type']."', '".$arrVal['path']."', ".$arrVal['rank'].',';
            if ($arrVal['open']) {
                $tpl_javascript .= "true);\n";
            } else {
                $tpl_javascript .= "false);\n";
            }
        }
        $this->setDispParam('tpl_javascript', $tpl_javascript);
    }

    /**
     * 現在の階層がルートディレクトリかどうかテンプレートに渡す
     *
     * @param  FormParam $objFormParam FormParamインスタンス
     * @return void
     */
    public function setIsTopDir($objFormParam)
    {
        // トップディレクトリか調査
        $is_top_dir = false;
        // 末尾の/をとる
        $top_dir_check = rtrim($objFormParam->getValue('top_dir'), '/');
        $now_dir_check = rtrim($objFormParam->getValue('now_dir'), '/');
        if ($top_dir_check == $now_dir_check) {
            $is_top_dir = true;
        }
        $this->setDispParam('tpl_is_top_dir', $is_top_dir);
    }
}
