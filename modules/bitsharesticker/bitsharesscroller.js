/*
	jQuery Scrolling Tweet Ticker 1.1.3 (September 6, 2011)
	Copyright 2010-2011 Scott Langendyk. All Rights Reserved.
	
	==============================================================================
	Basic Usage
	==============================================================================
		jQuery('#tweetticker').tweetTicker({
			username : 'username'
		});
	
	*For additional documentation please reference readme.txt
*/
(function (jQuery) {
	jQuery.fn.tweetTicker = function (options) {
		var options = jQuery.extend({},
		jQuery.fn.tweetTicker.defaults, options);
		var tweetTicker;
		var tweetsList;
		var overflowContainer;
		var appendThreshhold;
		var currentTweet;
		var listWidth;
		var currentRate = options.normalRate;

		return this.each(function () {
			if (options.tickerOnly == false) {
				tweetTicker = build();
			} else {
				tweetTicker = jQuery('<div class="tweetticker"><div class="tweetticker-replace"></div></div>');
			}
			jQuery(this).append(tweetTicker);
			tweetsList = jQuery('<ul class="tweetticker-tweets-list"><li class="tweetticker-tweet">' + options.text + '</li></ul>');
			overflowContainer = jQuery('<div class="tweetticker-overflow-container"></div>');
			tweetTicker.find('div.tweetticker-username a').attr('href', 'http://bitshares.org').html(options.title);
			overflowContainer.wrapInner(tweetsList);
			tweetTicker.find('div.tweetticker-replace').replaceWith(overflowContainer);


			
			overflowContainer.mouseover(function () {
				currentRate = options.hoverRate;
			})
			overflowContainer.mouseout(function () {
				currentRate = options.normalRate;
			});
			tweetsList.css('left', overflowContainer.width());
			animationLoop();
		});
		function animationLoop() {
		    var pos = tweetsList.position().left;
		    if(pos < (-1*tweetsList.width()))
		    {
		        tweetsList.css('left', overflowContainer.width());
		    }
			if (currentRate > 0) {
				tweetsList.animate({
					'left': '-=1px'
				},
				currentRate, 'linear', animationLoop);
			} else {
				animationLoop();
			}

		}
	
		
		function build() {
			var build;
			build += '<div class="tweetticker">';
			build += '<div class="tweetticker-container">';
			build += '<div class="tweetticker-container-left"></div>';
			build += '<div class="tweetticker-container-content">';
			build += '<div class="tweetticker-username"><a href="#">Bitshares Ticker</a></div>';
			build += '<div class="tweetticker-twitter-link"><a href="http://bitshares.org" target="_blank">Bitshares.org</a></div>';
			build += '<div class="tweetticker-tweetbox">';
			build += '<div class="tweetticker-tweetbox-content">';
			build += '<div class="tweetticker-replace"></div>';
			build += '</div>';
			build += '</div>';
			build += '</div>';
			build += '<div class="tweetticker-container-right"></div>';
			build += '</div>';
			build += '</div>';
			return jQuery(build);
		}
	};
	jQuery.fn.tweetTicker.defaults = {
		title: '',
		normalRate: 10,
		hoverRate: 100,
		startOffScreen: true,
		liveUpdating: true,
		tickerOnly: false,
		text: ''

	};
})(jQuery);