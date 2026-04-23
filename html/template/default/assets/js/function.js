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

    var pagetop = document.querySelector('.pagetop');
    if (pagetop) pagetop.style.display = 'none';

    window.addEventListener('scroll', function() {
        if (!pagetop) return;

        // ページトップフェードイン
        if (window.scrollY > 300) {
            pagetop.style.display = 'block';
        } else {
            pagetop.style.display = 'none';
        }

        // PC表示の時のみに適用
        if (window.innerWidth > 767) {
            var orderRole = document.querySelector('.ec-orderRole');
            if (orderRole) {
                var side = document.querySelector('.ec-orderRole__summary');
                var rect = orderRole.getBoundingClientRect();
                var min_move = rect.top + window.scrollY;
                var max_move = orderRole.offsetHeight;
                var margin_bottom = max_move - min_move;
                var scrollTop = window.scrollY;

                if (scrollTop > min_move && scrollTop < max_move) {
                    side.style.marginTop = (scrollTop - min_move) + 'px';
                } else if (scrollTop < min_move) {
                    side.style.marginTop = '0';
                } else if (scrollTop > max_move) {
                    side.style.marginTop = margin_bottom + 'px';
                }
            }
        }
    });

    // SP ハンバーガーメニュー
    var headerNavSP = document.querySelector('.ec-headerNavSP');
    if (headerNavSP) {
        headerNavSP.addEventListener('click', function() {
            var layoutRole = document.querySelector('.ec-layoutRole');
            var drawerRole = document.querySelector('.ec-drawerRole');
            var drawerRoleClose = document.querySelector('.ec-drawerRoleClose');
            if (layoutRole) layoutRole.classList.toggle('is_active');
            if (drawerRole) drawerRole.classList.toggle('is_active');
            if (drawerRoleClose) drawerRoleClose.classList.toggle('is_active');
            document.body.classList.toggle('have_curtain');
        });
    }

    var overlayRole = document.querySelector('.ec-overlayRole');
    if (overlayRole) {
        overlayRole.addEventListener('click', function() {
            document.body.classList.remove('have_curtain');
            var layoutRole = document.querySelector('.ec-layoutRole');
            var drawerRole = document.querySelector('.ec-drawerRole');
            var drawerRoleClose = document.querySelector('.ec-drawerRoleClose');
            if (layoutRole) layoutRole.classList.remove('is_active');
            if (drawerRole) drawerRole.classList.remove('is_active');
            if (drawerRoleClose) drawerRoleClose.classList.remove('is_active');
        });
    }

    var drawerRoleClose = document.querySelector('.ec-drawerRoleClose');
    if (drawerRoleClose) {
        drawerRoleClose.addEventListener('click', function() {
            document.body.classList.remove('have_curtain');
            var layoutRole = document.querySelector('.ec-layoutRole');
            var drawerRole = document.querySelector('.ec-drawerRole');
            if (layoutRole) layoutRole.classList.remove('is_active');
            if (drawerRole) drawerRole.classList.remove('is_active');
            this.classList.remove('is_active');
        });
    }

    // TODO: カート展開時のアイコン変更処理
    var cartArea = document.querySelector('.ec-headerRole__cart');
    if (cartArea) {
        cartArea.addEventListener('click', function(e) {
            if (e.target.closest('.ec-cartNavi') || e.target.closest('.ec-cartNavi--cancel')) {
                document.querySelectorAll('.ec-cartNaviIsset').forEach(function(el) {
                    el.classList.toggle('is-active');
                });
                document.querySelectorAll('.ec-cartNaviNull').forEach(function(el) {
                    el.classList.toggle('is-active');
                });
            }
        });
    }

    document.querySelectorAll('.ec-orderMail__link').forEach(function(el) {
        el.addEventListener('click', function() {
            var body = this.parentElement && this.parentElement.querySelector('.ec-orderMail__body');
            if (body) {
                body.style.display = body.style.display === 'none' ? '' : 'none';
            }
        });
    });

    document.querySelectorAll('.ec-orderMail__close').forEach(function(el) {
        el.addEventListener('click', function() {
            if (this.parentElement) {
                this.parentElement.style.display = 'none';
            }
        });
    });

    // ドロワーメニューに要素をコピー
    var drawerRole = document.querySelector('.ec-drawerRole');
    if (drawerRole) {
        document.querySelectorAll('.is_inDrawer').forEach(function(el) {
            drawerRole.insertAdjacentHTML('beforeend', el.innerHTML);
        });
    }

    document.querySelectorAll('.ec-blockTopBtn').forEach(function(el) {
        el.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // スマホのドロワーメニュー内の下層カテゴリ表示
    // TODO FIXME スマホのカテゴリ表示方法
    document.querySelectorAll('.ec-itemNav ul a').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var siblings = Array.from(this.parentElement.children).filter(function(c) {
                return c !== anchor;
            });
            if (siblings.length > 0) {
                var visible = siblings.some(function(c) {
                    return c.offsetParent !== null;
                });
                if (visible) {
                    return;
                }
                siblings.forEach(function(c) {
                    c.style.display = c.style.display === 'none' ? '' : 'none';
                });
                e.preventDefault();
            }
        });
    });

    // イベント実行時のオーバーレイ処理
    // classに「load-overlay」が記述されていると画面がオーバーレイされる
    document.querySelectorAll('.load-overlay').forEach(function(el) {
        el.addEventListener('click', function() { loadingOverlay(); });
        el.addEventListener('change', function() { loadingOverlay(); });
    });

    // submit処理についてはオーバーレイ処理を行う
    // click ではなく submit イベントを使うことで、
    // event.preventDefault() を click 時に呼ぶ AJAX フォーム（カート追加など）では
    // submit イベントが発火しないためオーバーレイが表示されない
    document.addEventListener('submit', function() {
        loadingOverlay();
    });

    // anchorをクリックした時にformを裏で作って指定のメソッドでリクエストを飛ばす
    document.querySelectorAll('a[token-for-anchor]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var dataset = this.dataset;
            if (dataset.confirm !== 'false') {
                if (!confirm(dataset.message ? dataset.message : eccube_lang['common.delete_confirm'])) {
                    return;
                }
            }

            loadingOverlay();

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
});

window.addEventListener('pageshow', function() {
    loadingOverlay('hide');
});

/**
 * オーバーレイ処理を行う関数
 */
function loadingOverlay(action) {
    if (action === 'hide') {
        document.querySelectorAll('.bg-load-overlay').forEach(function(el) {
            el.remove();
        });
    } else {
        // 二重生成防止 (連続サブミット時に複数重なるのを避ける)
        if (document.querySelector('.bg-load-overlay')) {
            return;
        }
        var overlay = document.createElement('div');
        overlay.className = 'bg-load-overlay';
        document.body.appendChild(overlay);
    }
}

/**
 * 要素FORMチェック
 */
function getAncestorOfTagType(elem, type) {
    while (elem.parentNode && elem.tagName !== type) {
        elem = elem.parentNode;
    }
    return (type === elem.tagName) ? elem : undefined;
}
