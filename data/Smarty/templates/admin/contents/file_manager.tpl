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
<form name="form1" method="post" action="?"  enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="now_file" value="<!--{$tpl_now_dir}-->" />
<input type="hidden" name="tree_select_file" value="" />
<input type="hidden" name="tree_status" value="" />
<input type="hidden" name="select_file" value="" />
<div id="admin-contents" class="contents-main">
<div id="contents-filemanager-tree">
  ディレクトリ
  <div id="tree"></div>
</div>
<div id="contents-filemanager-nowdir">
  <div id="now_dir">
    <img src="<!--{$TPL_DIR}-->img/contents/folder_open.gif" alt="フォルダ">
    &nbsp;<!--{$tpl_now_file}-->
  </div>
  <div id="file_view">
    <table id="contents-filemanager-filelist" class="list">
      <tr>
        <th>ファイル名</th>
        <th>サイズ</th>
        <th>更新日付</th>
      </tr>
      <!--{if !$tpl_is_top_dir}-->
      <tr id="parent_dir" onclick="fnSetFormVal('form1', 'select_file', '<!--{$tpl_parent_dir|escape}-->');fnSelectFile('parent_dir', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$tpl_parent_dir|escape}-->', true, '<!--{$tpl_now_dir|escape}-->', true)" style="" onMouseOver="fnChangeBgColor('parent_dir', '#808080');" onMouseOut="fnChangeBgColor('parent_dir', '');">
        <td>
          <img src="<!--{$TPL_DIR}-->img/contents/folder_parent.gif" alt="フォルダ">&nbsp;..
        </td>
        <td class="right">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <!--{/if}-->
      <!--{section name=cnt loop=$arrFileList}-->
      <!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
      <tr id="<!--{$id}-->" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|escape}-->');fnSelectFile('<!--{$id}-->', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$arrFileList[cnt].file_path|escape}-->', <!--{if $arrFileList[cnt].is_dir|escape}-->true<!--{else}-->false<!--{/if}-->, '<!--{$tpl_now_dir|escape}-->', false)" style="" onMouseOver="fnChangeBgColor('<!--{$id}-->', '#808080');" onMouseOut="fnChangeBgColor('<!--{$id}-->', '');">
        <td>
          <!--{if $arrFileList[cnt].is_dir}-->
          <img src="<!--{$TPL_DIR}-->img/contents/folder_close.gif" alt="フォルダ">
          <!--{else}-->
          <img src="<!--{$TPL_DIR}-->img/contents/file.gif">
          <!--{/if}-->
          <!--{$arrFileList[cnt].file_name|escape}-->
        </td>
        <td class="right"><!--{$arrFileList[cnt].file_size|number_format}--></td>
        <td><!--{$arrFileList[cnt].file_time|escape}--></td>
      </tr>
      <!--{/section}-->
    </table>
  </div>
  <div class="btn">
    <a class="btn_normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;"><span>表示</span></a>
    <a class="btn_normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;"><span>ダウンロード</span></a>
    <a class="btn_normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;"><span>削除</span></a>
  </div>
</div>
<div id="contents-filemanager-action">
  <p>現在のディレクトリ&nbsp;：&nbsp;<!--{$tpl_now_dir|sfTrimURL}-->/</p>
  <table class="form">
    <tr>
      <th>ファイルのアップロード</th>
      <td>
        <!--{if $arrErr.upload_file}--><span class="attention"><!--{$arrErr.upload_file}--></span><!--{/if}-->
        <input type="file" name="upload_file" size="64" <!--{if $arrErr.upload_file}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->><a class="btn_normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;">アップロード</a>
      </td>
    </tr>
    <tr>
      <th>フォルダ作成</th>
      <td>
        <!--{if $arrErr.create_file}--><span class="attention"><!--{$arrErr.create_file}--></span><!--{/if}-->
        <input type="text" name="create_file" value="" style="width:336px;<!--{if $arrErr.create_file}--> background-color:<!--{$smarty.const.ERR_COLOR|escape}--><!--{/if}-->"><a class="btn_normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;">作成</a>
      </td>
    </tr>
  </table>
</div>

</div>
</form>
