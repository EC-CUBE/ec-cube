<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<script type="text/javascript">
<!--
// SQL確認画面起動
function doPreview(){
    document.form1.mode.value="preview"
    document.form1.target = "_blank";
    document.form1.submit();
}

// formのターゲットを自分に戻す
function fnTargetSelf(){
    document.form1.target = "_self";
}

//-->
</script>


<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="sql_id" value="<!--{$arrForm.sql_id|h}-->" />
    <input type="hidden" name="csv_output_id" value="" />
    <input type="hidden" name="select_table" value="" />
    <div id="admin-contents" class="contents-main">
        <h2>SQL一覧</h2>

        <!--{if $arrSqlList}-->
            <table id="contents-csv-sqllist" class="list center">
                <!--{foreach key=key item=item from=$arrSqlList}-->
                    <tr style="background-color:<!--{if $item.sql_id == $arrForm.sql_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
                        <td>
                            <a href="?sql_id=<!--{$item.sql_id}-->" ><!--{$item.sql_name|h}--></a>
                        </td>
                        <td>
                            <div class="btn">
                                <a class="btn-normal" href="javascript:;" name='csv' onclick="fnTargetSelf(); eccube.fnFormModeSubmit('form1','csv_output','csv_output_id',<!--{$item.sql_id}-->); return false;"><span>CSV出力</span></a>
                                <a class="btn-normal" href="javascript:;" name='del' onclick="fnTargetSelf(); eccube.fnFormModeSubmit('form1','delete','sql_id',<!--{$item.sql_id}-->); return false;"><span>削除</span></a>
                            </div>
                        </td>
                    </tr>
                <!--{/foreach}-->
            </table>
        <!--{/if}-->

        <div class="btn addnew">
            <a class="btn-normal" href="javascript:;" name='subm' onclick="fnTargetSelf(); eccube.fnFormModeSubmit('form1','new_page','',''); return false;"><span>SQLを新規入力</span></a>
        </div>


        <h2>SQL設定<!--{if $arrForm.sql_id != ""}-->(編集中:<!--{$arrForm.sql_name|h}-->)<!--{else}-->(新規入力)<!--{/if}--></h2>
        <table id="contents-csv-sqlset" class="form">
            <tr>
                <th>名称<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.sql_name}--></span>
                    <input type="text" name="sql_name" value="<!--{$arrForm.sql_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
                    <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                </td>
            </tr>
            <tr>
                <th align="center">SQL文<span class="attention"> *</span><br /> (最初のSELECTは記述しないでください。)</td>
                <td align="left">
                    <span class="attention"><!--{$arrErr.csv_sql}--></span>
                    <div>
                        <textarea name="csv_sql" cols=50 rows=20 align="left" wrap=off style="<!--{if $arrErr.csv_sql != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"><!--{"\n"}--><!--{$arrForm.csv_sql|h}--></textarea>
                    </div>
                </td>
            </tr>
        </table>

        <div class="btn">
            <a class="btn-normal" href="javascript:;" name="subm" onclick="doPreview(); return false;"><span>構文エラーを確認する</span></a>
        </div>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" name="subm" onclick="fnTargetSelf(); eccube.fnFormModeSubmit('form1', 'confirm', '', '')"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>



        <div id="contents-csv-sqltbl">
            <h3>テーブル一覧</h3>
            <select name="arrTableList[]" size="20" style="width:325px; height:300px;" onChange="mode.value=''; select_table.value=this.value; submit();" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
                <!--{html_options options=$arrTableList selected=$arrForm.select_table}-->
            </select>
        </div>
        <div id="contents-csv-sqlcol">
            <h3>項目一覧</h3>
            <select name="arrColList[]" size="20" style="width:325px; height:300px;" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
                <!--{html_options options=$arrColList}-->
            </select>
        </div>

    </div>
</form>


</script>
