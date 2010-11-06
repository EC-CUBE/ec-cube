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

<script type="text/javascript" src="<!--{$TPL_DIR_DEFAULT}-->js/ui.core.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR_DEFAULT}-->js/ui.sortable.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR_DEFAULT}-->js/layout_design.js"></script>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id}-->" />
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->" />
<div id="design" class="contents-main">
    <!--{* ▼レイアウト編集ここから *}-->
    <h2>レイアウト編集</h2>
    <!--{* ▼レイアウトここから *}-->
    <div style="float: left; width: 75%;" align="center">
        <table id="design-layout-used" class="design-layout">
            <tr>
                <th colspan="3">&lt;head&gt;</td>
            </tr>
            <tr>
                <!-- ★☆★ HEADタグ内テーブル ☆★☆ -->
                <td colspan="3" id="HeadNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "HeadNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ Headタグ内テーブル ☆★☆ -->
            </tr>
            <tr>
                <th colspan="3">&lt;/head&gt;</td>
            </tr>
            <tr>
                <!-- ★☆★ ヘッダより上部ナビテーブル ☆★☆ -->
                <td colspan="3" id="HeaderTopNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "HeaderTopNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ ヘッダより上部ナビテーブル ☆★☆ -->
            </tr>
            <tr>
                <!-- ★☆★ ヘッダ内部ナビテーブル ☆★☆ -->
                <th id="layout-header">ヘッダー部</th>
                <td colspan="2" id="HeaderInternalNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "HeaderInternalNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
              </td>
              <!-- ★☆★ ヘッダ内部ナビテーブル ☆★☆ -->
            </tr>
            <tr>
                <!-- ★☆★ 上部ナビテーブル ☆★☆ -->
                <td colspan="3" id="TopNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "TopNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ 上部ナビテーブル ☆★☆ -->
            </tr>
            <tr>
                <!--{* 左ナビテーブルここから *}-->
                <td rowspan="3" id="LeftNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "LeftNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!--{* 左ナビテーブルここまで *}-->
                <!--{* メイン上部テーブルここから *}-->
                <td id="MainHead" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "MainHead"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!--{* メイン上部テーブルここまで *}-->
                <!--{* 右ナビここから *}-->
                <td rowspan="3" id="RightNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "RightNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!--{* 右ナビここまで *}-->
            </tr>
            <!--{* メインここから *}-->
            <tr>
                <th id="layout-main">メイン</td>
            </tr>
            <!--{* メインここまで *}-->
            <!--{* メイン下部ここから *}-->
            <tr>
                <td id="MainFoot" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "MainFoot"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
            <tr>
            <!--{* メイン下部ここまで *}-->
                 <!-- ★☆★ 下部ナビテーブル ☆★☆ -->
                <td colspan="3" id="BottomNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "BottomNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ 下部ナビテーブル ☆★☆ --> 
            </tr>
            <tr>
                <th colspan="3" id="layout-footer">フッター部</td>
            </tr>
            <tr>
                <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->
                <td colspan="3" id="FooterBottomNavi" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "FooterBottomNavi"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}--> />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->
            </tr>
        </table>
        <div class="btn">
            <button type='button' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"><span>登録する</span></button>
            <button type='button' name='preview' onclick="doPreview();"<!--{if $page_id == "0" or $exists_page == "0" }--> DISABLED<!--{/if}-->><span>プレビュー</span></button>
        </div>
    </div>
    <!--{* ▲レイアウトここまで *}-->

    <!--{* ▼未使用ブロックここから *}-->
    <div style="float: left; width: 25%;" align="center">
        <table id="design-layout-unused" class="design-layout">
            <tr>
                <th>未使用ブロック</th>
            </tr>
            <tr>
                <td id="Unused" class="ui-sortable" style="width: 145px;">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
                        <!--{if $item.target_id == "Unused"}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{$item.anywhere_selected}-->    />全ページ)</label> 
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
        <div class="btn"><button type='button' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"><span>ブロックを新規入力</span></button></div>
    </div>
    <!--{* ▲未使用ブロックここまで *}-->
    <!--▲レイアウト編集　ここまで-->

    <!--▼ページ一覧　ここから-->
    <h2 style="clear: both;">編集可能ページ一覧</h2>
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
