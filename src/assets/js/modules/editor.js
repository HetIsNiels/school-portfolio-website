//USE TOKEN
pf.editor = {
	"isOpen": false,
	"tool": null,
	"editing": null,

	"start": function() {
		$.get(pf.webUrl + 'assets/editor.html', {'_': new Date().getMilliseconds(), 'authCode7': pf.login.authCode7}, function (html) {
			$('body').append(html);

			pf.popups.fixPosition('editor-tools');
			pf.popups.fixPosition('editor-block-data');
			pf.popups.fixPosition('editor-page-info');
			pf.popups.fixPosition('editor-account-info');

			pf.editor.loaded();
		});
	},

	"stop": function() {
		pf.editor.isOpen = false;
		$('body').removeClass('editor');

		window.location.reload();
		//todo dynamicly close editor
	},

	"loaded": function() {
		pf.editor.isOpen = true;
		$('body').addClass('editor');

		$('[draggable]').on({
			'dragstart': function(event){
				pf.editor.tool = $(this);
			}
		});

		$(document).on({
			'click': function(event){
				var arr = $(this).attr('data-open').split(',');
				var page = $(this).attr('data-page');

				for(var dataOpen in arr) {
					dataOpen = arr[dataOpen];

					switch (dataOpen) {
						case 'page.info':
							$('#editor-page-info').fadeIn('slow').find('#page-caption').val(pf.pages.pageData.caption).siblings('#page-url').val(pf.pages.pageData.url);
							break;
						case 'page.create':
							$('#editor-page-create').fadeIn('slow');
							break;
						case 'page.list':
							if ($('#editor-page-list').is(':visible')) {
								$('#editor-page-list').fadeOut('fast');
								break;
							}

							$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'list'}, function (response) {
								if (response.code == 1) {
									$('#editor-page-list').fadeIn('slow');

									var html = '<table class="content-table">';
									html += '<tr class="heading-table">';
									html += '<td>ID</td>';
									html += '<td>Titel</td>';
									html += '<td>Link</td>';
									html += '<td>Opties</td>';
									html += '</tr>';

									for (var p in response.pages) {
										var page = response.pages[p];

										html += '<tr>';
										html += '<td>' + page.id + '</td>';
										html += '<td>' + page.caption + '</td>';
										html += '<td>' + page.url + '</td>';
										html += '<td>';
										html += '<div class="fa fa-trash fa-2x tool" data-open="page.delete,page.list" data-page="' + page.id + '"></div>';
										//html += 		'<div class="fa fa-pencil"></div>';
										html += '<div class="fa fa-paper-plane fa-2x tool" data-open="page.goto,page.list" data-page="' + page.id + '"></div>';
										html += '</td>';
										html += '</tr>';
									}

									html += '</table>';
									html += '<button class="button-big" data-open="page.list">Venster sluiten</button>';

									$('#page-list-list').html(html);
								} else {
									pf.tooltip.showError(response.msg, 2000, 'error');
								}
							});
							break;
						case 'page.goto':
							pf.pages.goto(page, true);
							break;
						case 'page.delete':
							$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'delete', 'page': page}, function (response) {
								if (response.code == 1) {
									pf.tooltip.showError(response.msg, 2000, 'good');
								}else{
									pf.tooltip.showError(response.msg, 2000, 'error');
								}
							});
							break;
						case 'account.info':
							$('#editor-account-info').fadeIn('slow');
							break;
						case 'auth.logout':
							pf.login.auth.logout();
							break;
					}
				}
			}
		}, '[data-open]');

		$('#put-content').on({
			'dragover': function(event){
				var target = $(event.target);

				if(pf.editor.tool.attr('data-type') != 'edit' && pf.editor.tool.attr('data-type') != 'delete' && target.parents('.content-holder').length != 0 && !target.hasClass('content-holder'))
					target = target.parents('.content-holder');

				if(target.attr('data-id') == null)
					target = target.parents('[data-id]');

				var offset = target.offset();
				$('#editor-overlay').css('top', offset.top + 'px').css('left', offset.left + 'px').css('width', target.outerWidth() + 'px').css('height', target.outerHeight() + 'px');

				var possible = false;
				var type = pf.editor.tool.attr('data-type');

				if(type == 'delete')
					possible = 3;
				else if(type == 'edit')
					possible = (target.attr('id') == 'put-content' ? -1 : 2);
				else
					possible = pf.pageElements[type].render.possible(target);

				event.originalEvent.dataTransfer.dropEffect = (possible ? 'move' : 'none');
				event.originalEvent.dataTransfer.effectAllowed = (possible ? 'all' : 'none');

				if(possible == 1){
					$('#editor-overlay').get(0).className = 'good';
					event.preventDefault();
				}else if(possible == 0){
					$('#editor-overlay').get(0).className = 'bad';
				}else if(possible == 2){
					$('#editor-overlay').get(0).className = 'edit';
					event.preventDefault();
				}else if(possible == 3){
					$('#editor-overlay').get(0).className = 'delete';
					event.preventDefault();
				}
			},

			'dragleave': function(){
				$('#editor-overlay').css('width', '0px').get(0).className = '';
			},

			'drop': function(event){
				if(pf.editor.tool == null)
					return;

				var target = $(event.target);

				if(pf.editor.tool.attr('data-type') != 'edit' && pf.editor.tool.attr('data-type') != 'delete' && target.parents('.content-holder').length != 0 && !target.hasClass('content-holder'))
					target = $(target).parents('.content-holder');

				if(target.attr('data-id') == null)
					target = target.parents('[data-id]');

				target = target.first();

				event.preventDefault();
				$('#editor-overlay').css('width', '0px').get(0).className = '';

				var data;

				if(pf.editor.tool.attr('data-type') == 'edit'){
					pf.editor.tool = null;

					function search(content, target){
						for(data in content){
							if(content[data].id == target){
								return content[data];
							}

							if(content[data].children && content[data].children.length > 0) {
								var result = search(content[data].children, target);
								if(result != null)
									return result;
							}
						}

						return null;
					}

					var result = search(pf.pages.pageData.content, target.attr('data-id'));
					if(result != null)
						pf.editor.setElementEditor(result);

					return;
				}else if(pf.editor.tool.attr('data-type') == 'delete'){
					pf.editor.tool = null;

					if(target.attr('id') == 'put-content')
						return;

					$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'delete-element', 'page': pf.pages.pageData.id, 'element': target.attr('data-id')}, function(response) {
						if(response.code == 1) {
							$('[data-id=' + target.attr('data-id') + ']').slideUp('fast');
						}else{
							pf.tooltip.showError(response.msg, 2000, 'error');
						}
					});

					return;
				}

				var values = {};
				var elementType = pf.editor.tool.attr('data-type');

				pf.editor.tool = null;
				for(var dataName in pf.pageElements[elementType]['params'])
					values[dataName] = pf.pageElements[elementType]['params'][dataName].default;

				var seq = ('sequence' in values ? values['sequence'] : 0);

				$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'create-element', 'page': pf.pages.pageData.id, 'parent': $(target).attr('data-id'), 'element-type': elementType, 'values': values, 'sequence': seq}, function(response) {
					if(response.code == 1) {
						pf.pages.pageData.content.push(response.element);

						$(target).append(pf.pages.getElement(response.element));

						pf.editor.setElementEditor(response.element);
					}else{
						pf.tooltip.showError(response.msg, 2000, 'error');
					}
				});
			}
		});

		$('#editor-block-data-form').on({
			'submit': function(event){
				event.preventDefault();

				var formDataContent = $('#editor-block-data-form-content');
				var elementId = formDataContent.attr('data-for');
				var element = pf.editor.editing;
				var seqO = ('sequence' in element.values ? element.values['sequence'] : 0);
				element.values = {};

				formDataContent.children('input,textarea').each(function(){
					if($(this).attr('type') == 'checkbox')
						element.values[$(this).attr('data-name')] = $(this).is(':checked');
					else
						element.values[$(this).attr('data-name')] = $(this).val();
				});

				var seq = ('sequence' in element.values ? element.values['sequence'] : 0);

				$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'modify-element', 'page': pf.pages.pageData.id, 'element': elementId, 'values': element.values, 'sequence': seq}, function(response) {
					if(response.code === 1) {
						if(seqO != seq)
							pf.pages.goto(pf.pages.page, true);
						else
							pf.pageElements[element.type].render.edit(element);
					}else{
						pf.tooltip.showError(response.msg, 2000, 'error');
					}
				});
			},

			'reset': function(event){
				$(this).parents('#editor-block-data').fadeOut('slow');
			}
		});

		$('#editor-page-info-form').on({
			'submit': function(event){
				event.preventDefault();

				var caption = $('#page-caption').val();
				var url = $('#page-url').val();

				$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'modify', 'page': pf.pages.pageData.id, 'caption': caption, 'url': url}, function(response) {
					if(response.code == 1) {
						pf.pages.goto(response.url, true);
						pf.tooltip.showError(response.msg, 2000, 'good');
					}else{
						pf.tooltip.showError(response.msg, 2000, 'error');
					}
				});
			},

			'reset': function(event){
				$(this).parents('#editor-page-info').fadeOut('slow');
			}
		});

		$('#editor-account-info-form').on({
			'submit': function(event){
				event.preventDefault();

				var username = $('#account-username').val();
				var password = $('#account-password').val();
				var passwordCurrent = $('#account-password-current').val();

				$.post(pf.webUrl + 'inc/ajax/account.php', {'type': 'modify', 'passwordCurrent': passwordCurrent, 'username': username, 'password': password}, function(response) {
					alert(response.msg);

					$('#editor-account-info-form').trigger('reset');
				});
			},

			'reset': function(event){
				$(this).parents('#editor-account-info').fadeOut('slow');
			}
		});

		$('#editor-page-create-form').on({
			'submit': function(event){
				event.preventDefault();

				var caption = $('#pagec-caption').val();
				var url = $('#pagec-url').val();

				$.post(pf.webUrl + 'inc/ajax/pages.php', {'type': 'create', 'caption': caption, 'url': url}, function(response) {
					if(response.code == 1) {
						pf.pages.goto(response.url, true);
						pf.tooltip.showError(response.msg, 2000, 'good');

						$('#editor-page-create-form').trigger('reset');
					}else{
						pf.tooltip.showError(response.msg, 2000, 'error');
					}
				});
			},

			'reset': function(event){
				$(this).parents('#editor-page-create').fadeOut('slow');
			}
		});
	},

	"setElementEditor": function(element){
		pf.editor.editing = element;

		if(!('sequence' in pf.pageElements[element.type]['params'])){
			pf.pageElements[element.type]['params']['sequence'] = {
				"default": 0,
				"caption": "Wat is de positie van het element?",
				"type": "number"
			}
		}

		/*if(pf.pageElements[element.type]['params'].length == 0){
			$('#editor-block-data').fadeOut('fast');
			return;
		}*/

		var html = '';

		for(var dataName in pf.pageElements[element.type]['params']){
			var dataData = pf.pageElements[element.type]['params'][dataName];
			var elmId = 'elmDat' + pf.counter.getNext();
			var value = element.values[dataName];

			if(value == null)
				value = dataData.default;

			if (dataData.type === 'string') {
				html += '<label class="heading" for="' + elmId + '">' + dataData.caption + '</label>';
				html += '<input data-name="' + dataName + '" class="inner" id="' + elmId + '" name="' + elmId + '" type="text" value="' + value + '" />';
			} else if (dataData.type === 'text') {
				html += '<label class="heading" for="' + elmId + '">' + dataData.caption + '</label>';

				if(dataData.allow_html)
					html += '<i class="inner">HTML is ingeschakeld voor dit element</i>';

				html += '<textarea data-name="' + dataName + '" class="inner" id="' + elmId + '" name="' + elmId + '">' + value + '</textarea>';
			} else if (dataData.type === 'number') {
				html += '<label class="heading" for="' + elmId + '">' + dataData.caption + '</label>';
				html += '<input data-name="' + dataName + '" class="inner" id="' + elmId + '" name="' + elmId + '" type="number" min="' + (dataData.min == null ? '0' : dataData.min) + '" max="' + (dataData.max == null ? '1000' : dataData.max) + '" value="' + value + '" />';
			} else if (dataData.type === 'bool') {
				html += '<label class="heading" for="' + elmId + '">' + dataData.caption + '</label>';
				html += '<input data-name="' + dataName + '" class="inner" id="' + elmId + '" name="' + elmId + '" type="checkbox" ' + (value == true ? 'checked="checked"' : '') + ' />';
			}
		}

		$('#editor-block-data-form-content').attr('data-type', element.type).attr('data-for', element.id).html(html).parents('#editor-block-data').fadeIn('fast');
	}
};