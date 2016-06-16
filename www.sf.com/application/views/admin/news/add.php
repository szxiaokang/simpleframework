<meta http-equiv="X-UA-Compatible" content="IE=8" />
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3> 添加新闻 <small><a class="btn btn-info btn-sm" href="lists">新闻管理</a> </small> </h3>
    </section>
    <!-- Main content -->

    <section class="content">
        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" id="addForm">
            <input id="__reset" name="__reset" type="reset" style="display:none;" /> 
            <input type="hidden" name="action" id="action" value="<?php echo isset($action) ? $action : 'add' ?>" />
            <input type="hidden" name="id" id="id" value="<?php echo isset($row['id']) ? $row['id'] : '' ?>" />
            <input type="hidden" name="url" id="url" value="<?php echo isset($url) ? $url : "" ?>" />
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
                <div class="col-sm-10">
                    <input name="title" type="text" value="<?php echo isset($row["title"]) ? $row['title'] : '' ?>" class="form-control" id="title" placeholder="新闻分类名称">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">副标题</label>
                <div class="col-sm-10">
                    <input name="sub_title" type="text" value="<?php echo isset($row["intro"]) ? $row['intro'] : '' ?>" class="form-control" id="sub_title" placeholder="副标题">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">类型</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-control">
                        <option value="">请选择分类</option>
                        <?php
                        foreach ($type_list as $type_id => $name) {
                            echo "<option value=\"{$type_id}\"";
                            if (isset($row['type']) && $row['type'] == $type_id) {
                                echo ' selected';
                            }
                            echo ">{$name}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">新闻图片</label>
                <div class="col-sm-5">
                    <input type="text" name="images" class="form-control" readonly="true" id="images" value="<?php echo isset($row["images"]) ? $row["images"] : '' ?>" /> 
                </div>
                <div class="col-sm-2">
                    <input type="button" class="btn btn-link" id="uploadButton" value="选择文件..." />
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">内容</label>
                <div class="col-sm-10">
                    <textarea name="content" class="form-control" style="height: 260px" id="content"><?php echo isset($row["content"]) ? $row["content"] : '' ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="_submit" class="btn btn-primary">提交</button>
                </div>
            </div>
            <div id="msg"> </div>
        </form>
    </section>
    <!-- /.content -->
</aside>
<link rel="stylesheet" href="/assets/kindeditor-4.1.10/themes/default/default.css" />
<script charset="utf-8" src="/assets/kindeditor-4.1.10/kindeditor-min.js"></script>
<script charset="utf-8" src="/assets/kindeditor-4.1.10/lang/zh_CN.js"></script>
<script type="text/javascript">
    var editor;
    KindEditor.ready(function (K) {
        editor = K.create('textarea[name="content"]', {
            allowFileManager: true,
            uploadJson: 'add?action=upload',
            fileManagerJson: 'add?action=manage',
            items: [
                'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'emoticons', 'image', 'link']
        });

        var uploadbutton = K.uploadbutton({
            button: K('#uploadButton')[0],
            fieldName: 'imgFile',
            url: 'add?action=upload',
            afterUpload: function (data) {
                if (data.error === 0) {
                    var url = K.formatUrl(data.url, 'absolute');
                    K('#images').val(url);
                } else {
                    _alert(data.message);
                }
            }
        });
        uploadbutton.fileBox.change(function (e) {
            uploadbutton.submit();
        });

    });

    var _submit = true;
    $(function () {
        $('#addForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    validators: {
                        notEmpty: {message: '标题不能为空'},
                        stringLength: {
                            min: 2,
                            max: 256,
                            message: '2-256个字符'
                        }
                    }
                },
                type: {
                    validators: {
                        notEmpty: {message: '请选择新闻类型'}
                    }
                }
            }
        }).on('success.form.bv', function (e) {

            if (editor.html() == '') {
                _alert('新闻内容不能为空:)');
                $('#_submit').removeAttr('disabled');
                return false;
            }
            e.preventDefault();
            //修改

            if ($('#id').val() != '' && $('#action').val() == 'edit') {
                $('#content').val(editor.html());
                $.post('add', $('#addForm').serialize(), function (data) {
                    if (data.code == 0) {
                        msgAlert('修改成功', $('#url').val());
                    } else {
                        msgFail(data.msg);
                    }
                }, 'json');
                return;
            }

            //添加
            $('#action').val('add');
            $('#content').val(editor.html());
            $.post('add', $('#addForm').serialize(), function (data) {
                if (data.code == 0) {
                    msgSuccess('addForm', '添加成功');
                } else {
                    msgFail(data.msg);
                }
            }, 'json');
        });
    })
</script>

