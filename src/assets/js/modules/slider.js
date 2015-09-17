pf.slider = {
	"current": null,

	"listen": function(){
		$(document).on('click', '*[data-slideto]', function(){
			var slideTo = $(this).attr('data-slideto');
			$('.heading.slide .arrow.up').removeClass('up').addClass('down');
			$('.inner.slide').stop(true, false).slideUp('fast');

			if(pf.slider.current != slideTo){
				$('*[data-slideto=' + slideTo + ']').children('.arrow').removeClass('down').addClass('up');
				$('.inner.slide[data-slide=' + slideTo + ']').stop(true, false).slideDown('fast');
				pf.slider.current = slideTo;
			}else
				pf.slider.current = null;
		});
	}
};