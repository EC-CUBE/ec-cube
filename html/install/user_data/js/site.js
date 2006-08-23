// 親ウィンドウの存在確認
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

// 郵便番号入力呼び出し
function fnCallAddress(php_url, tagname1, tagname2, input1, input2) {
	zip1 = document.form1[tagname1].value;
	zip2 = document.form1[tagname2].value;
	
	if(zip1.length == 3 && zip2.length == 4) {
		url = php_url + "?zip1=" + zip1 + "&zip2=" + zip2 + "&input1=" + input1 + "&input2=" + input2;
		window.open(url,"nomenu","width=500,height=350,scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
	} else {
		alert("郵便番号を正しく入力して下さい。");
	}
}

// 郵便番号から検索した住所を渡す。
function fnPutAddress(input1, input2) {
	// 親ウィンドウの存在確認
	if(fnIsopener()) {
		if(document.form1['state'].value != "") {
			// 項目に値を入力する。
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

// フォーカスを当てる
function fnSetFocus(name) {
	if(document.form1[name]) {
		document.form1[name].focus();
	}
}

// セレクトボックスに項目を割り当てる。
function fnSetSelect(name1, name2, val) {
	sele1 = document.form1[name1]; 
	sele2 = document.form1[name2];
	
	if(sele1 && sele2) {
		index=sele1.selectedIndex;
		
		// セレクトボックスのクリア	
		count=sele2.options.length
		for(i = count; i >= 0; i--) {
			sele2.options[i]=null;
		}
		
		// セレクトボックスに値を割り当てる
		len = lists[index].length
		for(i = 0; i < len; i++) {
			sele2.options[i]=new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele2.options[i].selected = true;
			}
		}
	}
}

// Enterキー入力をキャンセルする。(IEに対応)
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

// モードとキーを指定してSUBMITを行う。
function fnModeSubmit(mode, keyname, keyid) {

	switch(mode) {
	case 'delete':
		if(!window.confirm('入力内容を削除しても宜しいでしょうか')){
			return;
		}
		break;
	case 'confirm':
		if(!window.confirm('入力内容を登録しても宜しいでしょうか')){
			return;
		}
		break;
	case 'delete_all':
		if(!window.confirm('検索結果をすべて削除しても宜しいでしょうか')){
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
	document.forms[form]['mode'].value = mode;
	if(keyname != "" && keyid != "") {
		document.forms[form][keyname].value = keyid;
	}
	document.forms[form].submit();
}

function fnChangeAction(url) {
	document.form1.action = url;
}

// ページナビで使用する
function fnNaviPage(pageno) {
	document.form1['pageno'].value = pageno;
	document.form1.submit();
}

// ポイント入力制限
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

// 別のお届け先入力制限
function fnCheckInputDeliv() {
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


// ログイン時の入力チェック
function fnCheckLogin(formname) {
	var lstitem = new Array();
	lstitem[0] = 'login_email';
	lstitem[1] = 'login_pass';

	var max = lstitem.length;
	var errflg = false;
	var cnt = 0;
	
	//　必須項目のチェック
	for(cnt = 0; cnt < max; cnt++) {
		if(document.forms[formname][lstitem[cnt]].value == "") {
			errflg = true;
			break;
		}
	}
	
	// 必須項目が入力されていない場合	
	if(errflg == true) {
		alert('メールアドレス/パスワードを入力して下さい。');
		return false;
	}
}

// 時間の計測
function fnPassTime(){
	end_time = new Date();
	time = end_time.getTime() - start_time.getTime();
	alert((time/1000));
}
start_time = new Date();
