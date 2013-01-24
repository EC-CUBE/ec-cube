/*------------------------------------------
お気に入りを登録する
------------------------------------------*/
function fnAddFavoriteSphone(favoriteProductId) {
    $.mobile.showPageLoadingMsg();
    //送信データを準備
    var postData = {};
    $("#form1").find(':input').each(function(){  
        postData[$(this).attr('name')] = $(this).val();  
    });
    postData["mode"] = "add_favorite_sphone";
    postData["favorite_product_id"] = favoriteProductId;

    $.ajax({
           type: "POST",
           url: $("#form1").attr('action'),
           data: postData,
           cache: false,
           dataType: "text",
           error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(textStatus);
            $.mobile.hidePageLoadingMsg();
           },
           success: function(result){
              if (result == "true") {
                  alert(fnT("j_favorite_001"));
                  $(".btn_favorite").html(fnT("j_favorite_002"));
              } else {
                  alert(fnT("j_favorite_003"));
              }
              $.mobile.hidePageLoadingMsg();
           }
    });
}
