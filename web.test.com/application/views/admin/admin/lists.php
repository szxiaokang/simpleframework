<?php
/**
 * @filename lists.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-24 10:17:47
 * @version 1.0
 * @Description
 * 管理员列表页面
 */
?>

<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3>
            管理员列表 <small><a class="btn btn-info btn-sm" href="add">添加管理员</a> </small>
        </h3>
    </section>
    <!-- Main content -->
    <section class="content">
        <form id="searchForm" action="" method="get" class="form-inline">
            <div class="input-group">
                <input type="text" name="keywords" id="keywords" placeholder="用户名或Email" value="<?php echo isset($keywords) ? $keywords : '' ?>" class="form-control input-sm"> <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-search"></i>
                    </button> </span>
            </div>
            <div id="msg"></div>
        </form>
        <form id="listAdmin" action="" method="post">
            <input type="hidden" name="action" id="action" value="" />
            <input type="hidden" name="url" id="url" value="<?php echo isset($url) ? $url : ''; ?>" />
            <div class="view" style="margin-top:20px">
                <table width="100%" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="8%">&nbsp;</th>
                            <th width="10%">编号</th>
                            <th width="24%">用户名</th>
                            <th width="20%">Email</th>
                            <th width="15%">添加时间</th>
                            <th width="14%">最后登录</th>
                            <th width="9%">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($rows) && count($rows)) {
                            foreach ($rows as $row) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?php echo $row["adminid"] ?>" />
                                    </td>
                                    <td><?php echo $row["adminid"] ?></td>
                                    <td><?php echo $row["username"] ?></td>
                                    <td><?php echo $row["email"] ?></td>
                                    <td><?php echo date('Y-m-d H:i', $row["addtime"]) ?></td>
                                    <td><?php echo empty($row["lastlogin"]) ? '-' : date('Y-m-d H:i', $row['lastlogin']) ?></td>
                                    <td><a href="add?action=edit&id=<?php echo $row["adminid"] ?>&url=<?php echo $url ?>">修改</a></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan=7>空空如也...</td></tr>';
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
                            <button type="button" class="btn btn-danger btn-sm" onClick="batchDelete('listAdmin', 'ids[]', 'delete')">删除</button>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-8">
                        <nav>
                            <?php echo isset($page) ? $page : ''; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
</aside>

