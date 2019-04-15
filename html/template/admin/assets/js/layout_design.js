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
    var updateUpDown = function(sortable) {
        if (sortable instanceof $) {
            sortable = sortable.get(0);
        }
        $('div:not(.ui-sortable-helper)', sortable)
            .removeClass('first')
            .filter(':first').addClass('first').end()
            .children('input.target-id').val(sortable.id.replace('position_', ''));
        $(sortable)
            .find('input.block-row').each(function(i) {
            $(this).val(i);
        });
    };

    var sortableUpdate = function(e, ui) {
        updateUpDown(this);
        if (ui.sender)
            updateUpDown(ui.sender[0]);
    };
    window.updateUpDown = updateUpDown;

    $(document).ready(function() {
        // `window.els` is defined in layout.twig
        var $els = $(window.els.toString());

        $els.each(function() {
            updateUpDown(this);
        });

        $els.sortable({
            items: '> div.block',
            cursor: 'move',
            appendTo: 'body',
            placeholder: 'placeholder',
            connectWith: window.els,
            start: function(e, ui) {
                ui.helper.css("width", ui.item.width());
            },
            stop: function(e, ui) {
                // sortable が子要素を強制的に表示するため show(), hide() が使えない
                if ($(this).children('.block').length <= 0) {
                    // show placeholder
                    $(this).append($('#target-placeholder').html());
                }
                if (ui.item.parent().children('.block').length > 0) {
                    // hide placeholder
                    ui.item.parent().children('.target-placeholder').remove();
                }
            },
            update: sortableUpdate
        });
    });
})(jQuery, window, document);
