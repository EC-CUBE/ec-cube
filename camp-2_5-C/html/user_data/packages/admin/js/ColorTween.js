/**********************************************************************
TERMS OF USE - EASING EQUATIONS
Open source under the BSD License.
Copyright (c) 2001 Robert Penner
JavaScript version copyright (C) 2006 by Philippe Maegerman
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

   * Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
   * Redistributions in binary form must reproduce the above
copyright notice, this list of conditions and the following disclaimer
in the documentation and/or other materials provided with the
distribution.
   * Neither the name of the author nor the names of contributors may
be used to endorse or promote products derived from this software
without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*****************************************/
ColorTween.prototype = new Tween();
ColorTween.prototype.constructor = Tween;
ColorTween.superclass = Tween.prototype;

function ColorTween(obj,prop,func,fromColor,toColor,duration){
	this.targetObject = obj;
	this.targetProperty = prop;	
	this.fromColor = fromColor;
	this.toColor = toColor;
	this.init(new Object(),'x',func,0,100,duration);
	this.listenerObj = new Object();
	this.listenerObj.onMotionChanged = Delegate.create(this,this.onColorChanged);
	this.addListener(this.listenerObj);
}
var o = ColorTween.prototype;
o.targetObject = {};
o.targetProperty = {};
o.fromColor = '';
o.toColor = '';
o.currentColor = '';
o.listenerObj = {};
o.onColorChanged = function(){
	this.currentColor = this.getColor(this.fromColor,this.toColor,this._pos);
	this.targetObject[this.targetProperty] = this.currentColor;
}

/***********************************************
*
* Function    : getColor
*
* Parameters  :    start - the start color (in the form "RRGGBB" e.g. "FF00AC")
*            end - the end color (in the form "RRGGBB" e.g. "FF00AC")
*            percent - the percent (0-100) of the fade between start & end
*
* returns      : color in the form "#RRGGBB" e.g. "#FA13CE"
*
* Description : This is a utility function. Given a start and end color and
*            a percentage fade it returns a color in between the 2 colors
*
* Author      : www.JavaScript-FX.com
*
*************************************************/ 
o.getColor = function(start, end, percent)
{
	var r1=this.hex2dec(start.slice(0,2));
    var g1=this.hex2dec(start.slice(2,4));
    var b1=this.hex2dec(start.slice(4,6));

    var r2=this.hex2dec(end.slice(0,2));
    var g2=this.hex2dec(end.slice(2,4));
    var b2=this.hex2dec(end.slice(4,6));

    var pc = percent/100;

    r= Math.floor(r1+(pc*(r2-r1)) + .5);
    g= Math.floor(g1+(pc*(g2-g1)) + .5);
    b= Math.floor(b1+(pc*(b2-b1)) + .5);

    return("#" + this.dec2hex(r) + this.dec2hex(g) + this.dec2hex(b));
}
/*** These are the simplest HEX/DEC conversion routines I could come up with ***/
/*** I have seen a lot of fade routines that seem to make this a             ***/
/*** very complex task. I am sure somene else must've had this idea          ***/
/************************************************/  

o.dec2hex = function(dec){return(this.hexDigit[dec>>4]+this.hexDigit[dec&15]);}
o.hexDigit=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
o.hex2dec = function(hex){return(parseInt(hex,16))};