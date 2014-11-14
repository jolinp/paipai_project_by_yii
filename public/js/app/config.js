// JavaScript Document
var loader, app;
function Initialize () {
	
	this.layoutFun();
	this.checkBox();
	this.loadScript();
};

Initialize.prototype = {
	
	layoutFun: function () {
		
		document.getElementById('left').style.height = 'auto';
		
		this.mainHeight = document.getElementById('right').clientHeight;
		
		document.getElementById('left').style.height = this.mainHeight + 'px';
	},
	
	checkBox: function () {
		$('.checker').live('click', function () {
			var selector = $(this);
			if (selector.hasClass('checked')) {
				
				if (selector.hasClass('select-all')) {
					selector = $('.checker');
				};
				selector.removeClass('checked');
			} else {
				
				if (selector.hasClass('select-all')) {
					selector = $('.checker');
				};
				selector.addClass('checked');
			};
			
		});
	},
	
	loadScript: function () {
		loadScript('js/lib/plugs.js', function(){
			loader = new loaderFun();
			loadScript('js/app/app.js', function(){});
		});
		
	}
};
