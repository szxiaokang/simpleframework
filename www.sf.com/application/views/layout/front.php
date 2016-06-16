<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link href="/assets/css/bootstrap-theme.min.css" rel="stylesheet" />
        <link href="/assets/css/bootstrapValidator.min.css" rel="stylesheet" />
        <link href="/assets/css/jquery-ui.min.css" rel="stylesheet" />
        <link href="/assets/css/pager.css" rel="stylesheet" />

        <script type="text/javascript" src="/assets/js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrapValidator.min.js"></script>
        <script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/assets/js/front.js"></script>
        <title>SimpleFramework<?php echo isset($pageTitle) && !empty($pageTitle) ? ' - ' . $pageTitle : ''?></title>
        <style>.myfooter {border-top: solid 1px #eaeaea; text-align: center; padding: 10px; margin-top: 20px; color: #eaeaea}</style>
    </head>
    <body>
        <div id="alert_message" style="display:none"></div>
        <div class="container-fluid" style="margin-top:10px">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-10" style="padding-top:15px">
                    <nav class="navbar navbar-default">
                        <div class="container-fluid">
                    
                            <div class="navbar-header">
                                <a href="/"><img src="/assets/images/logo.gif" style="margin:5px 20px 0 -15px" /></a>
                            </div>
                            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <?php
                                    $url = getURL(FALSE);
                                    $urls = parse_url($url);
                                    ?>
                                    <li<?php echo $urls['path'] == '/' ? ' class="active"' : ''?>><a href="/">首页</a></li>
                                    <li<?php echo strpos($url, '/news/') ? ' class="active"' : ''?>><a href="/news/">新闻</a></li>
                                    <li<?php echo strpos($url, '/user/') ? ' class="active"' : ''?>><a href="/user/">用户</a></li>
                                    <li<?php echo $urls['path'] == '/index/about' ? ' class="active"' : ''?>><a href="/index/about">关于</a></li>
                                </ul>
                                <form class="navbar-form navbar-left" role="search" action="/news/">
                                    <div class="input-group">
                                        <input type="text" name="keywords" class="form-control" placeholder="新闻关键字">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit">Go!</button>
                                        </span>
                                    </div>
                                </form>
                                <ul class="nav navbar-nav navbar-right ">
                                    <?php
                                    if (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
                                        ?>
                                        <li><p class="navbar-text"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Hi <?php echo $_SESSION['email']?>, 欢迎回来!</p></li>
                                        <li><a href="/user/edit"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>个人设置</a></li>
                                        <li><button type="button" onclick="logout()" class="btn btn-default navbar-btn"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>退出</button></li>
                                        <?php
                                    } else {
                                        ?>
                                        <li>
                                            <button type="submit" class="btn btn-success navbar-btn" onclick="location.href='/user/login'">&nbsp;&nbsp;登录&nbsp;&nbsp;</button>
                                            <button type="submit" class="btn btn-info navbar-btn" onclick="location.href='/user/register'">&nbsp;&nbsp;注册&nbsp;&nbsp;</button>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <?php
            echo $simpleFrameworkViewContents;
            ?>
        </div>
        <div class="myfooter">SimpleFramework v1.0</div>
    </body>
</html>
