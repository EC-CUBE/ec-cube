// Version 1.0 - October 19, 2007
// Requires http://jquery.com version 1.2.1

(function($) {
	$.fn.biggerlink = function(options) {

		// Default settings
		var settings = {
			hoverclass:'hover', // class added to parent element on hover
			clickableclass:'hot', // class added to parent element with behaviour
			follow: true	// follow link? Set to false for js popups
		};
		if(options) {
			$.extend(settings, options);
		}
		$(this).filter(function(){
			 return $('a',this).length > 0;

		}).addClass(settings.clickableclass).each(function(i){
		
			// Add title of first link with title to parent
			$(this).attr('title', $('a[title]:first',this).attr('title'));
			
			// hover and trigger contained anchor event on click
			$(this)
			.mouseover(function(){
				window.status = $('a:first',this).attr('href');
				$(this).addClass(settings.hoverclass);
			})
			.mouseout(function(){
				window.status = '';
				$(this).removeClass(settings.hoverclass);
			})
			.bind('click',function(){
				$(this).find('a:first').trigger('click');
			})
			
			// triggerable events on anchor itself
			
			.find('a').bind('focus',function(){
				$(this).parents('.'+ settings.clickableclass).addClass(settings.hoverclass);
			}).bind('blur',function(){
				$(this).parents('.'+ settings.clickableclass).removeClass(settings.hoverclass);
			}).end()
			
			.find('a:first').bind('click',function(e){
				if(settings.follow == true)
				{
					window.location = this.href;
				}
				e.stopPropagation(); // stop event bubbling to parent
			}).end()
			
			.find('a',this).not(':first').bind('click',function(){
				$(this).parents('.'+ settings.clickableclass).find('a:first').trigger('click');
				return false;
			});
		});
		return this;
	};
})(jQuery);


