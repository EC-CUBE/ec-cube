/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// **************  設定部分 *******************

	// Flashファイルへの相対パス
	var flashFilePath = "swf/index.swf";

	// Flash横幅
	var flashWidth = "400";

	// Flash縦幅
	var flashHeight = "279";

	// Flashの必要バージョン
	var reqVersion = 6;

	// Flashがインストールされていないときに表示するメッセージ
	var noFlashMsg =
		"<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" summary=\" \">"
		+"<tr><td colspan=\"3\"><img src=\"./img/flash/image_flash01.jpg\" width=\"400\" height=\"174\" alt=\"\"></td></tr>"
		+"<tr>"
		+"<td><img src=\"./img/flash/image_flash02.jpg\" width=\"140\" height=\"22\" alt=\"\"></td>"
		+"<td><a href=\"http://www.macromedia.com/shockwave/download/download.cgi?P5_Language=Japanese&Lang=Japanese&P1_Prod_Version=ShockwaveFlash&amp;Lang=Japanese\" target=\"_blank\"><img src=\"./img/flash/download.gif\" width=\"205\" height=\"22\" alt=\"\" border=\"0\"></a></td>"
		+"<td><img src=\"./img/flash/image_flash03.jpg\" width=\"55\" height=\"22\" alt=\"\"></td></tr>"
		+"</tr>"
		+"<tr><td colspan=\"3\"><img src=\"./img/flash/image_flash04.jpg\" width=\"400\" height=\"84\" alt=\"\"></td></tr>"
		+"</table>";


// ************** メイン *********************

	var maxVersion = 7;
	var actualVersion = 0;
	var jsVersion = 1.0;
	var noflashflag;
	var flash2Installed = false;
	var flash3Installed = false;
	var flash4Installed = false;
	var flash5Installed = false;
	var flash6Installed = false;
	var flash7Installed = false;
	var rightVersion = false;
	var isIE = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
	var isWin = (navigator.appVersion.indexOf("Windows") != -1) ? true : false;
	jsVersion = 1.1;

	if(isIE && isWin){
		document.write('<SCR' + 'IPT LANGUAGE=VBScript\> \n');
		document.write('on error resume next \n');
		document.write('flash2Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.2"))) \n');
		document.write('flash3Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.3"))) \n');
		document.write('flash4Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.4"))) \n');
		document.write('flash5Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.5"))) \n');  
		document.write('flash6Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6"))) \n');  
		document.write('flash7Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.7"))) \n');  
		document.write('</SCR' + 'IPT\> \n');
	}




	function detectFlash() {
		
		if (navigator.plugins) {
			if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
				var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
				var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;
				var flashVersion = parseInt(flashDescription.charAt(flashDescription.indexOf(".") - 1));
				flash2Installed = flashVersion == 2;
				flash3Installed = flashVersion == 3;
				flash4Installed = flashVersion == 4;
				flash5Installed = flashVersion == 5;
				flash6Installed = flashVersion == 6;
				flash6Installed = flashVersion >= 7;
			}
		}

		for (var i = 2; i <= maxVersion; i++) {  
			if (eval("flash" + i + "Installed") == true) actualVersion = i;
		}

		if(navigator.userAgent.indexOf("WebTV") != -1) actualVersion = 3;

		if (actualVersion >= reqVersion) {
			rightVersion = true;
			document.write("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"" + flashWidth + "\" height=\"" + flashHeight + "\">");
			document.write("<param name=\"movie\" value=\"" + flashFilePath + "\">");
			document.write("<param name=\"quality\" value=\"high\">");
			document.write("<param name=\"bgcolor\" value=\"#ffffff\">");
			document.write("<param name=\"loop\" value=\"false\">");
			document.write("<embed src=\"" + flashFilePath + "\" quality=\"high\" bgcolor=\"#ffffff\" loop=\"false\"  width=\"" + flashWidth + "\" height=\"" + flashHeight + "\" type=\"application/x-shockwave-flash\" pluginspage=\"http://wsww.macromedia.com/shockwave/download/index.cgi?p1_prod_version=shockwaveflash\" Name=\"opening\"></embed>");
			document.write("</object>");
		} else {
			document.write(noFlashMsg);
		}
	}



	detectFlash();
