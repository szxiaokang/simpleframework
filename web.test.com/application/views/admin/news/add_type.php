<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3>
            添加新闻分类 <small><a class="btn btn-info btn-sm" href="list_type">分类管理</a> </small>
        </h3>
    </section>
    <!-- Main content -->

    <section class="content">
        <form id="addForm" action="" method="post" class="form-horizontal">
            <input type="hidden" name="action" id="action" value="<?php echo isset($action) ? $action : "add" ?>" /> 
            <input type="hidden" name="id" id="id" value="<?php echo isset($row["id"]) ? $row["id"] : "" ?>" />
            <input type="hidden" name="url" id="url" value="<?php echo isset($url) ? $url : "" ?>" />
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">类型名称</label>
                <div class="col-sm-10">
                    <input name="name" type="text" value="<?php echo isset($row["name"]) ? $row["name"] : "" ?>" class="form-control" id="name" placeholder="新闻分类名称">
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
    <!-- /.content -->
</aside>

<script type="text/javascript">
    $(function () {
        $('#addForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: {
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {
                            message: '名称不能为空'
                        },
                        stringLength: {
                            min: 4,
                            max: 20,
                            message: '4-20个字符'
                        },
                        callback: {
                            message: '此名称已经存在'
                        }
                    }
                }}
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            //修改
            if ($('#id').val() != '' && $('#action').val() == 'edit') {
                $.post('addType', $('#addForm').serialize(), function (data) {
                    if (data.code == 0) {
                        msgAlert('修改成功', $('#url').val());
                    } else {
                        msgFail('更新失败,请检查输入的内容');
                    }
                }, 'json');
                return;
            }

            //添加
            $.post('addType', {
                action: "check",
                name: $('#name').val(),
                id: $('#id').val()
            }, function (data) {
                if (data.code != 0) {
                    $('#addForm').data('bootstrapValidator').updateStatus('name', 'INVALID', 'callback');
                    return false;
                } else {
                    $('#action').val('add');
                    $.post('addType', $('#addForm').serialize(), function (data) {
                        if (data.code == 0) {
                            msgSuccess('addForm', '添加成功');
                        } else {
                            msgFail('添加失败,请检查输入的内容或重新输入类型名称');
                        }
                    }, 'json');
                }
            }, 'json');
        });
    })
</script>

