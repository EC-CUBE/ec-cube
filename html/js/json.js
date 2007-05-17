//==============================================================================
//  SYSTEM      :  暫定版クロスブラウザAjax用ライブラリ
//  PROGRAM     :  XMLHttpRequestによる送受信を行います
//  AUTHER      :  Toshirou Takahashi http://jsgt.org/mt/01/


	////
	// 動作可能なブラウザ判定
	//
	// @sample        if(chkAjaBrowser()){ location.href='nonajax.htm' }
	// @sample        oj = new chkAjaBrowser();if(oj.bw.safari){ /* Safari code */ }
	// @return        ライブラリが動作可能なブラウザだけtrue  true|false
	//
	//  Enable list (v038現在)
	//   WinIE 5.5+ 
	//   Konqueror 3.3+
	//   AppleWebKit系(Safari,OmniWeb,Shiira) 124+ 
	//   Mozilla系(Firefox,Netscape,Galeon,Epiphany,K-Meleon,Sylera) 20011128+ 
	//   Opera 8+ 
	//
	
	function chkAjaBrowser()
	{
		var a,ua = navigator.userAgent;
		this.bw= { 
		  safari    : ((a=ua.split('AppleWebKit/')[1])?a.split('(')[0].split('.')[0]:0)>=124 ,
		  konqueror : ((a=ua.split('Konqueror/')[1])?a.split(';')[0]:0)>=3.3 ,
		  mozes     : ((a=ua.split('Gecko/')[1])?a.split(' ')[0]:0) >= 20011128 ,
		  opera     : (!!window.opera) && ((typeof XMLHttpRequest)=='function') ,
		  msie      : (!!window.ActiveXObject)?(!!createHttpRequest()):false 
		}
		return (this.bw.safari||this.bw.konqueror||this.bw.mozes||this.bw.opera||this.bw.msie)
	}

	////
	// XMLHttpRequestオブジェクト生成
	//
	// @sample        oj = createHttpRequest()
	// @return        XMLHttpRequestオブジェクト(インスタンス)
	//
	function createHttpRequest()
	{
		if(window.XMLHttpRequest){
			 //Win Mac Linux m1,f1,o8 Mac s1 Linux k3 & Win e7用
			return new XMLHttpRequest() ;
		} else if(window.ActiveXObject){
			 //Win e4,e5,e6用
			try {
				return new ActiveXObject('Msxml2.XMLHTTP') ;
			} catch (e) {
				try {
					return new ActiveXObject('Microsoft.XMLHTTP') ;
				} catch (e2) {
					return null ;
	 			}
	 		}
		} else  {
			return null ;
		}
	}
	
	////
	// 送受信関数
	//
	// @sample         sendRequest(onloaded,'&prog=1','POST','./about2.php',true,true)
	// @sample         sendRequest(onloaded,{name:taro,id:123,sel:1},','POST','./about3.php',true,true)
	// @sample         sendRequest({onload:loaded,onbeforsetheader:sethead},'',','POST','./about3.php',true,true)
	// @param {string} callback 受信時に起動する関数名 
	// @param {object} callback 受信時に起動する関数名とヘッダ指定関数名{onload:関数名,onbeforsetheader:関数名} 
	// @param {array}  callback 受信時に起動する関数名とヘッダ指定 ary['onload']=関数名;ary['onbeforsetheader']=関数名 
	// @see                    http://jsgt.org/ajax/ref/head_test/header/Range/004/sample.htm
	// @param {string} data	   送信するデータ stringの場合(&名前1=値1&名前2=値2...)
	// @param {object} data	   送信するデータ objectの場合{名前1:値1,名前2:値2,...}
	// @param {array}  data	   送信するデータ arrayの場合は連想配列 ary['名前1']=値1;ary['名前2']=値2
	// @param {string}method   'POST' or 'GET'
	// @param {string}url      リクエストするファイルのURL
	// @param {string}async	   非同期ならtrue 同期ならfalse
	// @param {string}sload	   スーパーロード trueで強制、省略またはfalseでデフォルト
	// @param {string}user	   認証ページ用ユーザー名
	// @param {string}password 認証ページ用パスワード
	//
	sendRequest.README	 = {
		url		: 'http://jsgt.org/ajax/ref/lib/ref.htm',
		name	: 'sendRequest', 
		version	: 0.51, 
		license	: 'Public Domain',
		author	: 'Toshiro Takahashi http://jsgt.org/mt/01/',memo:''
	};
	function sendRequest(callback,data,method,url,async,sload,user,password)
	{
		//XMLHttpRequestオブジェクト生成
		var oj = createHttpRequest();
		if( oj == null ) return null;
		
		//強制ロードの設定
		var sload = (!!sendRequest.arguments[5])?sload:false;
		if(sload || method.toUpperCase() == 'GET')url += '?';
		if(sload)url=url+'t='+(new Date()).getTime();
		
		//ブラウザ判定
		var bwoj = new chkAjaBrowser();
		var opera	  = bwoj.bw.opera;
		var safari	  = bwoj.bw.safari;
		var konqueror = bwoj.bw.konqueror;
		var mozes	  = bwoj.bw.mozes ;
				
		//callbackを分解
		//{onload:xxxx,onbeforsetheader:xxx}
		if(typeof callback=='object'){
			var callback_onload = callback.onload;
			var callback_onbeforsetheader = callback.onbeforsetheader;
		} else {
			var callback_onload = callback;
			var callback_onbeforsetheader = null;
		}

		//受信処理
		//operaはonreadystatechangeに多重レスバグがあるのでonloadが安全
		//Moz,FireFoxはoj.readyState==3でも受信するので通常はonloadが安全
		//Win ieではonloadは動作しない
		//Konquerorはonloadが不安定
		//参考http://jsgt.org/ajax/ref/test/response/responsetext/try1.php
		if(opera || safari || mozes){
			oj.onload = function () { callback_onload(oj); }
		} else {
		
			oj.onreadystatechange =function () 
			{
				if ( oj.readyState == 4 ){
					//alert(oj.status+'--'+oj.getAllResponseHeaders());
					callback_onload(oj);
				}
			}
		}

		//URLエンコード
		data = uriEncode(data,url);
		if(method.toUpperCase() == 'GET') {
			url += data
		}
		
		//open メソッド
		oj.open(method,url,async,user,password);

		
		//リクエストヘッダカスタマイズ用コールバック
		//使う場合は、呼び出しHTML側のwindow直下へグローバルな関数setHeadersを
		//記述し、その中でsetRequestHeader()をセットしてください
		//@sample function setHeaders(oj){oj.setRequestHeader('Content-Type',contentTypeUrlenc)}
		//
		if(!!callback_onbeforsetheader)callback_onbeforsetheader(oj);

		//デフォルトヘッダapplication/x-www-form-urlencodedセット
		setEncHeader(oj);
		
		
		//デバック
		//alert('////jslb_ajaxxx.js//// \n data:'+data+' \n method:'+method+' \n url:'+url+' \n async:'+async);
		
		//send メソッド
		oj.send(data);

		//URIエンコードヘッダセット
		function setEncHeader(oj){
	
			//ヘッダapplication/x-www-form-urlencodedセット
			// @see  http://www.asahi-net.or.jp/~sd5a-ucd/rec-html401j/interact/forms.html#h-17.13.3
			// @see  #h-17.3
			//   ( enctype のデフォルト値は 'application/x-www-form-urlencoded')
			//   h-17.3により、POST/GET問わず設定
			//   POSTで'multipart/form-data'を指定する必要がある場合はカスタマイズしてください。
			//
			//  このメソッドがWin Opera8.0でエラーになったので分岐(8.01はOK)
			var contentTypeUrlenc = 'application/x-www-form-urlencoded; charset=UTF-8';
			if(!window.opera){
				oj.setRequestHeader('Content-Type',contentTypeUrlenc);
			} else {
				if((typeof oj.setRequestHeader) == 'function')
					oj.setRequestHeader('Content-Type',contentTypeUrlenc);
			}	
			return oj
		}

		//URLエンコード
		//引数dataは、stringかobjectで渡せます
		function uriEncode(data,url){
			var encdata =(url.indexOf('?')==-1)?'?dmy':'';
			if(typeof data=='object'){
				for(var i in data)
					encdata+='&'+encodeURIComponent(i)+'='+encodeURIComponent(data[i]);
			} else if(typeof data=='string'){
				if(data=='')return '';
				//&と=で一旦分解しencode
				var encdata = '';
				var datas = data.split('&');
				for(var i=1;i<datas.length;i++)
				{
					var dataq = datas[i].split('=');
					encdata += '&'+encodeURIComponent(dataq[0])+'='+encodeURIComponent(dataq[1]);
				}
			} 
			return encdata;
		}

		return oj
	}

function test(data){
	alert(data.responseText);
}
