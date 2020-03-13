<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::group('',function (){
    Route::group('api/:version',function (){
        // 轮播图
        Route::group('banner',function (){
            // 获取轮播图
            Route::get(':id','api/:version.Banner/getBanner');
        });
        // 主题
        Route::group('theme',function (){
            //获取所有的主题
            Route::get('','api/:version.Theme/getSimpleList');
            //获取主题内页面信息
            Route::get(':id','api/:version.Theme/getComplexOne');
        });
        // 商品
        Route::group('product',function (){
            //根据分类id获取该分类商品
            Route::get('/by_category','api/:version.Product/getAllInCategory');
            //获取最新上传的商品(分页，默认每页16个,一直往后刷就相当于获取全部商品)
            Route::get('/recent','api/:version.Product/getRecent');
            //根据商品id获取商品信息
            Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
        });
        // 分类
        Route::group('category',function (){
            Route::get('all','api/:version.Category/getAllCategories');
        });
        // Token令牌
        Route::group('token',function (){
            //获取Token
            Route::post('user','api/:version.Token/getToken');
            //验证Token是否有效
            Route::post('verify','api/:version.Token/verifyToken');
        });
        // 地址
        Route::group('address',function (){
            //创建或更新收货地址
            Route::post('','api/:version.Address/createOrUpdateAddress');
            //获取用户地址
            Route::get('','api/:version.Address/getUserAddress');
        });
        // 用户信息
        Route::group('user',function (){
            // 更新用户信息
            Route::post('','api/:version.User/updateInfo');
        });
        // 订单
        Route::group('order',function (){
            //下单
            Route::post('','api/:version.Order/placeOrder');
            //获取某用户的订单列表
            Route::get('by_user','api/:version.Order/getSummaryByUser');
            //获取订单详情
            Route::get(':id','api/:version.Order/getDetail',[],['id'=>'\d+']);
            //获取邮费
            Route::post('postage','api/:version.Order/getPostage');
            // 关闭订单
            Route::put('close/:id','api/:version.Order/close');
            // 确认收货
            Route::put('receive/:id','api/:version.Order/receive');
        });
        // 支付
        Route::group('pay',function (){
            //获取预订单
            Route::post('pre_order','api/:version.Pay/getPreOrder');
            //微信支付返回结果
            Route::post('notify','api/:version.Pay/receiveNotify');
        });
    });
});

// 获取轮播图
//Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');


//获取所有的主题
//Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
//获取主题内页面信息
//Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');





//获取分类列表
//Route::get('api/:version/category/all','api/:version.Category/getAllCategories');



//获取小程序Token
//Route::post('api/:version/token/user','api/:version.Token/getToken');
//验证Token是否有效
//Route::post('api/:version/token/verify','api/:version.Token/verifyToken');


//创建或更新收货地址
//Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
//获取用户地址
//Route::get('api/:version/address','api/:version.Address/getUserAddress');

// 更新用户信息
//Route::post('api/:version/user','api/:version.User/updateInfo');


//下单
//Route::post('api/:version/order','api/:version.Order/placeOrder');
//获取某用户的订单列表
//Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
//获取订单详情
//Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

//获取邮费
//Route::post('api/:version/order/postage','api/:version.Order/getPostage');


//获取预订单
//Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
//微信支付返回结果
//Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
