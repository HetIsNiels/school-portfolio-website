pf.tooltip = {
	"tooltipElement": null,
	"notifElement": null,
	"notifElementTimer": null,

	"listen": function(){
		pf.tooltip.tooltipElement = $('<div id="tooltip"></div>');
		pf.tooltip.notifElement = $('<div id="tt-notif"></div>');

		$('body').append(pf.tooltip.tooltipElement).append(pf.tooltip.notifElement);

		$(document).on('mouseover', '*[data-tooltip]', function(event){
			pf.tooltip.tooltipElement.addClass('shown').html($(this).attr('data-tooltip'));
		}).on('mouseout', '*[data-tooltip]', function(event){
			pf.tooltip.tooltipElement.removeClass('shown');
		}).on('mousemove', function(event){
			pf.tooltip.tooltipElement.css('top', event.pageY + 'px').css('left', (event.pageX + 15) + 'px');
		});
	},

	"showError": function(msg, time, type){
		if(type != null)
			pf.tooltip.notifElement.removeClass('good error info').addClass(type);

		pf.tooltip.notifElement.html(msg).slideDown('fast');

		if(pf.tooltip.notifElementTimer != null)
			window.clearTimeout(pf.tooltip.notifElementTimer);

		pf.tooltip.notifElementTimer = setTimeout(function(){
			pf.tooltip.notifElement.slideUp('slow');
		}, time);
	}
};