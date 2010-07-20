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
<!--{section name=question loop=$QUESTION.question}-->
	<!--{if $QUESTION.question[question].kind }-->
		<tr>
			<td colspan="2" bgcolor="#edf6ff" class="fs12n">質問<!--{$smarty.section.question.iteration}-->：<!--{$QUESTION.question[question].name|escape}--></td>
		</tr>
		<!--{if $QUESTION.question[question].kind eq 1}-->
		<tr>
			<td colspan="2" bgcolor="ffffff" class="fs12n">
			<textarea name="option[<!--{$smarty.section.question.index}-->]" cols="55" rows="8" class="area55" wrap="physical" <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.option[$smarty.section.question.index]|escape}--></textarea>
			<!--{if $arrErr.option[$smarty.section.question.index]}--><br><span class="red">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
			</td>
		</tr>
		<!--{elseif $QUESTION.question[question].kind eq 2}-->
		<tr>
			<td colspan="2" bgcolor="ffffff" class="fs12n">
			<input type="text" name="option[<!--{$smarty.section.question.index}-->]" size="55" class="box50" value="<!--{$arrForm.option[$smarty.section.question.index]|escape}-->" <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
			<!--{if $arrErr.option[$smarty.section.question.index]}--><br><span class="red">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
			</td>
			</tr>
		<!--{elseif $QUESTION.question[question].kind eq 3}-->
		<tr>
			<td colspan="2" bgcolor="ffffff">
			<table width="540" border="0" cellspacing="0" cellpadding="3" summary=" ">
				<input type="hidden" name="option[<!--{$smarty.section.question.index}-->][0]" value="">
				<tr><td class="fs12n">
					<span  <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
					<!--{html_checkboxes name="option[`$smarty.section.question.index`]" options=$QUESTION.question[question].option selected=$arrForm.option[question] separator="<br>"}-->
					</span>
					</td>
				</tr>
				<!--{if $arrErr.option[$smarty.section.question.index]}--><tr><td class="fs12n"><span class="red">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn></td></tr><!--{/if}-->
				</table>
			</td>
		</tr>
		<!--{elseif $QUESTION.question[question].kind eq 4}-->
		<tr>
			<td colspan="2" bgcolor="ffffff">
			<input type="hidden" name="option[<!--{$smarty.section.question.index}-->][0]" value="">
			<table width="540" border="0" cellspacing="0" cellpadding="3" summary=" ">
				<!--{section name=sub loop=$QUESTION.question[question].option}-->
					<!--{if $smarty.section.sub.index is even}--><tr><!--{/if}-->
					<td width="270" class="fs12n">
						<span  <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
						<!--{if $QUESTION.question[question].option[sub]}-->
						<input type="radio" name="option[<!--{$smarty.section.question.index}-->]" value="<!--{$smarty.section.sub.index|escape}-->" <!--{if $smarty.section.sub.index eq $arrForm.option[question]|default:"-1" }-->checked<!--{/if}-->>
						<!--{$QUESTION.question[question].option[sub]|escape}-->
						<!--{/if}-->
						</span>
					</td>
					<!--{if $smarty.section.sub.index is odd}--></tr><!--{/if}-->
				<!--{/section}-->
				<!--{if $arrErr.option[$smarty.section.question.index]}--><tr><td class="fs12n"><span class="red">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn></tr><!--{/if}-->
			</table>
			</td>
		</tr>
		<!--{/if}-->
	<!--{/if}-->
	
<!--{/section}-->
