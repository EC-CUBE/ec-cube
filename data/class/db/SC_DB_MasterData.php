<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * マスタデータを扱うクラス.
 *
 * プルダウン等で使用するマスタデータを扱う.
 * マスタデータは, DB に格納されているが, パフォーマンスを得るため,
 * 初回のみ DBへアクセスし, データを定義したキャッシュファイルを生成する.
 *
 * マスタデータのテーブルは, 下記のようなカラムが必要がある.
 * 1. キーとなる文字列
 * 2. 表示文字列
 * 3. 表示順
 * 上記カラムのデータ型は特に指定しないが, 1 と 2 は常に string 型となる.
 *
 * マスタデータがキャッシュされると, key => value 形式の配列として使用できる.
 * マスタデータのキャッシュは, MASTER_DATA_DIR/マスタデータ名.php というファイルが生成される.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_MasterData.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_MasterData {

    // {{{ properties

    /** SC_Query インスタンス */
    var $objQuery;

    /** デフォルトのテーブルカラム名 */
    var $columns = array("id", "name", "rank");

    // }}}
    // {{{ functions

    /**
     * マスタデータを取得する.
     *
     * 以下の順序でマスタデータを取得する.
     * 1. MASTER_DATA_DIR のマスタデータキャッシュを include_once() で読み込む
     * 2. 1 で読み込んだ値をチェックし, 値が変数定義されていれば値を返す.
     *    されていなければ, 次の処理を行う.
     * 3. 値が未定義の場合は, DBからマスタデータを取得する.
     * 4. 取得した後, マスタデータのキャッシュを生成し, 値を返す.
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name マスタデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスタデータ
     */
    function getMasterData($name, $columns = array()) {

        $columns = $this->getDefaultColumnName($columns);

        // 可変変数を定義
        $valiable = "_" . $name . "_master";
        // キャッシュを読み込み
        @include_once(MASTER_DATA_DIR . $name . ".php");

        // キャッシュがあれば, キャッシュの値を返す.
        if (!empty($$valiable)) {
            return $$valiable;
        }
        // マスタデータを取得
        $masterData = $this->getDbMasterData($name, $columns);
        // キャッシュ生成
        $this->createCache($name, $masterData);
        return $masterData;
    }

    /**
     * マスタデータをDBに追加する.
     *
     * 引数 $masterData をマスタデータとしてDBに追加し,
     * キャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name マスタデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param array $masterData マスタデータ
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスタデータの登録数
     */
    function registMasterData($name, $columns, $masterData, $autoCommit = true) {

        $columns = $this->getDefaultColumnName($columns);

        $this->objQuery = new SC_Query();
        if ($autoCommit) {
            $this->objQuery->begin();
        }
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[0] => $key,
                            $columns[1] => $val,
                            $columns[2] => (string) $i);
            $this->objQuery->insert($name, $sqlVal);
            $i++;
        }
        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $i;
    }

    /**
     * マスタデータを更新する.
     *
     * 引数 $masterData の値でマスタデータを更新する.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name マスタデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param array $masterData マスタデータ
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスタデータの更新数
     */
    function updateMasterData($name, $columns, $masterData, $autoCommit = true) {

        $columns = $this->getDefaultColumnName($columns);

        $this->objQuery = new SC_Query();
        if ($autoCommit) {
            $this->objQuery->begin();
        }

        // 指定のデータを更新
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[1] => $val);
            $this->objQuery->update($name, $sqlVal, $columns[0] . " = " .  $key);
            $i++;
        }
        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $i;
    }

    /**
     * マスタデータを削除する.
     *
     * 引数 $name のマスタデータを削除し,
     * キャッシュも削除する.
     *
     * @param string $name マスタデータ名
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスタデータの削除数
     */
    function deleteMasterData($name, $autoCommit = true) {
        $this->objQuery = new SC_Query();
        if ($autoCommit) {
            $this->objQuery->begin();
        }

        // DB の内容とキャッシュをクリア
        $result = $this->objQuery->delete($name);
        $this->clearCache($name);

        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $result;
    }

    /**
     * マスタデータのキャッシュを消去する.
     *
     * @param string $name マスタデータ名
     * @return bool 消去した場合 true
     */
    function clearCache($name) {
        $masterDataFile = MASTER_DATA_DIR . $name . ".php";
        if (is_file($masterDataFile)) {
            unlink($masterDataFile);
        }
    }

    /**
     * マスタデータのキャッシュを生成する.
     *
     * 引数 $name のマスタデータキャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     *
     * 引数 $isDefine が true の場合は, 定数を生成する.
     * 定数コメントを生成する場合は, $commentColumn を指定する.
     *
     * @param string $name マスタデータ名
     * @param array $masterData マスタデータ
     * @param bool $isDefine 定数を生成する場合 true
     * @param array $commentColumn [0] => キー, [1] => コメント文字列,
                                   [2] => 表示順 を表すカラム名を格納した配列
     * @return bool キャッシュの生成に成功した場合 true
     */
    function createCache($name, $masterData, $isDefine = false,
                         $commentColumn = array()) {

        // マスタデータを文字列にする
        $data = "<?php\n";
        // 定数を生成する場合
        if ($isDefine) {

            // 定数コメントを生成する場合
            if (!empty($commentColumn)) {
                $data .= $this->getMasterDataAsDefine($masterData,
                                 $this->getDbMasterData($name, $commentColumn));
            } else {
                $data .= $this->getMasterDataAsDefine($masterData);
            }

        // 配列を生成する場合
        } else {
            $data .= $this->getMasterDataAsString($name, $masterData);
        }
        $data .=  "?>\n";

        // ファイルを書き出しモードで開く
        $path = MASTER_DATA_DIR . $name . ".php";
        $handle = fopen($path, "w");
        if (!$handle) {
            return false;
        }
        // ファイルの内容を書き出す.
        if (fwrite($handle, $data) === false) {
            return false;
        }
        return true;
    }

    /**
     * DBからマスタデータを取得する.
     *
     * キャッシュの有無に関係なく, DBからマスタデータを検索し, 取得する.
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name マスタデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスタデータ
     */
    function getDbMasterData($name, $columns = array()) {

        $columns = $this->getDefaultColumnName($columns);

        $this->objQuery = new SC_Query();
        $this->objQuery->setorder($columns[2]);
        $results = $this->objQuery->select($columns[0] . ", " . $columns[1], $name);

        // 結果を key => value 形式に格納
        $masterData = array();
        foreach ($results as $result) {

            $masterData[$result[$columns[0]]] = $result[$columns[1]];
        }
        return $masterData;
    }

    // }}}
    // {{{ private functions

    /**
     * デフォルトのカラム名の配列を返す.
     *
     * 引数 $columns が空の場合, デフォルトのカラム名の配列を返す.
     * 空でない場合は, 引数の値をそのまま返す.
     *
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array カラム名を格納した配列
     */
    function getDefaultColumnName($columns = array()) {

        if (!empty($columns)) {
            return $columns;
        } else {
            return $this->columns;
        }
    }

    /**
     * マスタデータの配列を配列定義の文字列として出力する.
     *
     * @access private
     * @param string $name マスタデータ名
     * @param array $masterData マスタデータの配列
     * @return string 配列定義の文字列
     */
    function getMasterDataAsString($name, $masterData) {
        $data = "\$_" . $name . "_master = array(\n";
        $i = count($masterData);
        foreach ($masterData as $key => $val) {
            $data .= "'" . $key . "' => '" . $val . "'";
            if ($i > 1) {
                $data .= ",\n";
            }
            $i--;
        }
        $data .= ");\n";
        return $data;
    }

    /**
     * マスタデータの配列を定数定義の文字列として出力する.
     *
     * @access private
     * @param array $masterData マスタデータの配列
     * @param array $comments コメントの配列
     * @return string 定数定義の文字列
     */
    function getMasterDataAsDefine($masterData, $comments = array()) {
        $data = "";
        foreach ($masterData as $key => $val) {
            if (!empty($comments[$key])) {
                $data .= "/** " . $comments[$key] . " */\n";
            }
            $data .= "define('" . $key . "', " . $val . ");\n";
        }
        return $data;
    }
}
?>
