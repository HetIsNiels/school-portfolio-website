if(!"pf" in window)
	var pf = {'webUrl': ''};

pf.initialize = (function(){
	pf.pages.listen();
	pf.pages.goto(window.location.href.replace(pf.webUrl, ''));
	pf.slider.listen();
	pf.login.listen();
	pf.popups.listen();
	pf.tooltip.listen();
});