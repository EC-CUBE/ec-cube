<?php /* Smarty version 2.6.13, created on 2007-01-19 12:53:16
         compiled from contents/recomend.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contents/recomend.tpl', 95, false),array('modifier', 'sfGetEnabled', 'contents/recomend.tpl', 132, false),)), $this); ?>
<script language="JavaScript">
<!--
function lfnCheckSubmit( fm ){
	
	var err = '';
	/*
	if ( ! fm["title"].value ){
		err += '見出しコメントを入力して下さい。';
	}
	*/
	if ( ! fm["comment"].value ){
		if ( err ) err += '\n';
		err += 'オススメコメントを入力して下さい。';
	}
	if ( err ){
		alert(err);
		return false;
	} else {
		if(window.confirm('内容を登録しても宜しいですか')){
			return true;
		}
	}
}

function lfnCheckSetItem( rank ){
	var flag = true;
	var checkRank = '<?php echo $this->_tpl_vars['checkRank']; ?>
';
	if ( checkRank ){
		if ( rank != checkRank ){
			if( ! window.confirm('さきほど選択した<?php echo $this->_tpl_vars['checkRank']; ?>
位の情報は破棄されます。宜しいでしょうか')){
				flag = false;
			}
		} 
	}
	
	if ( flag ){
		win03('./recommend_search.php?rank=' + rank,'search','500','500');
	}
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
		<td class="mainbg" >
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
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->オススメ管理</span></td>
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

						<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['tpl_disp_max']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
						<!--▼おすすめ<?php echo $this->_sections['cnt']['iteration']; ?>
-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
						<form name="form<?php echo $this->_sections['cnt']['iteration']; ?>
" id="form<?php echo $this->_sections['cnt']['iteration']; ?>
" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
						<input type="hidden" name="mode" value="regist">
						<input type="hidden" name="product_id" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['product_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
						<input type="hidden" name="category_id" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['category_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
						<input type="hidden" name="rank" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['rank'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="15" align="center"><?php echo $this->_sections['cnt']['iteration']; ?>
</td>
								<td bgcolor="#ffffff" width="130" align="center">
								<?php if ($this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['main_list_image'] != ""): ?>
									<?php $this->assign('image_path', (@IMAGE_SAVE_URL)."/".($this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['main_list_image'])); ?>
								<?php else: ?>
									<?php $this->assign('image_path', (@NO_IMAGE_URL)); ?>
								<?php endif; ?>
								<img src="<?php echo $this->_tpl_vars['image_path']; ?>
" alt="" />
								</td>
								<td bgcolor="#ffffff" width="40" align="center">
									<?php if ($this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['product_id']): ?>
									<a href="#" onClick="return fnInsertValAndSubmit( document.form<?php echo $this->_sections['cnt']['iteration']; ?>
, 'mode', 'delete', '削除します。宜しいですか' )">削除</a>
									<?php endif; ?>
								</td>
								<td bgcolor="#ffffff" width="40" align="center">
									<a href="#" onclick="lfnCheckSetItem('<?php echo $this->_sections['cnt']['iteration']; ?>
'); return false;" target="_blank">
									<?php if ($this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['product_id']): ?>商品<br/>変更<?php else: ?>商品<br/>選択<?php endif; ?>
									</a></td>
								<td bgcolor="#ffffff" width="350">
								<table width="350" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr class="fs12">
										<td width="70">商品名：<?php echo ((is_array($_tmp=$this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr class="fs12">
										<td colspan="2">オススメコメント：</td>
									</tr>
									<tr>
										<td colspan="2" class="fs12n">
										<span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_sections['cnt']['iteration']]['comment']; ?>
</span>
										<textarea name="comment" cols="45" rows="4" style="width: 337px; height: 82px; " <?php echo ((is_array($_tmp=$this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['product_id'])) ? $this->_run_mod_handler('sfGetEnabled', true, $_tmp) : sfGetEnabled($_tmp)); ?>
><?php echo $this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['comment']; ?>
</textarea>
										</td>
									</tr>
									<?php if ($this->_tpl_vars['arrItems'][$this->_sections['cnt']['iteration']]['product_id']): ?>
									<tr><td colspan=2><input type="submit" name="subm" value="登録する" onclick="return lfnCheckSubmit(document.form<?php echo $this->_sections['cnt']['iteration']; ?>
);"/></td></tr>
									<?php endif; ?>
								</table>
								</td>
							</tr>
							</form>
						</table>
						<!--▲おすすめ<?php echo $this->_sections['cnt']['iteration']; ?>
-->
						<?php endfor; endif; ?>
						
						<!--登録テーブルここまで-->
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
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->		