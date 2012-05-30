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
gCssUA = navigator.userAgent.toUpperCase();
gCssBrw = navigator.appName.toUpperCase();

with (document) {
    write("<style type=\"text/css\"><!--");

    //WIN-IE
    if (gCssUA.indexOf("WIN") != -1 && gCssUA.indexOf("MSIE") != -1) {
        write(".fs10 {font-size: 62.5%; line-height: 150%; letter-spacing:1px;}");
        write(".fs12 {font-size: 75%; line-height: 150%; letter-spacing:1.5px;}");
        write(".fs14 {font-size: 87.5%; line-height: 150%; letter-spacing:2px;}");
        write(".fs18 {font-size: 117.5%; line-height: 130%; letter-spacing:2.5px;}");
        write(".fs22 {font-size: 137.5%; line-height: 130%; letter-spacing:3px;}");
        write(".fs24 {font-size: 150%; line-height: 130%; letter-spacing:3px;}");
        write(".fs30 {font-size: 187.5%; line-height: 125%; letter-spacing:3.5px;}");
        write(".fs10n {font-size: 62.5%; letter-spacing:1px;}");
        write(".fs12n {font-size: 75%; letter-spacing:1.5px;}");
        write(".fs14n {font-size: 87.5%; letter-spacing:2px;}");
        write(".fs18n {font-size: 117.5%; letter-spacing:2.5px;}");
        write(".fs22n {font-size: 137.5%; letter-spacing:1px;}");
        write(".fs24n {font-size: 150%; letter-spacing:1px;}");
        write(".fs30n {font-size: 187.5%; letter-spacing:1px;}");
        write(".fs12st {font-size: 75%; line-height: 150%; letter-spacing:1.5px; font-weight: bold;}");
    }

    //WIN-NN
    if (gCssUA.indexOf("WIN") != -1 && gCssBrw.indexOf("NETSCAPE") != -1) {
        write(".fs10 {font-size:72%; line-height:130%;}");
        write(".fs12 {font-size: 75%; line-height: 150%;}");
        write(".fs14 {font-size: 87.5%; line-height: 140%;}");
        write(".fs18 {font-size: 117.5%; line-height: 130%;}");
        write(".fs22 {font-size: 137.5%; line-height: 130%;}");
        write(".fs24 {font-size: 150%; line-height: 130%;}");
        write(".fs30 {font-size: 187.5%; line-height: 120%;}");
        write(".fs10n {font-size:72%;}");
        write(".fs12n {font-size: 75%;}");
        write(".fs14n {font-size: 87.5%;}");
        write(".fs18n {font-size: 117.5%;}");
        write(".fs22n {font-size: 137.5%;}");
        write(".fs24n {font-size: 150%;}");
        write(".fs30n {font-size: 187.5%;}");
        write(".fs12st {font-size: 75%; line-height: 150%; font-weight: bold;}");
    }

    //WIN-NN4.x
    if ( navigator.appName == "Netscape" && navigator.appVersion.substr(0,2) == "4." ) {
        write(".fs10 {font-size:90%; line-height: 130%;}");
        write(".fs12 {font-size: 100%; line-height: 140%;}");
        write(".fs14 {font-size: 110%; line-height: 135%;}");
        write(".fs18 {font-size: 130%; line-height: 175%;}");
        write(".fs24 {font-size: 190%; line-height: 240%;}");
        write(".fs30 {font-size: 240%; line-height: 285%;}");
        write(".fs10n {font-size:90%;}");
        write(".fs12n {font-size: 100%;}");
        write(".fs14n {font-size: 110%;}");
        write(".fs18n {font-size: 130%;}");
        write(".fs24n {font-size: 190%;}");
        write(".fs30n {font-size: 240%;}");
        write(".fs12st {font-size: 100%; line-height: 140%; font-weight: bold;}");
    }

    //MAC
    if (gCssUA.indexOf("MAC") != -1) {
        write(".fs10 {font-size: 10px; line-height: 14px;}");
        write(".fs12 {font-size: 12px; line-height: 18px;}");
        write(".fs14 {font-size: 14px; line-height: 18px;}");
        write(".fs18 {font-size: 18px; line-height: 23px;}");
        write(".fs22 {font-size: 22px; line-height: 27px;}");
        write(".fs24 {font-size: 24px; line-height: 30px;}");
        write(".fs30 {font-size: 30px; line-height: 35px;}");
        write(".fs10n {font-size: 10px;}");
        write(".fs12n {font-size: 12px;}");
        write(".fs14n {font-size: 14px;}");
        write(".fs18n {font-size: 18px;}");
        write(".fs22n {font-size: 22px;}");
        write(".fs24n {font-size: 24px;}");
        write(".fs30n {font-size: 30px;}");
        write(".fs12st {font-size: 12px; line-height: 18px; font-weight: bold;}");
    }

    write("--></style>");
}
