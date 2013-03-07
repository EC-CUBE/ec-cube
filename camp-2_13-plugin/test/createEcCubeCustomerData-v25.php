#!/usr/local/bin/php -q
<?php
/*
 * EC-CUBE 動作検証用会員データ生成スクリプト
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @auther Kentaro Habu
 * @version $Id$
 */

// {{{ requires
/** 適宜、htmlディレクトリへのrequire.phpを読み込めるよう パスを書き換えて下さい */
require_once(dirname(__FILE__) . "/../html/require.php");

// }}}
// {{{ constants

/** 会員の生成数 */
define('CUSTOMERS_VOLUME', 100);    // ※最大値:99999までで指定してください

/**
 * 会員メールアドレスのアカウント名
 * アカウント名の後ろに「+99999」の形で連番をつけて、別名アドレス(エイリアス)の形でメールアドレスを登録します。
 * 実際にメールを受信するためには、メールサーバが別名アドレスに対応している必要があります。
 * (例えば、Gmailは別名アドレスに対応しています)
 */
define('EMAIL_ADDRESS_ACCOUNT', 'test');

/**
 * 会員メールアドレスのドメイン名
 */
define('EMAIL_ADDRESS_DOMAIN', "@localhost");

// }}}
// {{{ Logic
set_time_limit(0);
while (@ob_end_flush());

$objData = new CreateEcCubeCustomerData();
$start = microtime(true);

/*
 * ※このスクリプトは、会員データの作成に
 * SC_Helper_Customer_Ex::sfEditCustomerData()を利用しており、
 * この関数内で、begin～commitされているので、
 * このスクリプト側でbegin～rollback/commitする事はできません。
 */
//$objData->objQuery->begin();

// 会員生成
print("creating Customer Data(for Test)...\n");
$objData->createCustomers();

//$objData->objQuery->rollback();
//$objData->objQuery->commit();
$end = microtime(true);
/* 
 * Windowsのコマンドプロンプトで文字化けしないように、
 * 標準出力に出すメッセージにはマルチバイト文字を使用しないようにした。
 * (「chcp 65001」を実行してもWindows7では文字化けした)
 */
print("create customer data DONE!\n");
printf("elapsed time: %f sec\n", $end - $start);
lfPrintLog(sprintf("elapsed time: %f sec\n", $end - $start));
exit;

// }}}
// {{{ classes

/**
 * EC-CUBE のテスト用会員データを生成する
 */
class CreateEcCubeCustomerData 
{

    /** SC_Query インスタンス */
    var $objQuery;

    /**
     * コンストラクタ.
     */
    function CreateEcCubeCustomerData()
    {
        $this->objQuery = new SC_Query();
    }

    /**
     * テスト用 会員データ を生成する.
     *
     * @return void
     */
    function createCustomers()
    {
        lfPrintLog("createCustomers START.(" . CUSTOMERS_VOLUME . " data)");
        for ($i = 0; $i < CUSTOMERS_VOLUME; $i++) {
            lfPrintLog("----------");
            lfPrintLog("creating customer data count:[" . ($i+1) . "] start.");
            
            $sqlval['name01'] = "検証";
            $sqlval['name02'] = sprintf("太郎%05d", $i+1);
            $sqlval['kana01'] = "ケンショウ";
            $sqlval['kana02'] = "タロウ";
            $sqlval['zip01'] = '101';
            $sqlval['zip02'] = '0051';
            $sqlval['pref'] = '13';	// 13:東京都
            $sqlval['addr01'] = "千代田区神田神保町";
            $sqlval['addr02'] = "1-3-5";
            $sqlval['tel01'] = '012';
            $sqlval['tel02'] = '3456';
            $sqlval['tel03'] = '7890';
            $sqlval['email'] = EMAIL_ADDRESS_ACCOUNT . "+" . sprintf("%05d", $i+1) . EMAIL_ADDRESS_DOMAIN;
            $sqlval['sex'] = '1';    // 1:男性 2:女性
            $sqlval['password'] = 'test';
            $sqlval['reminder'] = '1';    // 1:「母親の旧姓は？」
            $sqlval['reminder_answer'] = "てすと";
            $sqlval['mailmaga_flg'] = (string) '1';    // 1:HTMLメール＋テキストメールを受け取る 2:テキストメールを受け取る 3:受け取らない

            // 生年月日の作成
            $sqlval['birth']    = SC_Utils_Ex::sfGetTimestamp(2006, 9, 1);

            // 仮会員 1 本会員 2
            $sqlval['status']   = '2';

            /*
             * secret_keyは、テーブルで重複許可されていない場合があるので、
             * 本会員登録では利用されないがセットしておく。
             */
            $sqlval['secret_key'] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();

            // 入会時ポイント
            $CONF = SC_Helper_DB_Ex::sfGetBasisData();
            $sqlval['point'] = $CONF['welcome_point'];

            // 会員データの生成
            SC_Helper_Customer_Ex::sfEditCustomerData($sqlval);

            print("*");
            lfPrintLog("creating customer data count:[" . ($i+1) . "] end.");
        }
        print("\n");
        lfPrintLog("createCustomers DONE.(" . CUSTOMERS_VOLUME . " data created)");
    }

}

/** テスト用スクリプトのログ出力関数 */
function lfPrintLog($mess)
{
    $path = DATA_REALDIR . "logs/" .  basename(__FILE__, '.php') . ".log";
    GC_Utils::gfPrintLog($mess, $path);
}
