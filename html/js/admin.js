/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
// �����ԥ��С����ɲä��롣
function fnRegistMember() {
	// ɬ�ܹ��ܤ�̾����������ID���ѥ���ɡ�����
	var lstitem = new Array();
	lstitem[0] = 'name';
	lstitem[1] = 'login_id';
	lstitem[2] = 'password';
	lstitem[3] = 'authority';
	
	var max = lstitem.length;
	var errflg = false;
	var cnt = 0;
	
	//��ɬ�ܹ��ܤΥ����å�
	for(cnt = 0; cnt < max; cnt++) {
		if(document.form1[lstitem[cnt]].value == "") {
			errflg = true;
			break;
		}
	}
	
	// ɬ�ܹ��ܤ����Ϥ���Ƥ��ʤ����	
	if(errflg == true) {
		alert('ɬ�ܹ��ܤ����Ϥ��Ʋ�������');
		return false;
	} else {
		if(window.confirm('���Ƥ���Ͽ���Ƥ⵹�����Ǥ��礦��')){
			return true;
		} else {  
			return false;
		}
	}
}

//�ƥ�����ɥ��Υڡ������ѹ����롣
function fnUpdateParent(url) {
	// �ƥ�����ɥ���¸�߳�ǧ
	if(fnIsopener()) {
		window.opener.location.href = url;
	} else {
		window.close();
	}		
}

// �ƥ�����ɥ���ݥ��Ȥ����롣
function fnSubmitParent() {
	// �ƥ�����ɥ���¸�߳�ǧ
	if(fnIsopener()) {
		window.opener.document.form1.submit();
	} else {
		window.close();
	}		
}

//���ꤵ�줿id�κ����Ԥ��ڡ�����¹Ԥ��롣
function fnDeleteMember(id, pageno) {
	url = "./delete.php?id=" + id + "&pageno=" + pageno;
	if(window.confirm('��Ͽ���Ƥ������Ƥ⵹�����Ǥ��礦��')){
		location.href = url;
	}
}

// �饸���ܥ�������å����֤���¸
var lstsave = "";

// �饸���ܥ���Υ����å����֤�������롣
function fnGetRadioChecked() {
	var max;
	var cnt;
	var names = "";
	var startname = "";
	var ret;
	max = document.form1.elements.length;
	lstsave = Array(max);
	for(cnt = 0; cnt < max; cnt++) {
		if(document.form1.elements[cnt].type == 'radio') {
			name = document.form1.elements[cnt].name;
			/* radio�ܥ����Ʊ��̾��������³���Ƹ��Ф����Τǡ�
			   �ǽ��̾���θ��ФǤ��뤫�ɤ�����Ƚ�� */
			// 1���ܤθ���
			if(startname != name) {
				startname = name;	
				ret = document.form1.elements[cnt].checked;
				if(ret == true){
					// ��Ư�������å�����Ƥ��롣
					lstsave[name] = 1;
				}	
			// 2���ܤθ���
			} else {
				ret = document.form1.elements[cnt].checked;
				if(ret == true){
					// ���Ư�������å�����Ƥ��롣
					lstsave[name] = 0;
				}
			}
		}
	}
}

// �饸���ܥ�����ѹ������ä���Ƚ�ꤹ�롣
function fnChangeRadio(name, no, id, pageno) {
	// �ǽ�μ������֤����ѹ�����ξ��
	if(lstsave[name] != no) {
		// DBȿ�ǥڡ����¹�
		url = "./check.php?id=" + id + "&no=" + no + "&pageno=" + pageno;
		location.href = url;
	}
}

// �����ԥ��С��ڡ���������
function fnMemberPage(pageno) {
	location.href = "./index.php?pageno=" + pageno;
}

// �ڡ����ʥӤǻ��Ѥ���
function fnNaviSearchPage(pageno, mode) {
	document.form1['search_pageno'].value = pageno;
	document.form1['mode'].value = mode;
	document.form1.submit();
}

// �ڡ����ʥӤǻ��Ѥ���(mode = search����)
function fnNaviSearchOnlyPage(pageno) {
	document.form1['search_pageno'].value = pageno;
	document.form1['mode'].value = 'search';
	document.form1.submit();
}

// �ڡ����ʥӤǻ��Ѥ���(form2)
function fnNaviSearchPage2(pageno) {
	document.form2['search_pageno'].value = pageno;
	document.form2['mode'].value = 'search';
	document.form2.submit();
}

// �ͤ��������ƻ���ڡ�����submit
function fnSetvalAndSubmit( fname, key, val ) {
	fm = document[fname];
	fm[key].value = val;
	fm.submit();
}

// ���ܤ����ä��ͤ򥯥ꥢ���롣
function fnClearText(name) {
	document.form1[name].value = "";
}

// ���ƥ�����ɲ�
function fnAddCat(cat_id) {
	if(window.confirm('���ƥ������Ͽ���Ƥ⵹�����Ǥ��礦��')){
		document.form1['mode'].value = 'edit';
		document.form1['cat_id'].value = cat_id;
	}
}

// ���ƥ�����Խ�
function fnEditCat(parent_id, cat_id) {
	document.form1['mode'].value = 'pre_edit';
	document.form1['parent_id'].value = parent_id;
	document.form1['edit_cat_id'].value = cat_id;
	document.form1.submit();
}

// ���򥫥ƥ���Υ����å�
function fnCheckCat(obj) {
	val = obj[obj.selectedIndex].value;
	if (val == ""){
		alert ("�ƥ��ƥ��������Ǥ��ޤ���");
		obj.selectedIndex = 0;
	}
}

// ��ǧ�ڡ���������Ͽ�ڡ��������
function fnReturnPage() {
	document.form1['mode'].value = 'return';
	document.form1.submit();
}

// ����ʬ����Ͽ�ذ�ư
function fnClassCatPage(class_id) {
	location.href =  "./classcategory.php?class_id=" + class_id;
}

function fnSetFormValue(name, val) {
	document.form1[name].value = val;
}

function fnListCheck(list) {
	len = list.length;
	for(cnt = 0; cnt < len; cnt++) {
		document.form1[list[cnt]].checked = true;
	}
}

function fnAllCheck() {
	cnt = 1;
	name = "check:" + cnt;
	while (document.form1[name]) {
		document.form1[name].checked = true;
		cnt++;
		name = "check:" + cnt;
	}
}

function fnAllUnCheck() {
	cnt = 1;
	name = "check:" + cnt;
	while (document.form1[name]) {
		document.form1[name].checked = false;
		cnt++;
		name = "check:" + cnt;
	}
}

//���ꤵ�줿id�κ����Ԥ��ڡ�����¹Ԥ��롣
function fnDelete(url) {
	if(window.confirm('��Ͽ���Ƥ������Ƥ⵹�����Ǥ��礦��')){
		location.href = url;
	}
}

//���������ư����
function fnSetDelivFee(max) {
	for(cnt = 1; cnt <= max; cnt++) {
		name = "fee" + cnt;
		document.form1[name].value = document.form1['fee_all'].value;
	}
}

// �߸˿�����Ƚ��
function fnCheckStockLimit(icolor) {
	if(document.form1['stock_unlimited']) {
		list = new Array(
			'stock'
			);
		if(document.form1['stock_unlimited'].checked) {
			fnChangeDisabled(list, icolor);
			document.form1['stock'].value = "";
		} else {
			fnChangeDisabled(list, '');
		}
	}
}

// �߸˿�����Ƚ��
function fnCheckStockNoLimit(no, icolor) {
	$check_key = "stock_unlimited:"+no;
	$input_key = "stock:"+no;
	
	list = new Array($input_key	);
	if(document.form1[$check_key].checked) {
		fnChangeDisabled(list, icolor);
		document.form1[$input_key].value = "";
	} else {
		fnChangeDisabled(list, '');
	}
}

// �������¿�Ƚ��
function fnCheckSaleLimit(icolor) {
	list = new Array(
		'sale_limit'
		);	
	if(document.form1['sale_unlimited'].checked) {
		fnChangeDisabled(list, icolor);
		document.form1['sale_limit'].value = "";
	} else {
		fnChangeDisabled(list, '');
	}
}

// �߸˿�Ƚ��
function fnCheckAllStockLimit(max, icolor) {
	for(no = 1; no <= max; no++) {
		$check_key = "stock_unlimited:"+no;
		$input_key = "stock:"+no;
		
		list = new Array($input_key);
	
		if(document.form1[$check_key].checked) {
			fnChangeDisabled(list, icolor);
			document.form1[$input_key].value = "";
		} else {
			fnChangeDisabled(list, '');
		}
	}
}

// Form�����Submit 
function fnFormSubmit(form) {
	document.forms[form].submit();
}

// ��ǧ��å�����
function fnConfirm() {
	if(window.confirm('�������Ƥ���Ͽ���Ƥ⵹�����Ǥ��礦��')){
		return true;
	}
	return false;
}

//�����ǧ��å�����
function fnDeleteConfirm() {
	if(window.confirm('������Ƥ⵹�����Ǥ��礦��')){
		return true;
	}
	return false;
}

//���ޥ������ѹ���ǧ��å�����
function fnmerumagaupdateConfirm() {
	if(window.confirm("������Ͽ����Ƥ���᡼�륢�ɥ쥹�Ǥ���\n���ޥ��μ��ब�ѹ�����ޤ����������Ǥ�����")){
		return true;
	}
	return false;
}

// �ե�������������Ƥ��饵�֥ߥåȤ��롣
function fnInsertValAndSubmit( fm, ele, val, msg ){
	
	if ( msg ){
		ret = window.confirm(msg);
	} else {
		ret = true;
	}
	if( ret ){
		fm[ele].value = val;
		fm.submit();
		return false;
	}
	return false;
}

// ��ʬ�ʳ������Ǥ�ͭ����̵���ˤ���
function fnSetDisabled ( f_name, e_name, flag ) {
	fm = document[f_name];
	
	//��ɬ�ܹ��ܤΥ����å�
	for(cnt = 0; cnt < fm.elements.length; cnt++) {
		if( fm[cnt].name != e_name && fm[cnt].name != 'subm' && fm[cnt].name != 'mode') {
			fm[cnt].disabled = flag;
			if ( flag == true ){
				fm[cnt].style.backgroundColor = "#cccccc";
			} else {
				fm[cnt].style.backgroundColor = "#ffffff";
			}
		}
	}
}


//�ꥹ�ȥܥå�����ι��ܤ��ư����
function fnMoveCat(sel1, sel2, mode_name) {
	var fm = document.form1;
	for(i = 0; i < fm[sel1].length; i++) {
		if(fm[sel1].options[i].selected) {
			if(fm[sel2].value != "") {
				fm[sel2].value += "-" + fm[sel1].options[i].value;
			} else {
				fm[sel2].value = fm[sel1].options[i].value;
			}
		}
	}
	fm["mode"].value = mode_name;
	fm.submit();
}

//�ꥹ�ȥܥå�����ι��ܤ�������
function fnDelListContents(sel1, sel2, mode_name) {
	fm = document.form1;
	for(j = 0; j < fm[sel1].length; j++) {
		if(fm[sel1].options[i].selected) {
			fm[sel2].value = fm[sel2].value.replace(fm[sel1].options[i].value, "");
		}
	}
	
	fm["mode"].value = mode_name;
	fm.submit();
}

//����ܤβ��ʤ�ʲ��ιԤ˥��ԡ�����
function fnCopyValue(length, icolor) {
	fm = document.form1;
	for(i = 1; i <= length; i++) {
		fm['product_code:' + i].value = fm['product_code:1'].value;
		fm['stock:' + i].value = fm['stock:1'].value;
		fm['price01:' + i].value = fm['price01:1'].value;
		fm['price02:' + i].value = fm['price02:1'].value;
		fm['stock_unlimited:' + i].checked = fm['stock_unlimited:1'].checked;
		fm['stock:' + i].disabled = fm['stock:1'].disabled;		
		fm['stock:' + i].style.backgroundColor = fm['stock:1'].style.backgroundColor;
	}	
}

// ������ɽ����ɽ���ڤ��ؤ�
function fnDispChange(disp_id, inner_id, disp_flg){
	disp_state = document.getElementById(disp_id).style.display;
	
	if (disp_state == "") {
		document.form1[disp_flg].value="none";
		document.getElementById(disp_id).style.display="none";
		document.getElementById(inner_id).innerHTML = '<FONT Color="#FFFF99"> << ɽ�� </FONT>';
	}else{
		document.form1[disp_flg].value="";
		document.getElementById(disp_id).style.display="";
		document.getElementById(inner_id).innerHTML = ' <FONT Color="#FFFF99"> >> ��ɽ�� </FONT>'; 
	}
}



	