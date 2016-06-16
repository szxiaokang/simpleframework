<?php
/**
 * @filename main.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-6 16:50:06
 * @version 1.0
 * @Description
 * 
 */
?>

<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="padding:1px 15px 12px">
        <h3>
            后台统计
        </h3>
    </section>
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="glyphicon glyphicon-user"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">用户总数</span>
                        <span class="info-box-number"><?php echo $user_num ?></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-globe"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">新闻总数</span>
                        <span class="info-box-number"><?php echo $news_count['count_num'] ?></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="glyphicon glyphicon-circle-arrow-down"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">新闻总点击量</span>
                        <span class="info-box-number"><?php echo $news_count['count_click'] ?></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>



            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="glyphicon glyphicon-font"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">管理员总数</span>
                        <span class="info-box-number"><?php echo $admin_num ?></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>
        <!--table-->
        <div class="row" style="margin-top:30px">
            <div class="col-md-6">
                <h4>最新新闻</h4>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td>编号</td>
                            <td>标题</td>

                        </tr>
                    </thead>
                    <tbody>	
                        <?php
                        if (!empty($news_list['last_list'])) {
                            foreach ($news_list['last_list'] as $row) {
                                ?>
                                <tr>
                                    <td><?php echo $row['id'] ?></td>
                                    <td><?php echo $row['title'] ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan=\"2\">空空如也...</td></tr>";
                        }
                        ?>
                    </tbody>				
                </table>
            </div>
            <div class="col-md-6">
                <h4>最新用户</h4>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td>编号</td>
                            <td>Email</td>
                            <td>注册时间</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($user_list)) {
                            foreach ($user_list as $row) {
                                ?>
                                <tr>
                                    <td><?php echo $row['uid'] ?></td>
                                    <td><?php echo $row['email'] ?></td>
                                    <td><?php echo date('Y-m-d H:i', $row['addtime']) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan=\"3\">空空如也...</td></tr>";
                        }
                        ?>	
                    </tbody>		
                </table>
            </div>
        </div>

    </section>
    <!-- /.content -->
</aside>



