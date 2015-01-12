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

namespace Eccube\Framework\DB;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;

/**
 * マスターデータを扱うクラス.
 *
 * プルダウン等で使用するマスターデータを扱う.
 * マスターデータは, DB に格納されているが, パフォーマンスを得るため,
 * 初回のみ DBへアクセスし, データを定義したキャッシュファイルを生成する.
 *
 * マスターデータのテーブルは, 下記のようなカラムが必要がある.
 * 1. キーとなる文字列
 * 2. 表示文字列
 * 3. 表示順
 * 上記カラムのデータ型は特に指定しないが, 1 と 2 は常に string 型となる.
 *
 * マスターデータがキャッシュされると, key => value 形式の配列として使用できる.
 * マスターデータのキャッシュは, MASTER_DATA_REALDIR/マスターデータ名.php というファイルが生成される.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 */
class MasterData
{

    /** @var array デフォルトのテーブルカラム名 */
    public $columns = array('id', 'name', 'rank', 'remarks');

    /**
     * マスターデータを取得する.
     *
     * 以下の順序でマスターデータを取得する.
     * 1. MASTER_DATA_REALDIR にマスターデータキャッシュが存在しない場合、
     *    DBからマスターデータを取得して、マスターデータキャッシュを生成する。
     * 2. マスターデータキャッシュを読み込み、変数に格納し返す。
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name    マスターデータ名
     * @param array  $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスターデータ
     */
    public function getMasterData($name, $columns = array())
    {
        $columns = $this->getDefaultColumnName($columns);

        $filepath = MASTER_DATA_REALDIR . $name . '.serial';

        if (!file_exists($filepath)) {
            // キャッシュ生成
            $this->createCache($name, $columns);
        }

        // キャッシュを読み込み
        $masterData = unserialize(file_get_contents($filepath));

        return $masterData;
    }

    /**
     * マスターデータをDBに追加する.
     *
     * 引数 $masterData をマスターデータとしてDBに追加し,
     * キャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name    マスターデータ名
     * @param string[]  $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param  array   $masterData マスターデータ
     * @param  bool    $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの登録数
     */
    public function registMasterData($name, $columns, $masterData, $autoCommit = true)
    {
        $columns = $this->getDefaultColumnName($columns);

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        if ($autoCommit) {
            $objQuery->begin();
        }
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[0] => (string) $key,
                            $columns[1] => (string) $val,
                            $columns[2] => (string) $i);
            $objQuery->insert($name, $sqlVal);
            $i++;
        }
        if ($autoCommit) {
            $objQuery->commit();
        }

        return $i;
    }

    /**
     * マスターデータを更新する.
     *
     * 引数 $masterData の値でマスターデータを更新する.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name    マスターデータ名
     * @param array  $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param  array   $masterData マスターデータ
     * @param  bool    $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの更新数
     */
    public function updateMasterData($name, $columns, $masterData, $autoCommit = true)
    {
        $columns = $this->getDefaultColumnName($columns);

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        if ($autoCommit) {
            $objQuery->begin();
        }

        // 指定のデータを更新
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[1] => $val);
            $objQuery->update($name, $sqlVal, $columns[0] . ' = ' .  Utils::sfQuoteSmart($key));
            $i++;
        }
        if ($autoCommit) {
            $objQuery->commit();
        }

        return $i;
    }

    /**
     * マスターデータを追加する.
     *
     * 引数 $masterData の値でマスターデータを更新する.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param  string  $name       マスターデータ名
     * @param  string  $key        キー名
     * @param  string  $comment    コメント
     * @param  bool    $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの更新数
     */
    public function insertMasterData($name, $key, $value, $comment, $autoCommit = true)
    {
        $columns = $this->getDefaultColumnName();

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        if ($autoCommit) {
            $objQuery->begin();
        }

        // 指定のデータを追加
        $sqlVal[$columns[0]] = $key;
        $sqlVal[$columns[1]] = $value;
        $sqlVal[$columns[2]] = $objQuery->max($columns[2], $name) + 1;
        $sqlVal[$columns[3]] = $comment;
        $objQuery->insert($name, $sqlVal);

        if ($autoCommit) {
            $objQuery->commit();
        }

        return 1;
    }

    /**
     * マスターデータを削除する.
     *
     * 引数 $name のマスターデータを削除し,
     * キャッシュも削除する.
     *
     * @param  string  $name       マスターデータ名
     * @param  bool    $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの削除数
     */
    public function deleteMasterData($name, $autoCommit = true)
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        if ($autoCommit) {
            $objQuery->begin();
        }

        // DB の内容とキャッシュをクリア
        $result = $objQuery->delete($name);
        $this->clearCache($name);

        if ($autoCommit) {
            $objQuery->commit();
        }

        return $result;
    }

    /**
     * マスターデータのキャッシュを消去する.
     *
     * @param  string $name マスターデータ名
     * @return boolean|null   消去した場合 true
     */
    public function clearCache($name)
    {
        $masterDataFile = MASTER_DATA_REALDIR . $name . '.php';
        if (is_file($masterDataFile)) {
            unlink($masterDataFile);
        }
        $masterDataFile = MASTER_DATA_REALDIR . $name . '.serial';
        if (is_file($masterDataFile)) {
            unlink($masterDataFile);
        }
    }

    /**
     * マスターデータのキャッシュを生成する.
     *
     * 引数 $name のマスターデータキャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     *
     * 引数 $isDefine が true の場合は, 定数を生成する.
     * 定数コメントを生成する場合は, $commentColumn を指定する.
     *
     * @param string $name          マスターデータ名
     * @param bool   $isDefine      定数を生成する場合 true
     * @param string[]  $commentColumn [0] => キー, [1] => コメント文字列,
     *                             [2] => 表示順 を表すカラム名を格納した配列
     * @return bool キャッシュの生成に成功した場合 true
     */
    public function createCache($name, $columns = array(), $isDefine = false, $commentColumn = array())
    {
        // マスターデータを取得
        $masterData = $this->getDbMasterData($name, $columns);

        // マスターデータを文字列にする
        // 定数を生成する場合
        if ($isDefine) {
            $path = MASTER_DATA_REALDIR . $name . '.php';

            $data = "<?php\n";
            // 定数コメントを生成する場合
            if (!empty($commentColumn)) {
                $data .= $this->getMasterDataAsDefine($masterData, $this->getDbMasterData($name, $commentColumn));
            } else {
                $data .= $this->getMasterDataAsDefine($masterData);
            }
            $data .=  "\n";

        // 配列を生成する場合
        } else {
            $path = MASTER_DATA_REALDIR . $name . '.serial';
            $data = serialize($masterData);
        }

        // ファイルを書き出しモードで開く
        $handle = fopen($path, 'w');
        if (!$handle) {
            return false;
        }
        // ファイルの内容を書き出す.
        if (fwrite($handle, $data) === false) {
            fclose($handle);

            return false;
        }
        fclose($handle);

        return true;
    }

    /**
     * DBからマスターデータを取得する.
     *
     * キャッシュの有無に関係なく, DBからマスターデータを検索し, 取得する.
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name    マスターデータ名
     * @param array  $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスターデータ
     */
    public function getDbMasterData($name, $columns = array())
    {
        $columns = $this->getDefaultColumnName($columns);

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        if (isset($columns[2]) && strlen($columns[2]) >= 1) {
            $objQuery->setOrder($columns[2]);
        }
        $results = $objQuery->select($columns[0] . ', ' . $columns[1], $name);

        // 結果を key => value 形式に格納
        $masterData = array();
        foreach ($results as $result) {
            $masterData[$result[$columns[0]]] = $result[$columns[1]];
        }

        return $masterData;
    }

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
    public function getDefaultColumnName($columns = array())
    {
        if (!empty($columns)) {
            return $columns;
        } else {
            return $this->columns;
        }
    }

    /**
     * マスターデータの配列を定数定義の文字列として出力する.
     *
     * @access private
     * @param  array  $masterData マスターデータの配列
     * @param  array  $comments   コメントの配列
     * @return string 定数定義の文字列
     */
    public function getMasterDataAsDefine($masterData, $comments = array())
    {
        $data = '';
        foreach ($masterData as $key => $val) {
            if (!empty($comments[$key])) {
                $data .= '/** ' . $comments[$key] . " */\n";
            }
            $data .= "define('" . $key . "', " . $val . ");\n";
        }

        return $data;
    }
}
