// JavaScript Document
var zDialog = (function () {
    var Dialog = {

        createNew: function () {

            var dialog = {},
                w = window.innerWidth
                    || document.documentElement.clientWidth
                    || document.body.clientWidth,
                h = window.innerHeight
                    || document.documentElement.clientHeight
                    || document.body.clientHeight;

            var initialise = function (options) {

                dialog.confirmation = function () {

                    if(options.confirmation){
                        options.confirmation();
                    };
                    zDialog.close();
                };

                dialog.cancel = function () {

                    if(options.cancel){
                        options.cancel();
                    };
                    zDialog.close();
                };

                if (options.shade) {
                    dialog.shade();
                };
                if(!options.width){
                    options.width = 400;
                };
				if(!options.height){
                    options.height = 160;
                };
				if(!options.title){
                    options.title = '消息';
                };
            };

            var createDialog = function (options, btn) {

                initialise(options);

                var box = document.createElement('div'),
                    html = '<div id="zDialog-head" style="width: 100%;height: 26px;font-size: 12px;line-height: 21px;position: relative;">';

                html += options.title+'<span style="padding: 1px 10px;font-weight: bold;position: absolute;right: 1px;top: -5px;cursor: pointer;" onclick="zDialog.close();">关闭</span>';
                html += '</div>';
                html += '<div id="zDialog-content" style="width: 100%;min-height: '+ (options.height-80) +'px;">'+ options.message +'</div>';
                html += '<div id="zDialog-foot" style="width: 100%;height: 45px;position: relative;">';
                html += btn;
                html += '</div>';

                box.id = 'zDialog-box';
                box.style.width = options.width + 'px';
                box.style.position = 'absolute';
                box.style.top = document.documentElement.scrollTop+200 + 'px';
                box.style.left = (w - options.width)/2 + 'px';
                box.style.zIndex = '9999';

                box.innerHTML = html;

                document.body.appendChild(box);
            };

            dialog.confirmation;

            dialog.cancel;

            dialog.close = function () {
                document.body.removeChild(document.getElementById('zDialog-box'));
                if (document.getElementById('zShade')) {
                    document.body.removeChild(document.getElementById('zShade'));
                };
            };

            dialog.shade = function () {

                var shade = document.createElement('div');
                shade.id = 'zShade';
                shade.setAttribute('class', 'opacity3');
                shade.style.width = '100%';
                shade.style.height = '100%';
                shade.style.background = '#000';
                shade.style.position = 'absolute';
                shade.style.top = '0px';
                shade.style.left = '0px';
                shade.style.zIndex = '8888';


                document.body.appendChild(shade);
            };

            dialog.alert = function (options) {

                var btn = '<span class="zDialog-ok-btn" style="padding: 3px 12px;font-weight: bold;position: absolute;right: 16px;top: 12px;cursor: pointer;font-size: 14px;" onclick="zDialog.confirmation()">确定</span>';

                options.message = '<div style="width: 90%; margin: auto; padding-top:20px;line-height: 26px; text-align: left;font-size:14px;">'+ options.message +'</div>';

                createDialog(options, btn);
            };

            dialog.confirm = function (options) {

                var btn = '<span class="zDialog-ok-btn" style="padding: 3px 12px;font-weight: bold;position: absolute;right: 80px;top: 12px;cursor: pointer;font-size: 14px;" onclick="zDialog.confirmation()">确定</span>';
                btn += '<span class="zDialog-cancel-btn" style="padding: 3px 12px;font-weight: bold;position: absolute;right: 16px;top: 12px;cursor: pointer;font-size: 14px;" onclick="zDialog.cancel()">取消</span>';

                options.message = '<div style="width: 90%; margin: auto; padding-top:20px;line-height: 26px; text-align: left;font-size:14px;">'+ options.message +'</div>';

                createDialog(options, btn);
            };

            return dialog;
        }
    };

    return Dialog.createNew();
})();

