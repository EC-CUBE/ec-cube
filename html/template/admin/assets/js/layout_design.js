/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
;(function($, window, document, undefined){
    var updateUpDown = function(sortable){
        if (sortable instanceof $) {
            sortable = sortable.get(0);
        }
        $('div:not(.ui-sortable-helper)', sortable)
            .removeClass('first')
            .filter(':first').addClass('first').end()
            .children('input.target-id').val(sortable.id.replace('position_', ''));
        $(sortable)
            .find('input.block-row').each(function(i){
                $(this).val(i);
            });
    };

    var sortableUpdate = function(e, ui){
        updateUpDown(this);
        if(ui.sender)
            updateUpDown(ui.sender[0]);
    };
    window.updateUpDown = updateUpDown;

    $(document).ready(function(){
        // `window.els` is defined in layout.twig
        var $els = $(window.els.toString());

        $els.each(function(){
            updateUpDown(this);
        });

        $els.sortable({
            items: '> div.block',
            cursor: 'move',
            appendTo: 'body',
            placeholder: 'placeholder',
            connectWith: window.els,
            start: function(e,ui) {
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
