<h1 align="center">
  MiniShop-CMS-backend
</h1>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%3E%3D7.1-blue.svg" alt="php version" data-canonical-src="https://img.shields.io/badge/PHP-%3E%3D7.1-blue.svg" style="max-width:100%;"></a>
  <a href="https://www.kancloud.cn/manual/thinkphp5_1/353946" rel="nofollow"><img src="https://img.shields.io/badge/ThinkPHP-5.1.*-green.svg" alt="ThinkPHP version" data-canonical-src="https://img.shields.io/badge/ThinkPHP-5.1.*-green.svg" style="max-width:100%;"></a>
  <img src="https://img.shields.io/badge/license-license--2.0-lightgrey.svg" alt="LISENCE" data-canonical-src="https://img.shields.io/badge/license-license--2.0-lightgrey.svg" style="max-width:100%;"></a>
</p>

# 简介

本项目是基于TP5.1框架的微信小程序商城后端程序

配套项目请访问

* [微信小程序商城前端项目](https://github.com/Li-Zhi-Wei/MiniShop-WeApp)
* [商城后台VUE前端项目](https://github.com/Li-Zhi-Wei/MiniShop-CMS-VUE)
* [商城后台TP5后端项目](https://github.com/Li-Zhi-Wei/MiniShop-CMS-backend)

## 线上 Demo

微信扫码

![UP4RmQ.jpg](https://s1.ax1x.com/2020/07/06/UP4RmQ.jpg)

商城后台：[http://cms.jxw-lzw.cn/](http://cms.jxw-lzw.cn/) 用户名：super 密码：123456

# 架构

商城总体架构

![网上商城系统架构.png](https://i.loli.net/2020/08/12/G58ihZU9Hd6woac.png)

前台子系统架构

![前台子系统架构.png](https://i.loli.net/2020/08/12/rlHjWtKdTveZmsk.png)


# 目录结构

```
   www  WEB部署目录（或者子目录）
   ├─application           应用目录
   │  ├─api                api模块目录
   │  │  ├─controller      控制器目录
   │  │  ├─model           模型目录
   │  │  ├─service         服务目录
   │  │  └─validate        验证器目录
   │  │
   │  ├─lib                库
   │  │  ├─enum            枚举库
   │  │  └─exception       异常库
   │  │
   │  ├─command.php        命令行定义文件
   │  ├─common.php         公共函数文件
   │  └─tags.php           应用行为扩展定义文件
   │
   ├─config                应用配置目录
   │  ├─api                api模块配置目录
   │  │  ├─secure.php      安全配置
   │  │  ├─setting.php     一般配置
   │  │  └─wx.php          微信配置
   │  │
   │  ├─app.php            应用配置
   │  ├─cache.php          缓存配置
   │  ├─cookie.php         Cookie配置
   │  ├─database.php       数据库配置
   │  ├─log.php            日志配置
   │  ├─session.php        Session配置
   │  ├─template.php       模板引擎配置
   │  └─trace.php          Trace配置
   │
   ├─route                 路由定义目录
   │  ├─route.php          路由定义
   │  └─...                更多
   │
   ├─public                WEB目录（对外访问目录）
   │  ├─index.php          入口文件
   │  ├─router.php         快速测试文件
   │  └─images             图片储存位置
   │
   ├─thinkphp              TP5.1框架核心(未上传到仓库，请自行下载)               
   │
   ├─extend                扩展类库目录
   ├─runtime               应用的运行时目录（缓存等）
   ├─vendor                第三方类库目录（Composer依赖库）
   ├─build.php             自动生成定义文件（参考）
   ├─composer.json         composer 定义文件
   ├─LICENSE.txt           授权说明文件
   ├─README.md             README 文件
   └─think                 命令行入口文件
   
   业务逻辑集中在 application/api 中
```

# 快速开始

* 安装MySQL（version： 5.6+）

* 安装PHP环境（version： 7.1+）

* 安装WEB服务器（Apache / Nginx）

* 下载工程项目

## 数据库配置

新建一个数据库，导入DataBase.sql。进入工程根目录下``/config/database.php``，找到如下配置项：

```php
// 服务器地址
  'hostname'        => '',
// 数据库名
  'database'        => '',
// 用户名
  'username'        => 'root',
// 密码
  'password'        => '',
  
//省略后面一堆的配置项
```

## 其他配置

进入工程根目录下``/config/api/``，根据自己的实际情况填写各项配置

下载 TP5.1 框架核心，放入项目根目录中。

# 特别说明

本项目学习自七月大佬的课程，并结合项目需求增加了一些功能（如增加了邮费模块、将商品拆分为SPU、SKU并增加了更多设置），后台项目不在课程内，是我自己做的。
