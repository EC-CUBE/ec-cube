////
// ������Ajax�ѥ饤�֥�� jslb_ajax04.js
// �ǿ����� http://jsgt.org/mt/archives/01/000409.html 
// �嵭�����Ⱥ���Բġ��������ѡ���¤����ͳ��Ϣ�����פǤ���
// 

	////
	// XMLHttpRequest���֥�����������
	//
	// @sample oj=createHttpRequest()
	// @return XMLHttpRequest���֥�������
	//
	function createHttpRequest()
	{
		if(window.ActiveXObject){
			 //Win e4,e5,e6��
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
			 //Win Mac Linux m1,f1,o8 Mac s1 Linux k3��
			return new XMLHttpRequest() ;
		} else {
			return null ;
		}
	}
	
	////
	// �����ؿ�
	//
	// @sample sendRequest(onloaded,'&prog=1','POST','./about2.php',true,true)
	// @param callback �������˵�ư����ؿ�̾
	// @param data	 ��������ǡ���
	// @param method "POST" or "GET"
	// @param url�ꥯ�����Ȥ���ե������URL
	// @param async	��Ʊ���ʤ�true Ʊ���ʤ�false
	// @param sload	�����ѡ����� true�Ƕ�������ά�ޤ���false�ǥǥե����
	//
	function sendRequest(callback,data,method,url,async,sload)
	{
	
		//XMLHttpRequest���֥�����������
		var oj = createHttpRequest()
		if( oj == null ) return null
		
		//�������ɤ�����
		var sload = (!!sendRequest.arguments[5])?sload:false;
		if(sload)url=url+"?t="+(new Date()).getTime()

		//�֥饦��Ƚ��
		var ua = navigator.userAgent
		var safari	= ua.indexOf("Safari")!=-1
		var konqueror = ua.indexOf("Konqueror")!=-1
		var mozes	 = ((a=navigator.userAgent.split("Gecko/")[1] )
				?a.split(" ")[0]:0) >= 20011128 
		
		//��������
		//opera��onreadystatechange��¿�ť쥹�Х�������Τ�onload������
		//Moz,FireFox��oj.readyState==3�Ǥ��������Τ��̾��onload������
		//Win ie�Ǥ�onload��ư��ʤ�
		//Konqueror��onload���԰���
		//����http://jsgt.org/ajax/ref/test/response/responsetext/try1.php
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

		//URL���󥳡���
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

		//open �᥽�å�
		oj.open( method , url , async )

		//�إå����å�
		if(method == 'POST') {
			//���Υ᥽�åɤ�Win Opera8�ǥ��顼�ˤʤä��Τ�ʬ��
			if(!window.opera)
				oj.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
		} 

		//send �᥽�å�
		oj.send(data)

	}
