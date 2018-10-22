//Find slider element, read attribute, change css
if (plugins.swiper.length) {
	plugins.swiper.each(function () {
		var slider = $(this);

		slider.find(".swiper-slide")
		.each( function () {
			var $this = $(this), url;
			if ( url = $this.attr("data-slide-bg") ) {
				$this.css({
					"background-image": "url(" + url + ")",
					"background-repeat": "no-repeat",
					"background-size": "contian"
				})
			}
		})
		.end()
	});
}

//Initialize Sliders
var mySwiper = new Swiper('.swiper-container', {
        // AutoPlay
	autoplay: true,
	spaceBetween: 0,
        speed: 1200,
        watchSlidesProgress: true,
        watchVisibility: true,
	loop: true,

        // Lazy Loading 
        watchSlidesVisibility: true,
        preloadImages: false,
        lazyLoading: true,

	disableOnInteraction: false
});

$(document).ready(function(){
});
