<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_css" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="area_row" value=<!--{$area_row}-->>
<input type="hidden" name="old_css_name" value="<!--{$old_css_name}-->" />
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->CSS編集</span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<!--▼CSS編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
    							<tr class="fs12n">
		    						<td bgcolor="#ffffff" align="left">
				    					<!--{ if $arrErr.css_name != "" }--> <div align="center"> <span class="red12"><!--{$arrErr.css_name}--></span></div> <!--{/if}-->
						    			CSSファイル名：<input type="text" name="css_name" value="<!--{$css_name}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.css_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />.css<span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
								    </td>
    							</tr>
									<tr class="fs12n">
										<td bgcolor="#ffffff" align="center">
											<textarea name="css" cols=90 rows=<!--{$area_row}--> align="left" wrap=off style="width: 650px;"><!--{$css_data}--></textarea>
											<div align="right">
											<input type="button" value="大きくする" onClick="ChangeSize(this, css, 50, 30, area_row)">
											</div>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center">
											<input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" onclick="fnFormModeSubmit('form_css','confirm','','');" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" >
										</td>
									</tr>
								</table>
								<!--▲CSS編集　ここまで-->

    						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
		    					<tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
				    		</table>

    						<!--▼CSSファイル一覧　ここから-->
		    				<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
				    			<tr class="fs12n">
						    		<td bgcolor="#f2f1ec" align="center" colspan=2 ><strong>編集可能CSSファイル</strong></td>
    							</tr>
		    					
                  <!--{if count($arrCSSList) > 0}-->
				    			<!--{foreach key=key item=item from=$arrCSSList}-->
						    	<tr class="fs12n" height=20>
								    <td align="center" width=600 bgcolor="<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
    									<a href="<!--{$smarty.server.PHP_SELF}-->?css_name=<!--{$item.css_name}-->"><!--{$item.file_name}--></a>
		    						</td>
				    				<td  align="center" width=140 bgcolor="<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
								    	<input type="button" value="削除" name="del_<!--{$item.css_name}-->" onclick="fnFormModeSubmit('form_css','delete','css_name','<!--{$item.css_name}-->');"  />
				    				</td>
						    	</tr>
    							<!--{/foreach}-->
                  <!--{else}-->
				    			<tr class="fs12n">
						    		<td bgcolor="#ffffff" align="center" colspan=2 >CSSファイルが存在しません。</td>
    							</tr>
                  <!--{/if}-->

		    					<tr class="fs12n">
				    				<td bgcolor="#f2f1ec" align="center" colspan=2>
						    		<input type='button' value='新規CSS作成' name='subm' onclick="location.href='http://<!--{$smarty.server.HTTP_HOST}--><!--{$smarty.server.PHP_SELF|escape}-->'">
								    </td>
    							</tr>
		    				</table>
				    		<!--▲CSSファイル一覧　ここまで-->

								</td>
								<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->

<script type="text/javascript">
	function ChangeSize(button, TextArea, Max, Min, row_tmp){
		if(TextArea.rows <= Min){
			TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
		}else{
			TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
		}
	}
</script>
