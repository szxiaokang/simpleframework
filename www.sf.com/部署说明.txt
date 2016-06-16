1 创建simpleframework数据库, 将simpleframework.sql导入
2 增加nginx配置:
	server {
        listen       80;
        server_name www.sf.com;#可自定域名
		root E:/dev-www/simpleFramework/www.sf.com/www; #改成你的目录
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

5 将www/upload/ 目录置为可写

6 将application/logs 目录置为可写
    
注意: linux 下大小写的问题
	
	打开浏览器输入 www.sf.com
	祝你好运! :)