//==============================================================================
//  SYSTEM      :  �����ǥ����֥饦��Ajax�ѥ饤�֥��
//  PROGRAM     :  XMLHttpRequest�ˤ����������Ԥ��ޤ�
//  AUTHER      :  Toshirou Takahashi http://jsgt.org/mt/01/


	////
	// ư���ǽ�ʥ֥饦��Ƚ��
	//
	// @sample        if(chkAjaBrowser()){ location.href='nonajax.htm' }
	// @sample        oj = new chkAjaBrowser();if(oj.bw.safari){ /* Safari code */ }
	// @return        �饤�֥�꤬ư���ǽ�ʥ֥饦������true  true|false
	//
	//  Enable list (v038����)
	//   WinIE 5.5+ 
	//   Konqueror 3.3+
	//   AppleWebKit��(Safari,OmniWeb,Shiira) 124+ 
	//   Mozilla��(Firefox,Netscape,Galeon,Epiphany,K-Meleon,Sylera) 20011128+ 
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
	// XMLHttpRequest���֥�����������
	//
	// @sample        oj = createHttpRequest()
	// @return        XMLHttpRequest���֥�������(���󥹥���)
	//
	function createHttpRequest()
	{
		if(window.XMLHttpRequest){
			 //Win Mac Linux m1,f1,o8 Mac s1 Linux k3 & Win e7��
			return new XMLHttpRequest() ;
		} else if(window.ActiveXObject){
			 //Win e4,e5,e6��
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
	// �������ؿ�
	//
	// @sample         sendRequest(onloaded,'&prog=1','POST','./about2.php',true,true)
	// @sample         sendRequest(onloaded,{name:taro,id:123,sel:1},','POST','./about3.php',true,true)
	// @sample         sendRequest({onload:loaded,onbeforsetheader:sethead},'',','POST','./about3.php',true,true)
	// @param {string} callback �������˵�ư����ؿ�̾ 
	// @param {object} callback �������˵�ư����ؿ�̾�ȥإå�����ؿ�̾{onload:�ؿ�̾,onbeforsetheader:�ؿ�̾} 
	// @param {array}  callback �������˵�ư����ؿ�̾�ȥإå����� ary['onload']=�ؿ�̾;ary['onbeforsetheader']=�ؿ�̾ 
	// @see                    http://jsgt.org/ajax/ref/head_test/header/Range/004/sample.htm
	// @param {string} data	   ��������ǡ��� string�ξ��(&̾��1=��1&̾��2=��2...)
	// @param {object} data	   ��������ǡ��� object�ξ��{̾��1:��1,̾��2:��2,...}
	// @param {array}  data	   ��������ǡ��� array�ξ���Ϣ������ ary['̾��1']=��1;ary['̾��2']=��2
	// @param {string}method   'POST' or 'GET'
	// @param {string}url      �ꥯ�����Ȥ���ե������URL
	// @param {string}async	   ��Ʊ���ʤ�true Ʊ���ʤ�false
	// @param {string}sload	   �����ѡ����� true�Ƕ�������ά�ޤ���false�ǥǥե����
	// @param {string}user	   ǧ�ڥڡ����ѥ桼����̾
	// @param {string}password ǧ�ڥڡ����ѥѥ����
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
		//XMLHttpRequest���֥�����������
		var oj = createHttpRequest();
		if( oj == null ) return null;
		
		//�������ɤ�����
		var sload = (!!sendRequest.arguments[5])?sload:false;
		if(sload || method.toUpperCase() == 'GET')url += '?';
		if(sload)url=url+'t='+(new Date()).getTime();
		
		//�֥饦��Ƚ��
		var bwoj = new chkAjaBrowser();
		var opera	  = bwoj.bw.opera;
		var safari	  = bwoj.bw.safari;
		var konqueror = bwoj.bw.konqueror;
		var mozes	  = bwoj.bw.mozes ;
				
		//callback��ʬ��
		//{onload:xxxx,onbeforsetheader:xxx}
		if(typeof callback=='object'){
			var callback_onload = callback.onload;
			var callback_onbeforsetheader = callback.onbeforsetheader;
		} else {
			var callback_onload = callback;
			var callback_onbeforsetheader = null;
		}

		//��������
		//opera��onreadystatechange��¿�ť쥹�Х�������Τ�onload������
		//Moz,FireFox��oj.readyState==3�Ǥ��������Τ��̾��onload������
		//Win ie�Ǥ�onload��ư��ʤ�
		//Konqueror��onload���԰���
		//����http://jsgt.org/ajax/ref/test/response/responsetext/try1.php
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

		//URL���󥳡���
		data = uriEncode(data,url);
		if(method.toUpperCase() == 'GET') {
			url += data
		}
		
		//open �᥽�å�
		oj.open(method,url,async,user,password);

		
		//�ꥯ�����ȥإå��������ޥ����ѥ�����Хå�
		//�Ȥ����ϡ��ƤӽФ�HTML¦��windowľ���إ����Х�ʴؿ�setHeaders��
		//���Ҥ����������setRequestHeader()�򥻥åȤ��Ƥ�������
		//@sample function setHeaders(oj){oj.setRequestHeader('Content-Type',contentTypeUrlenc)}
		//
		if(!!callback_onbeforsetheader)callback_onbeforsetheader(oj);

		//�ǥե���ȥإå�application/x-www-form-urlencoded���å�
		setEncHeader(oj);
		
		
		//�ǥХå�
		//alert('////jslb_ajaxxx.js//// \n data:'+data+' \n method:'+method+' \n url:'+url+' \n async:'+async);
		
		//send �᥽�å�
		oj.send(data);

		//URI���󥳡��ɥإå����å�
		function setEncHeader(oj){
	
			//�إå�application/x-www-form-urlencoded���å�
			// @see  http://www.asahi-net.or.jp/~sd5a-ucd/rec-html401j/interact/forms.html#h-17.13.3
			// @see  #h-17.3
			//   ( enctype �Υǥե�����ͤ� 'application/x-www-form-urlencoded')
			//   h-17.3�ˤ�ꡢPOST/GET��鷺����
			//   POST��'multipart/form-data'����ꤹ��ɬ�פ�������ϥ������ޥ������Ƥ���������
			//
			//  ���Υ᥽�åɤ�Win Opera8.0�ǥ��顼�ˤʤä��Τ�ʬ��(8.01��OK)
			var contentTypeUrlenc = 'application/x-www-form-urlencoded; charset=UTF-8';
			if(!window.opera){
				oj.setRequestHeader('Content-Type',contentTypeUrlenc);
			} else {
				if((typeof oj.setRequestHeader) == 'function')
					oj.setRequestHeader('Content-Type',contentTypeUrlenc);
			}	
			return oj
		}

		//URL���󥳡���
		//����data�ϡ�string��object���Ϥ��ޤ�
		function uriEncode(data,url){
			var encdata =(url.indexOf('?')==-1)?'?dmy':'';
			if(typeof data=='object'){
				for(var i in data)
					encdata+='&'+encodeURIComponent(i)+'='+encodeURIComponent(data[i]);
			} else if(typeof data=='string'){
				if(data=='')return '';
				//&��=�ǰ�öʬ��encode
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