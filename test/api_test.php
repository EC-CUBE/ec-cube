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
※このプログラムにはセキュリティ考慮が一切されていませんので取り扱いには注意をして下さい。
<hr />
<form action="?" method="POST" id="form">
<input type="hidden" name="mode" id="mode" value="" />
EndPoint:<input type="text" name="EndPoint" style="width:400px;" value="<?php echo htmlspecialchars($_REQUEST['EndPoint']); ?>" /><select name="type">
<option value="json.php" <?php if($type =='json.php'){ echo 'selected';} ?>>json.php</option>
<option value="xml.php" <?php if($type =='xml.php'){ echo 'selected';} ?>>xml.php</option>
<option value="php.php" <?php if($type =='php.php'){ echo 'selected';} ?>>php.php</option>
<option value="index.php" <?php if($type =='index.php'){ echo 'selected';} ?>>index.php</option>
</select><br />
Service:<input type="text" name="Service" value="<?php echo htmlspecialchars($_REQUEST['Service']); ?>" /><br />
Operation:<input type="text" name="Operation" value="<?php echo htmlspecialchars($_REQUEST['Operation']); ?>" /><br />
<?php
for ($i = 0; $i < 10; $i++) {
    echo 'ExtArg[' . $i . ']:<input type="text" name="arg_key' . $i . '" value="' . htmlspecialchars($_REQUEST['arg_key' . $i]) . '" />:'
            . '<input type="text" name="arg_val' . $i . '" value="' . htmlspecialchars($_REQUEST['arg_val' . $i]) . '" /><br />';
}
?>
AccessKeyId: <input type="text" name="AccessKeyId" value="<?php echo htmlspecialchars($_REQUEST['AccessKeyId']); ?>" />&nbsp;
SecretKey: <input type="text" name="SecretKey" value="<?php echo htmlspecialchars($_REQUEST['SecretKey']); ?>" />&nbsp;<br />
<input type="button" value="Signature生成⇒" onclick="makeSignature();" />
Timestamp: <input type="text" name="Timestamp" value="<?php echo htmlspecialchars($_REQUEST['Timestamp']); ?>" />&nbsp;Signature: <input type="text" name="Signature" id="Signature" value="<?php echo htmlspecialchars($_REQUEST['Signature']); ?>" readonly /><br />
<?php if($check_str != "") {
    echo "<pre>{$check_str}</pre><br />";
} ?>
<input type="submit" />
</form>
<hr />
REST URI: <a href="<?php echo $url;?>">Link</a><br />
<textarea rows="1" cols="60"><?php echo htmlspecialchars($url);?></textarea>
<hr />
Response:<br />
<textarea rows="5" cols="100">
<?php echo htmlspecialchars($response);?>
</textarea>
</pre>
<hr />
Response decode:<br />
<pre>
<?php
if($type =="json.php") {
      var_dump(json_decode($response));
}else if($type=="xml.php") {
      $xml = simplexml_load_string($response);
      var_dump($xml);
      var_dump(libxml_get_errors () );
}else if($type=="php.php") {
      var_dump(unserialize($response));
}
?>
</pre>
<hr />
<?php if($type=="json.php" && $_REQUEST['Signature'] == "") {?>
JavaScript:<div id="res"></div>
<hr />
<pre id="dump"></pre>
<hr />

<script type="text/javascript">//<![CDATA[
    var query_params = {
        Service: '<?php echo $_REQUEST['Service'];?>',
        Operation: '<?php echo $_REQUEST['Operation'];?>'
        <?php
    for($i =0; $i <10; $i++) {
        if($_REQUEST['arg_key' . $i] != "") {
            echo ',' . $_REQUEST['arg_key' . $i] . ': \'' . $_REQUEST['arg_val' . $i] . '\'' . "\n";
        }
    }
        ?>
        };
    $(function(){
        var recvdata = function(data,textstatus) {
            $('#res').text(textstatus);
            var str = var_dump(data);
            $('#dump').text(str);
        }
        var recverror = function (result, textstatus, errorThrown) {
            $('#res').text(textstatus);
        }
        $.ajax({
                type: "GET",
                url: "<?php echo $_REQUEST['EndPoint'];?>json.php",
                dataType: 'json',
                data: query_params,
                success: recvdata,
                error: recverror
                 });
    });
//]]></script>
<?php } ?>

</body>
</html>
