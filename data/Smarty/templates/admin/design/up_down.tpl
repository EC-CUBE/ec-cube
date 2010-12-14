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
<script type="text/javascript"><!--
function confirmSubmit(mode, msg) {
  var form = document.form1;
  form['mode'].value = mode;
  if (window.confirm(msg)) {
    form.submit();
  } else {
    form['mode'].value = '';
  }
}
//-->
</script>

<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="uniqid" value="<!--{$uniqid}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|escape}-->" />
<div id="disign" class="contents-main">
  <p class="remark">
    テンプレートパッケージのアップロードを行います。<br />
    アップロードしたパッケージは、「テンプレート設定」で選択できるようになります。
  </p>
  <table>
    <!--{assign var=key value="template_code"}-->
    <tr>
      <th>テンプレートコード</td>
      <td>
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
      </td>
    </tr>
    <!--{assign var=key value="template_name"}-->
    <tr>
      <th>テンプレート名</td>
      <td>
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
      </td>
    </tr>
    <!--{assign var=key value="template_file"}-->
    <tr>
      <th>テンプレートファイル<br/>
        <span class="attention"><span class="fs14n">※ファイル形式は.tar/.tar.gzのみ</span></span>
      </td>
      <td>
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" size="64" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
      </td>
    </tr>
  </table>
  <div class="btn">
    <button type="submit" onClick="fnModeSubmit('upload', '', '');return false;"><span>アップロード</span></button>
  </div>
</div>
</form>
