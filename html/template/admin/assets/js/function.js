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

document.addEventListener('DOMContentLoaded', function() {

    // mainNavArea toggle
    var toggleBtn = document.querySelector('.c-headerBar__toggleBtn');
    var mainNavArea = document.querySelector('.c-mainNavArea');
    var curtain = document.querySelector('.c-curtain');
    if (toggleBtn && mainNavArea && curtain) {
        toggleBtn.addEventListener('click', function() {
            mainNavArea.classList.toggle('is-active');
            curtain.classList.toggle('is-active');
        });
        curtain.addEventListener('click', function() {
            mainNavArea.classList.toggle('is-active');
            curtain.classList.toggle('is-active');
        });
    }

    // Bootstrap tooltip
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    /** @deprecated プラグイン等の後方互換用 */
    document.querySelectorAll('[data-tooltip="true"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    // Bootstrap popover
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function(el) {
        new bootstrap.Popover(el);
    });

    /** @deprecated プラグイン等の後方互換用 */
    document.querySelectorAll('[data-toggle="popover"]').forEach(function(el) {
        new bootstrap.Popover(el);
    });

    // collapseIconChange: collapseと連動するアイコン変化
    document.querySelectorAll('.ec-collapse').forEach(function(el) {
        el.addEventListener('shown.bs.collapse', function() {
            var icon = document.querySelector('[href="#' + this.id + '"] i');
            if (icon) {
                icon.classList.remove('fa-plus-square-o');
                icon.classList.add('fa-minus-square-o');
            }
        });
        el.addEventListener('hidden.bs.collapse', function() {
            var icon = document.querySelector('[href="#' + this.id + '"] i');
            if (icon) {
                icon.classList.remove('fa-minus-square-o');
                icon.classList.add('fa-plus-square-o');
            }
        });
    });

    // cardCollapseIconChange: カードコンポーネントのcollapseと連動するアイコン変化
    document.querySelectorAll('.ec-cardCollapse').forEach(function(el) {
        el.addEventListener('hidden.bs.collapse', function() {
            var icon = document.querySelector('[href="#' + this.id + '"] i');
            if (icon) {
                icon.classList.remove('fa-angle-up');
                icon.classList.add('fa-angle-down');
            }
        });
        el.addEventListener('shown.bs.collapse', function() {
            var icon = document.querySelector('[href="#' + this.id + '"] i');
            if (icon) {
                icon.classList.add('fa-angle-up');
            }
        });
    });

    /////////// 2重submit制御.

    // a[token-for-anchor] を押下されるとJavaScriptで formを作成してPOSTする仕様になっていて、
    // aタグにdisable属性を付与しても駄目（form生成&postしてしまう）だったので、cssでpointer-event:none;しています。
    // https://github.com/EC-CUBE/ec-cube/pull/5971
    if (typeof Ladda !== 'undefined') {
        Ladda.bind('button[type=submit],a[token-for-anchor]', {timeout: 2000});
        document.querySelectorAll('button[type=submit].btn-ec-regular').forEach(function(btn) {
            btn.setAttribute('data-spinner-color', '#595959');
        });
    }

    // Laddaのtimeout(2000ms)でボタンが再有効化された後の二重送信を防止する.
    // 一度submitされたフォームは以降のsubmitをキャンセルする. (Issue #6671)
    // ページ遷移(登録成功・バリデーションエラー)でJSが再初期化されるため, フラグは自動的にリセットされる.
    var submittedForms = new WeakSet();
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (submittedForms.has(this)) {
                e.preventDefault();
                return;
            }
            submittedForms.add(this);
        });
    });

    // anchorをクリックした時にformを裏で作って指定のメソッドでリクエストを飛ばす
    // Twigには以下のように埋め込む
    // <a href="PATH" {{ csrf_token_for_anchor() }} data-method="(put/delete/postのうちいずれか)" data-confirm="xxxx" data-message="xxxx">
    //
    // オプション要素
    // data-confirm : falseを定義すると確認ダイアログを出さない。デフォルトはダイアログを出す
    // data-message : 確認ダイアログを出す際のメッセージをデフォルトから変更する
    //
    document.querySelectorAll('a[token-for-anchor]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            this.style.pointerEvents = 'none';
            var dataset = this.dataset;
            if (dataset.confirm !== 'false') {
                if (!confirm(dataset.message || '削除してもよろしいですか?')) {
                    this.style.pointerEvents = '';
                    return;
                }
            }
            var form = document.createElement('form');
            form.action = this.getAttribute('href');
            form.method = 'post';
            form.style.display = 'none';
            var inputs = {
                _token: this.getAttribute('token-for-anchor'),
                _method: dataset.method
            };
            for (var name in inputs) {
                if (inputs.hasOwnProperty(name)) {
                    var input = document.createElement('input');
                    input.name = name;
                    input.value = inputs[name];
                    form.appendChild(input);
                }
            }
            document.body.appendChild(form);
            form.submit();
        });
    });

    // 一覧ページのソート機能
    if (document.querySelector('.js-listSort')) {
        var sortKeyInput = document.querySelector('.js-listSort-key');
        var sortTypeInput = document.querySelector('.js-listSort-type');
        var currentSortkey = sortKeyInput.value;
        var sortTarget = document.querySelector('.js-listSort[data-sortkey="' + currentSortkey + '"]');
        if (sortTarget) {
            sortTarget.classList.add('listSort-current');
            if (sortTypeInput.value === 'd') {
                var sortIcon = sortTarget.querySelector('.fa');
                if (sortIcon) {
                    sortIcon.classList.add('fa-arrow-down');
                    sortIcon.classList.remove('fa-arrow-up');
                }
            }
        }
        document.querySelectorAll('.js-listSort').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var sortkey = this.dataset.sortkey;
                var sorttype = (sortKeyInput.value === sortkey && sortTypeInput.value !== 'd') ? 'd' : 'a';
                sortKeyInput.value = sortkey;
                sortTypeInput.value = sorttype;
                document.getElementById('search_form').submit();
            });
        });
    }

    // input[type="datetime-local"]、初期クリック時に当日の0時0分を設定
    document.querySelectorAll('[type="datetime-local"]').forEach(function(input) {
        input.addEventListener('click', function() {
            if (this.value === '' && !this.classList.contains('is_adjusted')) {
                this.classList.add('is_adjusted');
                var date = new Date();
                var adjusted_date = [
                    date.getFullYear(),
                    String(date.getMonth() + 1).padStart(2, '0'),
                    String(date.getDate()).padStart(2, '0')
                ].join('-');
                this.value = adjusted_date + 'T00:00';
            }
        });
    });

});

// 入力チェックエラー発生時にエラー発生箇所までスクロールさせる
window.addEventListener('load', function() {
    var errors = document.querySelectorAll('.form-error-message');
    if (!errors.length) {
        return;
    }

    function openPanel(errors) {
        errors.forEach(function(el) {
            var collapse = el.closest('.ec-cardCollapse');
            if (collapse) {
                collapse.classList.add('show');
                var icon = document.querySelector('[href="#' + collapse.id + '"] i');
                if (icon) {
                    icon.classList.remove('fa-angle-down');
                    icon.classList.add('fa-angle-up');
                }
            }
        });
    }

    openPanel(errors);

    var errorOffset = 0;
    var found = false;
    errors.forEach(function(el) {
        if (!found && el.offsetParent !== null) {
            errorOffset = el.getBoundingClientRect().top + window.scrollY;
            found = true;
        }
    });

    var header = document.querySelector('header');
    var errorMargin = Math.floor(window.innerHeight / 10) + (header ? header.offsetHeight : 0);
    window.scrollTo({ top: errorOffset - errorMargin, behavior: 'smooth' });
});

// toggle bulk button
// checkboxSelector のチェック状態に応じて btnSelector の表示を切り替える
var toggleBtnBulk = function(checkboxSelector, btnSelector) {
    var run = function() {
        var btns = document.querySelectorAll(btnSelector);
        var hasChecked = document.querySelectorAll(checkboxSelector + ':checked').length > 0;
        btns.forEach(function(btn) {
            btn.classList.toggle('d-block', hasChecked);
            btn.classList.toggle('d-none', !hasChecked);
        });
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
};

// 検索ワードによるリストフィルタリング
// el には jQuery オブジェクトまたは NodeList/配列 を渡す
var searchWord = function(searchText, el) {
    var elements = Array.from(el);
    if (searchText === '') {
        elements.forEach(function(item) { item.style.display = ''; });
        return;
    }
    elements.forEach(function(item) { item.style.display = 'none'; });
    elements.forEach(function(item) {
        if (item.textContent.toLowerCase().indexOf(searchText.toLowerCase()) !== -1) {
            item.style.display = '';
        }
    });
};
