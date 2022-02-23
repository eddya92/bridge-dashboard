var owl_carousel_custom = {
	init: function() {
		$('#owl-carousel-2').owlCarousel({
			loop:true,
			margin:10,
			items:1,
			nav:false,
			responsive : {
				576 : {
					items:1
				},
				768 : {
					items:2
				},
				992 : {
					items:3
				}
			}
		}),
			$('.play').on('click',function(){
				owl.trigger('play.owl.autoplay',[1000])
			}), $('.stop').on('click',function(){
			owl.trigger('stop.owl.autoplay')
		});

	}
};

(function($) {
	"use strict";
	owl_carousel_custom.init();
})(jQuery);
