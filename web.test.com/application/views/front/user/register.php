<?php
/**
 * @filename register.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-31 12:55:47
 * @version 1.0
 * @Description
 * 
 */
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-6">
        <h4>注册</h4>

        <section class="content">
            <form id="addAdmin" action="" method="post" class="form-horizontal">
                <input type="hidden" name="action" id="action" value="register" /> 
                <div class="form-group">
                    <label for="Email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-8">
                        <input name="email" type="email" class="form-control" value="" id="email" placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-8">
                        <input name="password" type="password" value="" class="form-control" id="password" placeholder="密码">
                    </div>
                </div>

                <div class="form-group">
                    <label for="repassword" class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-8">
                        <input name="repassword" type="password" value="" class="form-control" id="repassword" placeholder="确认密码">
                    </div>
                </div>
                <div class="form-group">
                    <label for="repassword" class="col-sm-2 control-label">性别</label>
                    <div class="col-sm-8">
                        <label class="radio-inline">
                            <input type="radio" name="sex" id="sex1" value="1" checked=""> 男
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="sex" id="sex2" value="2"> 女
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="captch" class="col-sm-2 control-label">验证码</label>
                    <div class="col-sm-7">
                        <input name="captcha" type="text" class="form-control" id="captcha" placeholder="验证码">
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
            <li role="presentation"><a href="/user/login">登录</a></li>
            <li role="presentation"><a href="/user/index">最新用户</a></li>
        </ul>	
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#refresh_captch').click(function () {
            $('#captch_img').attr('src', '/user/captcha?rand=' + Math.random());
        });

        $('#addAdmin').bootstrapValidator({
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
                        },
                        callback: {
                            message: '此Email已经存在'
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
                        },
                        identical: {
                            field: 'repassword',
                            message: '您两次输入的密码不一致'
                        }
                    }
                },
                repassword: {
                    validators: {
                        notEmpty: {
                            message: '确认密码不能为空'
                        },
                        identical: {
                            field: 'password',
                            message: '您两次输入的密码不一致'
                        }
                    }
                },
                captcha: {
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
            $('#action').val('register');
            $('#msg').hide();
            $.post('/user/register', $('#addAdmin').serialize(), function (data) {
                if (data.code == 0) {
                    msgSuccess('注册成功', '/user/profile');
                } else if (data.code == -3) {
                    msgFail('验证码不正确');
                    $('#refresh_captch').click();
                } else if (data.code == -10) {
                    $('#addAdmin').data('bootstrapValidator').updateStatus('email', 'INVALID', 'callback');
                }  else {
                    msgFail(data.message);
                    return false;
                }
            }, 'json');

        });
    })
</script>  
