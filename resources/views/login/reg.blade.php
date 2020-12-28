<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>注册</title>
<meta name="keywords"  content="DeathGhost" />
<meta name="description" content="DeathGhost" />
<meta name="author" content="DeathGhost,deathghost@deathghost.cn">
<link rel="icon" href="static/images/icon/favicon.ico" type="static/image/x-icon">
<link rel="stylesheet" type="text/css" href="static/css/style.css" /><script src="static/js/html5.js"></script>
<script src="static/js/jquery.js"></script>
<script>
$(document).ready(function(){
  $("nav .indexAsideNav").hide();
  $("nav .category").mouseover(function(){
	  $(".asideNav").slideDown();
	  });
  $("nav .asideNav").mouseleave(function(){
	  $(".asideNav").slideUp();
	  });
});
</script>
</head>
<body>
<!--header-->
  @include('layout.header')
 <script>
 $(document).ready(function(){
   //测试效果，程序对接如需变动重新编辑
   $(".switchNav li").click(function(){
     $(this).addClass("active").siblings().removeClass("active");
     });
   $("#chanpin").click(function(){
     $(".inputWrap input[type='text']").attr("placeholder","输入产品关键词或货号");
     });
   $("#shangjia").click(function(){
     $(".inputWrap input[type='text']").attr("placeholder","输入商家店铺名");
     });
   $("#zixun").click(function(){
     $(".inputWrap input[type='text']").attr("placeholder","输入关键词查找文章内容");
     });
   $("#wenku").click(function(){
     $(".inputWrap input[type='text']").attr("placeholder","输入关键词查找文库内容");
     });
   });
   
 </script>
 
<section class="wrap user_form">
 <div class="lt_img">
  <img src="static/images/form_bg.jpg"/>
 </div>
 <div class="rt_form">
  <h2>会员注册</h2>
  <ul>
   <li class="user_icon">
    <input type="text" class="textbox" name="user_plone" placeholder="手机号码"/><span></span>
   </li>
   <li class="link_li btn">
    <button type="button" id="span_tel" value="获取手机校验码" class="get_num_btn"/>获取手机校验码</button>
   </li>
   <li class="user_cc">
    <input type="text" class="textbox" name="code" placeholder="手机校验码"/><span></span>
   </li>
   <li class="user_pwd">
    <input type="password" class="textbox" name="user_pwd" placeholder="设置密码"/><span></span>
   </li>
   <li class="user_pwd">
    <input type="password" class="textbox" name="user_pwds" placeholder="确认密码"/><span></span>
   </li>
   <li class="link_li">
    <a href="{{url('/login')}}" title="登录账号" class="fr">已有账号，立即登录？</a>
   </li>
   <li class="link_li">
    <input type="button" id="aww" value="立即注册" class="sbmt_btn"/>
   </li>
  </ul>
 </div>
</section>
<!--footer-->
@include('layout.foot')
</body>
</html>
<script src="/static/js/jquery.js"></script>
<script>
    $(document).on('click','#aww',function(){
      var falg = false;
      var user_plone = $('input[name="user_plone"]').val();
      if (user_plone == '') {
                $("input[name='user_plone']+span").html("<font color='red'>用户名不能为空</font>");
                falg = false;
            } else {
                $("input[name='user_plone']+span").html("<font color='green'>√</font>");
                falg = true;
        }
      var pfalg = false;
      var user_pwd = $('input[name="user_pwd"]').val();
      if (user_pwd == '') {
                $("input[name='user_pwd']+span").html("<font color='red'>密码不能为空</font>");
                pfalg = false;
            } else {
                $("input[name='user_pwd']+span").html("<font color='green'>√</font>");
                pfalg = true;
          }
      var ofalg = false;
      var user_pwds = $('input[name="user_pwds"]').val();
      if (user_pwds == '') {
                $("input[name='user_pwds']+span").html("<font color='red'>确认密码不能为空</font>");
                ofalg = false;
            } else {
                $("input[name='user_pwds']+span").html("<font color='green'>√</font>");
                ofalg = true;
          }
      var ifalg = false;
      var code = $('input[name="code"]').val();
      if (code == '') {
                $("input[name='code']+span").html("<font color='red'>验证码不能为空</font>");
                ifalg = false;
            } else {
                $("input[name='code']+span").html("<font color='green'>√</font>");
                ifalg = true;
            }
            // alert(code);
      if(falg === false || pfalg === false || ofalg === false || ifalg === false){
          return false;
      }
      if(user_pwd!=user_pwds){
          alert('密码不一致');
      }
        var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
    if(myreg.test(user_plone)){
       $.post('https://api.winnel.top/regdo',{user_plone:user_plone,user_pwd:user_pwd,user_pwds:user_pwds,code:code},function (result) {
            if(result.code=='00001'){
                alert(result.message);
            }
            if(result.code=='00003'){
                alert(result.message);
            }
            if(result.code=='00005'){
                alert(result.message);
            }
             if(result.code=='00006'){
                alert(result.message);
            }
             if(result.code=='00007'){
                alert(result.message);
            }
            if(result.code=='00000'){
                location.href = "/login"
            }
        },'json')
    }else{
      alert('请输入正确的手机号');
    }
    });
    $('button').click(function () {
        var _this = $(this);
        _this.text('60s');
				times = setInterval(goTime, 1000);
        var name = $('input[name="user_plone"]').val();
        // alert(name);
        var mobilereg=/^[1][3,4,5,7,8][0-9]{9}$/;
        if(mobilereg.test(name)){
            //发送手机号验证码
            $.get('https://api.winnel.top/sendSMS',{name:name},function (res) {
                if(res.code=='00000'){
                }
                if(res.code=='00002'){
                    alert(res.message);
                }
            },'json');
        }else{
             alert('请输入正确的手机号');
        }
    })
    // 倒计时
			function goTime() {
				var s = $("#span_tel").text();
				s = parseInt(s);
				if (s <= 0) {
					clearInterval(times);
					$("#span_tel").text('获取');
					$(".btn").css('pointer-events', 'auto');
				} else {
					s = s - 1;
					$("#span_tel").text(s + 's');
					$(".btn").css('pointer-events', 'none');
				}
			}
</script>
  