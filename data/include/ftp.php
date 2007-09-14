<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
/*
    $host:FTP�ۥ���
    $user:FTP�桼��
    $pass:FTP�ѥ����
    $dst_file:������ե���������Хѥ���
    $src_file:�������ե���������Хѥ���
 */
function sfFtpCopy($host, $user, $pass, $dst_file, $src_file) {
    // FTP��³
    $conn_id = ftp_connect($host); 

    // ��³��ǧ
    if((!$conn_id)) {
        gfPrintLog("FTP��³�˼��Ԥ��ޤ�����SERVER:" . $host);
        return;
    }
    
    // FTP������
    $login_result = ftp_login($conn_id, $user, $pass); 
        
    if ((!$login_result)) { 
        gfPrintLog("FTP������˼��Ԥ��ޤ�����USER:" . $user . " SERVER:" . $host);
        return;
    }
    
    // �������ե������¸�ߥ����å�
    if (is_file($src_file)) {
        
        // �ե���������
        $upload = ftp_put($conn_id, $dst_file, $src_file, FTP_BINARY); 
    } else {
        gfPrintLog("FTP�������ե����뤬���Ĥ���ޤ���" . $src_file);
        return;
    }
    
    // �ե����륢�åץ���
    if (!$upload) { 
        gfPrintLog("���åץ��ɤ˼��Ԥ��ޤ�����SERVER:" . $host . " " . $src_file . " -> " . $dst_file);
    } else {
        gfPrintLog("���åץ��ɤ��������ޤ�����SERVER:" . $host . " " . $src_file . " -> " . $dst_file);
    }

    // ��³���Ĥ��� 
    ftp_close($conn_id);
}

function sfFtpDelete($host, $user, $pass, $dst_file) {
    // FTP��³
    $conn_id = @ftp_connect($host); 
    
    // ��³��ǧ
    if((!$conn_id)) {
        gfPrintLog("FTP��³�˼��Ԥ��ޤ�����SERVER:" . $host);
        return;
    }
    
    // FTP������
    $login_result = @ftp_login($conn_id, $user, $pass); 
    
    if ((!$login_result)) { 
        gfPrintLog("FTP������˼��Ԥ��ޤ�����USER:" . $user . " SERVER:" . $host);
        return;
    }
        
    // �ե�������
    if (@ftp_delete($conn_id, $dst_file)) { 
        gfPrintLog("�ե����������������ޤ�����SERVER:" . $host . " " . $dst_file);
    } else {
        gfPrintLog("�ե��������˼��Ԥ��ޤ�����SERVER:" . $host . " " . $dst_file);
    }
    
    // ��³���Ĥ��� 
    ftp_close($conn_id);
}

 /* 
 * �ؿ�̾ ��sfFtpExist
 * ����1��:FTP�ۥ���
 * $user:FTP�桼��
 * $pass:FTP�ѥ����
 * $file_path:�ե�����ѥ�
 * ����͡�¸�ߤ�����true̵���ä���false���֤�
 * ��������FTP���륵���Ф˥ե����뤬̵����Ĵ������
 */
 function sfFtpExist($host, $user, $pass, $file_path) {

    $conn_id = ftp_connect($host);
    $login_result = ftp_login($conn_id, $user, $pass);
    $res = ftp_size($conn_id, $file_path);

    // ����ͤ�-1�ʤ�ե����뤬¸�ߤ��ʤ�
    if($res == -1) {
        return false;
    }
        
    return true;
}
?>
