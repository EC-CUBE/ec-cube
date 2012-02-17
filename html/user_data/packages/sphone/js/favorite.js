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
                  alert("お気に入りに登録しました");
                  $(".btn_favorite").html("<p>お気に入り登録済み</p>");
              } else {
                  alert("お気に入りの登録に失敗しました");
              }
              $.mobile.hidePageLoadingMsg();
           }
    });
}
