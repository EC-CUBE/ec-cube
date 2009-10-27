<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/admin.js"></script>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( product_id, class_name1, class_name2 ){
    var err_text = '';
    var fm = window.opener.document.form1;
    var fm1 = window.opener.document;
    var class1 = "classcategory_id" + product_id + "_1";
    var class2 = "classcategory_id" + product_id + "_2";

    var class1_id = document.getElementById(class1).value;
    var class2_id = document.getElementById(class2).value;

    <!--{if $tpl_no != ''}-->
        var opner_product_id = 'edit_product_id';
        var opner_classcategory_id1 = 'edit_classcategory_id1';
        var opner_classcategory_id2 = 'edit_classcategory_id2';
        fm1.getElementById("no").value = <!--{$tpl_no}-->;
    <!--{else}-->
        var opner_product_id = 'add_product_id';
        var opner_classcategory_id1 = 'add_classcategory_id1';
        var opner_classcategory_id2 = 'add_classcategory_id2';
    <!--{/if}-->

    if (document.getElementById(class1).type == 'select-one' && class1_id == '') {
        err_text = class_name1 + "を選択してください。\n";
    }
    if (document.getElementById(class2).type == 'select-one' && class2_id == '') {
        err_text = err_text + class_name2 + "を選択してください。\n";
    }
    if (err_text != '') {
        alert(err_text);
        return false;
    }

    fm1.getElementById(opner_product_id).value = product_id;
    if (class1_id != '') {
        fm1.getElementById(opner_classcategory_id1).value = class1_id;
    }
    if (class2_id != '') {
        fm1.getElementById(opner_classcategory_id2).value = class2_id;
    }

    fm.mode.value = 'select_product_detail';
    fm.anchor_key.value = 'order_products';
    fm.submit();
    window.close();

    return true;
}
//-->
</script>

<script type="text/javascript">//<![CDATA[
// セレクトボックスに項目を割り当てる。
function lnSetSelect(name1, name2, id, val) {
    sele1 = document.form1[name1];
    sele2 = document.form1[name2];
    lists = eval('lists' + id);
    vals = eval('vals' + id);

    if(sele1 && sele2) {
        index = sele1.selectedIndex;

        // セレクトボックスのクリア
        count = sele2.options.length;
        for(i = count; i >= 0; i--) {
            sele2.options[i] = null;
        }

        // セレクトボックスに値を割り当てる
        len = lists[index].length;
        for(i = 0; i < len; i++) {
            sele2.options[i] = new Option(lists[index][i], vals[index][i]);
            if(val != "" && vals[index][i] == val) {
                sele2.options[i].selected = true;
            }
        }
    }
}
//]]>
</script>


<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
//]]>
</script>

<title>ECサイト管理者ページ</title>
</head>


<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<!--▼CONTENTS-->
<div align="center">
　
<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search">
<input name="anchor_key" type="hidden" value="">
<input name="search_pageno" type="hidden" value="">
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
    <tr class="fs12n">
        <td bgcolor="#f0f0f0" width="100">カテゴリ</td>
        <td bgcolor="#ffffff" width="287"><select name="search_category_id">
        <option value="" selected="selected">選択してください</option>
        <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
        </select>
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">商品名</td>
        <td bgcolor="#ffffff"><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">商品番号</td>
        <td bgcolor="#ffffff"><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code}-->" size="35" class="box35" /></td>
    </tr>
</table>
<br />
<input type="submit" name="subm" value="検索を開始" />
<br />
<br />

    <!--▼検索結果表示-->
    <!--{if $tpl_linemax}-->
    <table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
        <tr class="fs12">
            <td align="left"><!--{$tpl_linemax}-->件が該当しました。	</td>
        </tr>
        <tr class="fs12">
            <td align="center">
            <!--▼ページナビ-->
            <!--{$tpl_strnavi}-->
            <!--▲ページナビ-->
            </td>
        </tr>
        <tr><td height="10"></td></tr>
    </table>

    <!--▼検索後表示部分-->
    <table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
        <tr bgcolor="#f0f0f0" align="center" class="fs12">
            <td>商品画像</td>
            <td>商品番号</td>
            <td>商品名</td>
            <td>決定</td>
        </tr>
        <!--{section name=cnt loop=$arrProducts}-->
        <!--{assign var=id value=$arrProducts[cnt].product_id}-->
        <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
        <tr bgcolor="#FFFFFF" class="fs12n">
            <td width="90" align="center">
            <!--{if $arrProducts[cnt].main_list_image != ""}-->
                <!--{assign var=image_path value="`$arrProducts[cnt].main_list_image`"}-->
            <!--{else}-->
                <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
            <!--{/if}-->
            <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->">
            </td>
            <td>
            <!--{$arrProducts[cnt].name|escape}-->

            <!--{assign var=codemin value=`$arrProducts[cnt].product_code_min`}-->
            <!--{assign var=codemax value=`$arrProducts[cnt].product_code_max`}-->
            <!--{* 商品コード *}-->
                <!--{if $codemin != $codemax}-->
                    <!--{$codemin|escape}-->〜<!--{$codemax|escape}-->
                <!--{else}-->
                    <!--{$codemin|escape}-->
                <!--{/if}-->
            </td>
            <td>
                <!--{$arrProducts[cnt].name|escape}-->

                <!--{assign var=class1 value=classcategory_id`$id`_1}-->
                <!--{assign var=class2 value=classcategory_id`$id`_2}-->
                <!--{if $tpl_classcat_find1[$id]}-->
                <dt><!--{$tpl_class_name1[$id]|escape}-->：</dt>
                <dd>
                    <select name="<!--{$class1}-->" id="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
                    </select>
                    <!--{if $arrErr[$class1] != ""}-->
                    <br /><span class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</span>
                    <!--{/if}-->
                </dd>
                <!--{else}-->
                <input type="hidden" name="<!--{$class1}-->" id="<!--{$class1}-->" value="">
                <!--{/if}-->
                <!--{if $tpl_classcat_find2[$id]}-->
                    <dt><!--{$tpl_class_name2[$id]|escape}-->：</dt>
                    <dd>
                        <select name="<!--{$class2}-->" id="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
                        <option value="">選択してください</option>
                        </select>
                        <!--{if $arrErr[$class2] != ""}-->
                        <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
                        <!--{/if}-->
                    </dd>
                <!--{else}-->
                    <input type="hidden" name="<!--{$class2}-->" id="<!--{$class2}-->" value="">
                <!--{/if}-->
            </td>

            <td align="center"><a href="" onclick="return func_submit('<!--{$arrProducts[cnt].product_id}-->', '<!--{$tpl_class_name1[$id]}-->', '<!--{$tpl_class_name2[$id]}-->')">決定</a></td>
        </tr>
        <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{sectionelse}-->
        <tr bgcolor="#FFFFFF" class="fs10n">
            <td colspan="4">商品が登録されていません</td>
        </tr>
        <!--{/section}-->
    </table>
    <br />
    <br />
    <!--{/if}-->
    <!--▲検索結果表示-->

</form>

</div>
<!--▲CONTENTS-->
</body>
</html>