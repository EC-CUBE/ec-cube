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
<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="upload" />
<div id="disign" class="contents-main">
  <h2><!--{$template_name}--></h2>
  <table>
    <!--{assign var=key value="template_code"}-->
    <tr>
      <th>テンプレートコード</th>
      <td>
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" />
      </td>
    </tr>
    <!--{assign var=key value="template_name"}-->
    <tr>
      <th>テンプレート名</th>
      <td>
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" />
      </td>
    </tr>
    <!--{assign var=key value="template_file"}-->
    <tr>
      <th>テンプレートファイル<br /><span class="attention">※ファイル形式は.tar/.tar.gzのみ</span></th>
      <td>
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" size="64" <!--{if $arrErr.template_file}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> />
      </td>
    </tr>
  </table>

<div class="btn-area">
<a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'upload', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a>
</div>

</div>
</form>

<script type="text/javascript">
function ChangeImage(strUrl)
{
  document.main_img.src=strUrl;
}

// モードとキーを指定してSUBMITを行う。
function lfnModeSubmit(mode) {
  if(!window.confirm('登録しても宜しいですか?')){
    return false;
  }
  document.form1['mode'].value = mode;
  return true;
}


</script>
