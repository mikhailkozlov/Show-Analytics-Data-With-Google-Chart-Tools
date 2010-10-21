/*
 * Chart Loader (for jQuery)
 * version: 1.0 (09/22/2010)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://mikhailkozlov.com/demo/google_analytics+google_chart_api/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * Author: Mikhail A Kozlov
 * Website: http://mikhailkozlov.com/
 * Version: 0.1
 *
 * Usage:
 * 	JS:
 * 		$("#chart").drawChart();
 * 	HTML:
 * 		<div id="chart" style="height:300px; width:940px; padding:0; color:#258cd1" class="visits bars last_30"></div>
 * 
*/
(function($) {
	$.fn.drawChart = function(options) {
		// set default options
		var defaults = {
			type : "bars",
			uri:null
		},		
		// Take the options that the user selects, and merge them with defaults.
		options = $.extend(defaults, options);
		// for each item in the wrapped set
		return this.each(function() {
			// cache "this."
			var $this = $(this);
			// make sure we set file for ajax
			if(defaults.uri != null){
				defaults.color = $this.css('color');
				defaults.width = $this.width();
				defaults.height = $this.height();
				defaults.class = $this.attr("class").split(" ");
				$this.html('<center><i>Loading...</i></center>').load(defaults.uri,defaults, function(r){
					$this.find("div").animate({height:defaults.height}, 1000);
				});
			}else{
				$this.html('<center><i>Error. Please set source file</i></center>');
			}
		}); // end each		
	} // End plugin. Go eat cake or drink beer.
})(jQuery);