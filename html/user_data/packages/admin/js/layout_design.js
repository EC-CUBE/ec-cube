/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
(function($){
    var updateUpDown = function(sortable){
        $('div:not(.ui-sortable-helper)', sortable)
            .removeClass('first')
            .filter(':first').addClass('first').end()
            .children('input.target-id').val(sortable.id).end()
            .each(function(){
                var top = $(this).prevAll().length + 1;
                $(this).children('input.top').val(top);
            });
    };

    var sortableUpdate = function(e, ui){
        updateUpDown(this);
        if(ui.sender)
            updateUpDown(ui.sender[0]);
    };

    $(document).ready(function(){
        var els = ['#MainHead', '#MainFoot', '#LeftNavi', '#RightNavi', '#TopNavi', '#BottomNavi', '#HeadNavi', '#HeaderTopNavi', '#FooterBottomNavi', '#HeaderInternalNavi', '#Unused'];
        var $els = $(els.toString());

        $els.each(function(){
            updateUpDown(this);
        });

        $els.sortable({
            items: '> div',
            //handle: 'dt',
            cursor: 'move',
            //cursorAt: { top: 2, left: 2 },
            //opacity: 0.8,
            //helper: 'clone',
            appendTo: 'body',
            placeholder: 'clone',
            placeholder: 'placeholder',
            connectWith: els,
            start: function(e,ui) {
                ui.helper.css("width", ui.item.width());
            },
            //change: sortableChange,
            update: sortableUpdate
        });
    });

    $(window).bind('load',function(){
        setTimeout(function(){
            $('#overlay').fadeOut(function(){
                $('body').css('overflow', 'auto');
            });
        }, 750);
    });
})(jQuery);
