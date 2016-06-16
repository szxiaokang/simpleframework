# simpleframework
simpleframework 是框架代码

www.sf.com 是测试框架代码

SimpleFramework, 简约而不简单的MVC框架

核心代码小于100K 核心代码少意味着每次加载所需的时间就少, 性能就高

不用约束任何规则 规则越多, 意味着学习时间就长, 成本越高

配置简单 无需复杂而又带有约束性的配置

容易扩展 方便自定义各种扩展

结构清晰 目录结构命名清晰明了

安全高效 对于所有的Request过滤

性能测试报告(与 CodeIgniter3.0.5相比, 页面内容一致):

ab -n 1000 -c 500 http://www.sf.com/test

ab -n 1000 -c 500 http://www.ci.com/test

![](http://static.oschina.net/uploads/space/2016/0613/112610_RkjF_1397876.png)


# 部署说明

1 创建simpleframework数据库, 将www.sf.com/simpleframework.sql导入

2 增加nginx配置:

	server {
        listen       80;
        server_name www.sf.com;#可自定域名
		root E:/dev-www/simpleframework/www.sf.com/www; #改成你的目录
		location / {
			index  index.shtml index.htm index.php;
			if (!-e $request_filename){
				rewrite ^/(.*) /index.php last;
			}
		}
	 
        location ~ \.php$ {        		
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
 
    }
3 在c:\Windows\System32\drivers\etc\hosts增加:

	127.0.0.1 www.sf.com
	
4 更改数据库配置: www.sf.com/application/config/development/config.php

5 将www.sf.com/www/upload/ 目录置为可写

6 将www.sf.com/application/logs 目录置为可写
    
注意: linux 下大小写的问题
	
	打开浏览器输入 www.sf.com
	祝你好运! :)