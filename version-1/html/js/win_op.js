/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
<!--
	function win01(URL,Winname,Wwidth,Wheight){
		var WIN;
		WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=no,resizable=no,toolbar=no,location=no,directories=no,status=no");
		WIN.focus();
	}
// -->
	
<!--
	function win02(URL,Winname,Wwidth,Wheight){
		var WIN;
		WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
		WIN.focus();
	}
// -->

<!--
	function win03(URL,Winname,Wwidth,Wheight){
		var WIN;
		WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
		WIN.focus();
	}
// -->

<!--
function winSubmit(URL,formName,Winname,Wwidth,Wheight){
	WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
    document.forms[formName].target = Winname;
	WIN.focus();
}
//-->

<!--
	function ChangeParent()
	{
		window.opener.location.href="../contact/index.php";
	}
//-->


<!--//
function CloseChild()
{
	window.close();
}
//-->