<?php
/**
 * @filename edit.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-6-1 13:29:18
 * @version 1.0
 * @Description
 * 
 */
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-6">
        <h4>编辑信息</h4>

        <section class="content">
            <form id="editAdmin" action="" method="post" class="form-horizontal">
                <input type="hidden" name="action" id="action" value="" /> 
                <div class="form-group">
                    <label for="Email" class="col-sm-2 control-label">头像</label>
                    <div class="col-sm-3">
                        <input name="avatar" type="hidden" id="avatar">
                        <input type="button" class="btn btn-link" id="uploadButton" value="选择图片..." />
                    </div>
                    <div class="col-sm-3">
                        <img id="_avatar" src="<?php echo empty($row['avatar']) ? "/assets/images/{$row['sex']}.png" : $row['avatar'] ?>" width="45" height="45" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="Email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-8">
                        <input name="email" type="email" class="form-control" value="<?php echo $row['email'] ?>" readonly="" id="email" placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-8">
                        <input name="password" type="password" class="form-control" value="######" id="password" placeholder="密码">
                    </div>
                </div>

                <div class="form-group">
                    <label for="repassword" class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-8">
                        <input name="repassword" type="password" class="form-control" value="######" id="repassword" placeholder="确认密码">
                    </div>
                </div>
                <div class="form-group">
                    <label for="repassword" class="col-sm-2 control-label">性别</label>
                    <div class="col-sm-8">
                        <label class="radio-inline">
                            <input type="radio" name="sex" id="sex1" value="1"<?php echo $row['sex'] == 1 ? ' checked' : '' ?>> 男
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="sex" id="sex2" value="2"<?php echo $row['sex'] == 2 ? ' checked' : '' ?>> 女
                        </label>
                    </div>
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
</div>
<link rel="stylesheet" href="/assets/kindeditor-4.1.10/themes/default/default.css" />
<script charset="utf-8" src="/assets/kindeditor-4.1.10/kindeditor-min.js"></script>
<script type="text/javascript">
    $(function () {
        var editor;
        KindEditor.ready(function (K) {
            var uploadbutton = K.uploadbutton({
                button: K('#uploadButton')[0],
                fieldName: 'imgFile',
                url: '/user/edit?action=upload',
                afterUpload: function (data) {
                    if (data.error === 0) {
                        var url = K.formatUrl(data.url, 'absolute');
                        K('#avatar').val(url);
                        $('#_avatar').attr('src', url);
                    } else {
                        _alert(data.message);
                    }
                }
            });
            uploadbutton.fileBox.change(function (e) {
                uploadbutton.submit();
            });

        });
        $('#editAdmin').bootstrapValidator({
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
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();

            //提交
            $('#action').val('edit');
            $('#msg').hide();
            $.post('/user/edit', $('#editAdmin').serialize(), function (data) {
                if (data.code == 0) {
                    msgSuccess('修改成功', '/user/profile');
                } else {
                    msgFail(data.message);
                    return false;
                }
            }, 'json');

        });
    })
</script>  

