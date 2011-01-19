<!--{*
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
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
    <div id="under02column_shopping">
        <p class="flowarea">
            <img src="<!--{$TPL_DIR}-->img/picture/img_flow_01.gif" width="700" height="36" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

<div id="add-wrap" class="clearfix">

<div id="add-left">
        <p>下記一覧よりお届け先住所を選択して、「選択したお届け先に送る」ボタンをクリックしてください。</p>
        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。</p>
        <!--{/if}-->
        <p>※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p class="addbtn">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_add_address_on.gif','addition');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_add_address.gif','addition');"><img src="<!--{$TPL_DIR}-->img/button/btn_add_address.gif" width="160" height="22" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
            </p>
        <!--{/if}-->
</div>

<div id="add-right">
<p class="add-m">この商品を複数の<br />お届け先に送りますか？</p>
<a href="javascript:;" onclick="fnModeSubmit('multiple', '', ''); return false" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_several_address_on.gif','several');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_several_address.gif','several');"><img src="<!--{$TPL_DIR}-->img/button/btn_several_address.gif" width="129" height="20" alt="お届け先を複数指定する" name="several" id="several" /></a>
</div>

</div>

        
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="mode" value="customer_addr" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="other_deliv_id" value="" />
            <!--{if $arrErr.deli != ""}-->
                <p class="attention"><!--{$arrErr.deli}--></p>
            <!--{/if}-->
            <table summary="お届け先の指定">
                <tr>
                    <th>選択</th>
                    <th>住所種類</th>
                    <th>お届け先</th>
                    <th>変更</th>
                    <th>削除</th>
                </tr>
                <!--{section name=cnt loop=$arrAddr}-->
                <tr>
                     <td class="centertd">
                         <!--{if $smarty.section.cnt.first}-->
                            <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
                         <!--{else}-->
                            <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
                         <!--{/if}-->
                    </td>
                    <td>
                        <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
                            <!--{if $smarty.section.cnt.first}-->
                                会員登録住所
                            <!--{else}-->
                                追加登録住所
                            <!--{/if}-->
                        </label>
                    </td>
                    <td>
                        <!--{assign var=key value=$arrAddr[cnt].pref}-->
                        <!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br />
                        <!--{$arrAddr[cnt].name01|h}--> <!--{$arrAddr[cnt].name02|h}-->
                    </td>
                    <td class="centertd">
                        <!--{if !$smarty.section.cnt.first}-->
                            <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;">変更</a>
                        <!--{/if}-->
                    </td>
                    <td class="centertd">
                        <!--{if !$smarty.section.cnt.first}-->
                            <a href="?" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->'); return false">削除</a>
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{/section}-->
        </table>

            <div class="tblareabtn">
                <a href="<!--{$smarty.const.CART_URLPATH}-->" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back.gif',back03)">
                    <img src="<!--{$TPL_DIR}-->img/button/btn_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" /></a>
                <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_address_select_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_address_select.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_address_select.gif" alt="選択したお届け先に送る" class="box190" name="send_button" id="send_button" />
            </div>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
