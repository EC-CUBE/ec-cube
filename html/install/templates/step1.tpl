<!--{*
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
 *}-->
<script type="text/javascript">
$(function() {
    $('.option').hide();
    if ($('input[name=mail_backend]').val() == 'smtp') {
        $('.smtp').attr('disabled', false);
    } else {
        $('.smtp').attr('disabled', true);
    }
    $('#options').click(function() {
        $('.option').slideToggle();
    });
    $('input[name=mail_backend]').change(function() {
        if ($(this).val() == 'smtp') {
            $('.smtp').attr('disabled', false);
        } else {
            $('.smtp').attr('disabled', true);
        }
    });
});
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div class="contents">
    <div class="message">
        <h2><!--{t string="t_EC site settings_01"}--></h2>
    </div>
    <div class="block">
        <table>
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th><!--{t string="tpl_030_1" escape="none"}--></th>
                <td>
                <!--{assign var=key value="shop_name"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br />
                <span class="ex-text"><!--{t string="t_Fill in the name of your store._01"}--></span>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_108_1" escape="none"}--></th>
                <td>
                <!--{assign var=key value="admin_mail"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br />
                <span class="ex-text"><!--{t string="tpl_750"}--><br />
                <!--{t string="tpl_756"}--></span>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_751" escape="none"}--><br/><!--{t string="tpl_668" T_FIELD1=$smarty.const.ID_MIN_LEN T_FIELD2=$smarty.const.ID_MAX_LEN}--></th>
                <td>
                <!--{assign var=key value="login_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br />
                <span class="ex-text"><!--{t string="tpl_752"}--></span><br />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_218_1" escape="none"}--><br/><!--{t string="tpl_668" T_FIELD1=$smarty.const.ID_MIN_LEN T_FIELD2=$smarty.const.ID_MAX_LEN}--></th>
                <td>
                <!--{assign var=key value="login_pass"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$smarty.const.ID_MAX_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="50"  /><br />
                <span class="ex-text"><!--{t string="tpl_753"}--></span><br />
                </td>
            </tr>
        </table>

        <h2><!--{t string="tpl_754"}--></h2>
        <table>
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th><!--{t string="tpl_755"}--><br/><!--{t string="tpl_668" T_FIELD1=$smarty.const.ID_MIN_LEN T_FIELD2=$smarty.const.ID_MAX_LEN}--></th>
                <td>
                <!--{assign var=key value="admin_dir"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:'admin'|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="50" /><br />
                <span class="ex-text"><!--{t string="tpl_757"}--><br />
                 <!--{t string="tpl_758"}--><br />
                 <!--{t string="tpl_759" escape="none"}--></span><br />
                </td>
            </tr>
            <tr>
                <th><!--{t string="PARAM_LABEL_SSL_LIMIT"}--><br/></th>
                <td>
                <!--{assign var=key value="admin_force_ssl"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key].value == 1}-->checked="checked"<!--{/if}--> /><label for="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;"><!--{t string="tpl_644"}--></label><br />
                <span class="ex-text"><!--{t string="tpl_760"}--></span><br />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_643"}--><br/></th>
                <td>
                <!--{assign var=key value="admin_allow_hosts"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <span class="ex-text"><!--{t string="tpl_645" escape="none"}--></span><br />
                <textarea name="<!--{$key}-->" class="box280" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;"><!--{$arrForm[$key].value|h}--></textarea>
                </td>
            </tr>
        </table>

        <h2><!--{t string="tpl_761"}--></h2>
        <table>
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th><!--{t string="tpl_762" escape="none"}--></th>
                <td>
                <!--{assign var=key value="normal_url"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_763" escape="none"}--></th>
                <td>
                <!--{assign var=key value="secure_url"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_764"}--></th>
                <td>
                <!--{assign var=key value="domain"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br />
                <span class="ex-text"><!--{t string="tpl_765"}--></span>
                </td>
            </tr>
        </table>

        <p><a href="javascript:;" id="options"><!--{t string="tpl_766" escape="none"}--></a></p>
        <div class="option">
            <h2><!--{t string="tpl_767"}--></h2>
            <table>
                <col width="30%" />
                <col width="70%" />
                <tr>
                    <th><!--{t string="tpl_768" escape="none"}--></th>
                    <td>
                      <!--{assign var=key value="mail_backend"}-->
                      <span class="attention"><!--{$arrErr[$key]}--></span>
                      <!--{html_radios name=$key options=$arrMailBackend selected=$arrForm[$key].value|h}--><br />
                      <span class="ex-text"><!--{t string="tpl_769" escape="none"}--></span>
                    </td>
                </tr>
                <tr>
                    <th><!--{t string="tpl_770"}--></th>
                    <td>
                      <!--{assign var=key value="smtp_host"}-->
                      <span class="attention"><!--{$arrErr[$key]}--></span>
                      <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="smtp" /><br />
                      <span class="ex-text"><!--{t string="tpl_771"}--></span>
                    </td>
                </tr>
                <tr>
                    <th><!--{t string="tpl_772"}--></th>
                    <td>
                      <!--{assign var=key value="smtp_port"}-->
                      <span class="attention"><!--{$arrErr[$key]}--></span>
                      <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="smtp" /><br />
                      <span class="ex-text"><!--{t string="tpl_771"}--></span>
                    </td>
                </tr>
                <tr>
                    <th><!--{t string="tpl_773"}--></th>
                    <td>
                      <!--{assign var=key value="smtp_user"}-->
                      <span class="attention"><!--{$arrErr[$key]}--></span>
                      <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="smtp" /><br />
                      <span class="ex-text"><!--{t string="tpl_774"}--></span>
                    </td>
                </tr>
                <tr>
                    <th><!--{t string="tpl_775"}--></th>
                    <td>
                      <!--{assign var=key value="smtp_password"}-->
                      <span class="attention"><!--{$arrErr[$key]}--></span>
                      <input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="smtp" /><br />
                      <span class="ex-text"><!--{t string="tpl_774"}--></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;"><span class="btn-prev"><!--{t string="tpl_610"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_736"}--></span></a></li>
        </ul>
    </div>
    <div class="btn-area-bottom"></div>
</form>
