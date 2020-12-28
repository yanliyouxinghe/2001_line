<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\BrandModel;
use App\Model\GoodsModel;
use App\Model\CartgoryModel;
use App\Model\Shop_HistoryModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cookie;
class ListController extends Controller
{
    /**列表页 */
    public function list($id){  
        /**获取商品的品牌和价格数据 */
        $query=request()->all();
        $querys = $_SERVER['REDIRECT_QUERY_STRING']??'';
        $querys= $querys?'?'.$querys:'';
        // print_r($query);die;
        $url = 'https://api.winnel.top/getlist/'.$id.$querys;

	/**历史浏览记录 展示*/
	// $user_id = request()->input('user_id');
        $user_id = Redis::hget('reg','user_id');
        $data['user_id']=$user_id;
        if(!$user_id){
            //不登录 历史浏览记录展示
            $listhistory = $this->cookielist();
            // $listhistory=$listhistory['listhistory'];
            //print_r($listhistory);die;
        }else{
            //登录后  历史浏览记录展示
            // print_r(12222);die;
            $urlv = 'https://api.winnel.top/listhistory';
            $story = posturl($urlv,$data);
            $listhistory=$story['listhistory'];
        }
        
        
        $urls = request()->url();
        $getlist = geturl($url);
        // print_r($getlist);die;

        $brandInfo=$getlist['data']['brandInfo'];
        $priceInfo=$getlist['data']['priceInfo'];
        $goodsInfo=$getlist['data']['goodsInfo'];
        //$query=$getlist['data']['query'];
        // print_r($query);
        return view('list.list',['brandInfo'=>$brandInfo,'priceInfo'=>$priceInfo,'goodsInfo'=>$goodsInfo,'urls'=>$urls,'listhistory'=>$listhistory,'query'=>$query]);
    }

   /**登录后  清空历史浏览记录 */
    public function delhistorys(){
	//  $user_id = request()->input('user_id');   
	$user_id=Redis::hget('reg','user_id');
        if(!$user_id){
            return redirect('/login');
        }
        $data['user_id']=$user_id;
        $url = 'https://api.winnel.top/delhistory';
        $delthistory = posturl($url,$data);
        if($delthistory['code']==0){
            return json_encode(['code'=>'0','msg'=>'删除浏览历史记录成功']);
        }
    }

    /**cookie历史浏览记录展示 */
    public function cookielist(){
        $cookiehistory = Cookie::get('historyInfo');
        //$cookiehistory = unserialize($cookiehistoary);
        $cookiehistory = unserialize($cookiehistory);
        
        if($cookiehistory){
            $goods_ids = array_column($cookiehistory,'goods_id');
            $cookiegoods = GoodsModel::whereIn('goods_id',$goods_ids)->take(6)->get()->toArray();
            return $cookiegoods;
        }else{
            return;
        }
    }





}
