<?php
/*
  $Id: install_4.php,v 1.4 2003/04/18 10:29:44 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<p><span class="pageHeading">osCommerce</span><br><font color="#9a9a9a">�����ץ󥽡�����E���ޡ���������塼�����</font></p>

<p class="pageTitle">�������󥹥ȡ���</p>

<p><b>Step 2: osCommerce ������ʳ�ǧ�ڡ�����</b></p>

<form name="form1" action="<!--{$smarty.server.PHP_SELF}-->" method="post">
<input type="hidden" name="mode" value="complete">
<!--{foreach key=keyname item=val from=$arrForm}-->
<!--{assign var=key value=$keyname}--> 
<input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->">
<!--{/foreach}-->

<!--{if $create_db}-->
<p>����DB�κ�����Ԥ��ޤ�<br>�����塢�ǡ����١�����¤�򥤥�ݡ��Ȥ��ޤ�</p>
<!--{else}-->
<p>DB�ؤ���³���������ޤ���<br>OS�ǡ����١�����¤�򥤥�ݡ��Ȥ���Τǡ����ν��������Ǥ��ʤ��Ǥ���������</p>
<!--{/if}-->
<p>�ޤ�����������ե�����������ͤ��񤭹��ޤ�ޤ�:<br>
<!--{$conf_file}--><br>
����ե�����ξ����ѹ����ʤ��ǲ��������ޤ�������ե�����ˤϽ񤭹��߸��¤�Ϳ���Ƥ���������
<br></p>

�ʲ����������γ�ǧ�ˤʤ�ޤ����������ƤǤ������С����ؤ򥯥�å���������ե�����ؤν񤭹��ߤ�<br>
OS�ǡ����١�����¤�򥤥�ݡ��Ȥ��ޤ���
</br>

<p><b>1. ���󥹥ȡ���Τ���Υ��ץ��������ꤷ�Ƥ�������:</b></p>
<!--{*
<p><input type="checkbox" name="db_import", value="database"><b>OSDB�Υ���ݡ���</b><br>
OSDB�ơ��֥빽¤�򥤥�ݡ��Ȥ��ޤ���</p>

<p><input type="checkbox" name="install" value="configure"><b>��ư����</b><br>
�����ǻ��ꤷ�������֡������Фȥǡ����١����������Ф˴ؤ�������
���������ڡ�������Ӵ����ġ���δĶ�����ե�����˼�ưŪ����¸���ޤ���</p>
*}-->
<p><b>2. �����֡������Ф˴ؤ����������Ϥ��Ƥ�������:</b></p>

<p><b>�����֡������ФΥ롼�ȡ��ǥ��쥯�ȥ�</b><br>
<!--{assign var=key value="root_dir"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.ROOT_DIR}--><br>

<p><b>3. �ǡ����١����������Ф˴ؤ����������Ϥ��Ƥ�������:</b></p>

<p><b>�ǡ����١�����������</b><br>
<!--{assign var=key value="db_server"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_SERVER}-->
<br>
</p>

<p><b>�桼��̾</b><br>
<!--{assign var=key value="db_username"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_USERNAME}-->
<br>
���: �֥ǡ����١����Υ���ݡ��ȡפ������򤷤Ƥ�����ˤϡ�
������³��������Ȥˤ� Create ����� Drop ���¤�ɬ�פǤ���</p>

<p><b>�ǡ����١���̾</b><br>
<!--{assign var=key value="db_name"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_NAME}-->
<br>

<p><b>�ǡ����١������顼�᡼��������</b><br>
<!--{assign var=key value="db_error_mail_to"}-->
<!--{$arrErr[$key]}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_ERROR_MAIL_TO}-->
<br>

<p><b>�ǡ����١������顼�᡼���̾</b><br>
<!--{assign var=key value="db_error_mail_subject"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_ERROR_MAIL_SUBJECT}-->
<br>
�ǡ����١����˴ؤ��륨�顼�᡼��η�̾����ꤷ�ޤ���</p>

<p><b>���å��������ۤ��ѥɥᥤ��</b><br>
<!--{assign var=key value="domain_name"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DOMAIN_NAME}-->
<br>
���ꤷ���ɥᥤ��ʲ���Cookie��ͭ���ˤ��ޤ���www�ʤɤΥ��֥ɥᥤ��ϴޤ᤺����Ƭ�˥�����Ĥ��ƻ��ꤷ�Ƥ����������㤨��<i>.yourdomain.jp</i></p>

<p><b>HTTP�����С�</b><br>
<!--{assign var=key value="site_url"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.SITE_URL}-->
<br>
HTTP�����С���URL����ꤷ�ޤ���</p>

<p><b>HTTPS�����С�</b><br>
<!--{assign var=key value="ssl_url"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.SSL_URL}-->
<br>
SSL��³�ξ�硢HTTPS�����С���URL����ꤷ�ޤ���</p>
<!--{*
<p><?php echo osc_draw_checkbox_field('USE_PCONNECT', 'true'); ?> <b>��³Ū����³����Ѥ���</b><br>
��³Ū�ʥǡ����١�����³���ǽ�ˤ��ޤ�����ͭ�����Ф���Ѥ��Ƥ�����ϡ����դˤ��Ƥ�������</p>

<p><?php echo osc_draw_radio_field('STORE_SESSIONS', 'files', true); ?> <b>���å��������ե��������¸����</b><br>
<?php echo osc_draw_radio_field('STORE_SESSIONS', 'mysql'); ?> <b>���å��������ǡ����١�������¸����</b><br>
PHP���å����������¸�����ꤷ�Ƥ���������</p>


<p>���Υ��顼��ȯ�����ޤ���:</p>

<p><div class="boxMe"><b>����ե����뤬¸�ߤ��ޤ��󡣤��뤤�ϡ�Ŭ���ʥѡ��ߥå���󤬥��åȤ���Ƥ��ޤ���</b><br><br>���Τ褦�����Ƥ�������:
<ul class="boxMe"><li>cd <?php echo $HTTP_POST_VARS['DIR_FS_DOCUMENT_ROOT'] . $HTTP_POST_VARS['DIR_FS_CATALOG']; ?>includes/</li><li>touch configure.php</li><li>chmod 706 configure.php</li></ul>
<ul class="boxMe"><li>cd <?php echo $HTTP_POST_VARS['DIR_FS_DOCUMENT_ROOT'] . $HTTP_POST_VARS['DIR_FS_ADMIN']; ?>/includes/</li><li>touch configure.php</li><li>chmod 706 configure.php</li></ul></div></p>

<p class="noteBox">�⤷ <i>chmod 706</i> �Ǥ��ޤ�ư���ʤ���С�<i>chmod 777</i>���Ƥ���������</p>

<p class="noteBox">�⤷���ʤ���Microsoft Windows�Ķ��ǥ��󥹥ȡ����¹Ԥ��Ƥ�����ϡ��������ե���������������������褦�ˡ���¸������ե������̾�����Ѥ��ƤߤƤ���������</p>

<form name="install" action="install.php?step=4" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="����󥻥�"></a></td>
    <td align="center"><input type="image" src="images/button_retry.gif" border="0" alt="�ƻ��"></td>
  </tr>
</table>

</form>
*}-->

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('return', '', ''); return false;"><img src="images/button_cancel.gif" border="0" alt="����󥻥�"></a></td>
    <td align="center"><input type="hidden" name="install[]" value="configure"><input type="image" src="images/button_continue.gif" border="0" alt="����"></td>
  </tr>
</table>

</form>


