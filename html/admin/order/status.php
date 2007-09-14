<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

//���ơ���������-����ư��ǽ

class LC_Page {
    function LC_Page() {
        $this->tpl_mainpage = 'order/status.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'status';
        global $arrORDERSTATUS;
        global $arrORDERSTATUS_COLOR;
        $this->arrORDERSTATUS = $arrORDERSTATUS;
        $this->arrORDERSTATUS_COLOR = $arrORDERSTATUS_COLOR;
        
    }
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

//���ơ���������ʲ����
$objPage->SelectedStatus = $_POST['status'];
$objPage->arrForm = $_POST;

//��ʧ��ˡ�μ���
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

switch ($_POST['mode']){
    
    case 'search':
        switch($_POST['change_status']){
                
            default:
            break;
            
            //��������
            case ORDER_NEW:
                lfStatusMove(ORDER_NEW, $_POST['move']);
            break;
            
            //�����Ԥ�
            case ORDER_PAY_WAIT:
                lfStatusMove(ORDER_PAY_WAIT, $_POST['move']);
            break;
            
            //����󥻥�
            case ORDER_CANCEL:
                lfStatusMove(ORDER_CANCEL, $_POST['move']);
            break;
            
            //������
            case ORDER_BACK_ORDER:
                lfStatusMove(ORDER_BACK_ORDER, $_POST['move']);
            break;
            
            //ȯ���Ѥ�
            case ORDER_DELIV:
                lfStatusMove(ORDER_DELIV, $_POST['move']);
            break;
            
            //����Ѥ�
            case ORDER_PRE_END:
                lfStatusMove(ORDER_PRE_END, $_POST['move']);
            break;
            
            //���
            case 'delete':
                lfStatusMove("delete",$_POST['move']);
            break;
        }
        
        //������̤�ɽ��
        lfStatusDisp($_POST['status'],$_POST['search_pageno']);
        break;
        
    default:
        //�ǥե���Ȥǿ������հ���ɽ��
        lfStatusDisp(ORDER_NEW, $_POST['search_pageno']);
        $objPage->defaultstatus = ORDER_NEW;
        break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

//���ơ�����������ɽ��
function lfStatusDisp($status,$pageno){
    global $objPage;
    global $objQuery;
    
    $select ="*";
    $from = "dtb_order";
    $where="del_flg=0 AND status=?";
    $order = "order_id DESC";
    
    $linemax = $objQuery->count("dtb_order", "del_flg = 0 AND status=?", array($status));
    $objPage->tpl_linemax = $linemax;
    
    // �ڡ�������ν���
    $page_max = ORDER_STATUS_MAX;
    
    // �ڡ�������μ���
    $objNavi = new SC_PageNavi($pageno, $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
    $objPage->tpl_strnavi = $objNavi->strnavi;      // ɽ��ʸ����
    $startno = $objNavi->start_row;
    
    $objPage->tpl_pageno = $pageno;
    
    // �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
    $objQuery->setlimitoffset($page_max, $startno);
    
    //ɽ�����
    $objQuery->setorder($order);
    
    //������̤μ���
    $objPage->arrStatus = $objQuery->select($select, $from, $where, array($status));
    
    return $objPage;
}

//���ơ���������ι����ʺ����
function lfStatusMove($status_id, $arrMove){
    global $objQuery;
    global $objPage;
    global $arrORDERSTATUS;
    
    $table = 'dtb_order';
    $where = 'order_id = ?';
    $arrUpdate = array('update_date' => 'NOW()');
    
    $delflg  = '1'; // ����ե饰
    $message = '';  // ���ơ������ѹ���˥ݥåץ��åפ����å�����������
    
    if ( $status_id == 'delete' ) {
        $arrUpdate['del_flg'] = $delflg;
        $message = '���';
    }
    // ���ơ�������ȯ���Ѥߤλ���ȯ�����򹹿�
    elseif ( $status_id == ORDER_DELIV ) {
        $arrUpdate['status'] = $status_id;
        $arrUpdate['commit_date'] = 'NOW()';
        $message = $arrORDERSTATUS[$status_id] . '�ذ�ư';
    }
    else {
        $arrUpdate['status'] = $status_id;
        $message = $arrORDERSTATUS[$status_id] . '�ذ�ư';
    }
    
    if ( isset($arrMove) ){
        foreach ( $arrMove as $val ){
            if ( $val != "" ) {
                $objQuery->update($table, $arrUpdate, $where, array($val));
            }
            
        }
    }
    
    $objPage->tpl_onload = "window.alert('������ܤ�" . $message . "���ޤ�����');";
}

?>