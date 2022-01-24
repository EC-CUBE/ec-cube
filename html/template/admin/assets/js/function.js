/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
//mainNavArea　toggle
var mainNavArea = function() {
    $(function() {
        $('.c-headerBar__toggleBtn').on('click', function() {
            $('.c-mainNavArea').toggleClass('is-active');
            $('.c-curtain').toggleClass('is-active');
        });

        $('.c-curtain').on('click', function() {
            $('.c-mainNavArea').toggleClass('is-active');
            $('.c-curtain').toggleClass('is-active');
        });
    })
};
mainNavArea();

//Bootstrap ツールチップ
var toolTip = function() {
    $(function() {
        $('[data-tooltip="true"]').tooltip();
    })
};

toolTip();

//popover ポップオーバー
// header
var popoverHeader = function() {
    $(function() {
        $('.c-headerBar__userMenu').popover({
            container: 'body'
        })
    })
};
popoverHeader();
// all page
var popoverAll = function() {
    $(function() {
        $('[data-toggle="popover"]').popover();
    })
};
popoverAll();

//collapseIconChange　collapseと連動するアイコン変化
var collapseIconMinus = function() {
    $(function() {
        $('.ec-collapse').on('shown.bs.collapse', function() {
            var id = $(this).attr('id');
            var icon = $('[href="#' + id + '"]').find('i');
            icon.removeClass('fa-plus-square-o');
            icon.addClass('fa-minus-square-o');
        })
    })
};
collapseIconMinus();

var collapseIconPlus = function() {
    $(function() {
        $('.ec-collapse').on('hidden.bs.collapse', function() {
            var id = $(this).attr('id');
            var icon = $('[href="#' + id + '"]').find('i');
            icon.removeClass('fa-minus-square-o');
            icon.addClass('fa-plus-square-o');
        })
    })
};
collapseIconPlus();


//cardCollapseIconChange　カードコンポーネントのcollapseと連動するアイコン変化
var cardCollapseIconDown = function() {
    $(function() {
        $('.ec-cardCollapse').on('hidden.bs.collapse', function() {
            var id = $(this).attr('id');
            var icon = $('[href="#' + id + '"]').find('i');
            icon.removeClass('fa-angle-up');
            icon.addClass('fa-angle-down');
        })
    })
};
cardCollapseIconDown();

var cardCollapseIconUp = function() {
    $(function() {
        $('.ec-cardCollapse').on('shown.bs.collapse', function() {
            var id = $(this).attr('id');
            var icon = $('[href="#' + id + '"]').find('i');
            icon.addClass('fa-angle-up');
        })
    })
};
cardCollapseIconUp();

// toggle bulk button
var toggleBtnBulk = function(checkboxSelector, btnSelector) {
    $(function() {
        if ($(checkboxSelector + ':checked').length) {
            $(btnSelector).fadeIn('fast').addClass('d-block').removeClass('d-none');
        } else {
            $(btnSelector).fadeOut('fast', function() {
                $(this).addClass('d-none').removeClass('d-block');
            })
        }
    });
};

/////////// 2重submit制御.

if (typeof Ladda !== 'undefined') {
    Ladda.bind('button[type=submit]', {timeout: 2000});
}

// anchorをクリックした時にformを裏で作って指定のメソッドでリクエストを飛ばす
// Twigには以下のように埋め込む
// <a href="PATH" {{ csrf_token_for_anchor() }} data-method="(put/delete/postのうちいずれか)" data-confirm="xxxx" data-message="xxxx">
//
// オプション要素
// data-confirm : falseを定義すると確認ダイアログを出さない。デフォルトはダイアログを出す
// data-message : 確認ダイアログを出す際のメッセージをデフォルトから変更する
//
$(function() {
    var createForm = function(action, data) {
        var $form = $('<form action="' + action + '" method="post"></form>');
        for (input in data) {
            if (data.hasOwnProperty(input)) {
                $form.append('<input name="' + input + '" value="' + data[input] + '">');
            }
        }
        return $form;
    };

    $('a[token-for-anchor]').click(function(e) {
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
$(window).on('load', function() {
    var el = $('.form-error-message');
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
        var $collapse = el.parents('.ec-cardCollapse');
        $collapse.addClass('show');
        var id = $collapse.attr('id');
        var icon = $('[href="#' + id + '"]').find('i');
        icon.removeClass('fa-angle-down');
        icon.addClass('fa-angle-up');
    }
});

var searchWord = function (searchText, el) {
    var targetText;

    // 検索ボックスに値が入っていない場合
    if (searchText == '') {
        // 全て表示する
        el.show();
        return;
    }

    // 検索ボックスに値が入ってる場合
    // 表示を全て空にする
    el.hide();

    // 検索ワードが（子を含めて）含まれる要素のみ表示
    el.each(function () {
        targetText = $(this).text();
        // 検索対象となるリストに入力された文字列が存在するかどうかを判断
        if (targetText.toLowerCase().indexOf(searchText.toLowerCase()) != -1) {
            // 存在する場合はそのリストのテキストを用意した配列に格納
            $(this).show();
        }
    });
};

// 一覧ページのソート機能
$(function() {
    if ($('.js-listSort').length < 1) {
        return;
    }

    // 現在のソート状況をボタン表示に反映
    const sortkey = $('.js-listSort-key').val();
    const target = $('.js-listSort').filter('[data-sortkey="' + sortkey + '"]');
    if (target.length === 1) {
        target.addClass('listSort-current');
        if ($('.js-listSort-type').val() === 'd') {
            target.find('.fa').addClass('fa-arrow-down').removeClass('fa-arrow-up');
        }
    }

    // ソート実施
    $('.js-listSort').on({
        click: function (e) {
            const sortkey = $(e.currentTarget).data('sortkey');
            const sorttype = ($('.js-listSort-key').val() === sortkey && $('.js-listSort-type').val() !== 'd') ? 'd' : 'a';
            $('.js-listSort-key').val(sortkey);
            $('.js-listSort-type').val(sorttype);
            $('#search_form').submit();
            e.preventDefault();
        }
    });
});
