<?php /* Smarty version 2.6.13, created on 2007-01-10 18:48:01
         compiled from /home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery.tpl', 32, false),)), $this); ?>
<script type="text/javascript">
<!--
	function fnCheckAfterOpenWin(){
		if (<?php echo $this->_tpl_vars['tpl_linemax']; ?>
 >= <?php echo @DELIV_ADDR_MAX; ?>
){
			alert('最大登録数を超えています');
			return false;
		}else{
			win02('./delivery_addr.php','new_deiv','600','640');
		}
	}

//-->
</script>

<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" >
		<input type="hidden" name="mode" value=""> 
		<input type="hidden" name="other_deliv_id" value="">
		<input type="hidden" name="pageno" value="<?php echo $this->_tpl_vars['tpl_pageno']; ?>
">
			<tr valign="top">
				<td>
				<!--▼NAVI-->
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_navi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<!--▲NAVI-->
				</td>
				<td align="right">
				
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<?php if ($this->_tpl_vars['tpl_linemax'] < @DELIV_ADDR_MAX): ?>
						<td><!--★タイトル--><img src="<?php echo @URL_DIR; ?>
img/mypage/subtitle03.gif" width="515" height="32" alt="お届け先追加・変更"></td>
						<?php endif; ?>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#fff5e8">
						<table width="495" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td height="10"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="305" height="1" alt=""></td>
								<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="190" height="1" alt=""></td>
							</tr>
							<tr>
								<td><span class="fs12">登録住所以外へのご住所へ送付される場合等にご利用いただくことができます。</span><br>
								<span class="fs10">※最大<?php echo @DELIV_ADDR_MAX; ?>
件まで登録できます。</span></td>
								<td align="right"><?php if ($this->_tpl_vars['tpl_linemax'] < 20): ?><a href="<?php echo @URL_DIR; ?>
mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','600','640'); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/newadress_on.gif','newadress');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/newadress.gif','newadress');" target="_blank"><img src="<?php echo @URL_DIR; ?>
img/common/newadress.gif" width="160" height="22" alt="新しいお届け先を追加" border="0" name="newadress"></a><?php endif; ?></td>
							</tr>
							<tr><td height="10"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<?php if ($this->_tpl_vars['tpl_linemax'] > 0): ?>
						<!--表示エリアここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td colspan="6" bgcolor="#f0f0f0" class="fs12n"><strong>▼お届け先</strong></td>
							</tr>
							<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrOtherDeliv']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
								<?php $this->assign('OtherPref', ($this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['pref'])); ?> 
								<tr bgcolor="#ffffff">
									<td width="10" align="center" class="fs12"><?php echo $this->_sections['cnt']['iteration']; ?>
</td>
									<td width="80" class="fs12">お届け先住所</td>
									<td width="290" class="fs12">〒<?php echo $this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['zip01']; ?>
-<?php echo $this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['zip02']; ?>
<br>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPref'][$this->_tpl_vars['OtherPref']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
									<td width="30" align="center" class="fs12"><a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<?php echo $this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['other_deliv_id']; ?>
','deliv_disp','600','640'); return false;">変更</a>
									<td width="30" align="center" class="fs12"><a href="#" onclick="fnModeSubmit('delete','other_deliv_id','<?php echo $this->_tpl_vars['arrOtherDeliv'][$this->_sections['cnt']['index']]['other_deliv_id']; ?>
');">削除</a></td>
								</tr>
							<?php endfor; endif; ?>							
						</table>
						<!--表示エリアここまで-->
						<?php else: ?>
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td colspan="5" bgcolor="#ffffff" class="fs12n" align="center"><strong>新しいお届け先はありません。</strong></td>
							</tr>
						</table>
						<?php endif; ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

