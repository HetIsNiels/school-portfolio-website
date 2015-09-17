pf.pageElements = {
	"box": {
		"params": [],

		"render": {
			"edit": function(params){

			},

			"add": function(params){
				var html  = '<div class="box content-holder" data-id="' + params.id + '">';
					html += 	pf.pages.getElements(params.children);
					html += '</div>';

				return html;
			},

			"possible": function(target){
				return target.attr('id') == 'put-content';
			}
		}
	},

	"inner": {
		"params": {
			"text": {
				"default": "Tekst voor in het blok.",
				"caption": "Welke tekst wilt u weergeven?",
				"type": "text",
				"allow_html": true
			}
		},

		"render": {
			"edit": function (params){
				$('[data-id=' + params.id + ']').html(params.values.text == null ? '' : params.values.text);
			},

			"add": function(params){
				var html  = '<div class="inner" data-id="' + params.id + '">';
					html += 	(params.values.text == null ? '' : params.values.text);
					html += '</div>';

				return html;
			},

			"possible": function(target){
				return target.hasClass('content-holder');
			}
		}
	},

	"heading": {
		"params": {
			"text": {
				"default": "Titel van het blok.",
				"caption": "Welke titel wilt u weergeven?",
				"type": "string"
			},

			"isAccordion": {
				"default": false,
				"caption": "Moet het eerstvolgende element inklapbaar zijn?",
				"type": "bool"
			}
		},

		"render": {
			"edit": function (params) {
				$('[data-id=' + params.id + ']').removeClass('slide').addClass((params.values.isAccordion === true ? 'slide' : '')).children('.txt').html(params.values.text == null ? '' : params.values.text);
			},

			"add": function(params){
				var html  = '<div class="heading ' + (params.values.isAccordion === 'true' ? 'slide' : '') + '" data-id="' + params.id + '">';
					html += 	'<div class="txt">' + (params.values.text == null ? '' : params.values.text) + '</div>';
					html += 	'<div class="arrow up"></div>';
					html += 	'<div class="clear"></div>';
					html += '</div>';

				return html;
			},

			"possible": function(target){
				return target.hasClass('content-holder');
			}
		}
	},

	"iframe": {
		"params": {
			"url": {
				"default": "http://hetisniels.nl",
				"caption": "Wat is de URL van de webpagina?",
				"type": "string"
			},

			"height": {
				"default": 200,
				"caption": "Wat is de hoogte van de frame?",
				"type": "number"
			}
		},

		"render": {
			"edit": function (params) {
				var html  = '<iframe src="' + (params.values.url == null ? 'http://hetisniels.nl' : params.values.url) + '" height="' + (params.values.height == null ? 200 : params.values.height) + '">';
					html += '</iframe>';
					html += '<div class="iframe-overlay" style="height: ' + (params.values.height == null ? 200 : params.values.height) + 'px;"></div>';

				$('[data-id=' + params.id + ']').html(html);
			},

			"add": function(params){
				var html = '<div class="iframe-wrapper" data-id="' + params.id + '">';
					html += 	'<iframe src="' + (params.values.url == null ? 'http://hetisniels.nl' : params.values.url) + '" height="' + (params.values.height == null ? 200 : params.values.height) + '">';
					html += 	'</iframe>';
					html += 	'<div class="iframe-overlay" style="height: ' + (params.values.height == null ? 200 : params.values.height) + 'px;"></div>';
					html += '</div>';

				return html;
			},

			"possible": function(target){
				return target.hasClass('content-holder');
			}
		}
	},

	"columns": {
		"params": {
			"width": {
				"default": 25,
				"caption": "Wat is de breedte van de kolom?",
				"type": "number",
				"min": 0,
				"max": 100
			},

			"clearAfter": {
				"default": false,
				"caption": "Moet er een regel vrijgemaakt worden na deze kolom?",
				"type": "bool"
			}
		},

		"render": {
			"edit": function(params) {
				$('[data-id=' + params.id + ']').css('width', params.values.width + '%');

				$('.column-clear[data-for=' + params.id + ']').removeClass('shown hidden').addClass(params.values.clearAfter == true ? 'shown' : 'hidden');
			},

			"add": function(params) {
				var html  = '<div class="column content-holder" data-id="' + params.id + '" style="width: ' + (params.values.width == null ? 25 : params.values.width) + '%;">';
					html += 	pf.pages.getElements(params.children);
					html += '</div>';
					html += '<div class="column-clear ' + (params.values.clearAfter === true ? 'shown' : 'hidden') + '" data-for="' + params.id + '"></div>';

				return html;
			},

			"possible": function(target) {
				return target.hasClass('content-holder');
			}
		}
	}
};
/*
case 'columns':
for(var columnNum in elm.columns){
	var column = elm.columns[columnNum];

	html += '<div class="column">';
	html += 	'<div class="heading">';
	html += 		column.heading;
	html += 	'</div>';

	switch(column.type){
		case 'links':
			html += '<div class="inner">';

			for(var caption in column.urls){
				html += '<a href="' + column.urls[caption] + '">';
				html += 	caption;
				html += '</a>';
				html += '<br />';
			}

			html += '</div>';
			break;
	}

	html += '</div>';
}

html += '<div class="clear"></div>';
break;

case 'slide':
var slideId = pf.counter.getNext();

html += '<div class="heading slide" data-slideto="' + slideId + '">';
html += 	(elm.heading == null ? '' : elm.heading);
html += 	'<div class="arrow down"></div>';
html += '</div>';
html += '<div class="inner slide" data-slide="' + slideId + '">';
html += 	(elm.inner == null ? '' : elm.inner);
html += '</div>';
break;
	*/