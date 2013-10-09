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

function chgImg(fileName,img){
    if (typeof(img) == "object") {
        img.src = fileName;
    } else {
        document.images[img].src = fileName;
    }
}

function chgImgImageSubmit(fileName,imgObj){
    imgObj.src = fileName;
}

function win01(URL,Winname,Wwidth,Wheight){
    var option = {scrollbars: "no", resizable: "no"};
    eccube.openWindow(URL,Winname,Wwidth,Wheight,option);
}

function win02(URL,Winname,Wwidth,Wheight){
    eccube.openWindow(URL,Winname,Wwidth,Wheight);
}

function win03(URL,Winname,Wwidth,Wheight){
    var option = {menubar: "no"};
    eccube.openWindow(URL,Winname,Wwidth,Wheight,option);
}

function winSubmit(URL,formName,Winname,Wwidth,Wheight){
    var option = {menubar: "no", formTarget: formName};
    eccube.openWindow(URL,Winname,Wwidth,Wheight,option);
}

// 親ウィンドウの存在確認.
function fnIsopener() {
    return eccube.isOpener();
}

// 郵便番号入力呼び出し.
function fnCallAddress(php_url, tagname1, tagname2, input1, input2) {
    eccube.getAddress(php_url, tagname1, tagname2, input1, input2);
}

// 郵便番号から検索した住所を渡す.
function fnPutAddress(input1, input2, state, city, town) {
    eccube.putAddress(input1, input2, state, city, town);
}

function fnOpenNoMenu(URL) {
    window.open(URL,"nomenu","scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
}

function fnOpenWindow(URL,name,width,height) {
    var option = {resizable: "no", focus: false};
    eccube.openWindow(URL,name,width,height,option);
}

function fnSetFocus(name) {
    eccube.setFocus(name);
}

// セレクトボックスに項目を割り当てる.
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

        // セレクトボックスに値を割り当てる。
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
    eccube.setModeAndSubmit(mode, keyname, keyid);
}

function fnFormModeSubmit(form, mode, keyname, keyid) {
    eccube.fnFormModeSubmit(form, mode, keyname, keyid);
}

function fnSetFormSubmit(form, key, val) {
    return eccube.setValueAndSubmit(form, key, val);
}

function fnSetVal(key, val) {
    eccube.setValue(key, val);
}

function fnSetFormVal(form, key, val) {
    eccube.setValue(key, val, form);
}

function fnChangeAction(url) {
    eccube.changeAction(url);
}

// ページナビで使用する。
function fnNaviPage(pageno) {
    eccube.movePage(pageno);
}

function fnSearchPageNavi(pageno) {
    eccube.movePage(pageno, 'search');
}

function fnSubmit(){
    eccube.submitForm();
}

// ポイント入力制限。
function fnCheckInputPoint() {
    eccube.togglePointForm();
}

// 別のお届け先入力制限。
function fnCheckInputDeliv() {
    eccube.toggleDeliveryForm();
}

// 最初に設定されていた色を保存しておく。
var g_savecolor = new Array();

function fnChangeDisabled(list, color) {
    eccube.changeDisabled(list, color);
}

// ログイン時の入力チェック
function fnCheckLogin(formname) {
    return eccube.checkLoginFormInputted(formname);
}

// 時間の計測.
function fnPassTime(){
    end_time = new Date();
    time = end_time.getTime() - start_time.getTime();
    alert((time/1000));
}
start_time = new Date();

//親ウィンドウのページを変更する.
function fnUpdateParent(url) {
    eccube.changeParentUrl(url);
}

//特定のキーをSUBMITする.
function fnKeySubmit(keyname, keyid) {
    var values = {};
    values[keyname] = keyid;
    eccube.submitForm(values);
}

//文字数をカウントする。
//引数1：フォーム名称
//引数2：文字数カウント対象
//引数3：カウント結果格納対象
function fnCharCount(form,sch,cnt) {
    eccube.countChars(form,sch,cnt);
}

// テキストエリアのサイズを変更する.
function ChangeSize(buttonSelector, textAreaSelector, max, min) {
    eccube.toggleRows(buttonSelector, textAreaSelector, max, min);
}

/**
 * 規格2のプルダウンを設定する.
 */
function setClassCategories($form, product_id, $sele1, $sele2, selected_id2) {
    eccube.setClassCategories($form, product_id, $sele1, $sele2, selected_id2);
}

/**
 * 規格の選択状態に応じて, フィールドを設定する.
 */
function checkStock($form, product_id, classcat_id1, classcat_id2) {
    eccube.checkStock($form, product_id, classcat_id1, classcat_id2);
}

gCssUA = navigator.userAgent.toUpperCase();
gCssBrw = navigator.appName.toUpperCase();

with (document) {
    write("<style type=\"text/css\"><!--");

    //WIN-IE
    if (gCssUA.indexOf("WIN") != -1 && gCssUA.indexOf("MSIE") != -1) {
        write(".fs10 {font-size: 62.5%; line-height: 150%; letter-spacing:1px;}");
        write(".fs12 {font-size: 75%; line-height: 150%; letter-spacing:1.5px;}");
        write(".fs14 {font-size: 87.5%; line-height: 150%; letter-spacing:2px;}");
        write(".fs18 {font-size: 117.5%; line-height: 130%; letter-spacing:2.5px;}");
        write(".fs22 {font-size: 137.5%; line-height: 130%; letter-spacing:3px;}");
        write(".fs24 {font-size: 150%; line-height: 130%; letter-spacing:3px;}");
        write(".fs30 {font-size: 187.5%; line-height: 125%; letter-spacing:3.5px;}");
        write(".fs10n {font-size: 62.5%; letter-spacing:1px;}");
        write(".fs12n {font-size: 75%; letter-spacing:1.5px;}");
        write(".fs14n {font-size: 87.5%; letter-spacing:2px;}");
        write(".fs18n {font-size: 117.5%; letter-spacing:2.5px;}");
        write(".fs22n {font-size: 137.5%; letter-spacing:1px;}");
        write(".fs24n {font-size: 150%; letter-spacing:1px;}");
        write(".fs30n {font-size: 187.5%; letter-spacing:1px;}");
        write(".fs12st {font-size: 75%; line-height: 150%; letter-spacing:1.5px; font-weight: bold;}");
    }

    //WIN-NN
    if (gCssUA.indexOf("WIN") != -1 && gCssBrw.indexOf("NETSCAPE") != -1) {
        write(".fs10 {font-size:72%; line-height:130%;}");
        write(".fs12 {font-size: 75%; line-height: 150%;}");
        write(".fs14 {font-size: 87.5%; line-height: 140%;}");
        write(".fs18 {font-size: 117.5%; line-height: 130%;}");
        write(".fs22 {font-size: 137.5%; line-height: 130%;}");
        write(".fs24 {font-size: 150%; line-height: 130%;}");
        write(".fs30 {font-size: 187.5%; line-height: 120%;}");
        write(".fs10n {font-size:72%;}");
        write(".fs12n {font-size: 75%;}");
        write(".fs14n {font-size: 87.5%;}");
        write(".fs18n {font-size: 117.5%;}");
        write(".fs22n {font-size: 137.5%;}");
        write(".fs24n {font-size: 150%;}");
        write(".fs30n {font-size: 187.5%;}");
        write(".fs12st {font-size: 75%; line-height: 150%; font-weight: bold;}");
    }

    //WIN-NN4.x
    if ( navigator.appName == "Netscape" && navigator.appVersion.substr(0,2) == "4." ) {
        write(".fs10 {font-size:90%; line-height: 130%;}");
        write(".fs12 {font-size: 100%; line-height: 140%;}");
        write(".fs14 {font-size: 110%; line-height: 135%;}");
        write(".fs18 {font-size: 130%; line-height: 175%;}");
        write(".fs24 {font-size: 190%; line-height: 240%;}");
        write(".fs30 {font-size: 240%; line-height: 285%;}");
        write(".fs10n {font-size:90%;}");
        write(".fs12n {font-size: 100%;}");
        write(".fs14n {font-size: 110%;}");
        write(".fs18n {font-size: 130%;}");
        write(".fs24n {font-size: 190%;}");
        write(".fs30n {font-size: 240%;}");
        write(".fs12st {font-size: 100%; line-height: 140%; font-weight: bold;}");
    }

    //MAC
    if (gCssUA.indexOf("MAC") != -1) {
        write(".fs10 {font-size: 10px; line-height: 14px;}");
        write(".fs12 {font-size: 12px; line-height: 18px;}");
        write(".fs14 {font-size: 14px; line-height: 18px;}");
        write(".fs18 {font-size: 18px; line-height: 23px;}");
        write(".fs22 {font-size: 22px; line-height: 27px;}");
        write(".fs24 {font-size: 24px; line-height: 30px;}");
        write(".fs30 {font-size: 30px; line-height: 35px;}");
        write(".fs10n {font-size: 10px;}");
        write(".fs12n {font-size: 12px;}");
        write(".fs14n {font-size: 14px;}");
        write(".fs18n {font-size: 18px;}");
        write(".fs22n {font-size: 22px;}");
        write(".fs24n {font-size: 24px;}");
        write(".fs30n {font-size: 30px;}");
        write(".fs12st {font-size: 12px; line-height: 18px; font-weight: bold;}");
    }

    write("--></style>");
}
