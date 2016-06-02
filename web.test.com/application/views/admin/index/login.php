<?php
/**
 * @filename login.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 14:28:03
 * @version 1.0
 * @Description
 * 
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>英雄登录</title>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <style type="text/css">
            body {
                padding-top: 10%;
                padding-bottom: 40px;
                background-repeat: no-repeat;
            }
            .t {
                filter: alpha(opacity = 75);
                -moz-opacity: 0.75;
                opacity: 0.75;
            }
            .fullBg {
                position: fixed;
                top: 0;
                left: 0;
                overflow: hidden;
            }
            .col-md-4{width: 33.33%}
        </style>
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/jquery-ui.min.css" rel="stylesheet">
        <link href="/assets/css/bootstrapValidator.min.css" rel="stylesheet">
        <script src="/assets/js/jquery-1.11.3.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/bootstrapValidator.min.js"></script>
        <script src="/assets/js/jquery-ui.min.js"></script>
        <script src="/assets/js/jquery.fullbg.min.js"></script>

    </head>
    <body>
        <img id="background" />
        <div class="container-fluid">
            <div class="col-md-4"></div>
            <div class="col-md-4 t" id="_login" style="background-color:#fff; padding:20px;border-radius:5px; display:none">
                <form name="loginForm" id="loginForm" class="">
                    <input type="hidden" id="action" name="action" value="" />
                    <p class="lead text-info" style="margin-bottom:35px;">管理员登录, 请输入正确的用户名与密码 :)</p>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-user"></span>
                            </div>
                            <input type="text" class="form-control" name="username" id="username" placeholder="用户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-lock"></span>
                            </div>
                            <input type="password" class="form-control" name="password" id="password" placeholder="密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-sunglasses"></span>
                            </div>
                            <input type="text" class="form-control" id="code" name="code" placeholder="验证码">
                            <div class="input-group-addon" style="background:none; cursor:pointer" id="refresh_captch">
                                <img title="点击刷新验证码" id="captch" src="/admin/index/captcha" height="20" />
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <button class="btn btn-primary" type="submit">登 录</button>
                            <span id="msg" style="padding-left: 10px;" class="text-danger"> </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4"></div>
        </div>

        <script type="text/javascript">
            $(function () {
                var mp3_file = '/assets/images/dota2_hero_select.mp3';
                if (navigator.userAgent.indexOf('MSIE') !== -1) {
                    setTimeout(function () {
                        $("#mp3").html('<embed src="' + mp3_file + '" autostart="true" loop="true" hidden="true" /><bgsound src="' + mp3_file + '" autostart="true" loop="true" hidden="true" />');
                    }, 1000);
                } else {
                    $('#mp3').html('<audio src="' + mp3_file + '" id="audio1" hidden="false" autoplay="true" loop="true"></audio> ');
                }

                var rand = Math.floor(Math.random() * 30 + 1);
                $('#background').attr('src', "/assets/images/bg/" + rand + ".jpg");
                if (rand > 5) {
                    $('#_login').delay(800).fadeIn(2000);
                } else {
                    $('#_login').delay(800).slideDown(2000);
                }

                $('#refresh_captch').click(function () {
                    $('#captch').attr('src', '/admin/index/captcha?rand=' + Math.random());
                });

                $('#loginForm').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        //invalid : 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        username: {
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {
                                    message: '用户名不能为空'
                                },
                                stringLength: {
                                    min: 4,
                                    max: 20,
                                    message: '4-20个字符'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9_\.]+$/,
                                    message: '用户名必须是字符串或数字'
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
                        code: {
                            validators: {
                                notEmpty: {
                                    message: '验证码不能为空'
                                },
                                stringLength: {
                                    min: 4,
                                    max: 4,
                                    message: '4个字符'
                                }
                            }
                        }

                    }
                }).on('success.form.bv', function (e) {

                    e.preventDefault();
                    //添加
                    $('#msg').html('');
                    $('#action').val('login');
                    $.post('/admin/index/login', $('#loginForm').serialize(), function (data) {
                        if (data.code == 0) {
                            $('#msg').html('<span class="text-success">登录成功, 正在跳转...</span>');
                            setTimeout(function () {
                                location.href = '/admin/';
                            }, 1500);
                        } else if (data.code == -1) {
                            $('#msg').html('登录失败: 不正确的登录方式 :(');
                        } else if (data.code == -2) {
                            $('#msg').html('登录失败: :用户名与密码不能为空 :(');
                        } else if (data.code == -3) {
                            $('#msg').html('登录失败: 您输入的验证码不正确 :(');
                        } else {
                            $('#msg').html('登录失败: 用户名或密码错误 :(');
                        }
                    }, 'json');

                });
            });

            $(window).load(function () {
                $("#background").fullBg();
            });
        </script>
        <div style="display: " id="mp3"></div>
    </body>
</html>

