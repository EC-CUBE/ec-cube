/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
@charset "euc-jp";

body {
	background: #fff url(/img/common/bg.jpg);
	background-repeat: repeat-x;
	font-family:"�ͣ� �Х����å�","Hiragino Maru Gothic Pro","�ҥ饮�δݥ� Pro W4",Osaka,sans-serif;
}

input,select,option,textarea {
	font-family:"�ͣ� �Х����å�","Hiragino Maru Gothic Pro","�ҥ饮�δݥ� Pro W4",Osaka,sans-serif;
	font-size: 13px;
}

/*LINK*/
a:link { color: #006699; text-decoration: none; }
a:visited { color: #006699; text-decoration: none; }
a:hover { color: #f9a406; text-decoration: underline; }


/*FORM*/
.box3 { width: 33px; }	/*W3*/
.box6 { width: 54px; }	/*W6*/
.box10 { width: 82px; }	/*W10*/
.box20 { width: 152px; }	/*W20*/
.box25 { width: 187px; }	/*W25*/
.box30 { width: 222px; }	/*W30*/
.box33 { width: 243px; }	/*W33*/
.box35 { width: 257px; }	/*W35*/
.box40 { width: 292px; }	/*W40*/
.box45 { width: 341px; }	/*W45*/
.box50 { width: 362px; }	/*W50*/
.box60 { width: 432px; }	/*W60*/
.box65 { width: 467px; }	/*W65*/
.box76 { width: 544px; }	/*W76*/

.area40 { width: 302px; height: 134px; }	/*W40��H8*/
.area45 { width: 337px; height: 290px; }	/*W40��H20*/
.area46 { width: 337px; height: 134px; }	/*W40��H8*/
.area50 { width: 372px; height: 82px; }	/*W50?H4*/
.area55 { width: 407px; height: 82px; }	/*W50?H4*/
.area59 { width: 432px; height: 134px; }	/*W59��H8*/
.area60 { width: 433px; height: 134px; }	/*W60?H8*/
.area61 { width: 433px; height: 82px; }	/*W60?H4*/
.area65 { width: 444px; height: 290px; }	/*W65��H20*/
.area70 { width: 512px; height: 186px; }	/*W70?H12*/
.area75 { width: 547px; height: 186px; }	/*W75?H12*/
.area80 { width: 572px; height: 134px; }	/*W80��H8*/
.area90 { width: 650px; height: 420px; }
.area96 { width: 694px; height: 420px; }	/*W80��H30*/
.area96_2 { width: 694px; height: 160px; }	/*W80��H10*/
.area99 { width: 715px; height: 523px; }	/*W99?H40*/

/*COLOR*/
.ast { color: #cc0000; font-size: 90%; }
.darkred { color: #cc0000; }
.gray { color: #b6b7ba; }
.white { color: #ffffff; }
.whitest { color: #ffffff; font-weight: bold; }
.white10 { color: #ffffff; font-size: 62.5%;}
.red { color: #ff0000; }
.red10 { color:#ff0000; font-size: 10px; }
.red12 { color:#cc0000; font-size: 12px; }
.reselt { color: #ffcc00; font-size: 120%; font-weight: bold; }

.infodate {
	color: #cccccc; font-size: 62.5%; font-weight: bold;
	padding: 0 0 0 8px;
}

.infottl {
	color: #ffffff;
	font-size: 62.5%;
	line-height: 150%;
}

.info {
	padding: 0 4px;
	display: block;
}

.title {
	padding: 0px 0px 20px 25px;
	color: #ffffff;
	font-weight: bold;
	line-height: 120%;
}

.mainbg {
	background: #fff url(/img/contents/main_bg.jpg);
	background-repeat: repeat-x;
}

.infobg {
	background: #fff url(/img/contents/home_bg.jpg);
	background-repeat: no-repeat;
	background-color: #e3e3e3;
}


/*navi*/
#menu_navi {
	table-layout: fixed;
}

.navi a{
	background: url(/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #636469;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#ffffff;
	text-decoration:none;
	display:block ;
}

.navi a:visited {
	color:#ffffff;
	text-decoration:none;
}

.navi a:hover {
	background-color: #a5a5a5;
	color:#000000;
	text-decoration:none;
}

.navi_text {
	font-size: 75%;
	padding: 0 0 0 8px;
}

.navi-on a{
	background: url(/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #a5a5a5;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#000000;
	text-decoration:none;
	display:block;
}

.navi-on a:visited {
	color:#000000;
	text-decoration:none;
}

.navi-on a:hover {
	background-color: #a5a5a5;
	color:#000000;
	text-decoration:none;
}


/*subnavi*/
.subnavi a{
	background-color: #818287;
	width:140px;
	padding: 6px 5px 4px 5px;
	color:#ffffff;
	text-decoration:none;
	display:block;
}

.subnavi a:visited {
	color:#ffffff;
	text-decoration:none;
}

.subnavi a:hover {
	background-color: #b7b7b7;
	color:#000000;
	text-decoration:none;
}

.subnavi_text {
	font-size: 71%;
	padding: 0 0 0 8px;
}

.subnavi-on a{
	background-color: #b7b7b7;
	width:140px;
	padding: 6px 5px 4px 5px;
	color:#000000;
	text-decoration:none;
	display:block;
}

.subnavi-on a:visited {
	color:#000000;
	text-decoration:none;
}

.subnavi-on a:hover {
	background-color: #b7b7b7;
	color:#000000;
	text-decoration:none;
}



/*icon*/
.icon_edit{
	background: url(/img/contents/icon_edit.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_mail {
	background: url(/img/contents/icon_mail.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_delete {
	background: url(/img/contents/icon_delete.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_class {
	background: url(/img/contents/icon_class.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_confirm {
	background: url(/img/contents/icon_confirm.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}


/*send-page*/

.number a{
	background: url(/img/contents/number_bg.jpg);
	background-repeat: repeat-x;
	background-color: #505468;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}


.number a:visited {
	color:#ffffff;
	text-decoration:none;
}

.number a:hover{
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number-on a{
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
	
}

.number-on a:visited {
	color:#ffffff;
	text-decoration:none;
}

.number-on a:hover{
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

/*IMG*/
img {
	border: 0;
}
