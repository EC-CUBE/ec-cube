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
 * @version $Id$
 */
class SC_DB_MasterData {

    // {{{ properties

    /** SC_Query インスタンス */
    var $objQuery;

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
    function getMasterData($name, $columns) {
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

        // キャッシュを消去
        $this->clearCache($name);
        // 新規にデータを取得してキャッシュ生成
        $newData = $this->getMasterData($name, $columns);
        return $i;
    }

    /**
     * マスタデータを更新する.
     *
     * 引数 $masterData の値でマスタデータを更新し,
     * キャッシュを更新する.
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
        $this->objQuery = new SC_Query();
        if ($autoCommit) {
            $this->objQuery->begin();
        }
        // マスタデータを削除
        $this->deleteMasterData($name, false);

        // マスタデータを追加
        $this->registMasterData($name, $columns, $masterData, false);

        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return count($masterData);
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
     * @param string $name マスタデータ名
     * @param array $masterData マスタデータ
     * @return bool キャッシュの生成に成功した場合 true
     */
    function createCache($name, $masterData) {

        // 配列の定義を文字列にする
        $data = "<?php\n"
              . "\$_" . $name . "_master = array(\n";
        $i = count($masterData);
        foreach ($masterData as $key => $val) {
            $data .= "'" . $key . "' => '" . $val . "'";
            if ($i > 1) {
                $data .= ",\n";
            }
            $i--;
        }
        $data .= ");\n"
              .  "?>\n";

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

    // }}}
    // {{{ private functions

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
    function getDbMasterData($name, $columns) {
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
}
?>
