# 对接问卷星进行二次分析的调查问卷分析系统 使用Vue.js+iView+PHP实现的前后端分离开发模式的实战项目 
------------------------------------
环境配置
------------------------------------
/*
* 安装Web服务器
*/
yum install -y httpd
/*
* 安装配置数据库
*/
yum install -y mariadb-server
systemctl start mariadb
mysqladmin -u root password your_db_pass
/*
* PHP配置
*/
yum install -y php
yum install -y php-mysqlnd
yum install -y php-json
/*
* 开启web服务
*/
systemctl start httpd
/*
* 虚拟目录配置
*/
将vhost.conf放在/etc/httpd/conf.d下
------------------------------------
未完成
------------------------------------
设置最小宽度、最大宽度
添加维度-全选和取消全选的事件
搜索
------------------------------------
未修复BUG
------------------------------------
index.php 第382行