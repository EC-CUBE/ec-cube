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
function lfnChangePort(db_type) {

    type = db_type.value;

    if (type == 'pgsql') {
        form1.db_port.value = '<!--{$arrDB_PORT.pgsql}-->';
    }

    if (type == 'mysql') {
        form1.db_port.value = '<!--{$arrDB_PORT.mysql}-->';
    }
}
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div class="contents">
    <div class="message">
        <h2><!--{t string="tpl_Database settings_01"}--></h2>
        <!--{t string="tpl_* You must create a new database before installation._01"}-->
        <div class="attention"><!--{$arrErr.all}--></div>
    </div>
    <div class="block">
        <table>
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th><!--{t string="tpl_DB type <span class='attention'>*</span>_01" escape="none"}--></th>
                <td>
                <!--{assign var=key value="db_type"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onChange="lfnChangePort(this)">
                <!--{html_options options=$arrDB_TYPE selected=$arrForm[$key].value}-->
                </select>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_DB server_01"}--></th>
                <td>
                <!--{assign var=key value="db_server"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Port_01"}--></th>
                <td>
                <!--{assign var=key value="db_port"}-->
                <span class="attention"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_DB name <span class='attention'>*</span>_01" escape="none"}--></th>
                <td>
                <!--{assign var=key value="db_name"}-->
                <span class="attention"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_DB user <span class='attention'>*</span>_01" escape="none"}--></th>
                <td>
                <!--{assign var=key value="db_user"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_DB password <span class='attention'>*</span>_01" escape="none"}--></th>
                <td>
                <!--{assign var=key value="db_password"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" />
                </td>
            </tr>
        </table>
    </div>

    <div class="btn-area-top"></div>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_step1';document.form1.submit();return false;"><span class="btn-prev"><!--{t string="tpl_Go back_01"}--></span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_Next_01"}--></span></a></li>
            </ul>
        </div>
        <div class="btn-area-bottom"></div>
    </div>
</form>
