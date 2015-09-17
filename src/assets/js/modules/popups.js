pf.popups = {
	"dragging": [],

	"fixPosition": function(popup){
		var location = pf.storage.get(popup + '-location', '0,39,1').split(',');

		popup = $('#' + popup);

		if(location[0] < 39)
			location[0] = 39;
		else if(location[0] > (window.innerHeight - popup.outerHeight()))
			location[0] = window.innerHeight - popup.outerHeight();

		if(location[1] < 0)
			location[1] = 0;
		else if(location[1] > (window.innerWidth - popup.outerWidth()))
			location[1] = window.innerWidth - popup.outerWidth();

		popup.css('top', location[0] + 'px').css('left', location[1] + 'px').css('z-index', location[2]);
	},

	"listen": function(){
		$(document).on({
			'mousemove': function(event){
				if(pf.popups.dragging == null)
					return;

				var popup = $(pf.popups.dragging[0]);

				var top = (pf.popups.dragging[3] != 'horizontal' ? event.clientY - pf.popups.dragging[1] : popup.pageY);
				var left = (pf.popups.dragging[3] != 'vertical' ? event.clientX - pf.popups.dragging[2] : popup.pageX);

				if(top < 39)
					top = 39;
				else if(top > (window.innerHeight - popup.outerHeight()))
					top = window.innerHeight - popup.outerHeight();

				if(left < 0)
					left = 0;
				else if(left > (window.innerWidth - popup.outerWidth()))
					left = window.innerWidth - popup.outerWidth();

				popup.css('top', top + 'px').css('left', left + 'px');
			},

			'mousedown': function(event){
				if(pf.popups.dragging[0] != null){
					pf.storage.set(pf.popups.dragging[0].replace('#', '') + '-location', $(pf.popups.dragging[0]).offset().top + ',' + $(pf.popups.dragging[0]).offset().left + ',' + $(pf.popups.dragging[0]).css('z-index'));
					pf.popups.dragging = [];
					$('body').removeClass('dragging');
					return;
				}

				var target = $(event.target);
				if(target.attr('data-draggable') == 'true'){
					var parent = target.attr('data-dragparent');
					var offset = $(target).parents(parent).offset();
					var direction = $(target).attr('data-dragdirection');

					$(parent).css('z-index', pf.counter.getNext());

					if(direction == null)
						direction = 'all';

					$('body').addClass('dragging');
					pf.popups.dragging = [parent, event.pageY - offset.top, event.pageX - offset.left, direction];
				}
			}
		});
	}
};