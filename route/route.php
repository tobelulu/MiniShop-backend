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

//根据banndrId获取banner信息
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');


//获取所有的主题
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
//获取主题内页面信息
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');


//路由分组
Route::group('api/:version/product',function (){
    //根据分类id获取该分类商品
    Route::get('/by_category','api/:version.Product/getAllInCategory');
    //获取最新上传的商品(分页，默认每页16个,一直往后刷就相当于获取全部商品)
    Route::get('/recent','api/:version.Product/getRecent');
    //根据商品id获取商品信息
    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//    //获取所有商品
//    Route::get('/all','api/:version.Product/getAll');
//    //上传商品
//    Route::post('/upload','api/:version.Product/upload');
//    //下架商品(软删除)
//    Route::put('/delete','api/:version.Product/deleteOne');
});


//获取分类列表
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');
//添加分类
//Route::post('api/:version/category/add','api/:version.Category/add');


//获取小程序Token
Route::post('api/:version/token/user','api/:version.Token/getToken');
//验证Token是否有效
Route::post('api/:version/token/verify','api/:version.Token/verifyToken');
//获取appToken
//Route::post('api/:version/token/app','api/:version.Token/getAppToken');


//创建或更新收货地址
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
//获取用户地址
Route::get('api/:version/address','api/:version.Address/getUserAddress');


//下单
Route::post('api/:version/order','api/:version.Order/placeOrder');
//获取某用户的订单列表
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
//获取订单详情
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);
//获取所有订单列表
//Route::get('api/:version/order/paginate','api/:version.Order/getSummary');
//发货
//Route::put('api/:version/order/delivery','api/:version.Order/delivery');
//获取邮费
Route::get('api/:version/order/postage','api/:version.Order/getPostage');


//获取预订单
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
//微信支付返回结果
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
