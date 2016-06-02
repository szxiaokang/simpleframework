<?php
/**
 * @filename header.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 16:43:22
 * @version 1.0
 * @Description
 * 
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>系统管理</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/bootstrapValidator.min.css" rel="stylesheet">
        <link href="/assets/css/jquery-ui.min.css" rel="stylesheet">
        <link href="/assets/css/AdminLTE.css" rel="stylesheet">
        <script src="/assets/js/jquery-1.11.3.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/bootstrapValidator.min.js"></script>
        <script src="/assets/js/jquery-ui.min.js"></script>
        <script src="/assets/js/admin.js"></script>
        <script type="text/javascript">
            $(function () {
                $(".sidebar .treeview").tree();
                $("[data-toggle='offcanvas']").click(function (e) {
                    e.preventDefault();
                    //If window is small enough, enable sidebar push menu
                    if ($(window).width() <= 992) {
                        $('.row-offcanvas').toggleClass('active');
                        $('.left-side').removeClass("collapse-left");
                        $(".right-side").removeClass("strech");
                        $('.row-offcanvas').toggleClass("relative");
                    } else {
                        //Else, enable content streching
                        $('.left-side').toggleClass("collapse-left");
                        $(".right-side").toggleClass("strech");
                    }
                });

            })
        </script>
    </head>
    <body class="skin-blue">
        <div id="alert_message" style="display:none"></div>
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="#" class="logo">系统管理</a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>

                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li><a href="javascript:void(0)"> <i class="glyphicon glyphicon-user"></i> <span>
                                    <?php
                                    echo isset($_SESSION['username']) ? $_SESSION['username'] : '';
                                    ?>
                                    , 欢迎回来! </span></a></li>
                        <li><a href="javascript:logout()" > <i class="glyphicon glyphicon-log-out"></i> <span>退出</span></a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">

                        <?php
                        $uris = parse_url(getURL(FALSE));
                        $curr_url = $uris['path'];
                        $menus_maps = array(
                            array(
                                'icon' => 'glyphicon-globe',
                                'text' => '新闻管理',
                                'sub_menus' => array(
                                    '添加新闻' => '/admin/news/add',
                                    '新闻管理' => '/admin/news/lists',
                                    '添加新闻类型' => '/admin/news/addType',
                                    '新闻类型管理' => '/admin/news/listType'
                                )
                            ),
                            array(
                                'icon' => 'glyphicon-glass',
                                'text' => '管理员管理',
                                'sub_menus' => array(
                                    '添加管理员' => '/admin/admin/add',
                                    '管理员管理' => '/admin/admin/lists',
                                )
                            ),
                            array(
                                'icon' => 'glyphicon-glass',
                                'text' => '用户管理',
                                'sub_menus' => array(
                                    '用户列表' => '/admin/user/',
                                )
                            ),
                            array(
                                'icon' => 'glyphicon-list-alt',
                                'text' => '后台首页',
                                'url' => '/admin/',
                            )
                        );

                        $html = '';
                        foreach ($menus_maps as $item) {
                            if (!isset($item['sub_menus'])) {
                                $html .= '<li' . ($curr_url == $item['url'] ? ' class="curr"' : '') . '><a href="' . $item['url'] . '"> <i class="glyphicon ' . $item['icon'] . '"></i> <span>' . $item['text'] . '</span> </a>';
                                continue;
                            }
                            $active = in_array($curr_url, $item['sub_menus']) ? ' active' : '';
                            $html .= '<li class="treeview' . $active . '"><a href="#"><i class="glyphicon ' . $item['icon'] . '"></i><span>' . $item['text'] . '</span><i class="glyphicon glyphicon-menu-down pull-right"></i></a>';
                            $html .= '<ul class="treeview-menu">';
                            foreach ($item['sub_menus'] as $text => $url) {
                                $html .= '<li' . ($curr_url == $url ? ' class="curr"' : '') . '><a href="' . $url . '"><i class="glyphicon glyphicon-menu-right"></i> ' . $text . ' </a></li>';
                            }
                            $html .= '</ul>';
                            $html .= '</li>';
                        }
                        echo $html;
                        ?>

                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <?php
            echo $simpleFrameworkViewContents;
            ?>

        </div>
        <!-- ./wrapper -->

    </body>
</html>
