// JavaScript Document
(function() {
    // 页面头部
    var element = ['section', 'article', 'nav', 'header', 'footer' , 'aside' /* 其他HTML5元素 */];
    for (var i = 0, j = element.length; i < j; i++) {
        document.createElement(element[i]);
    }	
})();