<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{*ヘッダー読込*}-->
<!--{include file=`$tpl_dir_name`/header.tpl}-->

<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript">
<!--
<!--{$tpl_javascript}-->

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

// 全商品IDを取得する
function fnGetIds() {
    var change_tag = document.getElementsByTagName("div");
    var ids = "";
    var count = 0;

    for (var i = 0; i < change_tag.length; i++) {
        str = change_tag.item(i).id;
        if (str.match('cart_tag_*')) {
            var nama_id = change_tag.item(i).id;
            arrIds =  nama_id.split("_");

            if (count > 0) ids += '-';
            ids += arrIds[2];
            count ++;
        }
    }

    return ids;
}

// 読込後に実行する(on_load)
function init() {
    if(<!--{$tpl_init}-->) {
        var ids = fnGetIds();
        location.href = '?init=1&ids=' + ids;
    } else {
        <!--{$tpl_onload}-->
    }
}

window.onload = init;

//-->
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="cart" />
<input type="hidden" name="product_id" value="" />
<input type="hidden" name="cp" value="true" />

<!--{*コンテンツ読込*}-->
<!--{include file=`$tpl_dir_name`/contents.tpl}-->

</form>

<!--{*フッター読込*}-->
<!--{include file=`$tpl_dir_name`/footer.tpl}-->
