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
    if(window.confirm('登録内容を削除しても宜しいでしょうか')){
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
            eccube.changeDisabled(list, icolor);
            document.form1['stock'].value = "";
        } else {
            eccube.changeDisabled(list, '');
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
            eccube.changeDisabled(list, icolor);
            document.form1[elem1].value = "";
        } else {
            eccube.changeDisabled(list, '');
        }
    }
}

(function($) {
    /**
     * パンくず
     */
    var o;

    $.fn.breadcrumbs = function(options) {
        var defaults = {
            bread_crumbs: '',
            start_node: '<span>ホーム</span>',
            anchor_node: '<a onclick="eccube.setModeAndSubmit(\'tree\', \'parent_category_id\', '
                + '{category_id}); return false" href="javascript:;" />',
            delimiter_node: '<span>&nbsp;&gt;&nbsp;</span>'
        };

        return this.each(function() {
            if (options) {
                o = $.fn.extend(defaults, options);
            }
            var $this = $(this);
            var total = o.bread_crumbs.length;
            var $node = $(o.start_node);

            for (var i = total - 1; i >= 0; i--) {
                if (i == total -1) $node.append(o.delimiter_node);

                var anchor = o.anchor_node
                    .replace(/{category_id}/ig,
                        o.bread_crumbs[i].category_id);
                $(anchor)
                    .text(o.bread_crumbs[i].category_name)
                    .appendTo($node);

                if (i > 0) $node.append(o.delimiter_node);
            }
            $this.html($node);
            return this;
        });
    };
})(jQuery);

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
    selectFileHidden = selectHidden;
    treeStatusHidden = treeHidden;
    modeHidden = mode;

    for(i = 0; i < arrTree.length; i++) {
        id = arrTree[i][0];
        level = arrTree[i][3];

        if(i == 0) {
            old_id = "0";
            old_level = 0;
        } else {
            old_id = arrTree[i-1][0];
            old_level = arrTree[i-1][3];
        }

        // 階層上へ戻る
        if(level <= (old_level - 1)) {
            tmp_level = old_level - level;
            for(up_roop = 0; up_roop <= tmp_level; up_roop++) {
                tree += '</div>';
            }
        }

        // 同一階層で次のフォルダへ
        if(id != old_id && level == old_level) tree += '</div>';

        // 階層の分だけスペースを入れる
        for(space_cnt = 0; space_cnt < arrTree[i][3]; space_cnt++) {
            tree += "&nbsp;&nbsp;&nbsp;";
        }

        // 階層画像の表示・非表示処理
        if(arrTree[i][4]) {
            if(arrTree[i][1] == '_parent') {
                rank_img = IMG_MINUS;
            } else {
                rank_img = IMG_NORMAL;
            }
            // 開き状態を保持
            arrTreeStatus.push(arrTree[i][2]);
            display = 'block';
        } else {
            if(arrTree[i][1] == '_parent') {
                rank_img = IMG_PLUS;
            } else {
                rank_img = IMG_NORMAL;
            }
            display = 'none';
        }

        arrFileSplit = arrTree[i][2].split("/");
        file_name = arrFileSplit[arrFileSplit.length-1];

        // フォルダの画像を選択
        if(arrTree[i][2] == openFolder) {
            folder_img = IMG_FOLDER_OPEN;
            file_name = "<b>" + file_name + "</b>";
        } else {
            folder_img = IMG_FOLDER_CLOSE;
        }

        // 階層画像に子供がいたらオンクリック処理をつける
        if(rank_img != IMG_NORMAL) {
            tree += '<a href="javascript:fnTreeMenu(\'tree'+ i +'\',\'rank_img'+ i +'\',\''+ arrTree[i][2] +'\')"><img src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'">';
        } else {
            tree += '<img src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'">';
        }
        tree += '<a href="javascript:fnFolderOpen(\''+ arrTree[i][2] +'\')"><img src="'+ folder_img +'" border="0" name="tree_img'+ i +'" id="tree_img'+ i +'">&nbsp;'+ file_name +'</a><br/>';
        tree += '<div id="tree'+ i +'" style="display:'+ display +'">';
    }
    fnDrow(view_id, tree);
    //document.tree_form.tree_test2.focus();
}

// Tree状態をhiddenにセット
function setTreeStatus(name) {
    var tree_status = "";
    for(i=0; i < arrTreeStatus.length ;i++) {
        if(i != 0) tree_status += '|';
        tree_status += arrTreeStatus[i];
    }
    document.form1[name].value = tree_status;
}

// Tree状態を削除する(閉じる状態へ)
function fnDelTreeStatus(path) {
    for(i=0; i < arrTreeStatus.length ;i++) {
        if(arrTreeStatus[i] == path) {
            arrTreeStatus[i] = "";
        }
    }
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
    tMenu = $("#" + tName);

    if(tMenu.css("display") == 'none') {
        fnChgImg(IMG_MINUS, imgName);
        tMenu.show();
        // 階層の開いた状態を保持
        arrTreeStatus.push(path);
    } else {
        fnChgImg(IMG_PLUS, imgName);
        tMenu.hide();
        // 閉じ状態を保持
        fnDelTreeStatus(path);
    }
}

// ファイルリストダブルクリック処理
function fnDbClick(arrTree, path, is_dir, now_dir, is_parent) {
    if(is_dir) {
        if(!is_parent) {
            for(cnt = 0; cnt < arrTree.length; cnt++) {
                if(now_dir == arrTree[cnt][2]) {
                    open_flag = false;
                    for(status_cnt = 0; status_cnt < arrTreeStatus.length; status_cnt++) {
                        if(arrTreeStatus[status_cnt] == arrTree[cnt][2]) open_flag = true;
                    }
                    if(!open_flag) fnTreeMenu('tree'+cnt, 'rank_img'+cnt, arrTree[cnt][2]);
                }
            }
        }
        fnFolderOpen(path);
    } else {
        // Download
        eccube.setModeAndSubmit('download','','');
    }
}

// フォルダオープン処理
function fnFolderOpen(path) {
    // クリックしたフォルダ情報を保持
    document.form1[selectFileHidden].value = path;
    // treeの状態をセット
    setTreeStatus(treeStatusHidden);
    // submit
    eccube.setModeAndSubmit(modeHidden,'','');
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
    old_select_id = id;
}
