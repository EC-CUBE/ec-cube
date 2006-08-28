	var preLoadFlag = "false";

	function preLoadImg(){
		arrImgList = new Array (
			"/img/header/basis_on.jpg","/img/header/product_on.jpg","/img/header/customer_on.jpg","/img/header/order_on.jpg",
			"/img/header/sales_on.jpg","/img/header/mail_on.jpg","/img/header/contents_on.jpg",
			"/img/header/mainpage_on.gif","/img/header/seitecheck_on.gif","/img/header/logout_on.gif",
			"/img/contents/btn_search_on.jpg","/img/contents/btn_regist_on.jpg",
			"/img/contents/btn_csv_on.jpg","/img/contents/arrow_left.jpg","/img/contents/arrow_right.jpg"
		);
		arrPreLoad = new Array();
		for (i in arrImgList) {
			arrPreLoad[i] = new Image();
			arrPreLoad[i].src = arrImgList[i];
		}
		preLoadFlag = "true";
	}

	function chgImg(fileName,imgName){
		if (preLoadFlag == "true") {
			document.images[imgName].src = fileName;
		}
	}
	
	function chgImgImageSubmit(fileName,imgObj){
	imgObj.src = fileName;
	}
	
	// サブナビの表示切替
	function naviStyleChange(ids, color){
		document.getElementById(ids).style.backgroundColor = color;
	}	
