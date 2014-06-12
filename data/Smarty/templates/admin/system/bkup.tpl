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
    function fnRestore(list_name) {
        if (window.confirm('リストアしますか?')) {
            document.body.style.cursor = 'wait';
            eccube.setModeAndSubmit('restore', 'list_name', list_name);
        }
    }
</script>
<form name="form1" id="form1" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="edit" />
    <input type="hidden" name="list_name" value="" />
    <div id="system" class="contents-main">
        <p class="remark">
            データベースのバックアップを行います。<br />
            テンプレートファイル等はバックアップされません。
        </p>
        <table class="form">
            <tr>
                <th>バックアップ名<span class="attention"> *</span></th>
                <td>
                    <!--{if $arrErr.bkup_name}-->
                    <span class="attention"><!--{$arrErr.bkup_name}--></span>
                    <!--{/if}-->
                    <input type="text" name="bkup_name" value="<!--{$arrForm.bkup_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.bkup_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> ime-mode: disabled;" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                </td>
            </tr>
            <tr>
                <th>バックアップメモ</th>
                <td>
                    <!--{if $arrErr.bkup_memo}-->
                        <span class="attention"><!--{$arrErr.bkup_memo}--></span>
                    <!--{/if}-->
                    <textarea name="bkup_memo" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" cols="60" rows="5" class="area60" style="<!--{if $arrErr.bkup_memo != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"><!--{"\n"}--><!--{$arrForm.bkup_memo|h}--></textarea>
                    <span class="attention"> (上限<!--{$smarty.const.MTEXT_LEN}-->文字)</span>
                </td>
            </tr>
        </table>

        <div class="btn"><a class="btn-normal" href="javascript:;" name="cre_bkup" onclick="document.body.style.cursor = 'wait'; form1.mode.value='bkup'; document.form1.submit(); return false;"><span>バックアップデータを作成する</span></a></div>


        <h2>バックアップ一覧</h2>


        <!--{if $arrErr.list_name}-->
        <span class="attention"><!--{$arrErr.list_name}--></span><br />
        <!--{/if}-->
        <!--{* 一覧が存在する場合のみ表示する *}-->
        <!--{if count($arrBkupList) > 0}-->
            <table class="list">
                <tr>
                    <th>バックアップ名</th>
                    <th>バックアップメモ</th>
                    <th>作成日</th>
                    <th>リストア</th>
                    <th>ダウンロード</th>
                    <th class="delete">削除</th>
                </tr>
                <!--{section name=cnt loop=$arrBkupList}-->
                    <tr>
                        <td ><!--{$arrBkupList[cnt].bkup_name|h}--></td>
                        <td ><!--{$arrBkupList[cnt].bkup_memo|h}--></td>
                        <td align="center"><!--{$arrBkupList[cnt].create_date|sfCutString:19:true:false}--></td>
                        <td align="center"><a href="javascript:;" onclick="fnRestore('<!--{$arrBkupList[cnt].bkup_name}-->'); return false;">リストア</a></td>
                        <td align="center"><a href="javascript:;" onclick="eccube.setModeAndSubmit('download','list_name','<!--{$arrBkupList[cnt].bkup_name}-->'); return false;">ダウンロード</a></td>
                        <td align="center">
                            <a href="javascript:;" onclick="eccube.setModeAndSubmit('delete','list_name','<!--{$arrBkupList[cnt].bkup_name}-->'); return false;">削除</a>
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
        <!--{/if}-->

        <!--{if strlen($tpl_restore_msg) >= 1}-->
            <h2>実行結果</h2>
            <div class="message">
                <!--{if $tpl_restore_err == false}-->
                    <div class="btn"><a class="btn-normal" href="javascript:;" name="restore_config" onclick="document.body.style.cursor = 'wait'; form1.mode.value='restore_config'; form1.list_name.value='<!--{$tpl_restore_name|h}-->'; submit(); return false;"><span>エラーを無視してリストアする</span></a></div>
                <!--{/if}-->
                <!--{$tpl_restore_msg|h}-->
            </div>
        <!--{/if}-->

    </div>
</form>
