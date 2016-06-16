<?php

/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-6-2 9:40:46
 * @version 1.0
 * @Description
 * 
 */
?>

<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3>用户管理</h3>
    </section>
    <!-- Main content -->
    <section class="content">
        <form id="searchForm" action="" method="get" class="form-inline">
            <div class="input-group">
                <input type="text" name="keywords" id="keywords" placeholder="标题" value="<?php echo isset($keywords) ? $keywords : '' ?>" class="form-control input-sm"> <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-search"></i>
                    </button> </span>
            </div>
            <div id="msg"></div>
        </form>
        <form id="listForm" action="" method="post">
            <input type="hidden" name="action" id="action" value="" />
            <input type="hidden" name="url" id="url" value="<?php echo $url?>" />
                   <div class="view" style="margin-top:20px">
                <table width="100%" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">&nbsp;</th>
                            <th width="5%">编号</th>
                            <th width="11%">Email</th>
                            <th width="5%">性别</th>
                            <th width="10%">注册时间</th>
                            <th width="10%">最后登录时间</th>
                            <th width="10%">登录次数</th>
                            <th width="10%">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($rows as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row["uid"]?>" />							</td>
                                <td><?php echo $row["uid"]?></td>
                                <td><?php echo $row['email'] ?></td>
                                <td><?php echo $row['sex'] == 1 ? '男' : '女'?></td>
                                <td><?php echo date('Y-m-d H:i', $row["addtime"])?></td>
                                <td><?php echo !empty($row["last_login"]) ? date('Y-m-d H:i', $row["last_login"]) : '-'?></td>
                                <td><?php echo $row["login_num"]?></td>
                                <td><a href="/admin/user/detail?action=detail&uid=<?php echo $row["uid"]?>&url=<?php echo $url?>">查看</a></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-6 col-md-4">
                        <div class="form-group">
                            <label for="_ids"> <input type="checkbox" name="_ids" id="_ids" title="select all" onClick="selectBox(this, 'ids[]')"> 全选</label> &nbsp;&nbsp;
                            <button type="button" class="btn btn-danger btn-sm" onClick="batchDelete('listForm', 'ids[]', 'delete')">删除</button>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-8">
                        <nav>
                            <?php echo $page ?>
                        </nav>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
</aside>



