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

/**
 * position_* コンテナ内のブロック並び順メタデータを更新する.
 * layout.twig から window.updateUpDown として参照される.
 */
var updateUpDown = function(sortable) {
    var blocks = Array.from(sortable.children).filter(function(el) {
        return el.tagName === 'DIV' && !el.classList.contains('sortable-chosen');
    });

    blocks.forEach(function(block) {
        block.classList.remove('first');
        var targetId = block.querySelector('input.target-id');
        if (targetId) targetId.value = sortable.id.replace('position_', '');
    });

    if (blocks.length > 0) {
        blocks[0].classList.add('first');
    }

    sortable.querySelectorAll('input.block-row').forEach(function(input, i) {
        input.value = i;
    });
};
window.updateUpDown = updateUpDown;

document.addEventListener('DOMContentLoaded', function() {
    // `window.els` is defined in layout.twig (array of CSS selectors like ['#position_1', ...])
    if (!window.els || !window.els.length) return;

    var containers = Array.from(document.querySelectorAll(window.els.join(',')));

    containers.forEach(function(el) {
        updateUpDown(el);
    });

    containers.forEach(function(container) {
        Sortable.create(container, {
            group: 'layout-blocks',
            draggable: '.block',
            ghostClass: 'placeholder',
            animation: 150,
            onEnd: function(evt) {
                var to   = evt.to;
                var from = evt.from;

                // 移動先のプレースホルダーを管理
                if (to.querySelectorAll(':scope > .block').length > 0) {
                    to.querySelectorAll(':scope > .target-placeholder').forEach(function(ph) {
                        ph.remove();
                    });
                } else {
                    var tpl = document.getElementById('target-placeholder');
                    if (tpl) to.insertAdjacentHTML('beforeend', tpl.innerHTML);
                }

                // 移動元のプレースホルダーを管理 (別コンテナへ移動した場合)
                if (from !== to) {
                    if (from.querySelectorAll(':scope > .block').length > 0) {
                        from.querySelectorAll(':scope > .target-placeholder').forEach(function(ph) {
                            ph.remove();
                        });
                    } else {
                        var tpl = document.getElementById('target-placeholder');
                        if (tpl) from.insertAdjacentHTML('beforeend', tpl.innerHTML);
                    }
                    updateUpDown(from);
                }

                updateUpDown(to);
            }
        });
    });
});
