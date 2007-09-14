/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
// �ƥ�����ɥ���¸�߳�ǧ.
function fnIsopener() {
    var ua = navigator.userAgent;
    if( !!window.opener ) {
        if( ua.indexOf('MSIE 4')!=-1 && ua.indexOf('Win')!=-1 ) {
            return !window.opener.closed;
        } else {
        	return typeof window.opener.document == 'object';
        }
	} else {
		return false;
	}
}

// ͹���ֹ����ϸƤӽФ�.
function fnCallAddress(php_url, tagname1, tagname2, input1, input2) {
	zip1 = document.form1[tagname1].value;
	zip2 = document.form1[tagname2].value;
	
	if(zip1.length == 3 && zip2.length == 4) {
		url = php_url + "?zip1=" + zip1 + "&zip2=" + zip2 + "&input1=" + input1 + "&input2=" + input2;
		window.open(url,"nomenu","width=500,height=350,scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
	} else {
		alert("͹���ֹ�����������Ϥ��Ʋ�������");
	}
}

// ͹���ֹ椫�鸡������������Ϥ�.
function fnPutAddress(input1, input2) {
	// �ƥ�����ɥ���¸�߳�ǧ��.
	if(fnIsopener()) {
		if(document.form1['state'].value != "") {
			// ���ܤ��ͤ����Ϥ���.
			state_id = document.form1['state'].value;
			town = document.form1['city'].value + document.form1['town'].value;
			window.opener.document.form1[input1].selectedIndex = state_id;
			window.opener.document.form1[input2].value = town;
		}
	} else {
		window.close();
	}		
}

function fnOpenNoMenu(URL) {
	window.open(URL,"nomenu","scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
}

function fnOpenWindow(URL,name,width,height) {
	window.open(URL,name,"width="+width+",height="+height+",scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no");
}

function fnSetFocus(name) {
	if(document.form1[name]) {
		document.form1[name].focus();
	}
}

// ���쥯�ȥܥå����˹��ܤ������Ƥ�.
function fnSetSelect(name1, name2, val) {
	sele1 = document.form1[name1]; 
	sele2 = document.form1[name2];
	
	if(sele1 && sele2) {
		index=sele1.selectedIndex;
		
		// ���쥯�ȥܥå����Υ��ꥢ	
		count=sele2.options.length
		for(i = count; i >= 0; i--) {
			sele2.options[i]=null;
		}
		
		// ���쥯�ȥܥå������ͤ������Ƥ롣
		len = lists[index].length
		for(i = 0; i < len; i++) {
			sele2.options[i]=new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele2.options[i].selected = true;
			}
		}
	}
}

// Enter�������Ϥ򥭥�󥻥뤹�롣(IE���б�)
function fnCancelEnter()
{
	if (gCssUA.indexOf("WIN") != -1 && gCssUA.indexOf("MSIE") != -1) {
		if (window.event.keyCode == 13)
		{
			return false;
		}
	}
	return true;
}

// �⡼�ɤȥ�������ꤷ��SUBMIT��Ԥ���
function fnModeSubmit(mode, keyname, keyid) {
	switch(mode) {
	case 'delete_category':
		if(!window.confirm('���򤷤����ƥ���ȥ��ƥ�����Τ��٤ƤΥ��ƥ���������ޤ�')){
			return;
		}
		break;
	case 'delete':
		if(!window.confirm('���ٺ�������ǡ����ϡ������᤻�ޤ���\n������Ƥ⵹�����Ǥ�����')){
			return;
		}
		break;
	case 'confirm':
		if(!window.confirm('��Ͽ���Ƥ⵹�����Ǥ���')){
			return;
		}
		break;
	case 'delete_all':
		if(!window.confirm('������̤򤹤٤ƺ�����Ƥ⵹�����Ǥ���')){
			return;
		}
		break;
	default:
		break;
	}
	document.form1['mode'].value = mode;
	if(keyname != "" && keyid != "") {
		document.form1[keyname].value = keyid;
	}
	document.form1.submit();
}

function fnFormModeSubmit(form, mode, keyname, keyid) {
	switch(mode) {
	case 'delete':
		if(!window.confirm('���ٺ�������ǡ����ϡ������᤻�ޤ���\n������Ƥ⵹�����Ǥ�����')){
			return;
		}
		break;
	case 'confirm':
		if(!window.confirm('��Ͽ���Ƥ⵹�����Ǥ���')){
			return;
		}
		break;
	case 'regist':
		if(!window.confirm('��Ͽ���Ƥ⵹�����Ǥ���')){
			return;
		}
		break;		
	default:
		break;
	}
	document.forms[form]['mode'].value = mode;
	if(keyname != "" && keyid != "") {
		document.forms[form][keyname].value = keyid;
	}
	document.forms[form].submit();
}

function fnSetFormSubmit(form, key, val) {
	document.forms[form][key].value = val;
	document.forms[form].submit();
	return false;
}

function fnSetFormVal(form, key, val) {
	document.forms[form][key].value = val;
}

function fnChangeAction(url) {
	document.form1.action = url;
}

// �ڡ����ʥӤǻ��Ѥ��롣
function fnNaviPage(pageno) {
	document.form1['pageno'].value = pageno;
	document.form1.submit();
}

function fnSearchPageNavi(pageno) {
	document.form1['pageno'].value = pageno;
	document.form1['mode'].value = 'search';
	document.form1.submit();
	}

	function fnSubmit(){
	document.form1.submit();
}

// �ݥ�����������¡�
function fnCheckInputPoint() {
	if(document.form1['point_check']) {
		list = new Array(
						'use_point'
						);
	
		if(!document.form1['point_check'][0].checked) {
			color = "#dddddd";
			flag = true;
		} else {
			color = "";
			flag = false;
		}
		
		len = list.length
		for(i = 0; i < len; i++) {
			if(document.form1[list[i]]) {
				document.form1[list[i]].disabled = flag;
				document.form1[list[i]].style.backgroundColor = color;
			}
		}
	}
}

// �̤Τ��Ϥ����������¡�
function fnCheckInputDeliv() {
	if(!document.form1) {
		return;
	}
	if(document.form1['deliv_check']) {
		list = new Array(
						'deliv_name01',
						'deliv_name02',
						'deliv_kana01',
						'deliv_kana02',
						'deliv_pref',
						'deliv_zip01',
						'deliv_zip02',
						'deliv_addr01',
						'deliv_addr02',
						'deliv_tel01',
						'deliv_tel02',
						'deliv_tel03'
						);
	
		if(!document.form1['deliv_check'].checked) {
			fnChangeDisabled(list, '#dddddd');
		} else {
			fnChangeDisabled(list, '');
		}
	}
}


// �����������Ͽ�������¡�
function fnCheckInputMember() {
	if(document.form1['member_check']) {
		list = new Array(
						'password',
						'password_confirm',
						'reminder',
						'reminder_answer'
						);

		if(!document.form1['member_check'].checked) {
			fnChangeDisabled(list, '#dddddd');
		} else {
			fnChangeDisabled(list, '');
		}
	}
}

// �ǽ�����ꤵ��Ƥ���������¸���Ƥ�����
var g_savecolor = new Array();

function fnChangeDisabled(list, color) {
	len = list.length;
	
	for(i = 0; i < len; i++) {
		if(document.form1[list[i]]) {
			if(color == "") {
				// ͭ���ˤ��롣
				document.form1[list[i]].disabled = false;
				document.form1[list[i]].style.backgroundColor = g_savecolor[list[i]];
			} else {
				// ̵���ˤ��롣
				document.form1[list[i]].disabled = true;
				g_savecolor[list[i]] = document.form1[list[i]].style.backgroundColor;
				document.form1[list[i]].style.backgroundColor = color;//"#f0f0f0";	
			}			
		}
	}
}


// ������������ϥ����å�
function fnCheckLogin(formname) {
	var lstitem = new Array();
	
	if(formname == 'login_mypage'){
	lstitem[0] = 'mypage_login_email';
	lstitem[1] = 'mypage_login_pass';
	}else{
	lstitem[0] = 'login_email';
	lstitem[1] = 'login_pass';
	}
	var max = lstitem.length;
	var errflg = false;
	var cnt = 0;
	
	//��ɬ�ܹ��ܤΥ����å�
	for(cnt = 0; cnt < max; cnt++) {
		if(document.forms[formname][lstitem[cnt]].value == "") {
			errflg = true;
			break;
		}
	}
	
	// ɬ�ܹ��ܤ����Ϥ���Ƥ��ʤ����	
	if(errflg == true) {
		alert('�᡼�륢�ɥ쥹/�ѥ���ɤ����Ϥ��Ʋ�������');
		return false;
	}
}
	
// ���֤η�¬.
function fnPassTime(){
	end_time = new Date();
	time = end_time.getTime() - start_time.getTime();
	alert((time/1000));
}
start_time = new Date();

//�ƥ�����ɥ��Υڡ������ѹ�����.
function fnUpdateParent(url) {
	// �ƥ�����ɥ���¸�߳�ǧ
	if(fnIsopener()) {
		window.opener.location.href = url;
	} else {
		window.close();
	}		
}

//����Υ�����SUBMIT����.
function fnKeySubmit(keyname, keyid) {
	if(keyname != "" && keyid != "") {
		document.form1[keyname].value = keyid;
	}
	document.form1.submit();
}

//ʸ�����򥫥���Ȥ��롣
//����1���ե�����̾��
//����2��ʸ������������о�
//����3��������ȷ�̳�Ǽ�о�
function fnCharCount(form,sch,cnt) {
	document.forms[form][cnt].value= document.forms[form][sch].value.length;
}


// �ƥ����ȥ��ꥢ�Υ��������ѹ�����.
function ChangeSize(button, TextArea, Max, Min, row_tmp){
	
	if(TextArea.rows <= Min){
		TextArea.rows=Max; button.value="����������"; row_tmp.value=Max;
	}else{
		TextArea.rows =Min; button.value="�礭������"; row_tmp.value=Min;
	}
}

//͹���ֹ椫�齻��򸡺����뤿��������Ʊ��Ū�˥ե����फ��ǡ����١������������롣
function fnSendZipcode(){
	
	var zip01 = document.getElementsByName("order_zip01").item(0).value;
	var zip02 = document.getElementsByName("order_zip02").item(0).value;
	var checkNum = new RegExp("[^0-9]","g");
	
	if(checkNum.test(zip01 + zip02)){
		alert("���������Ϥ��Ƥ���������");
		document.getElementsByName("order_zip01").item(0).value = "";
		document.getElementsByName("order_zip02").item(0).value = "";
	}else if(zip01.length >= 3 && zip02.length >= 4){
		//input_zip_json.php��͹���ֹ������������äƤ����ǡ�����fnReturnAddress���Ϥ�
		sendRequest(fnReturnAddress,'&zip01='+zip01+'&zip02='+zip02,'GET','../input_zip_json.php',true,true);
	}
}

function fnReturnAddress(val){
	
	eval("var log ="  + val.responseText);
	if(log.flag ==1){
	document.getElementsByName("order_pref").item(0).value = log.pref;
	document.getElementsByName("order_addr01").item(0).value =log.city + log.town;
	}
}