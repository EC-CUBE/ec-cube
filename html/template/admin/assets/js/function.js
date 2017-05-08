/*!
 * function.js for EC-CUBE admin
 */

jQuery(document).ready(function ($) {
    /*
     * Brake point Check
     */
    $(window).on('load , resize', function () {
        $('body').removeClass('pc_view md_view sp_view');
        if (window.innerWidth < 768) {
            $('body').addClass('sp_view');
            $('#wrapper').removeClass('sidebar-open'); // for Drawer menu
        } else if (window.innerWidth < 992) {
            $('body').addClass('md_view');
            $('#wrapper').addClass('sidebar-open'); // for Drawer menu
        } else {
            $('body').addClass('pc_view');
            $('#wrapper').addClass('sidebar-open'); // for Drawer menu
        }
        return false;
    });


    /*
     * Drawer menu
     */

    $('.bt_drawermenu').on('click', function () {
        if ($('.sidebar-open #side').size() == 0) {
            $('#wrapper').addClass('sidebar-open');
        } else {
            $('#wrapper').removeClass('sidebar-open');
        }
        return false;
    });


/////////// SideBar accordion

    $("#side li .toggle").click(function () {
        if ($("+ul", this).css("display") == "none") {
            $(this).parent('li').addClass("active");
            $("+ul", this).slideDown(300);
        } else {
            $(this).parent('li').removeClass("active");
            $("+ul", this).slideUp(300);
        }
        return false;
    });

/////////// accordion

    $(".accordion .toggle").click(function () {
        if ($("+.accpanel", this).css("display") == "none") {
            $(this).addClass("active");
            $("+.accpanel", this).slideDown(300);
        } else {
            $(this).removeClass("active");
            $("+.accpanel", this).slideUp(300);
        }
        return false;
    });


/////////// dropdownの中をクリックしても閉じないようにする

    $(".dropdown-menu").click(function (e) {
        e.stopPropagation();
    });


/////////// 追従サイドバー

    // スクロールした時に以下の処理
    $(window).on("scroll", function () {
        //PC表示の時のみに適用
        if (window.innerWidth > 993) {

            if ($('#aside_wrap').length) {

                var side = $("#aside_column"),
                    wrap = $("#aside_wrap"),
                    heightH = $("#header").outerHeight(),
                    min_move = wrap.offset().top,
                    max_move = wrap.offset().top + wrap.height() - side.height() - 2 * parseInt(side.css("top")),
                    margin_bottom = max_move - min_move;

                var scrollTop = $(window).scrollTop();
                if (scrollTop > min_move && scrollTop < max_move) {
                    var margin_top = scrollTop - min_move;
                    side.css({"margin-top": margin_top + heightH + 10});
                } else if (scrollTop < min_move) {
                    side.css({"margin-top": 0});
                } else if (scrollTop > max_move) {
                    side.css({"margin-top": margin_bottom});
                }
            }

        }

        return false;
    });


//	var fixedcolumn = $('#aside_column'),
//	offset = fixedcolumn.offset();
//
//	$(window).scroll(function () {
//	  if($(window).scrollTop() > offset.top - 80) {
//		fixedcolumn.addClass('fixed');
//	  } else {
//		fixedcolumn.removeClass('fixed');
//	  }
//	});

    // マスク処理
    $('.prevention-mask').on('click', function() {
        $overlay = $('<div class="prevention-masked">');
        $('body').append($overlay);
    });

    // ダブルクリック禁止
    $('.prevention-btn').on('click', function() {
        $(this).attr('disabled', 'disabled');
        var $form = $(this).parents('form');
        // マスク表示させるためsetTimeoutを使って処理を遅らせる
        setTimeout(function(){
            $form.submit();
        }, 0);
        return false;
    });

/////////// 検索条件をクリア
    $('.search-clear').click(function (event) {
        event.preventDefault(event);
        $('#search_form .input_search, .search-box-inner input, .search-box-inner select').each(function () {
            if (this.type == "checkbox" || this.type == "radio") {
                this.checked = false;
            } else {
                if (this.type == "hidden") {
                    if (!this.name.match(/_token/i)) {
                        $(this).val("");
                    }
                } else {
                    $(this).val("");
                }
            }
        });
    });

/////////// アコーディオントグル制御( フォームに値があれば、アコーディオン中止 )
    //フォーム値確認用関数
    formPropStateSubscriber = function() {
        ad_flg = false;     //アコーディオン初期値
        return {
            formState : function() {
                this.chcekForm();
                return this.getFormState();
            },
            chcekForm : function(){
                 $('.search-box-inner input, .search-box-inner select').each(function () {
                    if (this.type == "checkbox" || this.type == "radio") {
                        if (this.checked) {
                            ad_flg = true;
                            return true;
                        }
                    } else {
                        if (this.type != "hidden") {
                            if ($(this).val()) {
                                ad_flg = true;
                                return true;
                            }
                        }
                    }
                });
            },
            getFormState : function() {
                return ad_flg;
            }
        };
    };
});

// anchorをクリックした時にformを裏で作って指定のメソッドでリクエストを飛ばす
// Twigには以下のように埋め込む
// <a href="PATH" {{ csrf_token_for_anchor() }} data-method="(put/delete/postのうちいずれか)" data-confirm="xxxx" data-message="xxxx">
//
// オプション要素
// data-confirm : falseを定義すると確認ダイアログを出さない。デフォルトはダイアログを出す
// data-message : 確認ダイアログを出す際のメッセージをデフォルトから変更する
//
$(function () {
    var createForm = function (action, data) {
        var $form = $('<form action="' + action + '" method="post"></form>');
        for (input in data) {
            if (data.hasOwnProperty(input)) {
                $form.append('<input name="' + input + '" value="' + data[input] + '">');
            }
        }
        return $form;
    };

    $('a[token-for-anchor]').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var data = $this.data();
        if (data.confirm != false) {
            if (!confirm(data.message ? data.message : '削除してもよろしいですか?')) {
                return false;
            }
        }

        var $form = createForm($this.attr('href'), {
            _token: $this.attr('token-for-anchor'),
            _method: data.method
        }).hide();

        $('body').append($form); // Firefox requires form to be on the page to allow submission
        $form.submit();
    });
});

// 入力チェックエラー発生時にエラー発生箇所までスクロールさせる
$(window).load(function() {
    var el = $('.errormsg');
    if (el.length) {
        // Open panel when has error
        openPanel(el);
        var errorOffset = el.first().offset().top;
        var screenHeight = $(window).height();
        var errorMargin = parseInt(screenHeight / 10) + $('header').outerHeight();

        $('html, body').animate({
            scrollTop: (errorOffset - errorMargin)
        }, 500);
    }

    function openPanel(el) {
        var accordion = el.parents('div.accordion');
        var $ac = $('.accpanel', accordion);
        if (!$ac) {
            return false;
        }

        if ($ac.css('display') == 'none') {
            $ac.siblings('.toggle').addClass('active');
            $ac.slideDown(0);
        }
    }
});
