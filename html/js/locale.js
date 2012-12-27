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

function fnT() {
    var msgid = '';

    if (arguments.length == 0) {
        throw new Error('invalid arguments.');
    }

    // message id only.
    if (arguments.length == 1) {
        msgid = arguments[0];
        if (cube_locale_messages[msgid]) {
            return cube_locale_messages[msgid];
        }
        return msgid;
    }

    // formated message
    if (arguments.length > 1) {
        msgid = arguments[0];
        if (cube_locale_messages[msgid]) {
            var message = cube_locale_messages[msgid];
            for (var i = 1; i < arguments.length; i++) {
                var reg = new RegExp("\\{" + (i - 1) + "\\}", "g");
                if (arguments[i] == null) {
                    message = message.replace(reg, "null");
                } else {
                    try {
                        message = message.replace(reg, arguments[i].toString());
                    } catch (e) {
                        message = message.replace(reg, "[object]");
                    }
                }
            }
            return message;
        }
        return msgid;
    }
    return msgid;
}

