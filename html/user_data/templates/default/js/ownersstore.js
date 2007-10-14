(function() {
var OwnersStore = function() {}
OwnersStore.prototype = {
    // detect MacX and Firefox use.
    detectMacFF: function() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('mac') != -1 && ua.indexOf('firefox') != -1) {
            return true;
        }
    },
    // remove ajax window
    remove: function() {
        $("#TB_window").fadeOut(
            "fast",
            function(){
                $('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();
            }
        );
        $("#TB_load").remove();
        //if IE 6
        if (typeof document.body.style.maxHeight == "undefined") {
            $("body", "html").css({height: "auto", width: "auto"});
            $("html").css("overflow", "");
        }
        return false;
    },
    // show loading page
    show_loading: function() {
        //if IE 6
        if (typeof document.body.style.maxHeight === "undefined") {
            $("body","html").css({height: "100%", width: "100%"});
            $("html").css("overflow","hidden");
            //iframe to hide select elements in ie6
            if (document.getElementById("TB_HideSelect") === null) {
                $("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(this.remove);
            }
        //all others
        } else {
            if(document.getElementById("TB_overlay") === null){
                $("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(this.remove);
            }
        }

        if(this.detectMacFF()){
            //use png overlay so hide flash
            $("#TB_overlay").addClass("TB_overlayMacFFBGHack");
        } else {
            //use background and opacity
            $("#TB_overlay").addClass("TB_overlayBG");
        }

        //add and show loader to the page
        $("body").append(
              "<div id='TB_load'>"
            + "  <p style='color:#ffffff'>Connecting Server . . .</p>"
            + "  <img src='/user_data/templates/default/img/ajax/loading.gif' />"
            + "</div>"
        );
        $('#TB_load').show();
    },
    // show results
    show_result: function(resp, status) {
        var title = status;
        var contents = resp.body;

        var TB_WIDTH = 700;
        var TB_HEIGHT = 400;
        var ajaxContentW = TB_WIDTH - 20;
        var ajaxContentH = TB_HEIGHT - 45;

        if ($("#TB_window").css("display") != "block"){
            $("#TB_window").append(
                "<div id='TB_title'>"
              + "  <div id='TB_ajaxWindowTitle'>" + title + "</div>"
              + "  <div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' onclick='OwnersStore.remove();'>close</a></div>"
              + "</div>"
              + "<div id='TB_ajaxContent' style='width:" + ajaxContentW + "px;height:" + ajaxContentH + "px'>"
              + "</div>"
            );
         //this means the window is already up, we are just loading new content via ajax
        } else {
            $("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
            $("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
            $("#TB_ajaxContent")[0].scrollTop = 0;
            $("#TB_ajaxWindowTitle").html(contents);
		}


        $("#TB_load").remove();
        $("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});

        // take away IE6
        if (!(jQuery.browser.msie && jQuery.browser.version < 7)) {
            $("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
        }
        $("#TB_ajaxContent").html(contents);
		$("#TB_window").css({display:"block"});
    },

    // exexute install or update
    download: function(product_id) {
        this.show_loading();
        $.post(
            '/upgrade/index.php',
            {mode: 'download', product_id: product_id},
            this.show_result,
            'json'
        )
    },
    // get products list
    products_list: function() {
        this.show_loading();
        var remove = this.remove;
        $.post(
            '/upgrade/index.php',
            {mode: 'products_list'},
            function(resp, status) {
                remove();
                $('#ownersstore_products_list').html(resp.body);
            },
            'json'
        )
    }
}
window.OwnersStore = new OwnersStore();
})();

