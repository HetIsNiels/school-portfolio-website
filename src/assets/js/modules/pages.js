pf.pages = {
	"page": null,
	"pageData": null,

	"listen": function(){
		$('*[data-page]').on({
			'click': function(){
				pf.pages.goto($(this).attr('data-page'));
			}
		});

		$(document).on({
			'click': function(){
				var heading = $(this);
				var state = heading.attr('data-slide-state');

				if(state == null)
					state = 1;

				if(state == 1) {
					heading.next().slideUp('fast');
					heading.children('.arrow').removeClass('up').addClass('down');
					heading.attr('data-slide-state', 0);
				}else{
					heading.next().slideDown('fast');
					heading.children('.arrow').removeClass('down').addClass('up');
					heading.attr('data-slide-state', 1);
				}
			}
		}, '.heading.slide');
	},

	"getElements": function(elms){
		var html = '';

		for(var elmNum in elms){
			html += pf.pages.getElement(elms[elmNum]);
		}

		return html;
	},

	"getElement": function(elm){
		return pf.pageElements[elm.type].render.add(elm);
	},

	"goto": function(page, force){
		if(force == null)
			force = false;

		if("editor" in pf && pf.editor.isOpen && !force)
			return;

		if(page == '')
			page = 'index';

		if(pf.pages.page == page && !force)
			return;

		if(pf.pages.page != null)
			$('#put-content').stop(true, true).slideUp('slow');

		pf.pages.page = page;

		if('pushState' in window.history) // Support IE 9 and lower
			window.history.pushState(null, null, pf.webUrl + page);

		$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'request', 'url': page}, function(json){
			pf.pages.pageData = json;

			if(json.code == 0) {
				if(json.url != page)
					return pf.pages.goto(json.url, true);
				else if("editor" in pf && pf.editor.isOpen)
					return $('#editor-page-create').fadeIn('slow');
				else
					return alert('Er is nog geen startpagina op deze website. Indien u de eigenaar bent dient u een pagina te creëren met de url \'index\'.');
			}

			document.title = json.caption + ' - Niels van Velzen';
			window.history.replaceState(null, document.title, pf.webUrl + json.url);

			$('#put-content').promise().done(function(){
				var html = '';

				for(var elmNum in json.content){
					var elm = json.content[elmNum];

					html += pf.pages.getElement(elm);
				}

				$(this).html(html).slideDown('fast');
			});
		}, 'json');
	}
};