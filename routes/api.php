<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::domain('api.winnel.top')->group(function () {

    //login
    Route::get('sendSMS','Api\LoginController@sendSMS');//注册
    Route::post('send_s','Api\LoginController@send_s');//注册

    
    Route::post('regdo','Api\LoginController@regdo');//执行注册
    //Route::get('/','Api\IndexController@index');   //前台首页
    Route::get('noticeinfo','Api\IndexController@noticeinfo'); //首页公告数据
    
    Route::any('getuserinfo','Api\LoginController@getuserinfo');//k
    Route::post('logindo','Api\LoginController@logindo');//执行登录
    Route::post('change_pwd','Api\LoginController@change_pwd');//找回密码修改密码

    

    //goods
    Route::post('/createhistory/{goods_id?}','Api\GoodsController@createhistory');    //Api 登录后 添加历史浏览记录
    Route::post('createcollect','Api\GoodsController@createcollect'); //Api个人收藏添加
    Route::post('listcollect','Api\GoodsController@listcollect'); //Api个人收藏展示
    Route::post('/cancel','Api\GoodsController@cancel');    //Api取消个人收藏
    Route::get('goods/{goods_id}','Api\GoodsController@goods');//详情

    //list
    Route::post('/listhistory','Api\ListController@listhistory');    //Api 登录后 展示历史浏览记录
    Route::post('/delhistory','Api\ListController@delhistory');    //登录后清空 历史浏览记录

    Route::post('addcart','Api\CartController@addcart');//加入购物车
    Route::get('getlist/{id}','Api\ListController@getlist'); //列表页品牌数据
    
    //index
    Route::any('cartgory','Api\IndexController@cartgory'); //首页菜单栏分类数据
    Route::any('slideshow','Api\IndexController@slideshow'); //首页商品表轮播图数据
    Route::get('goodsInfo','Api\IndexController@goodsInfo'); //首页商品表数据
    Route::post('noticeinfo','Api\IndexController@noticeinfo'); //首页公告数据
    //Route::get('/','Api\IndexController@index');   //前台首页
    Route::post('search','Api\IndexController@search');   //前台搜索
    Route::post('search_a','Api\IndexController@search_a');   //前台商家搜索
    Route::post('seuser_goods','Api\IndexController@seuser_goods');   //前台商家商品


    //cart
    Route::post('addcart','Api\CartController@addcart');//加入购物车
    Route::post('cart_count','Api\CartController@cart_count'); //购物车数据条数
    Route::post('cart','Api\CartController@cartdata'); //购物车列表数据
    Route::post('cart_del','Api\CartController@cart_del'); //购物车删除
    Route::post('buy_jian','Api\CartController@buy_jian'); //减少购买数量
    Route::post('buy_jia','Api\CartController@buy_jia'); //加购买数量
    Route::post('cart_zprice','Api\CartController@cart_zprice'); //总价格


    //order
    Route::post('account','Api\OrderController@account'); //总价格
    Route::post('addressinfo','Api\OrderController@addressinfo');    //提交订单页面收件人信息数据
    Route::post('cartgoodsinfo','Api\OrderController@cartgoodsinfo');    //提交订单页面商品数据
    Route::post('address_del','Api\OrderController@address_del');    //提交订单页面收货地址ajax删除
    Route::post('mor','Api\OrderController@mor');    //修改默认收货地址


    //seckill
    Route::get('seckill','Api\SeckillController@seckill'); //总价格
    Route::post('seckilldo','Api\SeckillController@seckilldo');//秒杀列表页
    Route::post('addcart','Api\CartController@addcart');//加入购物车

    Route::post('/coupons','Api\GoodsController@coupons');//领取优惠券视图
    Route::post('/couponsdo','Api\CouponsController@couponsdo');//领取优惠券
    Route::get('/couponsuse/{goods_id}','Api\OrderController@couponsuse');//使用优惠券
    Route::post('/couponsprice','Api\OrderController@couponsprice');//选择优惠券改变价格
    //user
    Route::get('getsondata','Api\UserController@getsondata'); //三级联动
    Route::get('store','Api\UserController@store'); //添加地址   地址列表
    Route::post('user','Api\UserController@user'); //用户信息
    Route::post('obligation','Api\UserController@obligation'); //未付款订单


    //小程序接口首页商品数据
    Route::get('wx_goodsdata','Api\WxController@wx_goodsdata'); //小程序首页数据
    Route::get('wx_partdata','Api\WxController@wx_partdata'); //小程序首页数据
    Route::get('wx_cartgory','Api\WxController@wx_cartgory'); //小程序首页分类写活
    Route::get('wx_cartdata','Api\WxController@wx_cartdata'); //小程序首页分类
    Route::get('wx_getopenid','Api\WxController@wx_getopenid'); //小程序将用户openid存入数据库
    Route::get('wx_addcart','Api\WxController@wx_addcart'); //小程序将用户openid存入数据库
    Route::get('wx_cart_data','Api\WxController@wx_cart_data'); //小程序获取用户的购物车数据
    Route::get('wx_num_puls','Api\CartController@buy_jia'); //小程序购物车减号
    Route::get('wx_num_mion','Api\CartController@buy_jian'); //小程序购物车加号
    Route::get('wx_cart_del','Api\WxController@cart_del'); //小程序购物车减号
    Route::get('wx_account','Api\WxController@wx_account'); //小程序购物车减号
    Route::get('wx_addorder','Api\WxController@wx_addorder'); //小程序订单数据
    Route::get('wx_orderinfo','Api\WxController@wx_orderinfo'); //小程序订单数据

    Route::get('wx_topay','Api\WxController@wx_topay'); //小程序支付
    Route::post('wx_notify_url','Api\WxController@wx_notify_url'); //小程序支付异步

    

    

    
    
});

  

