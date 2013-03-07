<script type="text/javascript">
    $(function() {
        var page_id = '<!--{$page_id|h}-->';
        if (page_id != '1') {
            $('.anywhere').attr('disabled', true);
            $('.anywhere:checked').each(function() {
                $(this).parents('.sort').children('input[type=hidden]').each(function() {
                    $(this).remove();
                });
            });
        }
    });
function doPreview(){
    document.form1.mode.value="preview"
    document.form1.target = "_blank";
    document.form1.submit();
}
function fnTargetSelf(){
    document.form1.target = "_self";
}

</script>

<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/ui.core.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/ui.sortable.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/layout_design.js"></script>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id|h}-->" />
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt|h}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

<div id="design" class="contents-main">
    <!--{* ▼レイアウトここから *}-->
    <div style="float: left; width: 75%;" align="center">
        <table id="design-layout-used" class="design-layout">
            <tr>
                <th colspan="3">&lt;head&gt;</th>
            </tr>
            <tr>
                <!-- ★☆★ HEADタグ内テーブル ☆★☆ -->
                <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_HEAD]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_HEAD]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ Headタグ内テーブル ☆★☆ -->
            </tr>
            <tr>
                <th colspan="3">&lt;/head&gt;</th>
            </tr>
            <tr>
                <!-- ★☆★ ヘッダより上部ナビテーブル ☆★☆ -->
                <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_HEAD_TOP]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_HEAD_TOP]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
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
                <td colspan="2" id="<!--{$arrTarget[$smarty.const.TARGET_ID_HEADER_INTERNAL]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_HEADER_INTERNAL]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ ヘッダ内部ナビテーブル ☆★☆ -->
            </tr>
            <tr>
                <!-- ★☆★ 上部ナビテーブル ☆★☆ -->
                <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_TOP]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_TOP]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ 上部ナビテーブル ☆★☆ -->
            </tr>

            <!--{if $device_type_id == $smarty.const.DEVICE_TYPE_MOBILE || $device_type_id == $smarty.const.DEVICE_TYPE_SMARTPHONE}-->
                <!--{* メイン上部テーブルここから *}-->
                <tr>
                    <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_MAIN_HEAD]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_MAIN_HEAD]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                </tr>
                <!--{* メイン上部テーブルここまで *}-->
                <!--{* メインここから *}-->
                <tr>
                    <th colspan="3" id="layout-main">メイン</th>
                </tr>
                <!--{* メインここまで *}-->
                <!--{* メイン下部ここから *}-->
                <tr>
                    <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_MAIN_FOOT]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_MAIN_FOOT]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                </tr>
            <!--{else}-->
                <tr>
                    <!--{* 左ナビテーブルここから *}-->
                    <td rowspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_LEFT]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_LEFT]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                    <!--{* 左ナビテーブルここまで *}-->
                    <!--{* メイン上部テーブルここから *}-->
                    <td id="<!--{$arrTarget[$smarty.const.TARGET_ID_MAIN_HEAD]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_MAIN_HEAD]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                    <!--{* メイン上部テーブルここまで *}-->
                    <!--{* 右ナビここから *}-->
                    <td rowspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_RIGHT]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_RIGHT]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                    <!--{* 右ナビここまで *}-->
                </tr>
                <!--{* メインここから *}-->
                <tr>
                    <th id="layout-main">メイン</th>
                </tr>
                <!--{* メインここまで *}-->
                <!--{* メイン下部ここから *}-->
                <tr>
                    <td id="<!--{$arrTarget[$smarty.const.TARGET_ID_MAIN_FOOT]}-->" class="ui-sortable">
                        <!--{assign var="firstflg" value=false}-->
                        <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                            <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_MAIN_FOOT]}-->
                                <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                    <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                    <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                    <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                    <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                    <!--{$item.name}-->
                                    <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                                </div>
                                <!--{assign var="firstflg" value=true}-->
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                </tr>
            <!--{/if}-->
            <tr>
            <!--{* メイン下部ここまで *}-->
                <!-- ★☆★ 下部ナビテーブル ☆★☆ -->
                <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_BOTTOM]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_BOTTOM]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ 下部ナビテーブル ☆★☆ -->
            </tr>
            <tr>
                <th colspan="3" id="layout-footer">フッター部</th>
            </tr>
            <tr>
                <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->
                <td colspan="3" id="<!--{$arrTarget[$smarty.const.TARGET_ID_FOOTER_BOTTOM]}-->" class="ui-sortable">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_FOOTER_BOTTOM]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
                <!-- ★☆★ フッタより下部ナビテーブル ☆★☆ -->
            </tr>
        </table>
    </div>
    <!--{* ▲レイアウトここまで *}-->

    <!--{* ▼未使用ブロックここから *}-->
    <div style="float: left; width: 25%;" align="center">
        <table id="design-layout-unused" class="design-layout">
            <tr>
                <th>未使用ブロック</th>
            </tr>
            <tr>
                <td id="<!--{$arrTarget[$smarty.const.TARGET_ID_UNUSED]}-->" class="ui-sortable" style="width: 145px;">
                    <!--{assign var="firstflg" value=false}-->
                    <!--{foreach key=key item=item from=$arrBlocs name="bloc_loop"}-->
                        <!--{if $item.target_id == $arrTarget[$smarty.const.TARGET_ID_UNUSED]}-->
                            <div class="sort<!--{if !$firstflg}--> first<!--{/if}-->">
                                <input type="hidden" class="name" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->" />
                                <input type="hidden" class="id" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->" />
                                <input type="hidden" class="target-id" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->" />
                                <input type="hidden" class="top" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->" />
                                <!--{$item.name}-->
                                <label class="anywherecheck">(<input type="checkbox" class="anywhere" name="anywhere_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="1" <!--{if $item.anywhere == 1}-->checked="checked"<!--{/if}--> />全ページ)</label>
                            </div>
                            <!--{assign var="firstflg" value=true}-->
                        <!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
        <div class="btn"><a class="btn-normal" href="javascript:;" onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','',''); return false;"><span>ブロックを新規入力</span></a></div>
    </div>
    <!--{* ▲未使用ブロックここまで *}-->
        <div class="btn-area">
            <ul>
            <!--{if $device_type_id == $smarty.const.DEVICE_TYPE_PC}-->
                <li><a class="btn-action" href="javascript:;" name='preview' onclick="doPreview();"><span class="btn-prev">プレビュー</span></a></li>
            <!--{/if}-->
                <li><a class="btn-action" href="javascript:;" name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','',''); return false;"><span class="btn-next">登録する</span></a></li>
            </ul>
        </div>
    <!--▲レイアウト編集　ここまで-->

    <!--▼ページ一覧　ここから-->
    <h2 style="clear: both;">編集可能ページ一覧</h2>
    <div class="btn addnew">
        <a class="btn-normal" href="javascript:;" onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','',''); return false;"><span>ページを新規入力</span></a>
    </div>
    <table class="list">
    <tr>
        <th>名称</th>
        <th class="edit">レイアウト</th>
        <th class="edit center">ページ詳細</th>
        <th class="delete center">削除</th>
    </tr>
    <!--{foreach key=key item=item from=$arrEditPage}-->
        <tr style="background-color:<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
            <td>
                <!--{$item.page_name}-->
            </td>
            <td class="center">
                <a href="?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->" >編集</a>
            </td>
            <td class="center">
                <!--{if $item.filename|strlen >= 1}-->
                    <a href="main_edit.php?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->">編集</a>
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $item.edit_flg == 1}-->
                    <a href="#" onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','page_id','<!--{$item.page_id|h}-->'); return false;">削除</a>
                <!--{/if}-->
            </td>
        </tr>
    <!--{/foreach}-->
    </table>
    <!--▲ページ一覧　ここまで-->
</div>
</form>
