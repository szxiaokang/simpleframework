function msgSuccess(msg, url) {
    var html = '<div class="alert alert-success"><i class="glyphicon glyphicon-ok"></i> ' + msg + ' :)</div>';
    $('#msg').show().html(html);
    setTimeout(function () {
        location.href = url;
    }, 2500);
}

function msgFail(msg) {
    html = '<div class="alert alert-danger"><i class="glyphicon glyphicon-remove"></i> ' + msg + ' :(</div>';
    $('#msg').html(html).show('slow');
}

function logout() {
    var html = '<p style="font-size:16px;margin-top:20px"><span style="float:left; margin:0 7px 50px 0;" class="ui-icon ui-icon-alert"></span>确定要退出吗?</p>';
    $('#alert_message').html(html);
    $("#alert_message").dialog({
        autoOpen: true,
        modal: true,
        width:480,
        title: '确认',
        buttons: [{
                text: "确定",
                click: function () {
                    location.href = "/user/logout";
                }
            }, {
                text: "取消",
                click: function () {
                    $(this).dialog("close");
                }
            }]
    });
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