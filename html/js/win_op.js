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

function win01(URL,Winname,Wwidth,Wheight){
    var WIN;
    WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=no,resizable=no,toolbar=no,location=no,directories=no,status=no");
    WIN.focus();
}

function win02(URL,Winname,Wwidth,Wheight){
    var WIN;
    WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
    WIN.focus();
}

function win03(URL,Winname,Wwidth,Wheight){
    var WIN;
    WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
    WIN.focus();
}

function winSubmit(URL,formName,Winname,Wwidth,Wheight){
    var WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
    document.forms[formName].target = Winname;
    WIN.focus();
}
