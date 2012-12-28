/*
 * This file is part of EC-CUBE
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
        alert(fnT('j_admin_001'));
        return false;
    } else {
        if(window.confirm(fnT('j_admin_002'))){
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
    if(window.confirm(fnT('j_admin_003'))){
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

// 管理者メンバーページの切替
function fnMemberPage(pageno) {
    location.href = "?pageno=" + pageno;
}

// ページナビで使用する
function fnNaviSearchPage(pageno, mode) {
    document.form1['search_pageno'].value = pageno;
    document.form1['mode'].value = mode;
    document.form1.submit();
}

// ページナビで使用する(mode = search専用)
function fnNaviSearchOnlyPage(pageno) {
    document.form1['search_pageno'].value = pageno;
    document.form1['mode'].value = 'search';
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
    if(window.confirm(fnT('j_admin_004'))){
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
        alert (fnT("j_admin_005"));
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

function fnAllCheck(input, selector) {
    if ($(input).attr('checked')) {
        $(selector).attr('checked', true);
    } else {
        $(selector).attr('checked', false);
    }
}

//指定されたidの削除を行うページを実行する。
function fnDelete(url) {
    if(window.confirm(fnT('j_admin_006'))){
        location.href = url;
        return false;
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

// Form指定のSubmit
function fnFormSubmit(form) {
    document.forms[form].submit();
}

// 確認メッセージ
function fnConfirm() {
    if(window.confirm(fnT('j_admin_007'))){
        return true;
    }
    return false;
}

//削除確認メッセージ
function fnDeleteConfirm() {
    if(window.confirm(fnT('j_admin_008'))){
        return true;
    }
    return false;
}

//メルマガ形式変更確認メッセージ
function fnmerumagaupdateConfirm() {
    if(window.confirm(fnT("j_admin_009"))){
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

// タグの表示非表示切り替え
function fnDispChange(disp_id, inner_id, disp_flg){
    disp_state = document.getElementById(disp_id).style.display;

    if (disp_state == "") {
        document.form1[disp_flg].value="none";
        document.getElementById(disp_id).style.display="none";
        document.getElementById(inner_id).innerHTML = fnT('j_admin_010');
    }else{
        document.form1[disp_flg].value="";
        document.getElementById(disp_id).style.display="";
        document.getElementById(inner_id).innerHTML = fnT('j_admin_011');
    }
}

// ページ読み込み時の処理
$(function(){
// ヘッダナビゲーション
    $("#navi li").hover(
        function(){
            $(this).addClass("sfhover");
        },
        function(){
            $(this).removeClass("sfhover");
        }
    );
});
//制限数判定
function fnCheckLimit(elem1, elem2, icolor) {
    if(document.form1[elem2]) {
        list = new Array(
                elem1
            );
        if(document.form1[elem2].checked) {
            fnChangeDisabled(list, icolor);
            document.form1[elem1].value = "";
        } else {
            fnChangeDisabled(list, '');
        }
    }
}
