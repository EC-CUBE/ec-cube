<!--{*
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
 *}-->

<script type="text/javascript">
$(function(){
    function calcSum(_this) {
            var sum = 0;
            var target = _this.attr('id');
            target = target.replace(/quantity_/, '');
            $('.quantity_' + target).each(function() {
                sum += parseInt($(this).val());
            });
            $('input[name="max_' + target + '"]').val(sum);

    }
    $(".addline").on("click", function(){
        console.log('click');
        var target = $(this).next().val(),
            max = $(this).next().next().val();        
        var line = parseInt($('input[name="line_of_num[' + target + ']"]').val());
        if (line >= max || line >= <!--{$tpl_addrmax}--> + 1) {
            return false;
        }

        var max = parseInt($('input[name="max_' + target + '"]').val());
        $('input[name="max_' + target + '"]').val(max + 1);
        line += 1;
        $('input[name="line_of_num[' + target + ']"]').val(line);

        $('#image_' + target).prop('rowspan', line + 1);
        $('#item_' + target).prop('rowspan', line + 1);

        var $inputs = $('#default').slice(0);
        $inputs.html($inputs.html().replace(/##PID##/g, target));
        $('#total_' + target).before($inputs);
        $inputs.find("input[type='number']").on("change", function(){
            calcSum($(this));
        });
    });

    $(function() {
        $('input[type="number"]').on("change", function(){
            calcSum($(this));
        });
    });
});
</script>
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area">
            <img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_01.jpg" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <p class="information">各商品のお届け先を選択してください。<br />（※数量の合計は、カゴの中の数量と合わせてください。）</p>
        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。</p>
        <!--{/if}-->
        <p class="mini attention">※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p class="addbtn">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加する" /></a>
            </p>
        <!--{/if}-->
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <table summary="商品情報">
                <col width="10%" />
                <col width="30%" />
                <col width="45%" />
                <col width="15%" />
                <tr>
                    <th class="alignC">商品写真</th>
                    <th class="alignC">商品名</th>
                    <th class="alignC">お届け先</th>
                    <th class="alignC">数量</th>
                </tr>
                <!--{assign var=index value=0}-->
                <!--{foreach item=item from=$arrCartItem}-->
                    <!--{assign var=p value=$item.productsClass}-->
                    <!--{assign var=key value="line_of_num"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$item.id}-->]" value="<!--{$arrForm[$key].value[$item.id]|h}-->" />
                    <!--{assign var=row value=`$arrForm[$key].value[$item.id]+1`}-->
                    <tr>
                        <td id="image_<!--{$item.id}-->" class="alignC" rowspan="<!--{$row}-->">
                            <a<!--{if $p.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$p.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"<!--{/if}-->><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$p.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$p.name|h}-->" /></a>
                        </td>
                        <td id="item_<!--{$item.id}-->" rowspan="<!--{$row}-->">
                            <!--{* 商品名 *}--><strong><!--{$p.name|h}--></strong><br />
                            <!--{if $p.classcategory_name1 != ""}-->
                                <!--{$p.class_name1|h}-->：<!--{$p.classcategory_name1|h}--><br />
                            <!--{/if}-->
                            <!--{if $p.classcategory_name2 != ""}-->
                                <!--{$p.class_name2|h}-->：<!--{$p.classcategory_name2|h}--><br />
                            <!--{/if}-->
                            <!--{$p.price02_inctax|n2s}-->円
                        </td>
                        <!--{assign var=sum value=0}-->
                        <!--{section name=line loop=$arrForm.line_of_num.value[$item.id]}-->
                            <!--{if !$smarty.section.line.first}-->
                            <tr>
                            <!--{/if}-->
                            <td>
                                <!--{assign var=key value="product_class_id"}-->
                                <input type="hidden" name="<!--{$key}-->[]" value="<!--{$item.id}-->" />
                                <!--{assign var=key value="shipping"}-->
                                <!--{if strlen($arrErr[$key][$index]) >= 1}-->
                                    <div class="attention"><!--{$arrErr[$key][$index]}--></div>
                                <!--{/if}-->
                                <select name="<!--{$key}-->[]" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->">
                                    <!--{html_options options=$arrAddress selected=$arrForm[$key].value[$index]}-->
                                </select>
                            </td>
                            <td class="alignR">
                                <!--{if $arrErr.line_of_num[$item.id] != '' && $smarty.section.line.first}-->
                                    <span class="attention"><!--{$arrErr.line_of_num[$item.id]}--></span>
                                <!--{/if}-->
                                <!--{assign var=key value="quantity"}-->
                                <!--{if $arrErr[$key][$index] != '' || $arrErr.line_of_num[$item.id] != ''}-->
                                    <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                                <!--{/if}-->
                                <!--{assign var=err value="`$arrErr[$key][$index]``$arrErr.line_of_num[$item.id]`"}-->
                                <input type="number" id="<!--{$key}-->_<!--{$item.id}-->" name="<!--{$key}-->[]" value="<!--{$arrForm[$key].value[$index]|h}-->" class="box40 quantity_<!--{$item.id}-->" style="<!--{$err|sfGetErrorColor}-->" maxlength="<!--{$arrForm[$key].length}-->" />
                                <!--{assign var=sum value=`$sum+$arrForm[$key].value[$index]`}-->
                                <!--{assign var=index value=`$index+1`}-->
                            </td>
                        </tr><!--{* rowspan の関係からここ *}-->
                        <!--{/section}-->
                    <tr id="total_<!--{$item.id}-->">
                        <td>
                            <a class="btn_normal addline" href="javascript:;" <!--{*onclick="addLine('<!--{$item.id}-->', '<!--{$item.quantity}-->');*}-->">お届け先の追加</a>
                            <input type="hidden" name="item.id" value="<!--{$item.id}-->" />
                            <input type="hidden" name="item.quantity" value="<!--{$item.quantity}-->" />
                        </td>
                        <td class="alignR"><input type="text" name="max_<!--{$item.id}-->" value="<!--{$sum}-->" class="box40" readonly="readonly" /><span class="attention"> / <!--{$item.quantity|n2s}--></span></td>
                    </tr>
                <!--{/foreach}-->
            </table>
            <div class="btn_area">
                <ul>
                    <li>
                        <a href="javascript:;" onclick="eccube.setModeAndSubmit('return', '', '');"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back03" id="back03" /></a>
                    </li>
                    <li>
                        <input type="image" class="hover_change_image box190" src="<!--{$TPL_URLPATH}-->img/button/btn_address_select.jpg" alt="選択したお届け先に送る" name="send_button" id="send_button" />
                    </li>
                </ul>
            </div>
        </form>
        <table style="display:none;"><tbody>
            <tr id="default">
                <td>
                    <!--{assign var=key value="product_class_id"}-->
                    <input type="hidden" name="<!--{$key}-->[]" value="##PID##" />
                    <!--{assign var=key value="shipping"}-->
                    <select name="<!--{$key}-->[]">
                        <!--{html_options options=$arrAddress}-->
                    </select><br />
                </td>
                <td class="alignR">
                    <!--{assign var=key value="quantity"}-->
                    <input type="number" id="<!--{$key}-->_##PID##" name="<!--{$key}-->[]" value="1" class="box40 <!--{$key}-->_##PID##" maxlength="<!--{$arrForm[$key].length}-->" />
                </td>
            </tr>
        </tbody></table>
    </div>
</div>