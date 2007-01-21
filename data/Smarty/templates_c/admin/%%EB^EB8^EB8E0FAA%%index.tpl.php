<?php /* Smarty version 2.6.13, created on 2007-01-10 00:00:15
         compiled from contents/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contents/index.tpl', 95, false),array('modifier', 'str_replace', 'contents/index.tpl', 251, false),array('modifier', 'nl2br', 'contents/index.tpl', 253, false),array('function', 'html_options', 'contents/index.tpl', 145, false),)), $this); ?>
<script type="text/javascript">
<!--

function func_regist(url) {
	res = confirm('この内容で<?php if ($this->_tpl_vars['edit_mode'] == 'on'): ?>編集<?php else: ?>登録<?php endif; ?>しても宜しいですか？');
	if(res == true) {
		document.form1.mode.value = 'regist';
		document.form1.submit();
		return false;
	}
	return false;
}

function func_edit(news_id) {
	document.form1.mode.value = "search";
	document.form1.news_id.value = news_id;
	document.form1.submit();
}

function func_del(news_id) {
	res = confirm('この新着情報を削除しても宜しいですか？');
	if(res == true) {
		document.form1.mode.value = "delete";
		document.form1.news_id.value = news_id;
		document.form1.submit();
	}
	return false;
}

function func_rankMove(term,news_id) {
	document.form1.mode.value = "move";
	document.form1.news_id.value = news_id;
	document.form1.term.value = term;
	document.form1.submit();
}

function moving(news_id,rank, max_rank) {

	var val;
	var ml;
	var len;

	ml = document.move;
	len = document.move.elements.length;
	j = 0;
	for( var i = 0 ; i < len ; i++) {
	    if ( ml.elements[i].name == 'position' && ml.elements[i].value != "" ) {
			val = ml.elements[i].value;
			j ++;
	    }
	}
	
	if ( j > 1) {
		alert( '移動順位は１つだけ入力してください。' );
		return false;
	} else if( ! val ) {
		alert( '移動順位を入力してください。' );
		return false;
	} else if( val.length > 4){
		alert( '移動順位は4桁以内で入力してください。' );
		return false;
	} else if( val.match(/[0-9]+/g) != val){
		alert( '移動順位は数字で入力してください。' );
		return false;
	} else if( val == rank ){
		alert( '移動させる番号が重複しています。' );
		return false;
	} else if( val == 0 ){
		alert( '移動順位は0以上で入力してください。' );
		return false;
	} else if( val > max_rank ){
		alert( '入力された順位は、登録数の最大値を超えています。' );
		return false;	
	} else {
		ml.moveposition.value = val;
		ml.rank.value = rank;
		ml.news_id.value = news_id;
		ml.submit();
		return false;
	}
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="">
<input type="hidden" name="news_id" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['news_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="term" value="">
	<tr valign="top">
		<td background="<?php echo @URL_DIR; ?>
img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_subnavi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->新規登録</span></td>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼登録テーブルここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">	
									<thead>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="78">日付<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="600"><span class="red"><?php echo $this->_tpl_vars['arrErr']['year'];  echo $this->_tpl_vars['arrErr']['month'];  echo $this->_tpl_vars['arrErr']['day']; ?>
</span>
											<select name="year" <?php if ($this->_tpl_vars['arrErr']['year']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
												<option value="" selected>----</option>
												<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrYear'],'selected' => $this->_tpl_vars['selected_year']), $this);?>

											</select>年
											<select name="month" <?php if ($this->_tpl_vars['arrErr']['month']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
												<option value="" selected>--</option>
												<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['selected_month']), $this);?>

											</select>月
											<select name="day" <?php if ($this->_tpl_vars['arrErr']['day']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
												<option value="" selected>--</option>
												<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDay'],'selected' => $this->_tpl_vars['selected_day']), $this);?>

											</select>日
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="" class="fs12n">タイトル<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="" class="fs12n"><?php if ($this->_tpl_vars['arrErr']['news_title']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['news_title']; ?>
</span><?php endif; ?>
										<textarea name="news_title" cols="60" rows="8" class="area60" maxlength="<?php echo @MTEXT_LEN; ?>
" <?php if ($this->_tpl_vars['arrErr']['news_title']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br/><span class="red"> （上限<?php echo @MTEXT_LEN; ?>
文字）</span>
										</td>
									</tr>
									</thead>
									<tfoot>
									<tr>
										<td bgcolor="#f2f1ec" width="38" class="fs12n">URL</td>
										<td bgcolor="#ffffff" width="600" class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['news_url']; ?>
</span><input type="text" name="news_url" size="60" class="box60"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['news_url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" <?php if ($this->_tpl_vars['arrErr']['news_url']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?> maxlength="<?php echo @URL_LEN; ?>
"/><span class="red"> （上限<?php echo @URL_LEN; ?>
文字）</span>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="78">リンク</td>
										<td bgcolor="#ffffff" width="600"><input type="checkbox" name="link_method" value="2" <?php if ($this->_tpl_vars['link_method'] == 2): ?> checked <?php endif; ?> >別ウィンドウで開く</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="38" class="fs12n">本文作成</td>
										<td bgcolor="#ffffff" width="600" class="fs12n"><?php if ($this->_tpl_vars['arrErr']['news_comment']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['news_comment']; ?>
</span><?php endif; ?><textarea name="news_comment" cols="60" rows="8" wrap="soft" class="area60" maxlength="<?php echo @LTEXT_LEN; ?>
" style="background-color:<?php if ($this->_tpl_vars['arrErr']['news_comment']):  echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>"><?php echo ((is_array($_tmp=$this->_tpl_vars['news_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br/><span class="red"> （上限3000文字）</span>
										</td>
									</tr>
									</tfoot>
								</table>
								<!--▲登録テーブルここまで-->
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="return func_regist();"></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
									</form>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->登録済み新着情報</span></td>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼一覧表示エリアここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
								<form name="move" id="move" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
								<input type="hidden" name="mode" value="moveRankSet">
								<input type="hidden" name="term" value="setposition">
								<input type="hidden" name="news_id" value="">
								<input type="hidden" name="moveposition" value="">
								<input type="hidden" name="rank" value="">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="30">順位</td>
										<td width="76">日付</td>
										<td width="269">タイトル</td>
										<td width="50">編集</td>
										<td width="50">削除</td>
										<td width="100">移動</td>
									</tr>
									<?php if ($this->_tpl_vars['arrErr']['moveposition']): ?>
									<tr bgcolor="#ffffff" class="fs12n"><td bgcolor="#ffffff" colspan="6"><span class="red"><?php echo $this->_tpl_vars['arrErr']['moveposition']; ?>
</span></td></tr>
									<?php endif; ?>
									<?php unset($this->_sections['data']);
$this->_sections['data']['name'] = 'data';
$this->_sections['data']['loop'] = is_array($_loop=$this->_tpl_vars['list_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['data']['show'] = true;
$this->_sections['data']['max'] = $this->_sections['data']['loop'];
$this->_sections['data']['step'] = 1;
$this->_sections['data']['start'] = $this->_sections['data']['step'] > 0 ? 0 : $this->_sections['data']['loop']-1;
if ($this->_sections['data']['show']) {
    $this->_sections['data']['total'] = $this->_sections['data']['loop'];
    if ($this->_sections['data']['total'] == 0)
        $this->_sections['data']['show'] = false;
} else
    $this->_sections['data']['total'] = 0;
if ($this->_sections['data']['show']):

            for ($this->_sections['data']['index'] = $this->_sections['data']['start'], $this->_sections['data']['iteration'] = 1;
                 $this->_sections['data']['iteration'] <= $this->_sections['data']['total'];
                 $this->_sections['data']['index'] += $this->_sections['data']['step'], $this->_sections['data']['iteration']++):
$this->_sections['data']['rownum'] = $this->_sections['data']['iteration'];
$this->_sections['data']['index_prev'] = $this->_sections['data']['index'] - $this->_sections['data']['step'];
$this->_sections['data']['index_next'] = $this->_sections['data']['index'] + $this->_sections['data']['step'];
$this->_sections['data']['first']      = ($this->_sections['data']['iteration'] == 1);
$this->_sections['data']['last']       = ($this->_sections['data']['iteration'] == $this->_sections['data']['total']);
?>
									<tr bgcolor="<?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id'] == $this->_tpl_vars['news_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>" class="fs12">
										<?php $this->assign('db_rank', ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['rank'])); ?>
										<?php $this->assign('rank', ($this->_tpl_vars['line_max']-$this->_tpl_vars['db_rank']+1)); ?>
										<td width="" align="center"><?php echo $this->_tpl_vars['rank']; ?>
</td>
										<td width="" align="center"><?php echo ((is_array($_tmp="-")) ? $this->_run_mod_handler('str_replace', true, $_tmp, "/", $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['cast_news_date']) : str_replace($_tmp, "/", $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['cast_news_date'])); ?>
</td>
										<td width="">
											<?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['link_method'] == 1 && $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_url'] != ""): ?><a href="<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_url']; ?>
" ><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</a>
											<?php elseif ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['link_method'] == 1 && $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_url'] == ""):  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

											<?php elseif ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['link_method'] == 2 && $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_url'] != ""): ?><a href="<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_url']; ?>
" target="_blank" ><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</a>
											<?php else:  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

											<?php endif; ?>
										</td>
										<td width="" align="center"><a href="#" onclick="return func_edit('<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');">編集</a></td>
										<td width="" align="center"><a href="#" onclick="return func_del('<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');">削除</a></td>
										<td width="" align="center">
										<?php if (count ( $this->_tpl_vars['list_data'] ) != 1): ?>
										<input type="text" name="pos-<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id']; ?>
" size="3" class="box3" />番目へ<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnFormModeSubmit('move', 'moveRankSet','news_id', '<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id']; ?>
'); return false;">移動</a><br />
										<?php endif; ?>
										<?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['rank'] != $this->_tpl_vars['max_rank']): ?><a href="#" onclick="return func_rankMove('up', '<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['max_rank'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');">上へ</a><?php endif; ?>　<?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['rank'] != 1): ?><a href="#" onclick="return func_rankMove('down', '<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['news_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['max_rank'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');">下へ</a><?php endif; ?>
										</td>
									</tr>
									<?php endfor; else: ?>
									<tr bgcolor="#ffffff" class="fs12n">
										<td colspan="6">現在データはありません。</td>
									</tr>
									<?php endif; ?>								
								</form>
								</table>
								<!--▲一覧表示エリアここまで-->
									
								</td>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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
</table>
<!--★★メインコンテンツ★★-->