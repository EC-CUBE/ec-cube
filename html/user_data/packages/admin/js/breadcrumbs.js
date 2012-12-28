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
(function($) {

     var o;

     $.fn.breadcrumbs = function(options) {
         var defaults = {
             bread_crumbs: '',
             start_node: fnT('j_breadcrumbs_001'),
             anchor_node: '<a onclick="fnModeSubmit(\'tree\', \'parent_category_id\', '
                 + '{category_id}); return false" href="javascript:;" />',
             delimiter_node: '<span>&nbsp;&gt;&nbsp;</span>'
         };

         return this.each(function() {
                              if (options) {
                                  o = $.fn.extend(defaults, options);
                              }
                              var $this = $(this);
                              var total = o.bread_crumbs.length;
                              var $node = $(o.start_node);

                              for (var i = total - 1; i >= 0; i--) {
                                  if (i == total -1) $node.append(o.delimiter_node);

                                  var anchor = o.anchor_node
                                      .replace(/{category_id}/ig,
                                               o.bread_crumbs[i].category_id);
                                  $(anchor)
                                      .text(o.bread_crumbs[i].category_name)
                                      .appendTo($node);

                                  if (i > 0) $node.append(o.delimiter_node);
                              }
                              $this.html($node);
                              return this;
                          });
     };
})(jQuery);
