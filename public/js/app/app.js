// JavaScript Document
function leftMenuFun(selector) {
    this.selector = selector;
    //todo 自动加载页面用的JS，最后可以开起来
    this.renew(this.selector, $Global.urls.index);
    this.leftEvent(this.selector);
};

leftMenuFun.prototype = {
    renew: function (selector, url, data) {
        $.ajax({
            async: true,
            type: "GET",
            url: url,
            data: data,
            dataType: 'html',
            beforeSend: function () {
                loader.open();
            },
            complete: function () {
                loader.close();
            },
            success: function (msg) {
                selector.html(msg);
				initialize.layoutFun();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                selector.html('<p style="padding:100px;">请求结果：' + textStatus + '<br />原因：' + url + ' ' + errorThrown + '</p>');
            }
        });
    },
    reNewPost: function (selector, url, data) {
        var loader = new loaderFun();
        $.ajax({
            async: true,
            type: "POST",
            url: url,
            data: data,
            dataType: 'html',
            beforeSend: function () {
                loader.open();
            },
            complete: function () {
                loader.close();
            },
            success: function (msg) {
                selector.html(msg);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                selector.html('<p style="padding:100px;">请求结果：' + textStatus + '<br />原因：' + url + ' ' + errorThrown + '</p>');
            }
        });
    },
    leftEvent: function (selector) {
        var renew = this.renew;
        $('.link[name="left-menu"]').click(function () {
            var url = $(this).attr('url');
            $('.link[name="left-menu"]').removeClass('link-active');
            $(this).addClass('link-active');
            renew(selector, url);
        });
    }
};

//宝贝列表翻页后仍然显示选择过的宝贝
function afterPageItemsList(id, data) {
    var itemObject = $("body").data("items");
    $(".item-view").each(function (index, element) {
        var itemCode = $(this).children(".item-pad").children("img").attr("id");
        if (itemObject[itemCode]) {
            $(this).children(".item-selected").show();
            $(this).addClass("item-pad-selected");

        }
    });
}

//添加或修改活动中的宝贝
function setItem(obj) {
    var discount = $(obj).parents("tr").find(".discount-value").val();
    var buyLimit = $(obj).parents("tr").find(".limit-value").val();
    var activityId = $("#activity_name_list").val();
    var operate = $(obj).attr("class");
    $.ajax({
        type: "POST",
        url: $(obj).attr("href"),
        data: {"activityId": activityId, "discount": discount * 1000, "buyLimit": buyLimit},
        dataType: 'json',
        success: function (data) {
            if(data.status == 'success'){
                switch(operate){
                    case 'adjust':
                        window.location.reload();
                        break;
                    case 'reduce':
                        $(obj).parents("tr").hide();
                        break;
                };
            }else{
                zDialog.alert({
                    width:400,
                    height:160,
                    shade: true,
                    title: '失败信息',
                    message: data.msg
                });
            };
        }
    });
};
function setItemAll(obj) {
    var discount = $(obj).parents("tr").find(".discount-value").val();
    var buyLimit = $(obj).parents("tr").find(".limit-value").val();
    var activityId = $("#activity_name_list").val();
    var operate = $(obj).attr("class");
    $.ajax({
        type: "POST",
        url: $(obj).attr("href"),
        data: {"activityId": activityId, "discount": discount * 1000, "buyLimit": buyLimit},
        dataType: 'json',
        success: function (data) {
            if(data.status == 'success'){
                switch(operate){
                    case 'adjust':
                        break;
                    case 'reduce':
                        $(obj).parents("tr").hide();
                        break;
                };
            };
        }
    });
}

//基础a标签跳转
function refreshPage(obj) {
    var url = $(obj).attr("href");
    leftMenu.renew(
        leftMenu.selector,
        url,
        {}
    );
}


/*下面正式开始*/

var leftMenu;

$(document).ready(function () {
    leftMenu = new leftMenuFun($('#right-content'));

    $(".item-view").live({
        mouseenter: function () {
            if (!$(this).hasClass("item-pad-selected")) {
                $(this).addClass("item-pad-hover");
                $(this).children(".item-actived").show();
            }
        },
        mouseleave: function () {
            $(this).removeClass("item-pad-hover");
            $(this).children(".item-actived").hide();
        }
    });

    $(".item-pad").live({
        click: function () {
            var items = $(this).parent(".item-view");
            var itemCode = $(this).children("img").attr("id");

            if (items.hasClass("item-pad-selected")) {
                items.removeClass("item-pad-selected");
                items.addClass("item-pad-hover");
                $(this).siblings(".item-selected").hide();
                $(this).siblings(".item-actived").show();

                var itemObject = $("body").data("items");
                delete itemObject[itemCode];

            } else {

                items.removeClass("item-pad-hover");
                items.addClass("item-pad-selected");
                $(this).siblings(".item-selected").show();
                $(this).siblings(".item-actived").hide();

                var itemObject = $("body").data("items");
                if (itemObject == undefined) {
                    var itemObject = {};
                }

                itemObject[itemCode] = true;
                $("body").data("items", itemObject);
            }
        }
    });

    //选择活动保存session
    $("#activity_name_list").live({
        "change": function (e) {
            var activityId = $(this).val();
            var url = $(this).attr("title");
            $.ajax({
                type: "GET",
                url: url,
                data: {"activityId": activityId},
            });
        }
    });

    //提交form
    $("#activity-form :input[type='submit']").live({
        "click": function () {
            leftMenu.reNewPost(
                leftMenu.selector,
                $("#activity-form").attr("action"),
                $("#activity-form").serialize()
            );
            console.log($("#activity-form").serialize());
            return false;
        }
    });

    //删除活动按钮
    $("#discount-activity-grid .delete").live({
        "click": function (e) {
            if (!confirm("是否删除此活动?")) {
                return false;
            } else {
                leftMenu.renew(
                    leftMenu.selector,
                    $(this).attr("href")
                );
			}
			return false;
        }
    });
	
	//ajax初始化数据
	$("#download-data").click(function(e) {
        leftMenu.renew(

        	leftMenu.selector,
            $(this).attr("title")
        );
    });
	
	$('.view, .update').live('click', function (){
		var url = $(this).attr("href");
		leftMenu.renew(
			leftMenu.selector,
			url,
			{}
		);
	});
    
})
;



