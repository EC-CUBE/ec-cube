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
<style type="text/css">
    div.dragged_elm {
        position:   absolute;
        border:     1px solid black;
        background: rgb(195,217,255);
        color:      #333;
        cursor:    move;
        PADDING-RIGHT:   2px;
        PADDING-LEFT:   2px;
        PADDING-BOTTOM: 5px;
        PADDING-TOP:   5px;
        FONT-SIZE:     10pt;
    }

    div.drop_target {
        border:      0px solid gray;
        position:    relative;
        text-align:  center;
        color:       #333;
    }

</style>
<script type="text/javascript">

function doPreview(){
  document.form1.mode.value="preview"
  document.form1.target = "_blank";
  document.form1.submit();
}
function fnTargetSelf(){
  document.form1.target = "_self";
}

// 初期処理
function init () {
    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };

    // ウィンドウサイズを取得
  scrX = GetWindowSize("width");
  scrY = GetWindowSize("height");

  // ウィンドウサイズ変更イベントに関連付け
    window.onresize = fnMoveObject;

    // divタグを取得
    all_elms = document.getElementsByTagName ( 'div' );

  // tdタグを取得
  all_td = document.getElementsByTagName ( 'td' );

  // 配列作成
  fnCreateArr(0);

  // alerttest(0);

    // 並び替え
  fnMoveObject();

  <!--{$complate_msg}-->
}

</script>

<script type="text/javascript" src="<!--{$TPL_DIR}-->js/layout_design.js"></script>


<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id}-->" />
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->" />
<div id="design" class="contents-main">

  <!--{* ▼レイアウト編集ここから *}-->
  <table>
    <tr>
      <th align="center">レイアウト編集</th>
      <th align="center">未使用ブロック</th>
    </tr>
    <tr>
      <!--{* ▼レイアウトここから *}-->
      <td>
        <table id="layout-table">
          <tr>
            <td colspan="3"> ヘッダー部 </td>
          </tr>
          <tr>
            <!--{* 左ナビテーブルここから *}-->
            <td rowspan="3">
              <div tid="LeftNavi" class="drop_target" id="t1" style="width: 145px; height: 100px;"></div>
            </td>
            <!--{* 左ナビテーブルここまで *}-->
            <!--{* メイン上部テーブルここから *}-->
            <td>
              <div tid="MainHead" class="drop_target" id="t2" style="width: 145px; height: 100px;"></div>
            </td>
            <!--{* メイン上部テーブルここまで *}-->
            <!--{* 右ナビここから *}-->
            <td rowspan="3">
              <div tid="RightNavi" class="drop_target" id="t3" style="width: 145px; height: 100px;"></div>
            </td>
            <!--{* 右ナビここまで *}-->
          </tr>
          <!--{* メインここから *}-->
          <tr>
            <td id='Main'>メイン</td>
          </tr>
          <!--{* メインここまで *}-->
          <!--{* メイン下部ここから *}-->
          <tr>
            <td name='MainFoot' id="layout">
              <div tid="MainFoot" class="drop_target" id="t4" style="width: 145px; height: 100px;"></div>
            </td>
          </tr>
          <!--{* メイン下部ここまで *}-->
          </tr>
          <tr>
            <td colspan=3>フッター部</td>
          </tr>
        </table>
      </td>
      <!--{* ▲レイアウトここまで *}-->

      <!--{* ▼未使用ブロックここから *}-->
      <td>
        <div tid="Unused" class="drop_target" id="t5" style="width: 160px; height: 500px; border: 1px solid #cccccc;"></div>
        <div class="btn"><button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"><span>新規ブロック作成</span></button></div>
      </td>
      <!--{* ▲未使用ブロックここまで *}-->
    </tr>
  </table>
  <div class="btn">
    <button type='button' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"><span>保存</span></button>
    <button type='button' name='preview' onclick="doPreview();"<!--{if $page_id == "0" or $exists_page == "0" }--> DISABLED<!--{/if}-->><span>プレビュー</span></button>
  </div>
  <!--▲レイアウト編集　ここまで-->


  <!--▼ページ一覧　ここから-->
  <h2>編集可能ページ</h2>

  <table class="list center">
  <!--{foreach key=key item=item from=$arrEditPage}-->
    <tr style="background-color:<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
      <td>
        <a href="<!--{$smarty.server.PHP_SELF|escape}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
      </td>
      <td>
        <div class="btn">
          <button type='button' onclick="location.href='./main_edit.php?page_id=<!--{$item.page_id}-->'"><span>メイン編集</span></button>
        </div>
      </td>
      <td>
        <!--{if $item.edit_flg == 1}-->
        <div class="btn">
          <button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');"><span>削除</span></button>
        </div>
        <!--{/if}-->
      </td>
    </tr>
  <!--{/foreach}-->
  </table>
  <div class="btn">
    <button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"><span>新規ページ作成</span></button>
  </div>
  <!--▲ページ一覧　ここまで-->



<!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
<div align="center" target_id="<!--{$item.target_id}-->" did="<!--{$smarty.foreach.bloc_loop.iteration}-->" class="dragged_elm" id="<!--{$item.target_id}-->"
   style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; width: 120px;">
   <!--{$item.name}-->
</div>

<input type="hidden" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
<input type="hidden" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
<input type="hidden" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
<input type="hidden" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
<!--{/foreach}-->
</form>
