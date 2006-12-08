RESULT_PAGE_PHP = 'result.php';			// 結果ページ表示PHP
CREATE_IMAGE_PHP = 'create_image.php';	// 画像生成PHP
RESULT_TEXT_ID = 'result';				// 結果文字表示場所ID(HTMLで定義)
CODE_IMG = 'code';						// コード表示IMGタグのID

// ブラウザによってXmlHttpRequestのObjectを振り分ける 
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
 		// Mozilla, Safariなど
		return new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		// IE
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		// 非対応
		alert("ブラウザがXmlHttpRequestに対応していません！！");
	}
}

// オブジェクト生成
var receiveReq = getXmlHttpRequestObject();

// リクエスト処理
function makeRequest(url, param) {
	// 受信完了かまだopenメソッドが呼び出されていない
	if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
		// サーバとの通信を開始
		receiveReq.open("POST", url, true);
		// サーバーからの応答時の処理を定義（結果のページへの反映）
		receiveReq.onreadystatechange = updatePage; 

		// ヘッダー定義
		receiveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		receiveReq.setRequestHeader("Content-length", param.length);
		receiveReq.setRequestHeader("Connection", "close");

		// 送信
		receiveReq.send(param);
	}   
}

// サーバーからの応答時の処理
function updatePage() {
	// 受信が完了していたら実行
	if (receiveReq.readyState == 4) {
		// 設定したIDへ生成した文字列をセット
		document.getElementById(RESULT_TEXT_ID).innerHTML = receiveReq.responseText;
		// コード画像を変化させる
		img = document.getElementById(CODE_IMG); 
		// キャッシュを回避するためにランダムな値をつける
		img.src = CREATE_IMAGE_PHP + '?' + Math.random();
	}
}

// 認証処理実行
function auth(forms) {
	var postData = forms.input_data.name + "=" + encodeURIComponent( forms.input_data.value );
	// リクエスト実行
	makeRequest(RESULT_PAGE_PHP, postData);
}