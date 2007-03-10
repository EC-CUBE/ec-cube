/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
	var preLoadFlag = "false";

	function preLoadImg(URL){

		arrImgList = new Array (
			URL+"img/header/basis_on.jpg",URL+"img/header/product_on.jpg",URL+"img/header/customer_on.jpg",URL+"img/header/order_on.jpg",
			URL+"img/header/sales_on.jpg",URL+"img/header/mail_on.jpg",URL+"img/header/contents_on.jpg",
			URL+"img/header/mainpage_on.gif",URL+"img/header/sitecheck_on.gif",URL+"img/header/logout.gif",
			URL+"img/contents/btn_search_on.jpg",URL+"img/contents/btn_regist_on.jpg",
			URL+"img/contents/btn_csv_on.jpg",URL+"img/contents/arrow_left.jpg",URL+"img/contents/arrow_right.jpg"
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
	function naviStyleChange(ids, bcColor, color){
		document.getElementById(ids).style.backgroundColor = bcColor;
	}	


	