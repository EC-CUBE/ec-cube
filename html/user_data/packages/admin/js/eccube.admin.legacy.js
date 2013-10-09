/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

// 親ウィンドウをポストさせる。
function fnSubmitParent() {
    // 親ウィンドウの存在確認
    if(eccube.isOpener()) {
        window.opener.document.form1.submit();
    } else {
        window.close();
    }
}

//指定されたidの削除を行うページを実行する。
function fnDeleteMember(id, pageno) {
    eccube.deleteMember(id, pageno);
}

// ラジオボタンチェック状態を保存
var lstsave = "";

// ラジオボタンのチェック状態を取得する。
function fnGetRadioChecked() {
    eccube.getRadioChecked();
}

// 管理者メンバーページの切替
function fnMemberPage(pageno) {
    eccube.moveMemberPage(pageno);
}

// ページナビで使用する
function fnNaviSearchPage(pageno, mode) {
    eccube.moveNaviPage(pageno, mode);
}

// ページナビで使用する(mode = search専用)
function fnNaviSearchOnlyPage(pageno) {
    eccube.moveSearchPage(pageno);
}

// ページナビで使用する(form2)
function fnNaviSearchPage2(pageno) {
    eccube.moveSecondSearchPage(pageno);
}

// 値を代入して指定ページにsubmit
function fnSetvalAndSubmit( fname, key, val ) {
    var values = {};
    values[key] = val;
    eccube.submitForm(values, fname);
}

// 項目に入った値をクリアする。
function fnClearText(name) {
    eccube.clearValue(name);
}

// カテゴリの追加
function fnAddCat(cat_id) {
    if(window.confirm('カテゴリを登録しても宜しいでしょうか')){
        eccube.setValue("mode", "edit");
        eccube.setValue("cat_id", cat_id);
    }
}

// カテゴリの編集
function fnEditCat(parent_id, cat_id) {
    var values = {
        mode: "pre_edit",
        parent_id: parent_id,
        edit_cat_id: cat_id
    };
    eccube.submitForm(values);
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
    eccube.setModeAndSubmit("return");
}

// 規格分類登録へ移動
function fnClassCatPage(class_id) {
    eccube.moveClassCatPage(class_id);
}

function fnListCheck(list) {
    len = list.length;
    for(cnt = 0; cnt < len; cnt++) {
        document.form1[list[cnt]].checked = true;
    }
}

function fnAllCheck(input, selector) {
    eccube.checkAllBox(input, selector);
}

//指定されたidの削除を行うページを実行する。
function fnDelete(url) {
    eccube.moveDeleteUrl(url);
}

//配送料金を自動入力
function fnSetDelivFee(max) {
    eccube.setDelivFee(max);
}

// 在庫数制限判定
function fnCheckStockLimit(icolor) {
    eccube.checkStockLimit(icolor);
}

// Form指定のSubmit
function fnFormSubmit(form) {
    eccube.submitForm({}, form);
}

// 確認メッセージ
function fnConfirm() {
    return eccube.doConfirm();
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
    eccube.insertValueAndSubmit(fm, ele, val, msg);
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
        document.getElementById(inner_id).innerHTML = '<FONT Color="#FFFF99"> << 表示 </FONT>';
    }else{
        document.form1[disp_flg].value="";
        document.getElementById(disp_id).style.display="";
        document.getElementById(inner_id).innerHTML = ' <FONT Color="#FFFF99"> >> 非表示 </FONT>';
    }
}

//制限数判定
function fnCheckLimit(elem1, elem2, icolor) {
    eccube.checkLimit(elem1, elem2, icolor);
}

/**
 * ファイル管理
 */
var tree = "";                      // 生成HTML格納
var count = 0;                      // ループカウンタ
var arrTreeStatus = [];             // ツリー状態保持
var old_select_id = '';             // 前回選択していたファイル
var selectFileHidden = "";          // 選択したファイルのhidden名
var treeStatusHidden = "";          // ツリー状態保存用のhidden名
var modeHidden = "";                // modeセットhidden名

// ツリー表示
function fnTreeView(view_id, arrTree, openFolder, selectHidden, treeHidden, mode) {
    eccube.fileManager.viewFileTree(view_id, arrTree, openFolder, selectHidden, treeHidden, mode);
}

// Tree状態をhiddenにセット
function setTreeStatus(name) {
    eccube.fileManager.setTreeStatus(name);
}

// Tree状態を削除する(閉じる状態へ)
function fnDelTreeStatus(path) {
    eccube.fileManager.deleteTreeStatus(path);
}
// ツリー描画
function fnDrow(id, tree) {
    // ブラウザ取得
    MyBR = fnGetMyBrowser();
    // ブラウザ事に処理を切り分け
    switch(myBR) {
        // IE4の時の表示
        case 'I4':
            document.all(id).innerHTML = tree;
            break;
        // NN4の時の表示
        case 'N4':
            document.layers[id].document.open();
            document.layers[id].document.write("<div>");
            document.layers[id].document.write(tree);
            document.layers[id].document.write("</div>");
            document.layers[id].document.close();
            break;
        default:
            document.getElementById(id).innerHTML=tree;
            break;
    }
}

// 階層ツリーメニュー表示・非表示処理
function fnTreeMenu(tName, imgName, path) {
    eccube.fileManager.toggleTreeMenu(tName, imgName, path);
}

// ファイルリストダブルクリック処理
function fnDbClick(arrTree, path, is_dir, now_dir, is_parent) {
    eccube.fileManager.doubleClick(arrTree, path, is_dir, now_dir, is_parent);
}

// フォルダオープン処理
function fnFolderOpen(path) {
    eccube.fileManager.openFolder(path);
}

// 閲覧ブラウザ取得
function fnGetMyBrowser() {
    myOP = window.opera;            // OP
    myN6 = document.getElementById; // N6
    myIE = document.all;            // IE
    myN4 = document.layers;         // N4
    if      (myOP) myBR="O6";       // OP6以上
    else if (myIE) myBR="I4";       // IE4以上
    else if (myN6) myBR="N6";       // NS6以上
    else if (myN4) myBR="N4";       // NN4
    else           myBR="";         // その他

    return myBR;
}

// imgタグの画像変更
function fnChgImg(fileName,imgName){
    $("#" + imgName).attr("src", fileName);
}

// ファイル選択
function fnSelectFile(id, val) {
    eccube.fileManager.selectFile(id);
}
