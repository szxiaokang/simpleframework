<?php
/**
 * @filename register.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-31 12:53:30
 * @version 1.0
 * @Description
 * 
 */
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-6">
        <h4>登录</h4>
        <section class="content">
            <form id="loginForm" action="" method="post" class="form-horizontal">
                <input type="hidden" name="action" id="action" value="login" /> 
                <input type="hidden" name="url" id="url" value="" />
                <div class="form-group">
                    <label for="Email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-7">
                        <input name="email" type="text" class="form-control" value="" id="email" placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-7">
                        <input name="password" type="password" value="" class="form-control" id="password" placeholder="密码">
                    </div>
                </div>

                <div class="form-group">
                    <label for="captch" class="col-sm-2 control-label">验证码</label>
                    <div class="col-sm-6">
                        <input name="captch" type="text" class="form-control" id="captch" placeholder="验证码">
                    </div>
                    <div style="background:none; cursor:pointer" id="refresh_captch"><img alt="验证码" title="点击刷新" id="captch_img" src="/user/captcha" /> </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </div>

                <div id="msg">
                </div>
            </form>
        </section>
    </div>
    <div class="col-xs-2">
        <h4>快捷链接</h4>
        <ul class="nav nav-pills nav-stacked">
            <li role="presentation"><a href="/user/register">注册</a></li>
            <li role="presentation"><a href="/user">最新用户</a></li>
        </ul>	
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#refresh_captch').click(function () {
            $('#captch_img').attr('src', '/user/captcha?rand=' + Math.random());
        });

        $('#loginForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Email不能为空'
                        },
                        emailAddress: {
                            message: '请输入正确的Email地址'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: '密码不能为空'
                        },
                        stringLength: {
                            min: 6,
                            max: 20,
                            message: '6-20个字符'
                        }
                    }
                },
                captch: {
                    validators: {
                        notEmpty: {
                            message: '验证码不能为空'
                        },
                        stringLength: {
                            min: 4,
                            max: 4,
                            message: '验证码为4位数字或字母'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();

            //提交
            $('#action').val('login');
            $('#msg').hide();
            $.post('/user/login', $('#loginForm').serialize(), function (data) {
                if (data.code == 0) {
                    msgSuccess('登录成功, 正在跳转 :)', '/user/profile');
                } else if (data.code == -3) {
                    msgFail('验证码不正确');
                    $('#refresh_captch').click();
                } else {
                    msgFail(data.message);
                }
            }, 'json');

        });
    })
</script>
