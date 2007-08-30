<?php
  /*
   * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
   *
   * http://www.lockon.co.jp/
   */

  // {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * カテゴリ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Category extends LC_Page {

    // {{{ properties

    /** フォームパラメータ */
    var $objFormParam;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'カテゴリー登録';
        $this->tpl_mainpage = 'products/category.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'category';
        $this->tpl_onload = " fnSetFocus('category_name'); ";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        // 通常時は親カテゴリを0に設定する。
        $this->arrForm['parent_category_id'] =
            isset($_POST['parent_category_id']) ? $_POST['parent_category_id'] : "";

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'edit':
            $this->objFormParam->convParam();
            $arrRet =  $this->objFormParam->getHashArray();
            $this->arrErr = $this->lfCheckError($arrRet);

            if(count($this->arrErr) == 0) {
                if($_POST['category_id'] == "") {
                    $objQuery = new SC_Query();
                    $count = $objQuery->count("dtb_category");
                    if($count < CATEGORY_MAX) {
                        $this->lfInsertCat($_POST['parent_category_id']);
                    } else {
                        print("カテゴリの登録最大数を超えました。");
                    }
                } else {
                    $this->lfUpdateCat($_POST['category_id']);
                }
            } else {
                $this->arrForm = array_merge($this->arrForm, $this->objFormParam->getHashArray());
                $this->arrForm['category_id'] = $_POST['category_id'];
            }
            break;
        case 'pre_edit':
            // 編集項目のカテゴリ名をDBより取得する。
            $oquery = new SC_Query();
            $where = "category_id = ?";
            $cat_name = $oquery->get("dtb_category", "category_name", $where, array($_POST['category_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['category_name'] = $cat_name;
            // POSTデータを引き継ぐ
            $this->arrForm['category_id'] = $_POST['category_id'];
            break;
        case 'delete':
            $objQuery = new SC_Query();
            // 子カテゴリのチェック
            $where = "parent_category_id = ? AND del_flg = 0";
            $count = $objQuery->count("dtb_category", $where, array($_POST['category_id']));
            if($count != 0) {
                $this->arrErr['category_name'] = "※ 子カテゴリが存在するため削除できません。<br>";
            }
            // 登録商品のチェック
            $where = "category_id = ? AND del_flg = 0";
            $count = $objQuery->count("dtb_products", $where, array($_POST['category_id']));
            if($count != 0) {
                $this->arrErr['category_name'] = "※ カテゴリ内に商品が存在するため削除できません。<br>";
            }

            if(!isset($this->arrErr['category_name'])) {
                // ランク付きレコードの削除(※処理負荷を考慮してレコードごと削除する。)
                $objDb->sfDeleteRankRecord("dtb_category", "category_id", $_POST['category_id'], "", true);
            }
            break;
        case 'up':
            $objQuery = new SC_Query();
            $objQuery->begin();
            $up_id = $this->lfGetUpRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
            if($up_id != "") {
                // 上のグループのrankから減算する数
                $my_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
                // 自分のグループのrankに加算する数
                $up_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id);
                if($my_count > 0 && $up_count > 0) {
                    // 自分のグループに加算
                    $this->lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $up_count);
                    // 上のグループから減算
                    $this->lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id, $my_count);
                }
            }
            $objQuery->commit();
            break;
        case 'down':
            $objQuery = new SC_Query();
            $objQuery->begin();
            $down_id = $this->lfGetDownRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
            if($down_id != "") {
                // 下のグループのrankに加算する数
                $my_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
                // 自分のグループのrankから減算する数
                $down_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id);
                if($my_count > 0 && $down_count > 0) {
                    // 自分のグループから減算
                    $this->lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id, $my_count);
                    // 下のグループに加算
                    $this->lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $down_count);
                }
            }
            $objQuery->commit();
            break;
        case 'tree':
            break;
        default:
            $this->arrForm['parent_category_id'] = 0;
            break;
        }

        $this->arrList = $this->lfGetCat($this->arrForm['parent_category_id']);
        $this->arrTree = $objDb->sfGetCatTree($this->arrForm['parent_category_id']);

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }



    // カテゴリの新規追加
    function lfInsertCat($parent_category_id) {

        $objQuery = new SC_Query();
        $objQuery->begin(); // トランザクションの開始


        if($parent_category_id == 0) {
            // ROOT階層で最大のランクを取得する。
            $where = "parent_category_id = ?";
            $rank = $objQuery->max("dtb_category", "rank", $where, array($parent_category_id)) + 1;
        } else {
            // 親のランクを自分のランクとする。
            $where = "category_id = ?";
            $rank = $objQuery->get("dtb_category", "rank", $where, array($parent_category_id));
            // 追加レコードのランク以上のレコードを一つあげる。
            $sqlup = "UPDATE dtb_category SET rank = (rank + 1) WHERE rank >= ?";
            $objQuery->exec($sqlup, array($rank));
        }

        $where = "category_id = ?";
        // 自分のレベルを取得する(親のレベル + 1)
        $level = $objQuery->get("dtb_category", "level", $where, array($parent_category_id)) + 1;

        // 入力データを渡す。
        $sqlval = $this->objFormParam->getHashArray();
        $sqlval['create_date'] = "Now()";
        $sqlval['update_date'] = "Now()";
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['parent_category_id'] = $parent_category_id;
        $sqlval['rank'] = $rank;
        $sqlval['level'] = $level;

        // INSERTの実行
        $objQuery->insert("dtb_category", $sqlval);

        $objQuery->commit();    // トランザクションの終了
    }

    // カテゴリの編集
    function lfUpdateCat($category_id) {
        $objQuery = new SC_Query();
        // 入力データを渡す。
        $sqlval = $this->objFormParam->getHashArray();
        $sqlval['update_date'] = "Now()";
        $where = "category_id = ?";
        $objQuery->update("dtb_category", $sqlval, $where, array($category_id));
    }

    // カテゴリの取得
    function lfGetCat($parent_category_id) {
        $objQuery = new SC_Query();

        if($parent_category_id == "") {
            $parent_category_id = '0';
        }

        $col = "category_id, category_name, level, rank";
        $where = "del_flg = 0 AND parent_category_id = ?";
        $objQuery->setoption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, "dtb_category", $where, array($parent_category_id));
        return $arrRet;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("カテゴリ名", "category_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
    }

    /* 入力内容のチェック */
    function lfCheckError($array) {

        $objErr = new SC_CheckError($array);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 階層チェック
        if(!isset($objErr->arrErr['category_name'])) {
            $objQuery = new SC_Query();
            $level = $objQuery->get("dtb_category", "level", "category_id = ?", array($_POST['parent_category_id']));

            if($level >= LEVEL_MAX) {
                $objErr->arrErr['category_name'] = "※ ".LEVEL_MAX."階層以上の登録はできません。<br>";
            }
        }

        if (!isset($_POST['category_id'])) $_POST['category_id'] = "";

        //

        // 重複チェック
        if(!isset($objErr->arrErr['category_name'])) {
            $objQuery = new SC_Query();
            $where = "parent_category_id = ? AND category_name = ?";
            $arrRet = $objQuery->select("category_id, category_name", "dtb_category", $where, array($_POST['parent_category_id'], $array['category_name']));

            if (empty($arrRet)) {
                $arrRet = array(array("category_id" => "", "category_name" => ""));
            }

            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['category_id'] != $_POST['category_id']
                && $arrRet[0]['category_name'] == $_POST['category_name']) {
                $objErr->arrErr['category_name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }

        return $objErr->arrErr;
    }


    // 並びが1つ下のIDを取得する。
    function lfGetDownRankID($objQuery, $table, $pid_name, $id_name, $id) {
        // 親IDを取得する。
        $col = "$pid_name";
        $where = "$id_name = ?";
        $pid = $objQuery->get($table, $col, $where, $id);
        // すべての子を取得する。
        $col = "$id_name";
        $where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
        $arrRet = $objQuery->select($col, $table, $where, array($pid));
        $max = count($arrRet);
        $down_id = "";
        for($cnt = 0; $cnt < $max; $cnt++) {
            if($arrRet[$cnt][$id_name] == $id) {
                $down_id = $arrRet[($cnt + 1)][$id_name];
                break;
            }
        }
        return $down_id;
    }

    // 並びが1つ上のIDを取得する。
    function lfGetUpRankID($objQuery, $table, $pid_name, $id_name, $id) {
        // 親IDを取得する。
        $col = "$pid_name";
        $where = "$id_name = ?";
        $pid = $objQuery->get($table, $col, $where, $id);
        // すべての子を取得する。
        $col = "$id_name";
        $where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
        $arrRet = $objQuery->select($col, $table, $where, array($pid));
        $max = count($arrRet);
        $up_id = "";
        for($cnt = 0; $cnt < $max; $cnt++) {
            if($arrRet[$cnt][$id_name] == $id) {
                $up_id = $arrRet[($cnt - 1)][$id_name];
                break;
            }
        }
        return $up_id;
    }

    function lfCountChilds($objQuery, $table, $pid_name, $id_name, $id) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        return count($arrRet);
    }

    function lfUpRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $sql = "UPDATE $table SET rank = (rank + $count) WHERE $id_name IN ($line) ";
        $sql.= "AND del_flg = 0";
        $ret = $objQuery->exec($sql);
        return $ret;
    }

    function lfDownRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $sql = "UPDATE $table SET rank = (rank - $count) WHERE $id_name IN ($line) ";
        $sql.= "AND del_flg = 0";
        $ret = $objQuery->exec($sql);
        return $ret;
    }
}
?>
