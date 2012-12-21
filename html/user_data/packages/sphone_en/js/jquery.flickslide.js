// FlickSlide v1.0.2
// Copyright (c) 2011 Kosuke Araki - twitterF@kaleido_kosuke
// Modified by Kentaro Ohkouchi <ohkouchi@loop-az.jp>
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
(function($){
     $.fn.flickSlide=function(settings){
         var currentX;
         var maxX;
         var strUA=navigator.userAgent.toLowerCase(),
         webkitUA=['iphone','android','ipad'],
         runiLayout=false,
         iLayoutLocation='',
         currentX=0,
         maxX=0,
         slideObj={},
         slideLock=1,
         slideTimer={},
         slideLotation={},
         slideDuration=4000,
         slideCount=0,
         pagerMax=0,
         orientationChangeDelay=0;
         for(var i=0;i<webkitUA.length;i++){
             if(strUA.indexOf(webkitUA[i],0)!=-1){
                 runiLayout=true;
                 if(webkitUA[i]==='android'){
                     orientationChangeDelay=400;
                 }
                 if(webkitUA[i]==='iphone'){
                     orientationChangeDelay=0;
                 }
             }
         }
//if(runiLayout!==true){return;}
//if(typeof $(this)===undefined||$(this).length===0){return;}
         window.addEventListener("orientationchange",
                                 function(){
                                     if(runiLayout!==true){
                                         return;
                                     }
                                     switch(window.orientation){
                                     case 0:
                                         orientationChangeCore();
                                         break;

                                     case 90:
                                         orientationChangeCore();
                                         break;

                                     case -90:
                                         orientationChangeCore();
                                         break;
                                     }
                                 },false);

         function orientationChangeCore(){
             clearTimeout(slideTimer);
             setTimeout(function(){
                            var styles=getComputedStyle($('.moveWrap').get(0));
                            if(styles){
                                $('.resizable').css('width',styles.width);
                                $('.slideMask').css('height',$('.move').outerHeight())
                                               .css('width',styles.width-1);
                                if($('#whobought_area').size()>0){
                                    maxX = Number(
                                        $(settings.parentArea + ' .flickSlideContainer li.slideUnit').length-1) * Number(
                                            getComputedStyle(
                                                $(settings.parentArea + ' .flickSlideContainer li.slideUnit').get(0))
                                                .width.replace('px',''))*-1;
                                        $('div.flickSlideContainer ul.move').get(0).style.webkitTransform = 'translate(0,0)';
                                }
                                currentX=0;
                                slideCount=0;
                                slidePager();
                                slideTimer=setTimeout(lotation,slideDuration);
                            }else{
                            }
                        },orientationChangeDelay);
         }
         function lotation(){
             //if(slideLock===0){var slideUnitWidth=slideObj.children('li.slideUnit').outerWidth();slideObj.get(0).style.webkitTransition='-webkit-transform 0.6s ease-out';diffX=-151;if(currentX===maxX){slideObj.get(0).style.webkitTransform='translate(0, 0)';currentX=0;slideCount=0;slidePager();}else{currentX=currentX-slideUnitWidth;slideObj.get(0).style.webkitTransform='translate('+currentX+'px, 0)';slideCount++;slidePager();}}
             //slideLock=0;slideTimer=setTimeout(lotation,slideDuration);
         }
         function slidePager(){
             var currentPager=$(settings.parentArea ? settings.parentArea : '.flickSlideBottom').find('.slidePagerPointer.active');
             var nextID='.pager'+String(slideCount);

             currentPager.removeClass('active');
             currentPager.parent().children(nextID).addClass('active');
             switch(slideCount){
             case 0:
                 $('.flickSlideBottom .bottomLeft').addClass('off');
                 $('.flickSlideBottom .bottomRight').removeClass('off');
                 break;

             case pagerMax:
                 $('.flickSlideBottom .bottomRight').addClass('off');
                 $('.flickSlideBottom .bottomLeft').removeClass('off');
                 break;

             default:
                 $('.flickSlideBottom .bottomLeft').removeClass('off');
                 $('.flickSlideBottom .bottomRight').removeClass('off');
                 break;
             }
         }

         $.fn.slideButton=function(settings){
             var settings = $.extend({
                                       direction:'prev',
                                       widthSource:{}
                                   },settings);
             var self=$(this);
             self.click(function(){
                            var slideUnitWidth=settings.widthSource.outerWidth();
                            slideLock=1;
                            clearTimeout(slideTimer);
                            slideObj.get(0).style.webkitTransition='-webkit-transform 0.6s ease-out';
                            if(settings.direction==='prev'){
                                if(currentX==0){
                                    slideObj.get(0).style.webkitTransform='translate(0, 0)';
                                    slideTimer=setTimeout(lotation,slideDuration);
                                    slideLock=0;
                                }else{
                                    currentX = currentX+slideUnitWidth;
                                    slideObj.get(0).style.webkitTransform = 'translate('+currentX+'px, 0)';
                                    slideCount--;
                                    slidePager();
                                    slideTimer=setTimeout(lotation,slideDuration);
                                    slideLock=0;
                                }
                            }else if(settings.direction==='next'){
                                if(currentX===maxX){
                                    slideObj.get(0).style.webkitTransform='translate('+maxX+'px, 0)';
                                    slideTimer=setTimeout(lotation,slideDuration);
                                    slideLock=0;
                                }else{
                                    currentX=currentX-slideUnitWidth;
                                    slideObj.get(0).style.webkitTransform='translate('+currentX+'px, 0)';
                                    slideCount++;
                                    slidePager();
                                    slideTimer=setTimeout(lotation,slideDuration);
                                    slideLock=0;
                                }
                            }
                        });
         };

         $.fn.touchDrag=function(settings){
             var settings=$.extend({
                                       slideDuration:4000,
                                       targetWidth:150
                                   },settings);
             var diffX=0;
             slideObj=$(this);
             slideDuration=settings.slideDuration;
             slideObj.bind('touchstart',{type:'start'},touchHandler);
             slideObj.bind('touchmove',{type:'move'},touchHandler);
             slideObj.bind('touchend',{type:'end'},touchHandler);
             function touchHandler(e){
                 var slideUnitWidth=slideObj.children('li.slideUnit').outerWidth();
                 var touch=e.originalEvent.touches[0];
                 if(e.type=="touchstart"){
                     clearTimeout(slideTimer);
                     diffX=0;
                     startX=touch.pageX;
                     startY=touch.pageY;
                     startTime=(new Date()).getTime();
                 }else if(e.type=="touchmove"){
                     diffX=touch.pageX-startX;
                     diffY=touch.pageY-startY;
                     if(Math.abs(diffX)-Math.abs(diffY)>0){
                         e.preventDefault();
                         moveX=Number(currentX+diffX);
                         slideObj.css('-webkit-transition','none');
                         slideObj.get(0).style.webkitTransform='translate( '+moveX+'px, 0)';
                     }
                 }else if(e.type=="touchend"){
                     var endTime=(new Date()).getTime();
                     var diffTime=endTime-startTime;
                     if(diffTime<300){
                         slideObj.get(0).style.webkitTransition='-webkit-transform 0.5s ease-out';
                     }else{
                         slideObj.get(0).style.webkitTransition='-webkit-transform 0.6s ease-out';
                     }
                     if(diffX>settings.targetWidth||(diffX>60&&diffTime<400&&orientationChangeDelay===0)){
                         if(currentX==0){
                             slideObj.get(0).style.webkitTransform='translate(0, 0)';
                         }else{
                             currentX=currentX+slideUnitWidth;
                             slideObj.get(0).style.webkitTransform='translate('+currentX+'px, 0)';
                             slideCount--;
                             slidePager();
                         }
                     }else if(diffX<(settings.targetWidth*-1)||(diffX<-60&&diffTime<400&&orientationChangeDelay===0)){
                         if(currentX===maxX){
                             slideObj.get(0).style.webkitTransform='translate('+maxX+'px, 0)';
                         }else{
                             currentX=currentX-slideUnitWidth;
                             slideObj.get(0).style.webkitTransform='translate('+currentX+'px, 0)';
                             slideCount++;slidePager();
                         }
                     }else{
                         if(currentX===0){
                             slideObj.get(0).style.webkitTransform='translate(0, 0)';
                         }else if(currentX===maxX){
                             slideObj.get(0).style.webkitTransform='translate('+maxX+'px, 0)';
                         }else{
                             slideObj.get(0).style.webkitTransform='translate('+currentX+'px, 0)';
                         }
                     }
                     slideTimer=setTimeout(lotation,slideDuration);slideLock=0;}}
             slideTimer=setTimeout(lotation,slideDuration);
         };

         var settings=$.extend({
                                   target:'',
                                   colum:1,
                                   height:170,
                                   duration:4000,
                                   parentArea:''
                               },settings);
         var contents=$(this);
         var targetWidth=contents.outerWidth();
         var contentsLength=contents.length;
         var wrap=$('<div class="flickSlideContainer"><div class="moveWrap"><ul class="move"></ul></div></div>');
         var slideMask=$('<div class="slideMask resizable"></div>');
         var bottom=$('<div class="flickSlideBottom"><div class="bottomLeft off">&lt;</div><ul class="slidePager"></ul><div class="bottomRight">&gt;</div></div>');var bottom2=$('<div class="flickSlideBottom"><div class="bottomLeft off"></div><ul class="slidePager"></ul><div class="bottomRight"></div></div>');

         var img = $(this).contents().find('img');

         /*
         img.removeAttr('width')
            .removeAttr('height')
            .css({
                     width:'100%',
                     height:'auto'
                 });
          */
         var loop=Math.floor(contentsLength/settings.colum);
         loop=contentsLength%settings.colum>0?loop++:loop;
         pagerMax=loop-1;

         var contentsCount=0;
         for(var i=0;i<loop;i++){
             var unitElem=$('<li/>')
                 .addClass('slideUnit').
                 addClass('resizable');

                        var pager=$('<li class="pager'+i+' slidePagerPointer"></li>');
             if(i===0){
                 pager.addClass('active');
             }
             for(var j=0;j<settings.colum;j++){
                 var itemElem=$('<div/>');
                 if(typeof contents[contentsCount]!==undefined){
                     itemElem.append($(contents[contentsCount]).children());
                 }
                 unitElem.append(itemElem);
                 contentsCount++;
             }
             // 画像の高さに合わせて padding-top を入れるよう変更
             var imgHeight = unitElem.children().find('img').attr('height');
             if (imgHeight < 1) {
                  imgHeight = settings.height;
             }
             var paddingSize = (settings.height / 2) - (imgHeight / 2);
             unitElem.css('padding-top', paddingSize + 'px');
             wrap.contents().find('ul.move').append(unitElem);
             bottom.children('ul.slidePager').append(pager);
         }
         //スライド最大幅を表示エリアの横幅×liの数→liの横幅×liの数に変更
         $(settings.target).after(wrap);
         $(settings.target).remove();
         bottom.children('.bottomLeft').slideButton({
                                                        direction:'prev',
                                                        widthSource:wrap.contents().find('li.slideUnit')
                                                    });
         bottom.children('.bottomRight').slideButton({
                                                         direction:'next',
                                                         widthSource:wrap.contents().find('li.slideUnit')
                                                     });
         wrap.contents().find('ul.move').touchDrag({
                                                       duration:settings.duration,
                                                       targetWidth:(targetWidth*0.4)
                                                   });
         if(contentsLength > 1) {
             wrap.after(bottom);
         } else {
             wrap.after(bottom2);
         };
         $(window).bind('load',function(){
                            var styles=getComputedStyle($('.moveWrap').get(0));
                            if(styles){
                                $('.resizable').css('width',styles.width);
                                $('.slideMask').css('height',$('.move').outerHeight())
                                               .css('width',styles.width-1);

                                if ($(settings.parentArea + ' .flickSlideContainer li.slideUnit').get(0)){
                                    maxX=Number($(settings.parentArea + ' .flickSlideContainer li.slideUnit').length-1)*Number(getComputedStyle($(settings.parentArea + ' .flickSlideContainer li.slideUnit').get(0)).width.replace('px',''))*-1;
                                }
                            }
                            var slideFirstChild=$('ul.move li:first').clone();
                            $('ul.move').show();
                        });
     };
 })(jQuery);
var is={
    ie:navigator.appName=='Microsoft Internet Explorer',
    java:navigator.javaEnabled(),
    ns:navigator.appName=='Netscape',
    ua:navigator.userAgent.toLowerCase(),
    version:parseFloat(navigator.appVersion.substr(21))|| parseFloat(navigator.appVersion),win:navigator.platform=='Win32'
};
is.mac=is.ua.indexOf('mac')>=0;
if(is.ua.indexOf('opera')>=0){
    is.ie=is.ns=false;is.opera=true;
}
if(is.ua.indexOf('gecko')>=0){
    is.ie=is.ns=false;is.gecko=true;
}
