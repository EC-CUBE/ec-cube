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
<!--{$tpl_header}-->
 * This e-mail has been automatically sent.
 It can be viewed optimally using monospaced font.

===========================================================
  * This e-mail has been sent by <!--{$tpl_shopname}--> to the party making 
  an inquiry. 
  If you do not recall making an inquiry, please disregard
  this e-mail. 
  In addition, please contact <!--{$tpl_infoemail}--> regarding this matter. 
===========================================================

Dear <!--{$arrForm.name01.value}-->,

We have received your inquiry below. 
We will contact you as soon as we are able to confirm the issue at hand.
Thank you for your patience. 

*Name :<!--{$arrForm.name01.value}--> <!--{$arrForm.name02.value}-->
*Postal code:<!--{* <!--{if $arrForm.zip01.value && $arrForm.zip02.value}--><!--{$arrForm.zip01.value}-->-<!--{$arrForm.zip02.value}--><!--{/if}--> *}--><!--{if $arrForm.zipcode.value}--><!--{$arrForm.zipcode.value}--><!--{/if}-->

*Address  :<!--{$arrForm.addr01.value}--><!--{$arrForm.addr02.value}-->
*Phone number:<!--{$arrForm.tel01.value}-->-<!--{$arrForm.tel02.value}-->-<!--{$arrForm.tel03.value}-->
*E-mail address:<!--{$arrForm.email.value}-->
*Details of inquiry
<!--{$arrForm.contents.value}-->
<!--{$tpl_footer}-->
