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
<form name="form1" id="form1" method="post" action="?" onSubmit="return window.confirm('登録しても宜しいですか');">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="page_id" value="" />
<div id="basis" class="contents-main">
  <!--{if count($arrPageData) > 0 }-->
    <!--{foreach name=page key=key item=item from=$arrPageData}-->
    <input type="hidden" name="disp_flg<!--{$item.page_id}-->" value="<!--{$disp_flg[$item.page_id]}-->" />
    <!-- <!--{$item.page_name}--> ここから -->
    <h2><!--{$item.page_name}--> <!--{$item.url}--><a href="#" id="switch<!--{$item.page_id}-->" style="float:right " onClick="fnDispChange('disp<!--{$item.page_id}-->', 'switch<!--{$item.page_id}-->', 'disp_flg<!--{$item.page_id}-->');"><!--{if $disp_flg[$item.page_id] == ""}--> &gt;&gt; 非表示<!--{else}--> << 表示<!--{/if}--></a></h2>
    
    <div id="disp<!--{$item.page_id}-->" style="display:<!--{$disp_flg[$item.page_id]}-->">
      <table>
        <tr>
          <th>メタタグ:Author</th>
          <td>
          <span class="attention"><!--{$arrErr[$item.page_id].author}--></span>
          <input type="text" name="meta[<!--{$item.page_id}-->][author]" value="<!--{$item.author|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].author != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
        </tr>
        <tr>
          <th>メタタグ:Description</th>
          <td>
          <span class="attention"><!--{$arrErr[$item.page_id].description}--></span>
          <input type="text" name="meta[<!--{$item.page_id}-->][description]" value="<!--{$item.description|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].description != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
        </tr>
        <tr>
          <th>メタタグ:Keywords</th>
          <td>
          <span class="attention"><!--{$arrErr[$item.page_id].keyword}--></span>
          <input type="text" name="meta[<!--{$item.page_id}-->][keyword]" value="<!--{$item.keyword|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].keyword != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->' /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></td>
        </tr>
      </table>
    
      <div class="btn"><a class="btn-normal" href="javascript:;" onclick="document.form1.page_id.value = <!--{$item.page_id}-->; fnFormModeSubmit('form1', 'confirm', '', '');"><span>この内容で登録する</span></a></div>
    
    </div>
    <!-- <!--{$item.page_name}--> ここまで -->
    <!--{/foreach}-->
  <!--{else}-->
    <div class="no-data">
          表示するデータがありません
    </div>
  <!--{/if}-->

</div>
</form>
