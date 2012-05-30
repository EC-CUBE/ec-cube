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

<script type="text/javascript">//<![CDATA[
    $(function() {
        var bread_crumbs = <!--{$tpl_now_dir}-->;
        var file_path = '<!--{$tpl_file_path}-->';
        var $delimiter = '<span>&nbsp;&gt;&nbsp;</span>';
        var $node = $('h2');
        var total = bread_crumbs.length;
        for (var i in bread_crumbs) {
            file_path += bread_crumbs[i] + '/';
            $('<a href="javascript:;" onclick="fnFolderOpen(\'' + file_path + '\'); return false;" />')
                .text(bread_crumbs[i])
                .appendTo($node);
            if (i < total - 1) $node.append($delimiter);
        }
    });

    var IMG_FOLDER_CLOSE   = "<!--{$TPL_URLPATH}-->img/contents/folder_close.gif";  // フォルダクローズ時画像
    var IMG_FOLDER_OPEN    = "<!--{$TPL_URLPATH}-->img/contents/folder_open.gif";   // フォルダオープン時画像
    var IMG_PLUS           = "<!--{$TPL_URLPATH}-->img/contents/plus.gif";          // プラスライン
    var IMG_MINUS          = "<!--{$TPL_URLPATH}-->img/contents/minus.gif";         // マイナスライン
    var IMG_NORMAL         = "<!--{$TPL_URLPATH}-->img/contents/space.gif";         // スペース
//]]></script>
<form name="form1" method="post" action="?"  enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="now_file" value="<!--{$tpl_now_dir|h}-->" />
<input type="hidden" name="now_dir" value="<!--{$tpl_now_file|h}-->" />
<input type="hidden" name="tree_select_file" value="" />
<input type="hidden" name="tree_status" value="" />
<input type="hidden" name="select_file" value="" />
<div id="admin-contents" class="contents-main">
    <div id="contents-filemanager-tree">
        <div id="tree"></div>
    </div>
    <div id="contents-filemanager-right">
        <table class="now_dir">
            <tr>
                <th>ファイルのアップロード</th>
                <td>
                    <!--{if $arrErr.upload_file}--><span class="attention"><!--{$arrErr.upload_file}--></span><!--{/if}-->
                    <input type="file" name="upload_file" size="40" <!--{if $arrErr.upload_file}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->><a class="btn-normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;">アップロード</a>
                </td>
            </tr>
            <tr>
                <th>フォルダ作成</th>
                <td>
                    <!--{if $arrErr.create_file}--><span class="attention"><!--{$arrErr.create_file}--></span><!--{/if}-->
                    <input type="text" name="create_file" value="" style="width:336px;<!--{if $arrErr.create_file}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->"><a class="btn-normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;">作成</a>
                </td>
            </tr>
        </table>
        <h2><!--{* jQuery で挿入される *}--></h2>
        <table class="list">
            <tr>
                <th>ファ