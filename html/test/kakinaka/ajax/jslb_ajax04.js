////
// 暫定版Ajax用ライブラリ jslb_ajax04.js
// 最新情報 http://jsgt.org/mt/archives/01/000409.html 
// 上記コメント削除不可。商用利用、改造、自由。連絡不要です。
// 

	////
	// XMLHttpRequestオブジェクト生成
	//
	// @sample oj=createHttpRequest()
	// @return XMLHttpRequestオブジェクト
	//
	function createHttpRequest()
	{
		if(window.ActiveXObject){
			 //Win e4,e5,e6用
			try {
				return new ActiveXObject("Msxml2.XMLHTTP") ;
			} catch (e) {
				try {
					return new ActiveXObject("Microsoft.XMLHTTP") ;
				} catch (e2) {
					return null ;
	 			}
	 		}
		} else if(window.XMLHttpRequest){
			 //Win Mac Linux m1,f1,o8 Mac s1 Linux k3用
			return new XMLHttpRequest() ;
		} else {
			return null ;
		}
	}
	
	////
	// 送信関数
	//
	// @sample sendRequest(onloaded,'&prog=1','POST','./about2.php',true,true)
	// @param callback 受信時に起動する関数名
	// @param data	 送信するデータ
	// @param method "POST" or "GET"
	// @param urlリクエストするファイルのURL
	// @param async	非同期ならtrue 同期ならfalse
	// @param sload	スーパーロード trueで強制、省略またはfalseでデフォルト
	//
	function sendRequest(callback,data,method,url,async,sload)
	{
	
		//XMLHttpRequestオブジェクト生成
		var oj = createHttpRequest()
		if( oj == null ) return null
		
		//強制ロードの設定
		var sload = (!!sendRequest.arguments[5])?sload:false;
		if(sload)url=url+"?t="+(new Date()).getTime()

		//ブラウザ判定
		var ua = navigator.userAgent
		var safari	= ua.indexOf("Safari")!=-1
		var konqueror = ua.indexOf("Konqueror")!=-1
		var mozes	 = ((a=navigator.userAgent.split("Gecko/")[1] )
				?a.split(" ")[0]:0) >= 20011128 
		
		//受信処理
		//operaはonreadystatechangeに多重レスバグがあるのでonloadが安全
		//Moz,FireFoxはoj.readyState==3でも受信するので通常はonloadが安全
		//Win ieではonloadは動作しない
		//Konquerorはonloadが不安定
		//参考http://jsgt.org/ajax/ref/test/response/responsetext/try1.php
		if(window.opera || safari || mozes){
			oj.onload = function () { callback(oj) }
		} else {
		
			oj.onreadystatechange =function () 
			{
				if ( oj.readyState == 4 ){
					callback(oj)
				}
			}
			
		}

		//URLエンコード
		if(method == 'GET') {
		
			var encdata = ''
			var datas = data.split('&')
			for(i=0;i<datas.length;i++)
			{
				var dataq = datas[i].split('=')
				encdata += '&'+encodeURI(dataq[0])+'='+encodeURI(dataq[1])
			}
			url=url + encodeURI(data)
		}		

		//open メソッド
		oj.open( method , url , async )

		//ヘッダセット
		if(method == 'POST') {
			//このメソッドがWin Opera8でエラーになったので分岐
			if(!window.opera)
				oj.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
		} 

		//send メソッド
		oj.send(data)

	}
