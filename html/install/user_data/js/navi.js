	var preLoadFlag = "false";

	function preLoadImg(){
		arrImgList = new Array (
			"/img/header/entry_on.gif","/img/header/contact_on.gif","/img/header/cartin_on.gif",
			"/img/header/fashion_on.gif","/img/header/jewely_on.gif","/img/header/electronic_on.gif","/img/header/pc_on.gif","/img/header/beauty_on.gif","/img/header/tv_on.gif","/img/header/login_on.gif",
			"/img/left/shopping_on.gif","/img/left/flow_on.gif","/img/left/faq_on.gif","/img/left/mailmagazine_on.gif","/img/left/point_on.gif","/img/left/fax_on.gif","/img/left/order_on.gif",
			"/img/right_product/detail_on.gif","/img/right_product/review_on.gif","/img/right_product/cart_on.gif",
			"/img/top/backnumber_on.jpg","/img/top/diary01_on.gif","/img/top/diary02_on.gif","/img/top/more_on.gif",
			"/img/button/fortop_on.gif","/img/button/back_on.gif","/img/button/back02_on.gif","/img/button/back03_on.gif","/img/button/next_on.gif","/img/button/close_on.gif","/img/button/confirm_on.gif","/img/button/entry_on.gif","/img/button/next_on.gif","/img/button/reji_on.gif","/img/button/send_on.gif","/img/button/top_on.gif",
			"/img/right_mailmagazine/entry_on.gif","/img/right_mailmagazine/release_on.gif",
			"/img/shopping/complete_on.gif","/img/shopping/loan_on.gif","/img/login/log_on.gif"
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