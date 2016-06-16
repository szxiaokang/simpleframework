
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3>
            新闻管理 <small><a class="btn btn-info btn-sm" href="add">添加新闻</a> </small>
        </h3>
    </section>
    <!-- Main content -->
    <section class="content">
        <form id="searchForm" action="" method="get" class="form-inline">
            <div class="input-group">
                <select name="type" id="type" class="form-control">
                    <option value="">全部分类</option>
                    <?php
                    foreach ($type_list as $type_id => $name) {
                        echo "<option value=\"{$type_id}\"";
                        if ($type == $type_id) {
                            echo ' selected';
                        }
                        echo ">{$name}</option>";
                    }
                    ?>
                </select>
            </div>
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
                            <th width="11%">分类</th>
                            <th width="50%">标题</th>
                            <th width="12%">添加</th>
                            <th width="11%">修改</th>
                            <th width="6%">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($rows as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row["id"]?>" />							</td>
                                <td><?php echo $row["id"]?></td>
                                <td><?php echo isset($type_list[$row["type"]]) ? $type_list[$row["type"]] : '-' ?></td>
                                <td>
                                    <?php
                                    if (!empty($row['images'])) {
                                    echo "<a href=\"". $row["images"] ."\" target=\"_blank\"><span class=\"glyphicon glyphicon-picture\" title=\"有新闻图片\"></span></a>";
                                    }
                                    ?>
                                   <?php echo $row["title"]?></td>
                                <td><?php echo date('Y-m-d H:i', $row["addtime"])?></td>
                                <td><?php echo !empty($row["edittime"]) ? date('Y-m-d H:i', $row["edittime"]) : '-'?></td>
                                <td><a href="add?action=edit&id=<?php echo $row["id"]?>&url=<?php echo $url?>">修改</a></td>
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


