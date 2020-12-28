@extends('admin.layout.header')
@section('title','商品添加')
@section('content')

<blockquote class="layui-elem-quote layui-text">
<h4 style="color:green">商品添加</h4>
</blockquote>
<form class="layui-form layui-form-pane" action="/goods/store" method="post" enctype="multipart/form-data">


<div class="layui-tab">
  <ul class="layui-tab-title">
    <li class="layui-this">通用信息</li>
    <li>详细描述</li>
    <li>其他信息</li>
    <li>商品属性</li>
    <li>商品相册</li>
  </ul>
  <div class="layui-tab-content">
    <!-- 通用信息 -->
    <div class="layui-tab-item layui-show">

    <div class="layui-form-item">
    <label class="layui-form-label">商品名称</label>
    <div class="layui-input-block">
      <input type="text" name="goods_name" lay-verify="title"  autocomplete="off" placeholder="请输入商品名称" class="layui-input"><span></span>
    </div>
    </div>


    <div class="layui-form-item">
    <label class="layui-form-label">商品货号</label>
    <div class="layui-input-inline">
      <input type="text" name="goods_sn" lay-verify="required" placeholder="请输入商品货号" autocomplete="off" class="layui-input"><span></span>
    </div>
  </div>


    <div class="layui-form-item">
    <label class="layui-form-label">本店售价</label>
    <div class="layui-input-inline">
      <input type="text" name="shop_price" lay-verify="required" placeholder="请输入售价" autocomplete="off" class="layui-input"><span></span>
    </div>
  </div>

   
  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">商品分类</label>
      <div class="layui-input-inline">
        <select name="cat_id">
        <option value="">请选择</option>
              @foreach($weight_list as $k=>$v)
                <option value="{{$v->cat_id}}">{{str_repeat('|--',$v->level)}}{{$v->cat_name}}</option>
              @endforeach
        </select>
      </div>
    </div>
  </div>

    <div class="layui-inline">
      <label class="layui-form-label">商品品牌</label>
      <div class="layui-input-inline">
        <select name="brand_id" lay-verify="required" lay-search="">
          <option value="">请选择</option>
            @foreach($Ecsbrand as  $v)
            <option value="{{$v->brand_id}}">{{$v->brand_name}}</option>
            @endforeach
         </select>
      </div>
    </div>

 
    <div class="form-group">
        <label for="firstname" class="col-sm-2 control-label">商品图片</label>
        <div class="layui-upload-drag" id="test10">
            <input type="hidden" id="fileview" name="goods_img" value="">
            <i class="layui-icon"></i>
            <p>点击上传，或将文件拖拽到此处</p>
            <div class="layui-hide" id="uploadDemoView">
                <hr>
                <img src="" alt="上传成功后渲染" style="max-width: 196px">
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="firstname" class="col-sm-2 control-label">商品缩略图</label>
        <div class="layui-upload-drag" id="test1">
            <input type="hidden" id="fileview1" name="goods_thumb" value="">
            <i class="layui-icon"></i>
            <p>点击上传，或将文件拖拽到此处</p>
            <div class="layui-hide" id="uploadDemoView1">
                <hr>
                <img src="" alt="上传成功后渲染" style="max-width: 196px">
            </div>
        </div>
    </div>

    </div>
    <!-- 通用信息 -->


    <!-- 详细描述 -->
    <div class="layui-tab-item">
    <textarea id="demo" name="goods_desc" style="display: none;"  ></textarea><span></span>
    </div>
    <!-- 详细描述 -->


    <!-- 其他属性 -->
    <div class="layui-tab-item">
    <div class="layui-form-item">
    <label class="layui-form-label">商品重量</label>
    <div class="layui-input-inline">
      <input type="text" name="goods_weight" lay-verify="required" placeholder="请输入商品重量" autocomplete="off" class="layui-input"><span></span>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">商品存库</label>
    <div class="layui-input-inline">
      <input type="text" name="goods_number" lay-verify="required" placeholder="请输入商品存库" autocomplete="off" class="layui-input">
      <span class="notice-span" style="display:block" id="noticeStorage">库存在商品为虚货或商品存在货品时为不可编辑状态，库存数值取决于其虚货数量或货品数量</span>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">库存警告数量</label>
    <div class="layui-input-inline">
      <input type="text" name="warn_number" lay-verify="required" placeholder="请输入商品存库" autocomplete="off" class="layui-input"><span></span>
    
    </div>
  </div>



  <div class="layui-form-item">
    <label class="layui-form-label">加入推荐</label>
    <div class="layui-input-block">
      <input type="checkbox" name="is_best" title="精品" value="1">
      <input type="checkbox" name="is_new" title="新品"  value="1" checked="">
      <input type="checkbox" name="is_hot" title="热销" value="1">
    </div>
  </div>


  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">促销开始时间</label>
      <div class="layui-input-block">
        <input type="text" name="promote_start_date" id="date1" autocomplete="off" class="layui-input"><span></span>
      </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">促销结束时间</label>
      <div class="layui-input-block">
        <input type="text" name="promote_end_date" id="date" autocomplete="off" class="layui-input"><span></span>
      </div>
    </div>
</div>
    </div>
    <!-- 其他属性 -->



    <!-- 商品类型 -->
    <div class="layui-tab-item">
      <table>
        <tr>
              <td class="layui-form-label">商品类型：</td>
              <td>
                <select name="goods_type"  onchange="getAttrList(0)" lay-filter="demo" lay-verify="required">
                  <option value="0">请选择商品类型</option>
                  @foreach($type as $v)
                  <option value="{{$v->cat_id}}">{{$v->cat_name}}</option>
                  @endforeach
              </select>
              <br>

              <span class="notice-span" style="display:block" id="noticeGoodsType">请选择商品的所属类型，进而完善此商品的属性</span></td>
          </tr>
          <tr>
            <td id="tbody-goodsAttr" colspan="2" style="padding:0">
            <table width="100%" class="layui-table" id="attrTable"></table></td>
          </tr>
        </table>
    </div>
    <!-- 商品类型 -->


    <!-- 多图片上传 -->
    <div class="layui-tab-item">
    <div class="layui-upload">
  <button type="button" class="layui-btn" id="test2">多图片上传</button> 
  <blockquote class="layui-elem-quote  layui-quote-nm"  style="margin-top: 10px;">
    预览图：
    <div class="layui-upload-list demo2" id="demo2"></div>
 </blockquote>
</div>

    </div>
    <!-- 多图片上传 -->

  </div>    

 <center><div>
        <button type="submit" class="layui-btn aww">添加</button>
        <button type="reset" class="layui-btn layui-btn-danger">重置</button>
  </div></center> 
</div>
</form>
<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<script>
 $(document).on('click','.aww',function(){
         //alert(111);
      var falg = false;
      var goods_name = $('input[name="goods_name"]').val();
      if (goods_name == '') {
                $("input[name='goods_name']+span").html("<font color='red'>商品名不能为空</font>");
                falg = false;
            } else {
                $("input[name='goods_name']+span").html("<font color='green'>√</font>");
                falg = true;
        }
      var pfalg = false;
      var goods_sn = $('input[name="goods_sn"]').val();
      if (goods_sn == '') {
                $("input[name='goods_sn']+span").html("<font color='red'>商品货号不能为空</font>");
                pfalg = false;
            } else {
                $("input[name='goods_sn']+span").html("<font color='green'>√</font>");
                pfalg = true;
          }
      var afalg = false;
      var shop_price = $('input[name="shop_price"]').val();
      if (shop_price == '') {
                $("input[name='shop_price']+span").html("<font color='red'>本店售价不能为空</font>");
                afalg = false;
            } else {
                $("input[name='shop_price']+span").html("<font color='green'>√</font>");
                afalg = true;
          }
           var bfalg = false;
      var goods_weight = $('input[name="goods_weight"]').val();
      if (goods_weight == '') {
                $("input[name='goods_weight']+span").html("<font color='red'>商品重量不能为空</font>");
               bfalg = false;
            } else {
                $("input[name='goods_weight']+span").html("<font color='green'>√</font>");
                bfalg = true;
        }
         var cfalg = false;
      var goods_number = $('input[name="goods_number"]').val();
      if (goods_number == '') {
                $("input[name='goods_number']+span").html("<font color='red'>商品库存不能为空</font>");
                cfalg = false;
            } else {
                $("input[name='goods_number']+span").html("<font color='green'>√</font>");
                cfalg = true;
        }
         var dfalg = false;
      var warn_number = $('input[name="warn_number"]').val();
      if (warn_number == '') {
                $("input[name='warn_number']+span").html("<font color='red'>库存警告不能为空</font>");
                dfalg = false;
            } else {
                $("input[name='warn_number']+span").html("<font color='green'>√</font>");
                dfalg = true;
        }
         var efalg = false;
      var promote_start_date = $('input[name="promote_start_date"]').val();
      if (promote_start_date == '') {
                $("input[name='promote_start_date']+span").html("<font color='red'>促销开始时间不能为空</font>");
                efalg = false;
            } else {
                $("input[name='promote_start_date']+span").html("<font color='green'>√</font>");
                efalg = true;
        }
         var ffalg = false;
      var promote_end_date = $('input[name="promote_end_date"]').val();
      if (promote_end_date == '') {
                $("input[name='promote_end_date']+span").html("<font color='red'>促销结束不能为空</font>");
                ffalg = false;
            } else {
                $("input[name='promote_end_date']+span").html("<font color='green'>√</font>");
                ffalg = true;
        }
      if(falg === false || pfalg === false || afalg === false || bfalg === false || cfalg === false || dfalg === false || efalg === false || ffalg === false){
          return false;
      }
    })
function addSpec(obj){
    var newobj = $(obj).parent().parent().clone();
    newobj.find('a').html('[-]');
    newobj.find('a').attr('onclick','descSpec(this)');
    $(obj).parent().parent().after(newobj);
    layui.use(['form'], function() {
            var element = layui.element;
            var form = layui.form;
            form.render('select');
                });
}

function descSpec(obj){
    $(obj).parent().parent().remove();
}

</script>
@endsection
