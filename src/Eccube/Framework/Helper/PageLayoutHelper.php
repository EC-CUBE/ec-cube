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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Helper\BlocHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;

/**
 * Webページのレイアウト情報を制御するヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 */
class PageLayoutHelper
{
    
    public $classes = array(
        'calendar'  => '\\Eccube\\Page\\Bloc\\Calendar',
        'cart'      => '\\Eccube\\Page\\Bloc\\Cart',
        'category'  => '\\Eccube\\Page\\Bloc\\Category',
        'login'     => '\\Eccube\\Page\\Bloc\\Login',
        'login_footer' => '\\Eccube\\Page\\Bloc\\LoginFooter',
        'login_header' => '\\Eccube\\Page\\Bloc\\LoginHeader',
        'navi_footer' => '\\Eccube\\Page\\Bloc\\NaviFooter',
        'navi_header' => '\\Eccube\\Page\\Bloc\\NaviHeader',
        'news'      => '\\Eccube\\Page\\Bloc\\News',
        'recommend' => '\\Eccube\\Page\\Bloc\\Recommend',
        'search_products' => '\\Eccube\\Page\\Bloc\\SearchProducts',
    );

    /**
     * ページのレイアウト情報を取得し, 設定する.
     *
     * 現在の URL に応じたページのレイアウト情報を取得し, LC_Page インスタンスに
     * 設定する.
     *
     * @access public
     * @param  LC_Page $objPage        LC_Page インスタンス
     * @param  boolean $preview        プレビュー表示の場合 true
     * @param  string  $url            ページのURL($_SERVER['SCRIPT_NAME'] の情報)
     * @param  integer $device_type_id 端末種別ID
     * @return void
     */
    public function sfGetPageLayout(&$objPage, $preview = false, $url = '', $device_type_id = DEVICE_TYPE_PC)
    {
        // URLを元にページ情報を取得
        if ($preview === false) {
            $url = preg_replace('|^' . preg_quote(ROOT_URLPATH) . '|', '', $url);
            $arrPageData = $this->getPageProperties($device_type_id, null, 'url = ?', array($url));
        // プレビューの場合は, プレビュー用のデータを取得
        } else {
            $arrPageData = $this->getPageProperties($device_type_id, 0);
        }

        if (empty($arrPageData[0])) {
            trigger_error('ページ情報を取得できませんでした。', E_USER_WARNING);
            $objPage->arrPageLayout = array();
            $objPage->tpl_column_num = 1;
        } else {
            $objPage->tpl_mainpage = $this->getTemplatePath($device_type_id) . $arrPageData[0]['filename'] . '.tpl';

            if (!file_exists($objPage->tpl_mainpage)) {
                $msg = 'メイン部のテンプレートが存在しません。[' . $objPage->tpl_mainpage . ']';
                trigger_error($msg, E_USER_WARNING);
            }

            $objPage->arrPageLayout =& $arrPageData[0];
            if (strlen($objPage->arrPageLayout['author']) === 0) {
                $arrInfo = Application::alias('eccube.helper.db')->getBasisData();
                $objPage->arrPageLayout['author'] = $arrInfo['company_name'];
            }

            // ページタイトルを設定
            if (Utils::isBlank($objPage->tpl_title)) {
                $objPage->tpl_title = $objPage->arrPageLayout['page_name'];
            }

            // 該当ページのブロックを取得し, 配置する
            $masterData = Application::alias('eccube.db.master_data');
            $arrTarget = $masterData->getMasterData('mtb_target');
            $arrBlocs = $this->getBlocPositions($device_type_id, $objPage->arrPageLayout['page_id']);
            // 無効なプラグインのブロックを取り除く.
            $objPlugin = PluginHelper::getSingletonInstance();
            $arrBlocs = $objPlugin->getEnableBlocs($arrBlocs);
            // php_path, tpl_path が存在するものを, 各ターゲットに配置
            foreach ($arrTarget as $target_id => $value) {
                foreach ($arrBlocs as $arrBloc) {
                    if ($arrBloc['target_id'] != $target_id) {
                        continue;
                    }
                    if (array_key_exists($arrBloc['filename'], $this->classes)
                        || is_file($arrBloc['tpl_path'])) {
                        $objPage->arrPageLayout[$arrTarget[$target_id]][] = $arrBloc;
                    } else {
                        $error = "ブロックが見つかりません\n"
                            . 'tpl_path: ' . $arrBloc['tpl_path'] . "\n"
                            . 'php_path: ' . $arrBloc['php_path'];
                        trigger_error($error, E_USER_WARNING);
                    }
                }
            }
            // カラム数を取得する
            $objPage->tpl_column_num = $this->getColumnNum($objPage->arrPageLayout);
        }
    }

    /**
     * ページの属性を取得する.
     *
     * この関数は, dtb_pagelayout の情報を検索する.
     * $device_type_id は必須. デフォルト値は DEVICE_TYPE_PC.
     * $page_id が null の場合は, $page_id が 0 以外のものを検索する.
     *
     * @access public
     * @param  integer $device_type_id 端末種別ID
     * @param  integer $page_id        ページID; null の場合は, 0 以外を検索する.
     * @param  string  $where          追加の検索条件
     * @param  string[]   $arrParams      追加の検索パラメーター
     * @return array   ページ属性の配列
     */
    public function getPageProperties($device_type_id = DEVICE_TYPE_PC, $page_id = null, $where = '', $arrParams = array())
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'device_type_id = ? ' . (Utils::isBlank($where) ? $where : 'AND ' . $where);
        if ($page_id === null) {
            $where = 'page_id <> ? AND ' . $where;
            $page_id = 0;
        } else {
            $where = 'page_id = ? AND ' . $where;
        }
        $objQuery->setOrder('page_id');
        $arrParams = array_merge(array($page_id, $device_type_id), $arrParams);

        return $objQuery->select('*', 'dtb_pagelayout', $where, $arrParams);
    }

    /**
     * ブロック情報を取得する.
     *
     * @access public
     * @param  integer $device_type_id 端末種別ID
     * @param  string  $where          追加の検索条件
     * @param  array   $arrParams      追加の検索パラメーター
     * @param  boolean $has_realpath   php_path, tpl_path の絶対パスを含める場合 true
     * @return array   ブロック情報の配列
     */
    public function getBlocs($device_type_id = DEVICE_TYPE_PC, $where = '', $arrParams = array(), $has_realpath = true)
    {
        /* @var $objBloc BlocHelper */
        $objBloc = Application::alias('eccube.helper.bloc', $device_type_id);
        $arrBlocs = $objBloc->getWhere($where, $arrParams);
        if ($has_realpath) {
            $this->setBlocPathTo($device_type_id, $arrBlocs);
        }

        return $arrBlocs;
    }

    /**
     * ブロック配置情報を取得する.
     *
     * @access public
     * @param  integer $device_type_id 端末種別ID
     * @param  integer $page_id        ページID
     * @param  boolean $has_realpath   php_path, tpl_path の絶対パスを含める場合 true
     * @return array   配置情報を含めたブロックの配列
     */
    public function getBlocPositions($device_type_id, $page_id, $has_realpath = true)
    {
        $objQuery = Application::alias('eccube.query');

        $table = <<< __EOF__
        dtb_blocposition AS pos
            JOIN dtb_bloc AS bloc
                ON bloc.bloc_id = pos.bloc_id
                    AND bloc.device_type_id = pos.device_type_id
__EOF__;
        $where = 'bloc.device_type_id = ? AND ((anywhere = 1 AND pos.page_id != 0) OR pos.page_id = ?)';
        $objQuery->setOrder('target_id, bloc_row');
        $arrBlocs = $objQuery->select('*', $table, $where, array($device_type_id, $page_id));
        if ($has_realpath) {
            $this->setBlocPathTo($device_type_id, $arrBlocs);
        }

        //全ページ設定と各ページのブロックの重複を削除
        $arrUniqBlocIds = array();
        foreach ($arrBlocs as $index => $arrBloc) {
            if ($arrBloc['anywhere'] == 1) {
                $arrUniqBlocIds[] = $arrBloc['bloc_id'];
            }
        }
        foreach ($arrBlocs as $bloc_index => $arrBlocData) {
            if (in_array($arrBlocData['bloc_id'], $arrUniqBlocIds) && $arrBlocData['anywhere'] == 0) {
                unset($arrBlocs[$bloc_index]);
            }
        }

        return $arrBlocs;
    }

    /**
     * ページ情報を削除する.
     *
     * XXX ファイルを確実に削除したかどうかのチェック
     *
     * @access public
     * @param  integer $page_id        ページID
     * @param  integer $device_type_id 端末種別ID
     * @return integer 削除数
     */
    public function lfDelPageData($page_id, $device_type_id = DEVICE_TYPE_PC)
    {
        $objQuery = Application::alias('eccube.query');
        // page_id が空でない場合にはdeleteを実行
        if ($page_id != '') {
            $arrPageData = $this->getPageProperties($device_type_id, $page_id);
            $ret = $objQuery->delete('dtb_pagelayout', 'page_id = ? AND device_type_id = ?', array($page_id, $device_type_id));
            // ファイルの削除
            $this->lfDelFile($arrPageData['filename'], $device_type_id);
        }

        return $ret;
    }

    /**
     * ページのファイルを削除する.
     *
     * dtb_pagelayout の削除後に呼び出すこと。
     *
     * @access private
     * @param  string  $filename
     * @param  integer $device_type_id 端末種別ID
     * @return void    // TODO boolean にするべき?
     */
    public function lfDelFile($filename, $device_type_id)
    {
        $objQuery = Application::alias('eccube.query');

        /*
         * 同名ファイルの使用件数
         * PHP ファイルは, 複数のデバイスで共有するため, device_type_id を条件に入れない
         */
        $exists = $objQuery->exists('dtb_pagelayout', 'filename = ?', array($filename));

        if (!$exists) {
            // phpファイルの削除
            $del_php = HTML_REALDIR . $filename . '.php';
            if (file_exists($del_php)) {
                unlink($del_php);
            }
        }

        // tplファイルの削除
        $del_tpl = $this->getTemplatePath($device_type_id) . $filename . '.tpl';
        if (file_exists($del_tpl)) {
            unlink($del_tpl);
        }
    }

    /**
     * 編集可能ページかどうか.
     *
     * @access public
     * @param  integer                   $device_type_id 端末種別ID
     * @param  integer                   $page_id        ページID
     * @return boolean �合 true
     */
    public function isEditablePage($device_type_id, $page_id)
    {
        if ($page_id == 0) {
            return false;
        }
        $arrPages = $this->getPageProperties($device_type_id, $page_id);
        if ($arrPages[0]['edit_flg'] != 2) {
            return true;
        }

        return false;
    }

    /**
     * テンプレートのパスを取得する.
     *
     * @access public
     * @param  integer $device_type_id 端末種別ID
     * @param  boolean $isUser         USER_REALDIR 以下のパスを返す場合 true
     * @return string  テンプレートのパス
     */
    public function getTemplatePath($device_type_id = DEVICE_TYPE_PC, $isUser = false)
    {
        $templateName = '';
        switch ($device_type_id) {
            case DEVICE_TYPE_MOBILE:
                $dir = MOBILE_TEMPLATE_REALDIR;
                $templateName = MOBILE_TEMPLATE_NAME;
                break;

            case DEVICE_TYPE_SMARTPHONE:
                $dir = SMARTPHONE_TEMPLATE_REALDIR;
                $templateName = SMARTPHONE_TEMPLATE_NAME;
                break;

            case DEVICE_TYPE_PC:
            default:
                $dir = TEMPLATE_REALDIR;
                $templateName = TEMPLATE_NAME;
                break;
        }
        $userPath = USER_REALDIR;
        if ($isUser) {
            $dir = $userPath . USER_PACKAGE_DIR . $templateName . '/';
        }

        return $dir;
    }

    /**
     * DocumentRoot から user_data のパスを取得する.
     *
     * 引数 $hasPackage を true にした場合は, user_data/packages/template_name
     * を取得する.
     *
     * @access public
     * @param  integer $device_type_id 端末種別ID
     * @param  boolean $hasPackage     パッケージのパスも含める場合 true
     * @return string  端末に応じた DocumentRoot から user_data までのパス
     */
    public function getUserDir($device_type_id = DEVICE_TYPE_PC, $hasPackage = false)
    {
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            $templateName = MOBILE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            $templateName = SMARTPHONE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_PC:
        default:
            $templateName = TEMPLATE_NAME;
        }
        $userDir = ROOT_URLPATH . USER_DIR;
        if ($hasPackage) {
            return $userDir . USER_PACKAGE_DIR . $templateName . '/';
        }

        return $userDir;
    }

    /**
     * ブロックの php_path, tpl_path を設定する.
     *
     * @access private
     * @param  integer $device_type_id 端末種別ID
     * @param  array   $arrBlocs       設定するブロックの配列
     * @return void
     */
    public function setBlocPathTo($device_type_id = DEVICE_TYPE_PC, &$arrBlocs = array())
    {
        foreach ($arrBlocs as $key => $value) {
            $arrBloc =& $arrBlocs[$key];
            $arrBloc['php_path'] = Utils::isBlank($arrBloc['php_path']) ? '' : HTML_REALDIR . $arrBloc['php_path'];
            // php_pathがある場合は、pathを追加せずにLC_Page_FrontParts_Blocで処理する
            if (! Utils::isBlank($arrBloc['php_path']) ) continue;

            $bloc_dir = $this->getTemplatePath($device_type_id) . BLOC_DIR;
            $arrBloc['tpl_path'] = Utils::isBlank($arrBloc['tpl_path']) ? '' : $bloc_dir . $arrBloc['tpl_path'];
        }
    }

    /**
     * カラム数を取得する.
     *
     * @access private
     * @param  array   $arrPageLayout レイアウト情報の配列
     * @return integer $col_num カラム数
     */
    public function getColumnNum($arrPageLayout)
    {
        // メインは確定
        $col_num = 1;
        // LEFT NAVI
        if (isset($arrPageLayout['LeftNavi']) && count($arrPageLayout['LeftNavi']) > 0) $col_num++;
        // RIGHT NAVI
        if (isset($arrPageLayout['RightNavi']) && count($arrPageLayout['RightNavi']) > 0) $col_num++;
        return $col_num;
    }
}
