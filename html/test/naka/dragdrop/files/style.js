
var d = document;
var n = navigator;
var Mac = n.appVersion.indexOf('Mac');
var MSIE = n.userAgent.indexOf('MSIE');
var Safari = n.userAgent.indexOf('Safari');
var Netscape = n.userAgent.indexOf('Netscape');
var css = null;
	
if(Mac>-1){
	if((Safari>1)||(MSIE>1)){
		css = 'mac-webkit.css';
	}else{
		css = 'mac.css';
	}
}else{
	if(MSIE>1){
		css = 'win-ie.css';
	}else{
		css = 'win.css';
	}
}
d.write('<link rel="stylesheet" href="/drecomcms/css/' + css + '" type="text/css">\n');
