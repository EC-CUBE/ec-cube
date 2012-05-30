<?php
/*
 * APIの動作確認・検証用プログラム
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 */

/**
 * APIの動作確認・検証用プログラム
 *  MEMO:本プログラム自体は、EC-CUBE側には一切依存しませんので、クリアな環境でテスト出来る簡易プログラムです。
 *       初期化や入力チェックを省いている為、display_errorsを強制的にOffにしています。
 *
 * @package Test
 * @author Spirit of Co.,Ltd.
 * @version $Id$
 */
ini_set('display_errors', 'Off');
if($_REQUEST['EndPoint'] && $_REQUEST['Service'] && $_REQUEST['Operation']) {
    $url = "{$_REQUEST['EndPoint']}{$_REQUEST['type']}?Service={$_REQUEST['Service']}&Operation={$_REQUEST['Operation']}";
    for($i =0; $i <10; $i++) {
        if($_REQUEST['arg_key' . $i] != "") {
            $url .= '&' . $_REQUEST['arg_key' . $i] . '=' . $_REQUEST['arg_val' . $i];
        }
    }
    if($_REQUEST['mode'] == 'signature') {
        $arrParam = array();
        if($_REQUEST['Timestamp'] == '') {
            $arrParam['Timestamp'] = date('Y-m-d') . 'T' . date('h:i:s') .'Z';
        }else{
            $arrParam['Timestamp'] = $_REQUEST['Timestamp'];
        }
        $arrParam['AccessKeyId'] = $_REQUEST['AccessKeyId'];

        $arrParam['Service'] = $_REQUEST['Service'];
        $arrParam['Operation'] = $_REQUEST['Operation'];
        for($i =0; $i <10; $i++) {
            if($_REQUEST['arg_key' . $i] != "") {
                $arrParam[ $_REQUEST['arg_key' . $i] ] = $_REQUEST['arg_val' . $i];
            }
        }
        ksort($arrParam);
        $check_str = '';
        foreach($arrParam as $key => $val) {
            if($val != "") {
                $check_str .= '&' . str_replace('%7E', '~', rawurlencode($key)) . '=' . str_replace('%7E', '~', rawurlencode($val));
            }
        }
        $check_str = substr($check_str,1);
        $arrParseUrl = parse_url($_REQUEST['EndPoint'] . $_REQUEST['type']);
        $check_str = "GET\n" . $arrParseUrl['host'] . "\n" . $arrParseUrl['path'] . "\n" . $check_str;
        $_REQUEST['Signature'] = base64_encode(hash_hmac('sha256', $check_str, $_REQUEST['SecretKey'], true));
    }
    if($_REQUEST['mode'] != 'signature') {
        if($_REQUEST['Signature'] != "") {
            $signature = urlencode($_REQUEST['Signature']);
            $url .= "&AccessKeyId={$_REQUEST['AccessKeyId']}&Timestamp={$_REQUEST['Timestamp']}&Signature={$signature}";
        }
        $response = file_get_contents($url);
    }
}
$type = $_REQUEST['type'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
function var_dump(obj) {
   var str = '';
   $.each(obj, function (key, value) {
            if(typeof value == "object" && value != null) {
                str += " Key: \"" + key + "\" {\n" + var_dump(value) + "}\n";
            }else{
                str += " Key: \"" + key + "\" Type: " + typeof(value) + " Value: \"" + value + "\"";
            }
         });
   return str;
}

function makeSignature() {
    $('#mode').val("signature");
    $('#form').submit();
}


</script>
</head>
<body>
EC-CUBE API TEST<br />
※このプログラムに