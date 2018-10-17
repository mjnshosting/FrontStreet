var mySwiper = new Swiper ('.swiper-container', {

                pagination: '.swiper-pagination',
                paginationClickable: true,

                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',

                // AutoPlay
                autoplay: 2000,
                speed: 1200,
                watchSlidesProgress: true,
                watchVisibility: true,

                // Loop
                loop: true,

                // Keyboard and Mousewheel
                keyboardControl: false,
                mousewheelControl: false,
                mousewheelForceToAxis: false, 

                // Lazy Loading 
                watchSlidesVisibility: true,
                preloadImages: false,
                lazyLoading: true,

		disableOnInteraction: false,
            })

$(document).ready(function(){
});
