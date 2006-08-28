
// 管理者メンバーを追加する。
function fnRegistMember() {
	// 必須項目の名前、ログインID、パスワード、権限
	var lstitem = new Array();
	lstitem[0] = 'name';
	lstitem[1] = 'login_id';
	lstitem[2] = 'password';
	lstitem[3] = 'authority';
	
	var max = lstitem.length;
	var errflg = false;
	var cnt = 0;
	
	//　必須項目のチェック
	for(cnt = 0; cnt < max; cnt++) {
		if(document.form1[lstitem[cnt]].value == "") {
			errflg = true;
			break;
		}
	}
	
	// 必須項目が入力されていない場合	
	if(errflg == true) {
		alert('必須項目を入力して下さい。');
		return false;
	} else {
		if(window.confirm('内容を登録しても宜しいでしょうか')){
			return true;
		} else {  
			return false;
		}
	}
}

//親ウィンドウのページを変更する。
function fnUpdateParent(url) {
	// 親ウィンドウの存在確認
	if(fnIsopener()) {
		window.opener.location.href = url;
	} else {
		window.close();
	}		
}

// 親ウィンドウをポストさせる。
function fnSubmitParent() {
	// 親ウィンドウの存在確認
	if(fnIsopener()) {
		window.opener.document.form1.submit();
	} else {
		window.close();
	}		
}

//指定されたidの削除を行うページを実行する。
function fnDeleteMember(id, pageno) {
	url = "./delete.php?id=" + id + "&pageno=" + pageno;
	if(window.confirm('登録内容を削除しても宜しいでしょうか')){
		location.href = url;
	}
}

// ラジオボタンチェック状態を保存
var lstsave = "";

// ラジオボタンのチェック状態を取得する。
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
			/* radioボタンは同じ名前が２回続けて検出されるので、
			   最初の名前の検出であるかどうかの判定 */
			// 1回目の検出
			if(startname != name) {
				startname = name;	
				ret = document.form1.elements[cnt].checked;
				if(ret == true){
					// 稼働がチェックされている。
					lstsave[name] = 1;
				}	
			// 2回目の検出
			} else {
				ret = document.form1.elements[cnt].checked;
				if(ret == true){
					// 非稼働がチェックされている。
					lstsave[name] = 0;
				}
			}
		}
	}
}

// ラジオボタンに変更があったか判定する。
function fnChangeRadio(name, no, id, pageno) {
	// 最初の取得状態から変更ありの場合
	if(lstsave[name] != no) {
		// DB反映ページ実行
		url = "./check.php?id=" + id + "&no=" + no + "&pageno=" + pageno;
		location.href = url;
	}
}

// 管理者メンバーページの切替
function fnMemberPage(pageno) {
	location.href = "./index.php?pageno=" + pageno;
}

// ページナビで使用する
function fnNaviSearchPage(pageno, mode) {
	document.form1['search_pageno'].value = pageno;
	document.form1['mode'].value = mode;
	document.form1.submit();
}

// ページナビで使用する(form2)
function fnNaviSearchPage2(pageno) {
	document.form2['search_pageno'].value = pageno;
	document.form2['mode'].value = 'search';
	document.form2.submit();
}

// 値を代入して指定ページにsubmit
function fnSetvalAndSubmit( fname, key, val ) {
	fm = document[fname];
	fm[key].value = val;
	fm.submit();
}

// 項目に入った値をクリアする。
function fnClearText(name) {
	document.form1[name].value = "";
}

// カテゴリの追加
function fnAddCat(cat_id) {
	if(window.confirm('カテゴリを登録しても宜しいでしょうか')){
		document.form1['mode'].value = 'edit';
		document.form1['cat_id'].value = cat_id;
	}
}

// カテゴリの編集
function fnEditCat(parent_id, cat_id) {
	document.form1['mode'].value = 'pre_edit';
	document.form1['parent_id'].value = parent_id;
	document.form1['edit_cat_id'].value = cat_id;
	document.form1.submit();
}

// 選択カテゴリのチェック
function fnCheckCat(obj) {
	val = obj[obj.selectedIndex].value;
	if (val == ""){
		alert ("親カテゴリは選択できません");
		obj.selectedIndex = 0;
	}
}

// 確認ページから登録ページへ戻る
function fnReturnPage() {
	document.form1['mode'].value = 'return';
	document.form1.submit();
}

// 規格分類登録へ移動
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

//指定されたidの削除を行うページを実行する。
function fnDelete(url) {
	if(window.confirm('登録内容を削除しても宜しいでしょうか')){
		location.href = url;
	}
}

//配送料金を自動入力
function fnSetDelivFee(max) {
	for(cnt = 1; cnt <= max; cnt++) {
		name = "fee" + cnt;
		document.form1[name].value = document.form1['fee_all'].value;
	}
}

// 在庫数制限判定
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

// 在庫数制限判定
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

// 購入制限数判定
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

// 在庫数判定
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

// Form指定のSubmit 
function fnFormSubmit(form) {
	document.forms[form].submit();
}

// 確認メッセージ
function fnConfirm() {
	if(window.confirm('この内容で登録しても宜しいでしょうか')){
		return true;
	}
	return false;
}

//削除確認メッセージ
function fnDeleteConfirm() {
	if(window.confirm('削除しても宜しいでしょうか')){
		return true;
	}
	return false;
}

//メルマガ形式変更確認メッセージ
function fnmerumagaupdateConfirm() {
	if(window.confirm("既に登録されているメールアドレスです。\nメルマガの種類が変更されます。宜しいですか？")){
		return true;
	}
	return false;
}

// フォームに代入してからサブミットする。
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

// 自分以外の要素を有効・無効にする
function fnSetDisabled ( f_name, e_name, flag ) {
	fm = document[f_name];
	
	//　必須項目のチェック
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


//リストボックス内の項目を移動する
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

//リストボックス内の項目を削除する
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

//一行目の価格を以下の行にコピーする
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

// タグの表示非表示切り替え
function fnDispChange(disp_id, inner_id, disp_flg){
	disp_state = document.getElementById(disp_id).style.display;
	
	if (disp_state == "") {
		document.form1[disp_flg].value="none";
		document.getElementById(disp_id).style.display="none";
		document.getElementById(inner_id).innerHTML = "<<表示";
	}else{
		document.form1[disp_flg].value="";
		document.getElementById(disp_id).style.display="";
		document.getElementById(inner_id).innerHTML = ">>非表示"; 
	}
	
}

	function naviStyleChange(ids, color){
		document.getElementById(ids).style.backgroundColor = color;
	}	


	