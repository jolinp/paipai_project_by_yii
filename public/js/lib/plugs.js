/**
 * Author 南吴
 * Date 2014-01-16
 **/

function maskFun () {
    this.maskDom = '<div id="z-mask" class="opacity" style="position: absolute;width:100%;height:100%;left:0px;top:0px;z-index: 2000; background: rgb(0,0,0)"></div>';
};
maskFun.prototype = {
    add: function () {
        $('body').append(this.maskDom);
    },
    delMask: function () {
        $('#z-mask').remove();
    }
};


function loaderFun (value) {

    this.value = value ? value : '正在加载';
    this.loaderDom = '<div id="z-loader" ' +
        'style="width: 100px;background:rgb(0,0,0);color:rgb(255,255,255);font-size:1.4em;padding:8px;position: absolute;z-index:2100;' +
        'text-indent: 25px;border-radius: 4px;overflow: hidden; text-overflow: ellipsis; white-space:nowrap;top:30%;left:49%;">' +
        '<img style="position: absolute; left: 6px; top: 6px;" src="images/preloader.gif"><span class="z-loader-txt">'+ this.value +'</span></div>';
};
loaderFun.prototype = {
    open: function (state) {
		if(!$('#z-loader').is()){
			if (state) {
				var mask = new maskFun();
				mask.add();
			};
			$('body').append(this.loaderDom);
			$('#z-loader').css('top', (document.documentElement.scrollTop+200)+'px');
		};
    },
    close: function () {
        var mask = new maskFun();
        mask.delMask();
        $('#z-loader').remove();
    }
};
