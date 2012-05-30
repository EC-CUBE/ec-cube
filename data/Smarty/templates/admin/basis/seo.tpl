<!--{*
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
*}-->

<form name="form1" id="form1" method="post" action="?" onSubmit="return window.confirm('登録しても宜しいですか');">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="device_type_id" value="" />
<input type="hidden" name="page_id" value="" />
<div id="basis" class="contents-main">
    <!--{if count($arrPageData) > 0}-->
        <!--{foreach name=device key=device_key item=arrDevicePageData from=$arrPageData}-->
            <!--{if count($arrDevicePageData) > 0}-->
                <!--{foreach name=page key=key item=item from=$arrDevicePageData}-->
                    <!-- <!--{$item.page_name}--> ここから -->
                    <!--{if $smarty.foreach.page.first == true}--><h1><span class="subtitle"><!--{$arrDeviceTypeName[$item.device_type_id]}--></span></h1><!--{/if}-->
                    <h2><!--{$item.page_name}--> <!--{$item.url}--></h2>

                    <div id="_<!--{$item.device_type_id}-->_<!--{$item.page_id}-->">
                        <table>
                            <tr>
                                <th>メタタグ:Author</th>
                                <td>
                                <span class="attention"><!--{$arrErr[$item.device_type_id][$item.page_id].author}--></span>
                                <input type="text" name="meta[<!--{$item.device_type_id}-->][<!--{$item.page_id}-->][author]" value="<!--{$item.author|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].author != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
                            </tr>
                            <tr>
                                <th>メタタグ:Description</th>
                                <td>
                                <span class="attention"><!--{$arrErr[$item.device_type_id][$item.page_id].description}--></span>
                                <input type="text" name="meta[<!--{$item.device_type_id}-->][<!--{$item.page_id}-->][description]" value="<!--{$item.description|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].description != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
                            </tr>
                            <tr>
                                <th>メタタグ:Keywords</th>
                                <td>
                                <span class="attention"><!--{$arrErr[$item.device_type_id][$item.page_id].keyword}--></span>
                                <input type="text" name="meta[<!--{$item.device_type_id}-->][<!--{$item.page_id}-->][keyword]" value="<!--{$item.keyword|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].keyword != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
                            </tr>
                        </table>

                        <div class="btn-area">
                            <ul>
                                <li><a class="btn-action" href="javascript:;" onclick="document.form1.device_type_id.value = <!--{$item.device_type_id}-->; document.form1.page_id.value = <!--{$item.page_id}-->; fnFormModeSubmit('form1', 'confirm', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
                            </ul>
                        </div>

                    </div>
                    <!-- <!--{$item.page_name}--> ここまで -->
                <!--{/foreach}-->
            <!--{/if}-->
        <!--{/foreach}-->
    <!--{else}-->
        <div class="no-data">
            表示するデータがありません
        </div>
    <!--{/if}-->

</div>
</form>
