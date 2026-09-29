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
;(function($, window, document, undefined) {
    /**
     * セクション (#position_*) 内のブロックの hidden input (配置先・並び順) を DOM の順序に合わせる.
     * layout.twig のコンテキストメニューからも window.updateUpDown として呼ばれる.
     */
    var updateUpDown = function(sortable) {
        if (sortable instanceof $) {
            sortable = sortable.get(0);
        }
        $('div', sortable)
            .removeClass('first')
            .filter(':first').addClass('first').end()
            .children('input.target-id').val(sortable.id.replace('position_', ''));
        $(sortable)
            .find('input.block-row').each(function(i) {
            $(this).val(i);
        });
    };
    window.updateUpDown = updateUpDown;

    // ブロックが無いセクションには「ドラッグ&ドロップしてください」の案内を表示する
    var togglePlaceholder = function(sortable) {
        var $sortable = $(sortable);
        if ($sortable.children('.block').length > 0) {
            $sortable.children('.target-placeholder').remove();
        } else if ($sortable.children('.target-placeholder').length === 0) {
            $sortable.append($('#target-placeholder').html());
        }
    };

    $(document).ready(function() {
        // `window.els` is defined in layout.twig
        var $els = $(window.els.toString());

        $els.each(function() {
            updateUpDown(this);
        });

        $els.each(function() {
            Sortable.create(this, {
                // 同じ group のセクション間でブロックを移動できる
                group: 'layout-blocks',
                draggable: '.block',
                animation: 150,
                onEnd: function(evt) {
                    togglePlaceholder(evt.from);
                    togglePlaceholder(evt.to);
                    updateUpDown(evt.to);
                    if (evt.from !== evt.to) {
                        updateUpDown(evt.from);
                    }
                }
            });
        });
    });
})(jQuery, window, document);
