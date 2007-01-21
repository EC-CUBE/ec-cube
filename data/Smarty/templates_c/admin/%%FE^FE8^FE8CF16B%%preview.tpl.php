<?php /* Smarty version 2.6.13, created on 2007-01-18 18:20:49
         compiled from mail/preview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/preview.tpl', 8, false),array('modifier', 'nl2br', 'mail/preview.tpl', 8, false),)), $this); ?>
<?php if ($this->_tpl_vars['escape_flag'] == 1):  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['body'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp));  else:  echo $this->_tpl_vars['body'];  endif; ?>