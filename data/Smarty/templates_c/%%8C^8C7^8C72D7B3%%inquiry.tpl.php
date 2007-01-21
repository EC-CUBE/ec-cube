<?php /* Smarty version 2.6.13, created on 2007-01-19 12:55:32
         compiled from inquiry/inquiry.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'inquiry/inquiry.tpl', 9, false),array('modifier', 'default', 'inquiry/inquiry.tpl', 50, false),array('function', 'sfSetErrorStyle', 'inquiry/inquiry.tpl', 14, false),array('function', 'html_checkboxes', 'inquiry/inquiry.tpl', 32, false),)), $this); ?>
<?php unset($this->_sections['question']);
$this->_sections['question']['name'] = 'question';
$this->_sections['question']['loop'] = is_array($_loop=$this->_tpl_vars['QUESTION']['question']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['question']['show'] = true;
$this->_sections['question']['max'] = $this->_sections['question']['loop'];
$this->_sections['question']['step'] = 1;
$this->_sections['question']['start'] = $this->_sections['question']['step'] > 0 ? 0 : $this->_sections['question']['loop']-1;
if ($this->_sections['question']['show']) {
    $this->_sections['question']['total'] = $this->_sections['question']['loop'];
    if ($this->_sections['question']['total'] == 0)
        $this->_sections['question']['show'] = false;
} else
    $this->_sections['question']['total'] = 0;
if ($this->_sections['question']['show']):

            for ($this->_sections['question']['index'] = $this->_sections['question']['start'], $this->_sections['question']['iteration'] = 1;
                 $this->_sections['question']['iteration'] <= $this->_sections['question']['total'];
                 $this->_sections['question']['index'] += $this->_sections['question']['step'], $this->_sections['question']['iteration']++):
$this->_sections['question']['rownum'] = $this->_sections['question']['iteration'];
$this->_sections['question']['index_prev'] = $this->_sections['question']['index'] - $this->_sections['question']['step'];
$this->_sections['question']['index_next'] = $this->_sections['question']['index'] + $this->_sections['question']['step'];
$this->_sections['question']['first']      = ($this->_sections['question']['iteration'] == 1);
$this->_sections['question']['last']       = ($this->_sections['question']['iteration'] == $this->_sections['question']['total']);
?>
	<?php if ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['kind']): ?>
		<tr>
			<td colspan="2" bgcolor="#edf6ff" class="fs12n">質問<?php echo $this->_sections['question']['iteration']; ?>
：<?php echo ((is_array($_tmp=$this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
		</tr>
		<?php if ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['kind'] == 1): ?>
		<tr>
			<td colspan="2" bgcolor="ffffff" class="fs12n">
			<textarea name="option[<?php echo $this->_sections['question']['index']; ?>
]" cols="55" rows="8" class="area55" wrap="physical" <?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]):  echo sfSetErrorStyle(array(), $this); endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['option'][$this->_sections['question']['index']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
			<?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]): ?><br><span class="red">質問<?php echo $this->_sections['question']['iteration']; ?>
を入力して下さい</sapn><?php endif; ?>
			</td>
		</tr>
		<?php elseif ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['kind'] == 2): ?>
		<tr>
			<td colspan="2" bgcolor="ffffff" class="fs12n">
			<input type="text" name="option[<?php echo $this->_sections['question']['index']; ?>
]" size="55" class="box50" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['option'][$this->_sections['question']['index']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" <?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]):  echo sfSetErrorStyle(array(), $this); endif; ?>>
			<?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]): ?><br><span class="red">質問<?php echo $this->_sections['question']['iteration']; ?>
を入力して下さい</sapn><?php endif; ?>
			</td>
			</tr>
		<?php elseif ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['kind'] == 3): ?>
		<tr>
			<td colspan="2" bgcolor="ffffff">
			<table width="540" border="0" cellspacing="0" cellpadding="3" summary=" ">
				<input type="hidden" name="option[<?php echo $this->_sections['question']['index']; ?>
][0]" value="">
				<tr><td class="fs12n">
					<span  <?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]):  echo sfSetErrorStyle(array(), $this); endif; ?>>
					<?php echo smarty_function_html_checkboxes(array('name' => "option[".($this->_sections['question']['index'])."]",'options' => $this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['option'],'selected' => $this->_tpl_vars['arrForm']['option'][$this->_sections['question']['index']],'separator' => "<br>"), $this);?>

					</span>
					</td>
				</tr>
				<?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]): ?><tr><td class="fs12n"><span class="red">質問<?php echo $this->_sections['question']['iteration']; ?>
を入力して下さい</sapn></td></tr><?php endif; ?>
				</table>
			</td>
		</tr>
		<?php elseif ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['kind'] == 4): ?>
		<tr>
			<td colspan="2" bgcolor="ffffff">
			<input type="hidden" name="option[<?php echo $this->_sections['question']['index']; ?>
][0]" value="">
			<table width="540" border="0" cellspacing="0" cellpadding="3" summary=" ">
				<?php unset($this->_sections['sub']);
$this->_sections['sub']['name'] = 'sub';
$this->_sections['sub']['loop'] = is_array($_loop=$this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['option']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sub']['show'] = true;
$this->_sections['sub']['max'] = $this->_sections['sub']['loop'];
$this->_sections['sub']['step'] = 1;
$this->_sections['sub']['start'] = $this->_sections['sub']['step'] > 0 ? 0 : $this->_sections['sub']['loop']-1;
if ($this->_sections['sub']['show']) {
    $this->_sections['sub']['total'] = $this->_sections['sub']['loop'];
    if ($this->_sections['sub']['total'] == 0)
        $this->_sections['sub']['show'] = false;
} else
    $this->_sections['sub']['total'] = 0;
if ($this->_sections['sub']['show']):

            for ($this->_sections['sub']['index'] = $this->_sections['sub']['start'], $this->_sections['sub']['iteration'] = 1;
                 $this->_sections['sub']['iteration'] <= $this->_sections['sub']['total'];
                 $this->_sections['sub']['index'] += $this->_sections['sub']['step'], $this->_sections['sub']['iteration']++):
$this->_sections['sub']['rownum'] = $this->_sections['sub']['iteration'];
$this->_sections['sub']['index_prev'] = $this->_sections['sub']['index'] - $this->_sections['sub']['step'];
$this->_sections['sub']['index_next'] = $this->_sections['sub']['index'] + $this->_sections['sub']['step'];
$this->_sections['sub']['first']      = ($this->_sections['sub']['iteration'] == 1);
$this->_sections['sub']['last']       = ($this->_sections['sub']['iteration'] == $this->_sections['sub']['total']);
?>
					<?php if (!(1 & $this->_sections['sub']['index'])): ?><tr><?php endif; ?>
					<td width="270" class="fs12n">
						<span  <?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]):  echo sfSetErrorStyle(array(), $this); endif; ?>>
						<?php if ($this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['option'][$this->_sections['sub']['index']]): ?>
						<input type="radio" name="option[<?php echo $this->_sections['question']['index']; ?>
]" value="<?php echo ((is_array($_tmp=$this->_sections['sub']['index'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" <?php if ($this->_sections['sub']['index'] == ((is_array($_tmp=@$this->_tpl_vars['arrForm']['option'][$this->_sections['question']['index']])) ? $this->_run_mod_handler('default', true, $_tmp, "-1") : smarty_modifier_default($_tmp, "-1"))): ?>checked<?php endif; ?>>
						<?php echo ((is_array($_tmp=$this->_tpl_vars['QUESTION']['question'][$this->_sections['question']['index']]['option'][$this->_sections['sub']['index']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

						<?php endif; ?>
						</span>
					</td>
					<?php if ((1 & $this->_sections['sub']['index'])): ?></tr><?php endif; ?>
				<?php endfor; endif; ?>
				<?php if ($this->_tpl_vars['arrErr']['option'][$this->_sections['question']['index']]): ?><tr><td class="fs12n"><span class="red">質問<?php echo $this->_sections['question']['iteration']; ?>
を入力して下さい</sapn></tr><?php endif; ?>
			</table>
			</td>
		</tr>
		<?php endif; ?>
	<?php endif; ?>
	
<?php endfor; endif; ?>