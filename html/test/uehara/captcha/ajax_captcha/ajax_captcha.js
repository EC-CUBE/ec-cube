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

//Initiate the AJAX request
function makeRequest(url, param) {
//If our readystate is either not started or finished, initiate a new request
 if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
   //Set up the connection to captcha_test.html. True sets the request to asyncronous(default) 
   receiveReq.open("POST", url, true);
   //Set the function that will be called when the XmlHttpRequest objects state changes
   receiveReq.onreadystatechange = updatePage; 

   receiveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   receiveReq.setRequestHeader("Content-length", param.length);
   receiveReq.setRequestHeader("Connection", "close");

   //Make the request
   receiveReq.send(param);
 }   
}

//Called every time our XmlHttpRequest objects state changes
function updatePage() {
 //Check if our response is ready
 if (receiveReq.readyState == 4) {
   //Set the content of the DIV element with the response text
   document.getElementById('result').innerHTML = receiveReq.responseText;
   //Get a reference to CAPTCHA image
   img = document.getElementById('imgCaptcha'); 
   //Change the image
   img.src = 'create_image.php?' + Math.random();
 }
}

//Called every time when form is perfomed
function getParam(theForm) {
 //Set the URL
 var url = 'captcha.php';
 //Set up the parameters of our AJAX call
 var postStr = theForm.txtCaptcha.name + "=" + encodeURIComponent( theForm.txtCaptcha.value );
 //Call the function that initiate the AJAX request
 makeRequest(url, postStr);
}