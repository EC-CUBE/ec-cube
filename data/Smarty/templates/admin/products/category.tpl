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
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/jquery.tablednd.js"></script>
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id}-->">
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id}-->">
<input type="hidden" name="keySet" value="">
<div id="products" class="contents-main">
  <div class="btn">
    <button type="button" onclick="fnModeSubmit('csv','','');">CSV ダウンロード</button>
    <button type="button" onclick="location.href='../contents/csv.php?tpl_subno_csv=category'">CSV 出力項目設定</a>
  </div>

  <!--{* ▼画面左 *}-->
  <div id="products-category-left">
    <a href="?">▼ホーム</a><br />
    <!--{section name=cnt loop=$arrTree}-->
      <!--{assign var=level value="`$arrTree[cnt].level`}-->

      <!--{* 上の階層表示の時にdivを閉じる *}-->
      <!--{assign var=close_cnt value="`$before_level-$level+1`"}-->
      <!--{if $close_cnt > 0}-->
        <!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
      <!--{/if}-->

      <!--{* スペース繰り返し *}-->
      <!--{section name=n loop=$level}-->　　<!--{/section}-->
      
      <!--{* カテゴリ名表示 *}-->
      <!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
      <!--{if $arrTree[cnt].level != $smarty.const.LEVEL_MAX}-->
        <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false;">
        <!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
          <img src="<!--{$smarty.const.URL_DIR}-->misc/openf.gif">
        <!--{else}-->
          <img src="<!--{$smarty.const.URL_DIR}-->misc/closef.gif">
        <!--{/if}-->
        <!--{$disp_name|sfCutString:20|escape}--></a><br />
      <!--{else}-->
        <img src="<!--{$smarty.const.URL_DIR}-->misc/closef.gif">
        <!--{$disp_name|sfCutString:20|escape}--></a><br />
      <!--{/if}-->

      <!--{if $arrTree[cnt].display == true}-->
        <div id="f<!--{$arrTree[cnt].category_id}-->">
      <!--{else}-->
        <div id="f<!--{$arrTree[cnt].category_id}-->" style="display:none">
      <!--{/if}-->

      <!--{if $smarty.section.cnt.last}-->
        <!--{section name=n loop=$level}--></div><!--{/section}-->
      <!--{/if}-->

      <!--{assign var=before_level value="`$arrTree[cnt].level`}-->
    <!--{/section}-->
  </div>
  <!--{* ▲画面左 *}-->

  <!--{* ▼画面右 *}-->
  <div id="products-category-right">
    
    <!--{if $arrErr.category_name}-->
    <span class="attention"><!--{$arrErr.category_name}--></span>
    <!--{/if}-->
    <input type="text" name="category_name" value="<!--{$arrForm.category_name|escape}-->" size="30" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
    <button type="submit" onclick="fnModeSubmit('edit','','');"><span>登録</span></button><span class="attention">（上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
    
    <!--{if count($arrList) > 0}-->
<script type="text/javascript">
// カテゴリーテーブルのイニシャライズ
$(document).ready(function() {
    $("#categoryTable").tableDnD({
	    onDragClass: "movingHandle",
        onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var keys = row.id;

            for (var i = 0; i < rows.length; i++) {
                if (row.id == rows[i].id) {
                    keys += "-" + i;
                    break;
                }
            }

            fnModeSubmit('moveByDnD','keySet', keys);
        },
        dragHandle: "dragHandle"
    });

    $("#categoryTable tr").hover(function() {
        $(this.cells[0]).addClass('activeHandle');
    }, function() {
        $(this.cells[0]).removeClass('activeHandle');
    });
});
</script>
    <table class="list" id="categoryTable">
      <tr class="nodrop nodrag">
	  	<th width="40">移動</th>
        <th>ID</th>
        <th>カテゴリ名</th>
        <th>編集</th>
        <th>削除</th>
        <th>移動</th>
      </tr>
      <!--{section name=cnt loop=$arrList}-->
      <tr id="<!--{$arrList[cnt].category_id}-->" style="background:<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" align="left">
        <td class="dragHandle">&sect;</td>
		<td class="center"><!--{$arrList[cnt].category_id}--></td>
        <td>
        <!--{if $arrList[cnt].level != $smarty.const.LEVEL_MAX}-->
          <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrList[cnt].category_id}-->); return false"><!--{$arrList[cnt].category_name|escape}--></a>
        <!--{else}-->
          <!--{$arrList[cnt].category_name|escape}-->
        <!--{/if}-->
        </td>
        <td align="center">
          <!--{if $arrForm.category_id != $arrList[cnt].category_id}-->
          <a href="?" onclick="fnModeSubmit('pre_edit', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">編集</a>
          <!--{else}-->
          編集中
          <!--{/if}-->
        </td>
        <td align="center">
          <a href="?" onclick="fnModeSubmit('delete', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">削除</a>
        </td>
        <td align="center">
        <!--{* 移動 *}-->
        <!--{if $smarty.section.cnt.iteration != 1}-->
        <a href="?" onclick="fnModeSubmit('up','category_id', <!--{$arrList[cnt].category_id}-->); return false;">上へ</a>
        <!--{/if}-->
        <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
        <a href="?" onclick="fnModeSubmit('down','category_id', <!--{$arrList[cnt].category_id}-->); return false;">下へ</a>
        <!--{/if}-->
        </td>
      </tr>
      <!--{/section}-->
    </table>
    <!--{else}-->
    <p>この階層には、カテゴリが登録されていません。</p>
    <!--{/if}-->
  </div>
  <!--{* ▲画面右 *}-->

</div>
</form>
