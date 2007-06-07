<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
/*
    $host:FTPホスト
    $user:FTPユーザ
    $pass:FTPパスワード
    $dst_file:送信先ファイル（相対パス）
    $src_file:送信元ファイル（絶対パス）
 */
function sfFtpCopy($host, $user, $pass, $dst_file, $src_file) {
    // FTP接続
    $conn_id = ftp_connect($host); 

    // 接続確認
    if((!$conn_id)) {
        gfPrintLog("FTP接続に失敗しました。SERVER:" . $host);
        return;
    }
    
    // FTPログイン
    $login_result = ftp_login($conn_id, $user, $pass); 
        
    if ((!$login_result)) { 
        gfPrintLog("FTPログインに失敗しました。USER:" . $user . " SERVER:" . $host);
        return;
    }
    
    // 送信元ファイルの存在チェック
    if (is_file($src_file)) {
        
        // ファイル送信
        $upload = ftp_put($conn_id, $dst_file, $src_file, FTP_BINARY); 
    } else {
        gfPrintLog("FTP送信元ファイルが見つかりません。" . $src_file);
        return;
    }
    
    // ファイルアップロード
    if (!$upload) { 
        gfPrintLog("アップロードに失敗しました。SERVER:" . $host . " " . $src_file . " -> " . $dst_file);
    } else {
        gfPrintLog("アップロードに成功しました。SERVER:" . $host . " " . $src_file . " -> " . $dst_file);
    }

    // 接続を閉じる 
    ftp_close($conn_id);
}

function sfFtpDelete($host, $user, $pass, $dst_file) {
    // FTP接続
    $conn_id = @ftp_connect($host); 
    
    // 接続確認
    if((!$conn_id)) {
        gfPrintLog("FTP接続に失敗しました。SERVER:" . $host);
        return;
    }
    
    // FTPログイン
    $login_result = @ftp_login($conn_id, $user, $pass); 
    
    if ((!$login_result)) { 
        gfPrintLog("FTPログインに失敗しました。USER:" . $user . " SERVER:" . $host);
        return;
    }
        
    // ファイル削除
    if (@ftp_delete($conn_id, $dst_file)) { 
        gfPrintLog("ファイル削除に成功しました。SERVER:" . $host . " " . $dst_file);
    } else {
        gfPrintLog("ファイル削除に失敗しました。SERVER:" . $host . " " . $dst_file);
    }
    
    // 接続を閉じる 
    ftp_close($conn_id);
}

 /* 
 * 関数名 ：sfFtpExist
 * 引数1　:FTPホスト
 * $user:FTPユーザ
 * $pass:FTPパスワード
 * $file_path:ファイルパス
 * 戻り値：存在したらtrue無かったらfalseを返す
 * 説明　：FTPするサーバにファイルが無いか調査する
 */
 function sfFtpExist($host, $user, $pass, $file_path) {

    $conn_id = ftp_connect($host);
    $login_result = ftp_login($conn_id, $user, $pass);
    $res = ftp_size($conn_id, $file_path);

    // 戻り値が-1ならファイルが存在しない
    if($res == -1) {
        return false;
    }
        
    return true;
}
?>
