function msgSuccess(form, msg) {
    var html = '<div class="alert alert-success"><i class="glyphicon glyphicon-ok"></i> ' + msg + ' :)</div>';
    $('#msg').show().html(html).delay(2000).hide('slow');
    setTimeout(function () {
        $('#' + form).data('bootstrapValidator').resetForm(true);
        if ($('#__reset')) {
            $('#__reset').click();
        } else {
            document.getElementById(form).reset();
        }
    }, 2500);
}

function msgAlert(msg, url) {
    var html = '<div class="alert alert-success"><i class="glyphicon glyphicon-ok"></i> ' + msg + ' :)</div>';
    $('#msg').show().html(html).delay(2000).hide('slow');
    setTimeout(function () {
        location.href = url;
    }, 2000);
}

function msgFail(msg) {
    html = '<div class="alert alert-danger"><i class="glyphicon glyphicon-remove"></i> ' + msg + ' :(</div>';
    $('#msg').show().html(html).delay(4000).hide('slow');
}

function _alert(msg) {
    var html = '<p style="font-size:16px;margin-top:20px"><span style="float:left; margin:0 7px 50px 0;" class="ui-icon ui-icon-alert"></span> ' + msg + '</p>';
    $('#alert_message').html(html);
    $('#alert_message').dialog({
        title: '提示',
        buttons: {
            OK: function () {
                $(this).dialog("close");
            }
        }
    });
}

function logout() {
    var html = '<p style="font-size:16px;margin-top:20px"><span style="float:left; margin:0 7px 50px 0;" class="ui-icon ui-icon-alert"></span>确定要退出吗?</p>';
    $('#alert_message').html(html);
    $("#alert_message").dialog({
        autoOpen: true,
        modal: true,
        title: '确认',
        width: 420,
        height:220,
        buttons: [{
                text: "确定",
                click: function () {
                    location.href = "/admin/index/logout?action=logout";
                }
            }, {
                text: "取消",
                click: function () {
                    $(this).dialog("close");
                }
            }]
    });
}

/**
 * desc: 全选/反选 objName 全选/反选的 name
 */
function selectBox(obj, ids) {
    $("input[name='" + ids + "']").each(function () {
        this.checked = obj.checked;
    });
}

/**
 * 批量删除提示+提交
 * @param string target_form 表单id
 * @param string target_checkbox_ids 复选框id
 * @param string act_value 动作标签
 * @returns Boolean
 */
function batchDelete(target_form, target_checkbox_ids, act_value) {

    var flag = false;
    $("input[name='" + target_checkbox_ids + "']").each(function () {
        if (this.checked) {
            flag = true;
            return;
        }
    });
    if (!flag) {
        var html = '<p style="font-size:16px;margin-top:20px"><span style="float:left; margin:0 7px 50px 0;" class="ui-icon ui-icon-alert"></span>请至少选择一项 :)</p>';
        $('#alert_message').html(html);
        $('#alert_message').dialog({
            title: '提示',
            width: 420,
            height:220,
            buttons: {
                OK: function () {
                    $(this).dialog("close");
                }
            }
        });
        return false;
    }

    var html = '<p style="font-size:16px;margin-top:20px"><span style="float:left; margin:0 7px 50px 0;" class="ui-icon ui-icon-alert"></span>确定要删除吗?(不可恢复)</p>';
    $('#alert_message').html(html);
    $("#alert_message").dialog({
        autoOpen: true,
        modal: true,
        title: '确认',
        width: 420,
        height:220,
        buttons: [{
                text: "确定",
                click: function () {
                    $('#action').val(act_value);
                    $('#' + target_form).submit();
                }
            }, {
                text: "取消",
                click: function () {
                    $(this).dialog("close");
                }
            }]
    });
    return false;
}


//左部菜单
(function ($) {
    "use strict";
    $.fn.tree = function () {
        return this.each(function () {
            var btn = $(this).children("a").first();
            var menu = $(this).children(".treeview-menu").first();
            var isActive = $(this).hasClass('active');

            //initialize already active menus
            if (isActive) {
                menu.show();
                btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
            }
            //Slide open or close the menu on link click
            btn.click(function (e) {
                e.preventDefault();
                if (isActive) {
                    //Slide up to close menu
                    menu.slideUp();
                    isActive = false;
                    btn.children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
                    btn.parent("li").removeClass("active");
                } else {
                    //Slide down to open menu
                    menu.slideDown();
                    isActive = true;
                    btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
                    btn.parent("li").addClass("active");
                }
            });

            /* Add margins to submenu elements to give it a tree look */
            menu.find("li > a").each(function () {
                var pad = parseInt($(this).css("margin-left")) + 10;

                $(this).css({"margin-left": pad + "px"});
            });

        });

    };


}(jQuery));

