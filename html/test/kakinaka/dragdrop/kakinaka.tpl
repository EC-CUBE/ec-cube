<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- saved from url=(0054)http://www.res-system.com/weblog/media/1/dragdrop.html -->
<HTML><HEAD><TITLE>DragDrop Sample</TITLE>
<META http-equiv=Content-Type content="text/html; charset=EUC-JP">
<SCRIPT src="./js/dragdrop.js" type=text/javascript></SCRIPT>

<STYLE TYPE="text/css">
<!--
.style_item0{
	BORDER-RIGHT: #000000 1px solid;
	BORDER-TOP: #000000 1px solid;
	BORDER-LEFT: #000000 1px solid;
	BORDER-BOTTOM: #000000 1px solid;
	PADDING-RIGHT: 5px;
	PADDING-LEFT: 5px;
	PADDING-BOTTOM: 5px;
	PADDING-TOP: 5px;	
	CURSOR: move;
	POSITION: absolute;
	BACKGROUND-COLOR: #aaaaff;
	TEXT-ALIGN: center;
}

.style_item1{
	BORDER-RIGHT: #000000 1px solid;
	BORDER-TOP: #000000 1px solid;
	BORDER-LEFT: #000000 1px solid;
	BORDER-BOTTOM: #000000 1px solid;
	PADDING-RIGHT: 5px;
	PADDING-LEFT: 5px;
	PADDING-BOTTOM: 5px;
	PADDING-TOP: 5px;	
	CURSOR: move;
	POSITION: absolute;
	BACKGROUND-COLOR: #ffaaaa;
	TEXT-ALIGN: center;
}

.style_item2{
	BORDER-RIGHT: #000000 1px solid;
	BORDER-TOP: #000000 1px solid;
	BORDER-LEFT: #000000 1px solid;
	BORDER-BOTTOM: #000000 1px solid;
	PADDING-RIGHT: 5px;
	PADDING-LEFT: 5px;
	PADDING-BOTTOM: 5px;
	PADDING-TOP: 5px;	
	CURSOR: move;
	POSITION: absolute;
	BACKGROUND-COLOR: #aaffaa;
	TEXT-ALIGN: center;
}

.style_flame{
	POSITION: absolute;
}

-->
</STYLE>

<META content="MSHTML 6.00.2900.2873" name=GENERATOR></HEAD>
<BODY>
<table>
	<tr><td height="200"></td></tr>
</table>
<table width="500" bgcolor="#cccccc"  border="0" cellspacing="1" cellpadding="10" summary=" ">
<form name="form1" id="form1" method="post" action="./recv_post.php" onsubmit="preSubmit()">
<input type="hidden" name="item0" value="0">
<input type="hidden" name="item1" value="0">
<input type="hidden" name="item2" value="0">
	<tr>
		<td bgcolor="#f5f5f5" align="center">カゴに入れる</td>
	</tr>
	<tr>
		<td height="250" id="td1" bgcolor="#ffffff"><DIV id="flame0" class="style_flame" style="TOP: 250px; LEFT: 10px; WIDTH: 500px; HEIGHT: 250px;"></DIV></td>
	</tr>
</table>
<table width="500">
	<tr><td height="5"></td></tr>
	<tr><td align="center"><input type="submit" name="subm" value="アイテムの状況を送信"></td></tr>
</form>
</table>

<DIV id="item0" class="style_item0" style="TOP: 50px; LEFT: 45px; WIDTH: 100px; HEIGHT: 135px;">
<table width="138" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td><img src="http://[MART_DOMAIN]/fs/image/module/s2_img_01.gif" width="138" height="21"></td>
  </tr>
  <tr> 
    <td background="http://[MART_DOMAIN]/fs/image/module/s2_img_02.gif" align="center">
      <table width="118" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="22" align="center"><b>ショットメッセージ</b></td>
        </tr>
        <tr> 
          <td height="1" bgcolor="666666"></td>
        </tr>
        <tr> 
          <td height="60" align="center"> 
            <table width="90" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><marquee direction="up" width="100%" height="75" align="middle" loop="infinite" scrollamount="2">訪問して下さって<br>ありがとうございます。<br>多様な商品で<br>お返しします。</marquee></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td><img src="http://[MART_DOMAIN]/fs/image/module/s2_img_03.gif" width="138" height="11"></td>
  </tr>
  <tr>
    <td align="center"> 
      <table width="118" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="2" height="5"></td>
        </tr>
        <tr> 
          <td width="15"><img src="http://[MART_DOMAIN]/fs/image/module/s2_img_04.gif" width="11" height="11"></td>
          <td height="17"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Tel [MART_TEL]</font></td>
        </tr>
        <tr> 
          <td><img src="http://[MART_DOMAIN]/fs/image/module/s2_img_04.gif" width="11" height="11"></td>
          <td height="17"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Fax [MART_FAX]</font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</DIV>
<DIV id="item1" class="style_item1" style="TOP: 50px; LEFT: 195px; WIDTH: 100px; HEIGHT: 75px;">
				<!--▼左ナビ-->
					<!--{include file=../../../../data/Smarty/templates/frontparts/leftnavi.tpl}-->
				<!--▲左ナビ-->
				</DIV>
<DIV id="item2" class="style_item2" style="TOP: 50px; LEFT: 345px; WIDTH: 100px; HEIGHT: 75px;">
<img src="./img/item2.jpg"></DIV>
</BODY>
</HTML>



<script type="text/javascript">

///////////////////////////////////////////////////////////////////////////////

function addEvent( elm, evtType, fn, useCapture) {

    if (elm.addEventListener) {
        elm.addEventListener(evtType, fn, useCapture);
        return true;

    }
    else if (elm.attachEvent) {

        var r = elm.attachEvent('on' + evtType, fn);
        return r;

    }
    else {
        elm['on'+evtType] = fn;

    }
    
}

///////////////////////////////////////////////////////////////////////////////

function init () {

    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };

    //
    // Assign Event Handlers
    //
/*
    var all_elms = document.getElementsByTagName ( 'div' );

    for ( var i = 0; i < all_elms.length; i++ ) {

        if ( getAttrValue ( all_elms[i], 'class' ) == 'dragged_elm' ) {
           
            addEvent ( all_elms[i], 'mousedown', onMouseDown, false );
            
        }
    }
*/
}

addEvent ( window, 'load', init, false );
</script>
