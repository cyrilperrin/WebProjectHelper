/**
 * jQuery-Plugin "clearField"
 * 
 * @version: 1.0, 31.07.2009
 * 
 * @author: Stijn Van Minnebruggen
 *          stijn@donotfold.be
 *          http://www.donotfold.be
 * 
 * @example: $('selector').clearField();
 * @example: $('selector').clearField({ blurClass: 'myBlurredClass', activeClass: 'myActiveClass' });
 * 
 */

(function($) {

jQuery.fn.clearField = function(settings) {
	
	/**
	 * Settings
	 * 
	 */
	
	settings = jQuery.extend({
		blurClass: 'clearFieldBlurred',
		activeClass: 'clearFieldActive'
	}, settings);
	
	
	/**
	 * loop each element
	 * 
	 */
	
	jQuery(this).each(function() {
		
		/**
		 * Set element
		 * 
		 */
		
		var el = jQuery(this);
		
		
		/**
		 * Add rel attribute
		 * 
		 */
		
		if(el.attr('rel') == undefined) {
			el.attr('rel', el.val()).addClass(settings.blurClass);
		}
		
		
		/**
		 * Set focus action
		 * 
		 */
		
		el.focus(function() {
			
			if(el.val() == el.attr('rel')) {
				el.val('').removeClass(settings.blurClass).addClass(settings.activeClass);
			}
			
		});
		
		
		/**
		 * Set blur action
		 * 
		 */
		
		el.blur(function() {
			
			if(el.val() == '') {
				el.val(el.attr('rel')).removeClass(settings.activeClass).addClass(settings.blurClass);
			}
			
		});
		
		
	});
	
	return jQuery;
	
};

})(jQuery);
