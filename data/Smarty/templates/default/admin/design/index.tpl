<script type="text/javascript">

function doPreview(){
  document.form1.mode.value="preview"
  document.form1.target = "_blank";
  document.form1.submit();
}
function fnTargetSelf(){
  document.form1.target = "_self";
}

</script>

<script type="text/javascript" src="<!--{$TPL_DIR}-->js/ui.core.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/ui.sortable.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/layout_design.js"></script>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id}-->" />
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->" />
<div id="design" class="contents-main">
  <!--{* ▼レイアウト編集ここから *}-->
  <table id="design-layout-wrap">
    <thead>
      <tr>
        <th id="design-layout-wrap-edit">レイアウト編集</th>
        <th id="design-layout-wrap-unused">未使用ブロック</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <!--{* ▼レイアウトここから *}-->
      <td>
        <table id="design-layout-body">
                          <tr>
            <!-- ★☆★ HEADタグ内テーブル ☆★☆ -->
            <td colspan="3" id="layout-head">
                <div id="HeadNavi" class="ui-sortable" style="position: relative; width: 468px; ">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "HeadNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                  <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!-- ★☆★ Headタグ内テーブル ☆★☆ -->          
          </tr>
                  <tr>
            <!-- ★☆★ ヘッダより上部ナビテーブル ☆★☆ -->
            <td colspan="3" id="layout-headertop">
                <div id="HeaderTopNavi" class="ui-sortable" style="position: relative; width: 468px; ">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "HeaderTopNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                  <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!-- ★☆★ ヘッダより上部ナビテーブル ☆★☆ -->          
          </tr>
          <tr>
            <td colspan="3" id="layout-header">ヘッダー部</td>
          </tr>
          <tr>
            <!-- ★☆★ 上部ナビテーブル ☆★☆ -->
            <td colspan="3" id="layout-top">
                <div id="TopNavi" class="ui-sortable" style="position: relative; width: 468px; ">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "TopNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                  <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!-- ★☆★ 上部ナビテーブル ☆★☆ -->          
          </tr>
          <tr>
            <!--{* 左ナビテーブルここから *}-->
            <td rowspan="3" id="layout-left">
              <div id="LeftNavi" class="ui-sortable" style="position: relative; width: 145px; height: 100px;">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "LeftNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                     <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!--{* 左ナビテーブルここまで *}-->
            <!--{* メイン上部テーブルここから *}-->
            <td id="layout-main-head">
              <div id="MainHead" class="ui-sortable" style="position: relative; width: 145px; height: 100px;">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "MainHead"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                     <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!--{* メイン上部テーブルここまで *}-->
            <!--{* 右ナビここから *}-->
            <td rowspan="3" id="layout-right">
              <div id="RightNavi" class="ui-sortable" style="position: relative; width: 145px; height: 100px;">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "RightNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                     <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!--{* 右ナビここまで *}-->
          </tr>
          <!--{* メインここから *}-->
          <tr>
            <td id="layout-main">メイン</td>
          </tr>
          <!--{* メインここまで *}-->
          <!--{* メイン下部ここから *}-->
          <tr>
            <td id="layout-main-foot">
              <div id="MainFoot" class="ui-sortable" style="position: relative; width: 145px; height: 100px;">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "MainFoot"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                     <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
          </tr>
          <tr>
          <!--{* メイン下部ここまで *}-->
             <!-- ★☆★ 下部ナビテーブル ☆★☆ -->
            <td colspan="3" id="layout-bottom">
                <div id="BottomNavi" class="ui-sortable" style="position: relative; width: 468px; ">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "BottomNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                     <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!-- ★☆★ 下部ナビテーブル ☆★☆ --> 
            </tr>
          <tr>
            <td colspan="3" id="layout-footer">フッター部</td>
          </tr>
                            <tr>
            <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->
            <td colspan="3" id="layout-footerbottom">
                <div id="FooterBottomNavi" class="ui-sortable" style="position: relative; width: 468px; ">
                <!--{assign var="firstflg" value=false}-->
                <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                <!--{if $item.target_id == "FooterBottomNavi"}-->
                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                  <!--{$item.name}-->
                  <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                  <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                  <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                  <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                  <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
                </div>
                <!--{assign var="firstflg" value=true}-->
                <!--{/if}-->
                <!--{/foreach}-->
              </div>
            </td>
            <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->          
          </tr>
        </table>
      </td>
      <!--{* ▲レイアウトここまで *}-->

      <!--{* ▼未使用ブロックここから *}-->
      <td>
        <div id="Unused" class="ui-sortable" style="position: relative; width: 145px; height: 800px;">
          <!--{assign var="firstflg" value=false}-->
          <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
          <!--{if $item.target_id == "Unused"}-->
          <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
            <!--{$item.name}-->
            <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
            <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
            <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
            <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
               <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->  />全ページ)</label> 
               
          </div>
          <!--{assign var="firstflg" value=true}-->
          <!--{/if}-->
          <!--{/foreach}-->
        </div>
        <div class="btn"><button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"><span>ブロックを新規入力</span></button></div>
      </td>
      <!--{* ▲未使用ブロックここまで *}-->
    </tr>
    </tbody>
  </table>
  <div class="btn">
    <button type='button' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"><span>登録する</span></button>
    <button type='button' name='preview' onclick="doPreview();"<!--{if $page_id == "0" or $exists_page == "0" }--> DISABLED<!--{/if}-->><span>プレビュー</span></button>
  </div>
  <!--▲レイアウト編集　ここまで-->


  <!--▼ページ一覧　ここから-->
  <h2>編集可能ページ一覧</h2>
  <table class="list center">
    <tr>
      <th>名称</th>
      <th><strong>レイアウト</strong></th>
      <th>ページ詳細</th>
      <th>削除</th>
    </tr>
  <!--{foreach key=key item=item from=$arrEditPage}-->
    <tr style="background-color:<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
      <td>
        <!--{$item.page_name}-->
      </td>
      <td>
        <a href="?page_id=<!--{$item.page_id}-->" ><strong>編集</strong></a>
      </td>
      <td>
        <!--{if $item.filename|strlen >= 1}-->
          <a href="main_edit.php?page_id=<!--{$item.page_id}-->">編集</a>
        <!--{/if}-->
      </td>
      <td>
        <!--{if $item.edit_flg == 1}-->
          <a href="?" onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');">削除</a>
        <!--{/if}-->
      </td>
    </tr>
  <!--{/foreach}-->
  </table>
  <div class="btn addnew">
    <button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"><span>ページを新規入力</span></button>
  </div>
  <!--▲ページ一覧　ここまで-->
</div>
</form>
