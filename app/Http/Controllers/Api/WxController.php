<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\GoodsModel;
use App\Model\Goods_Gallery;
use App\Model\CartgoryModel;
use App\Model\UserModel;
use App\Model\ProductModel;
use App\Model\CartModel;
use App\Model\Goods_AttrModel;
use App\Model\GoodsAttrModel;
use App\Model\UseraddressModel;
use App\Model\Order_GoodsModel;
use App\Model\RegionModel;
use App\Model\Se_Order_InfoModel;
use App\Model\Order_InfoModel;
use App\Model\CouponsModel;
use Illuminate\Support\Facades\DB;
use Log;
class WxController extends Controller
{   

    //首页数据
    public function wx_goodsdata(){
        $cat_id = request()->input('cat_id');
        if($cat_id==0){
           $goodsdata = GoodsModel::select('goods_id','goods_name','goods_img','goods_brief','shop_price')->orderBy('shop_price','asc')->paginate(20);
        }else{
           $goodsdata = GoodsModel::select('goods_id','goods_name','goods_img','goods_brief','shop_price')->orderBy('shop_price','asc')->where('cat_id',$cat_id)->paginate(20);
        }
        return json_encode($goodsdata);
    }



    public function wx_partdata(){
        $goods_id = request()->input('goodsid');
        if(!$goods_id){
            return json_encode(['code'=>1]);
        }

    $goodsdata = GoodsModel::leftjoin('sh_goods_gallery','sh_goods.goods_id','=','sh_goods_gallery.goods_id')
                            ->select('sh_goods.goods_id','sh_goods.goods_name','sh_goods_gallery.img_url','sh_goods.goods_brief','sh_goods.shop_price','sh_goods.goods_desc')
                            ->where('sh_goods.goods_id',$goods_id)
                            ->get()->toArray();

    $data = [];
    $img = [];
    foreach($goodsdata as $v){
        $data['goods_id'] = $v['goods_id'];
        $data['goods_name'] = $v['goods_name'];
        $data['goods_brief'] = $v['goods_brief'];
        $data['shop_price'] = $v['shop_price'];
        $data['goods_desc'] = $v['goods_desc'];
        $img[] = $v['img_url'];
    }
    $data['img'] = $img;
    $currentlen = count($data['img']);
    $data['currentlen'] = $currentlen;
    
    return json_encode($data);
    }

    public function wx_cartgory(){
        $catdata = CartgoryModel::select('cat_id','cat_name')->where(['parent_id'=>0,'is_show'=>1])->get();
        return json_encode($catdata);

    }

    public function wx_cartdata(){
        $cat_id = request()->input('cat_id');
        if(!$cat_id){
            return json_encode(['code'=>2,'msg'=>'Error:请传分类id']);
        }
        $cat_data = GoodsModel::leftjoin('sh_category','sh_goods.cat_id','=','sh_category.cat_id')
                              ->select('sh_goods.goods_id','sh_goods.goods_name','sh_goods.goods_img','sh_goods.goods_brief','sh_goods.shop_price')
                              ->where('sh_goods.cat_id',$cat_id)
                              ->get();
        return json_encode($cat_data);
        
    }

    public function wx_getopenid(){
        $appid = request()->input('appid');
        $code = request()->input('code');
        $appsecret = request()->input('appsecret');
        $nickName = request()->input('nickName');
        $avatarUrl = request()->input('avatarUrl');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";
        //echo $url;die;
        $data = $this->geturl($url);
        
        $open_id = $data['openid'];
        if(!$open_id){
            return json_encode(['code'=>1,'msg'=>'openid 异常','data'=>[]]);
        }
        $userdata = [
            'user_name'=>$nickName,
            'headimg'=>$avatarUrl,
            'open_id'=>$open_id,
            'login_type'=>'wx'
        ];
        
        $data['user_id'] = UserModel::where(['user_name'=>$nickName,'open_id'=>$open_id])->value('user_id');
      
        if(!$data['user_id']){
            $data['user_id'] = UserModel::insertGetId($userdata);
        }
        if($data){
             return json_encode(['code'=>0,'msg'=>'OK','data'=>$data]);
        }else{
            return json_encode(['code'=>1,'msg'=>'error','data'=>[]]);
        }
    }


    public function geturl($url){
        $headerArray =["Content-type:application/json;","Accept:application/json"];
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //路径是https请求方式 跳过证书认证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//数据以字符串形式返回，不是直接输出到浏览器
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);//添加header头信息
        $output = curl_exec($ch);//执行
        curl_close($ch);//关闭
        $output = json_decode($output,true);//将json串转换为数组
        return $output;
    }



    //加入购物车
    public function wx_addcart(Request $request){
        $user_id = $request->input('user_id');
        // dd($user_id);
        if(!$user_id){
            return json_encode(['code'=>'1001','msg'=>'请先登录']);
        }
            $goods_id=$request->goods_id;
            $buy_number= $request->buy_number;
            $seuser_id=GoodsModel::where('goods_id',$goods_id)->value('seuser_id');
            
            // print_r($seuser_id);die;
            $goods_attr_id= $request->goods_attr_id;
            if(isset($goods_attr_id)){
                $attr_price = Goods_AttrModel::whereIn('goods_attr_id',$goods_attr_id)
                ->sum('attr_price');
                $shop_price=GoodsModel::where(['goods_id'=>$goods_id])->value('shop_price');
                $shop_price = $attr_price + $shop_price;



                //echo $shop_price;exit;
               $shop_price = number_format($shop_price,2,".","");
            }else{
                $shop_price=GoodsModel::where(['goods_id'=>$goods_id])->value('shop_price');
                $shop_price = number_format($shop_price,2,".","");
            }
            //print_r($shop_price);die;
        // $data=json_encode($data);
        
        // dd($data);

        // 判断商品id 购买数量 是否缺少参数
        if(!$goods_id || !$buy_number){
            return  json_encode(['code'=>'1003','msg'=>'缺少参数']);
        }
        $goods = GoodsModel::select('goods_id','goods_name','shop_price','is_show','goods_number','goods_sn')->where('goods_id',$goods_id)->first();
        // dd($goods);
        // dd($goods['is_show']);
        if($goods->is_show==0){
            return  json_encode(['code'=>'1004','msg'=>'商品已下架']);
        }
         //查询product的库存 购买数量大于库存  提示库存不足
        if(isset($goods_attr_id)){
            $goods_attr_id = implode('|',$goods_attr_id); //imploade 将数组用|分割成字符串
            // dump($goods_attr_id);
            // echo 123;
            $product = ProductModel::select('product_id','product_number','product_sn')->where(['goods_id'=>$goods_id,'goods_attr'=>$goods_attr_id])->first();

            if($product['product_number']>$buy_number){
                return json_encode(['code'=>'1005','msg'=>'商品库存不足']);
            }
        }else{
             $goods_number = GoodsModel::select('goods_id','goods_number','goods_sn')->where(['goods_id'=>$goods_id])->first();
            //  dump($goods_number);
             if($goods_number->goods_number<$buy_number){
                return json_encode(['code'=>'1005','msg'=>'商品库存不足']);
            }
        }
            //根据当前用户id ，商品id和规格判断购物车是否有次商品  没有添加入库  有更新购买数量
        //购买数量大于库存提示 把购物车数量改为最大库存 更新
        $cart = CartModel::where(['user_id'=>$user_id,'goods_id'=>$goods_id,'goods_attr_id'=>$goods_attr_id])->first();
        // print_r($cart);die;
        // dd($cart);
        if($cart){
            //更新购买数量

            $buy_number = $cart['buy_number']+$buy_number;
            $res = CartModel::where('cart_id',$cart->cart_id)->update(['buy_number'=>$buy_number]);
            if($res){
                return json_encode(['code'=>'0','msg'=>'添加成功']);
            }else{
                  return json_encode(['code'=>'3','msg'=>'添加失败']);
            }
        }else{
            //echo $shop_price;exit;
              //添加购物车
            $data = [
                'user_id'=>$user_id,
                'product_id'=>$product->product_id??0,
                'buy_number'=>$buy_number,
                'goods_attr_id'=>$goods_attr_id??'',
                'goods_sn'=>$product->product_sn??$goods->goods_sn,
                'shop_price'=>$shop_price,
                'seuser_id'=>$seuser_id??''
            ];
            $goods = $goods?$goods->toArray():[];
            unset($goods['shop_price']);
            $data = array_merge($data,$goods);
            unset($data['is_show']);
            unset($data['goods_number']);
            $res = CartModel::create($data);
            // dd($res);
            if($res){
                return json_encode(['code'=>'0','msg'=>'添加成功']);
            }else{
                return json_encode(['code'=>'1','msg'=>'添加失败']);
            }
        }
    }


    public function wx_cart_data(){
        
    $user_id = request()->input('user_id');
    
    $cart_data = CartModel::select('sh_cart.cart_id','sh_cart.goods_name','sh_cart.goods_id','sh_goods.goods_brief','sh_cart.buy_number','sh_goods.goods_img','sh_goods.shop_price')
                    ->leftjoin('sh_goods','sh_cart.goods_id','=','sh_goods.goods_id')
                    ->where(['user_id'=>$user_id])
                    ->get();

        foreach ($cart_data as $key=>$val){
            $attr_name =[];
            $val->goods_attr_id = trim($val->goods_attr_id,'');

            if($val->goods_attr_id && $val->goods_attr_id!==''){
                $goods_attr_id = explode("|",$val->goods_attr_id);
                if(count($goods_attr_id)){
                    foreach($goods_attr_id as $k=>$v){
                        $attr_Data=Goods_AttrModel::select('sh_goods_attr.attr_value','sh_attribute.attr_name')
                            ->leftjoin('sh_attribute','sh_goods_attr.attr_id','=','sh_attribute.attr_id')
                            ->where(['goods_attr_id'=>$v])
                            ->get();
                        $attr_name[]= $attr_Data[0]['attr_name'].":".$attr_Data[0]['attr_value'];               
                    }
                    $val['attr_nane']=$attr_name;
                }
            }

        }
        if(!count($cart_data)){
            $respoer = [
                'code'=>1,
                'msg'=>'您的购物车中没有商品',
                'data'=>[],
            ];
        }else{
            $respoer = [
                'code'=>0,
                'msg'=>'OK',
                'data'=>$cart_data 
            ];
        }
    	return json_encode($respoer);

    
    }


     //购物车删除
     public function cart_del(){
        $cart_id = request()->cart_id;
        $cart_id = trim($cart_id,",");
        $cart = explode(",",$cart_id);
        if(!$cart_id){
            $respoer = [
                'code'=>'1',
                'msg'=>'Error,参数缺失',
                'data'=>[],
            ];
        }
        
      
            foreach($cart as $k=>$v){
                $isdel=CartModel::where('cart_id',$v)->delete();
            }

        if($isdel){
                
            $respoer = [
                'code'=>'0',
                'msg'=>'OK'
            ];
        }else{
            $respoer = [
                'code'=>'1',
                'msg'=>'Error,原因可能为您的购物车中不存在某件商品，建议刷新页面',
                'data'=>[],
            ];
        }
       
    	return json_encode($respoer);
    }


    public function wx_addorder(){
        $cart_id = request()->cart_id;
        $cart_id = rtrim($cart_id,',');
        $cart_ids = explode(',',$cart_id);
        if(count($cart_ids) <= 0){
            $respoer = [
                'code'=>'1',
                'msg'=>'Error,原因可能是你未选择商品',
                'data'=>[],
            ];
        }
        $cart_data = [];
        foreach($cart_ids as $v){
            $cart_data[] = CartModel::select('sh_cart.cart_id','sh_cart.goods_name','sh_cart.goods_id','sh_goods.goods_brief','sh_cart.buy_number','sh_goods.goods_img','sh_goods.shop_price')
            ->leftjoin('sh_goods','sh_cart.goods_id','=','sh_goods.goods_id')
            ->where(['cart_id'=>$v])
            ->first();
        }
        
        $total = DB::select("select sum(shop_price*buy_number) as total from `sh_cart` where cart_id IN ($cart_id)");
        if($cart_data){
            $respoer = [
                'code'=>'0',
                'msg'=>'OK',
                'data'=>$cart_data,
                'total'=>$total
            ];
        }else{
            $respoer = [
                'code'=>'2',
                'msg'=>'Error,操作繁忙，请稍后再试',
                'data'=>[],
            ];
        }
       
    	return json_encode($respoer);


       
    }



    //微信小程序支付
    public function wx_topay(){
        $user_id = request()->input('user_id');
        if(!$user_id){
            $respoer = [
                'code'=>'2',
                'msg'=>'Error,您还没有登录哦',
                'data'=>[],
            ];
        return json_encode($respoer);
        }
        $order_id = request()->input('order_id');
        $order_sn = Order_InfoModel::where('order_id',$order_id)->value('order_sn');
        if(!$order_sn){
            $respoer = [
                'code'=>'5',
                'msg'=>'Error,此订单不存在',
                'data'=>[],
            ];
        return json_encode($respoer);
        }
        //opedid
        $openid = 'ohOx35BdcM3zQcEiwzxf_96xZ8P4';
        //appid
        $appid = 'wx10ebd61cc38799c4';
        //appsecret
        $appsecret = 'f87a776ca35a0127d6d92ec29e2a2495';
        //key
        $key = 'sdg634fghgu5654rtghfghgfy4575htg';
        //mch_id
        $mch_id = '1499304962';
        //read
        $random = $this->createRandomStr(32);
        
        $params = [
            'appid'=>$appid,
            'mch_id'=>$mch_id,
            'nonce_str'=>$random,
            'body'=>'测试',
            'out_trade_no'=> $order_sn,
            'total_fee'=>'1',
            'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],
            'notify_url'=>'https://api.winnel.top/wx_notify_url',
            'trade_type'=>'JSAPI',
            'openid'=>$openid
        ];

        $params['sign'] = $this->getsign($params,$key);
        
        //转换为xml
        $xml= $this->ArrToXml($params);
        
        //获取prepay_id的url
        $unUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = $this->curlRequest($unUrl,$xml);
        //将返回的数据转换为数组
        $preArr = $this->XmlToArr($data);
        //获取prepay_id
        $prepay_id =  $preArr['prepay_id'];
        
        //构建小程序调起支付API所需数据并将数据签名
        $jsApiParams = [
            'appId' => $appid,
            'timeStamp' => strval(time()),
            'nonceStr'  => $random,
            'package'   => 'prepay_id=' .  $prepay_id,
            'signType'  => 'MD5',
        ];
        $jsSign = $this->getSign($jsApiParams,$key);
        $jsApiParams['sign'] = $jsSign;

        return json_encode($jsApiParams);




    }



    //xml转换为数组
    public function XmlToArr($xml){
        if($xml=='')return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        return $arr;
    }

    //转换为xml
    public function ArrToXml($arr){
        if(!is_array($arr) || count($arr)==0) return '';

        $xml = "<xml>";
        foreach($arr as $key=>$val){
            if(is_numeric($val)){
                $xml .= "<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml; 
    }

    //签名
    public function getsign($params,$key){

        //排序
        ksort($params);
        //转换为字符串
        $str = urldecode(http_build_query($params));
        //拼接key
        $str .= '&key='.$key;
        //md5并大写
        return strtoupper(md5($str));

    }


    //生成随机的32位字符串
    function createRandomStr($length){ 
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
        $strlen = 32; 
        while($length > $strlen){ 
        $str .= $str; 
        $strlen += 32; 
        } 
        $str = str_shuffle($str); 
        return substr($str,0,$length); 
    }


    //发送网络请求
    function curlRequest($url,$data = ''){
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_TIMEOUT] = 30; //超时时间
        if(!empty($data)){
            $params[CURLOPT_POST] = true;
            $params[CURLOPT_POSTFIELDS] = $data;
        }
        $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
        $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }

    //支付异步
    public function wx_notify_url(){

        $request = file_get_contents("php://input");
        \Log::info('小程序支付异步通知：'.$request);
        // 6AF92FB5420EFDA5D9564290996C0605
        $post_data = $this->XmlToArr($request);
        $post_sign = $post_data['sign'];

        unset($post_data['sign']);
        ksort($post_data);
        $str = http_build_query($post_data);
        $user_sign = strtoupper(md5($str."&key=sdg634fghgu5654rtghfghgfy4575htg"));
        if($post_data['return_code']=='SUCCESS' && $post_sign==$user_sign){
            $ordernumber = $post_data['out_trade_no'];
            \Log::info('小程序支付验签成功');
             echo "成功";

             $str = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
             
             Order_InfoModel::where('order_sn',$ordernumber)->update(['is_paid'=>1]);
        }else{
            \Log::info('微信支付失败：'.$post_data['return_code'].PHP_EOL);
            echo "微信支付失败";
            echo "success";
            $str = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>";


        }
        echo $str;

    }


    public function wx_orderinfo(){
        $user_id = request()->user_id;
        // print_r($user_id);die;
       
        if(!$user_id){
            return json_encode(['code'=>1,'msg'=>'Error,请登录']);
        }
        $datas = request()->all();
        $cart_ids = rtrim($datas['cart_id'],',');
        $cart_id = explode(',',$cart_ids);
        if(empty($datas['address_id']) || empty($datas['pay_type']) || empty($datas['cart_id']) || empty($datas['total_price'])){
            return json_encode(['code'=>2,'msg'=>'Error,参数丢失']);
        }

        if($datas['pay_type']==1){
            $datas['pay_name'] = "支付宝";
        }else if($datas['pay_type']==2){
            $datas['pay_name'] = "微信支付";
        }else if($datas['pay_type']==3){
            $datas['pay_name'] = "货到付款";
        }else if($datas['pay_type']==4){
            $datas['pay_name'] = "余额支付";
        }

        DB::beginTransaction();
       
        try {
            $order_sn = $this->order_sn($user_id);
            $addressdatas = UseraddressModel::where(['user_id'=>$user_id,'address_id'=>$datas['address_id']])->get();
            if(!$addressdatas){
                return json_encode(['code'=>3,'msg'=>'Error,请选择收获地址']);
            }
            $addressdata = $addressdatas[0];
            $data = [
                'order_sn' => $order_sn,
                'user_id' => $user_id,
                'email' => $addressdata->email,
                'consignee' => $addressdata->consignee,
                'country' => $addressdata->country,
                'province' => $addressdata->province,
                'city' => $addressdata->city,
                'district' => $addressdata->district,
                'address' => $addressdata->address,
                'zipcode' => $addressdata->zipcode,
                'tel' => $addressdata->tel,
                'pay_type' => $datas['pay_type'],
                'pay_name' => $datas['pay_name'],
                'total_price' => $datas['total_price'],
                'addtime' => time(),
            ];
            $order_id = Order_InfoModel::insertGetId($data);
            // print_r($order_id);die;
            $goodsinfo = CartModel::select('sh_cart.goods_id','sh_cart.goods_sn','sh_cart.product_id','sh_cart.goods_name','sh_cart.shop_price','sh_cart.buy_number','sh_cart.goods_attr_id','sh_cart.seuser_id')
                    ->whereIn('cart_id',$cart_id)
                    ->get();
                if(!count($goodsinfo->toArray())){
                    throw new Exception('购物车内没有此商品');
                }

                    $goods_data = [];
                    foreach($goodsinfo as $k=>$v){
                        $goods_data[$k]['order_id'] = $order_id;
                        $goods_data[$k]['goods_id'] = $v->goods_id;
                        $goods_data[$k]['goods_sn'] = $v->goods_sn;
                        $goods_data[$k]['product_id'] = $v->product_id;
                        $goods_data[$k]['goods_name'] = $v->goods_name;
                        $goods_data[$k]['shop_price'] = $v->shop_price;
                        $goods_data[$k]['buy_number'] = $v->buy_number;
                        $goods_data[$k]['goods_attr_id'] = $v->goods_attr_id?$v->goods_attr_id:'';
                        $goods_data[$k]['seuser_id']=$v->seuser_id?$v->seuser_id:'';
                    }
                    
            $ret = Order_GoodsModel::insert($goods_data);
        
            DB::commit();
            return json_encode(['code'=>0,'msg'=>'订单生成成功','data'=>$order_id]);
        }
    catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            return json_encode(['code'=>4,'msg'=>'订单生成失败']);
        }

    }
    
        //生成惟一的订单号
        public function order_sn($user_id){
            $order_sn = rand(100000,999999).$user_id.time();
            $is_cf = Order_InfoModel::where(['order_sn'=>$order_sn])->first();
            if($is_cf){
                $this->order_sn($user_id);
            }else{
                return $order_sn;
            }
            

        }


}
