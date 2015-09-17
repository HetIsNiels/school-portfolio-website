pf.login = {
	"authCode4": null,
	"authCode7": null,

	"listen": function(){
		pf.login.authCode4 = Math.round(Math.random() * 8000 + 1000);

		pf.login.auth.lookupActiveSession(function(response){
			if(response.code == 1) {
				if("editor" in pf) {
					pf.editor.start();
					pf.login.dialog.hide();
				}else{
					$.getScript(response.editorSource, function(){
						pf.login.dialog.hide();
						pf.editor.start();
					});
				}
			}
		});

		$(document).on({
			'keydown': function(event){
				if(event.ctrlKey && event.altKey && event.keyCode == 73){
					event.preventDefault();

					if(pf.login.authCode7 != null && pf.login.authCode7 != '')
						return;

					pf.login.dialog.show();
				}
			}
		});

		$('#login-form').on(
			{
				'submit': function(event){
					event.preventDefault();

					pf.login.auth.login($('#login-username').val(), $('#login-password').val(), function(response){
						if(response.code == 1 || response.code == 3) {
							pf.login.cookie.show();

							if("editor" in pf) {
								pf.editor.start();
								pf.login.dialog.hide();
							}else{
								$.getScript(response.editorSource, function(){
									pf.login.dialog.hide();
									pf.editor.start();
								});
							}
						}else{
							pf.login.notif.show(response.msg);
						}
					});
				},

				'reset': function(event){
					pf.login.dialog.hide();
				}
			}
		);
	},

	"dialog": {
		"show": function(){
			$('#login-part').fadeIn('fast');
			$('#login-username').focus();
		},

		"hide": function(){
			$('#login-part').fadeOut('slow');
		}
	},

	"auth": {
		"login": function(username, password, callback){
			$.post(pf.webUrl + 'inc/ajax/authLogin.php', {'type': 'login', 'authCode4': pf.login.authCode4, 'username': username, 'password': password}, function(responseJSON) {
				if (responseJSON.authCode4 == pf.login.authCode4){
					pf.login.authCode7 = responseJSON.authCode7;

					if(callback != null)
						callback(responseJSON);
				}else{
					console.log(responseJSON.authCode4 + ' != ' + pf.login.authCode4);
				}
			});
		},

		"logout": function(callback){
			$.post(pf.webUrl + 'inc/ajax/authLogin.php', {'type': 'logout', 'authCode4': pf.login.authCode4, 'authCode7': pf.login.authCode7}, function(responseJSON) {
				if (responseJSON.authCode4 == pf.login.authCode4){
					pf.login.authCode7 = null;

					if("editor" in pf && pf.editor.isOpen)
						pf.editor.stop();

					if(callback != null)
						callback(responseJSON);
				}else{
					console.log(responseJSON.authCode4 + ' != ' + pf.login.authCode4);
				}
			});
		},

		"lookupActiveSession": function(callback){
			$.post(pf.webUrl + 'inc/ajax/authLogin.php', {'type': 'lookup', 'authCode4': pf.login.authCode4}, function(responseJSON) {
				if (responseJSON.authCode4 == pf.login.authCode4){
					pf.login.authCode7 = responseJSON.authCode7;

					if(callback != null)
						callback(responseJSON);
				}else{
					console.log(responseJSON.authCode4 + ' != ' + pf.login.authCode4);
				}
			});
		}
	},

	"cookie": {
		"isShown": false,

		"show": function(){
			return; // Disable feature

			if(pf.login.cookie.isShown)
				return;

			pf.login.cookie.isShown = true;

			$('#cookie').addClass('show');

			setTimeout(function(){
				pf.login.cookie.isShown = false;
				$('#cookie').removeClass('show');
			}, 2000);
		}
	},

	"notif": {
		"isShown": false,

		"show": function(msg){
			if(pf.login.notif.isShown)
				return;

			pf.login.notif.isShown = true;

			$('#login-notif').html(msg).addClass('shown');

			setTimeout(function(){
				pf.login.notif.isShown = false;
				$('#login-notif').removeClass('shown');
			}, 2000);
		}
	}
};