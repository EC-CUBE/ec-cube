<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript">
<!--
// モードとキーを指定してSUBMITを行う。
function fnSubFormStatusSubmit(mode, keyname, keyid) {
    document.subFormStatus['mode'].value = mode;
    if(keyname != "" && keyid != "") {
        document.subFormStatus[keyname].value = keyid;
    }
    document.subFormStatus.submit();
}
//-->
</script>
<form name="subFormStatus" id="subFormStatus" method="post" action="<!--{$smarty.const.URL_DIR}-->admin/order/status.php" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="status" value="<!--{if $arrForm.status == ""}-->1<!--{else}--><!--{$arrForm.status}--><!--{/if}-->" />
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" />
<input type="hidden" name="order_id" value="" />
<ul id="navi-order" class="level1">
    <li id="navi-order-index"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'index'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.URL_DIR}-->admin/order/index.php"><span>受注管理</span></a></li>
    <li id="navi-order-add"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'add'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.URL_DIR}-->admin/order/edit.php?mode=add"><span>新規受注入力</span></a></li>
    <li id="navi-order-status"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'status'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.URL_DIR}-->admin/order/status.php"><span>ステータス管理</span></a>
        <ul id="navi-status-sub" class="level2">
            <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
                <li id="navi-status-<!--{$key}-->"
                    class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'status' && ($key == $SelectedStatus || $key == $defaultstatus)}-->on<!--{/if}-->"
                ><a href="#" onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->' );"><span><!--{$item}--></span></a></li>
            <!--{/foreach}-->
        </ul>
    </li>
</ul>
</form>
