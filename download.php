<?php
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

define('WYHEAD','');
define('WYPHP' , dirname(__FILE__).'/WangYa/');
define('WYTEMP', 'temp');
define("WYCON" , '');
define("WYDB"  , '');
define('WYNAME'  , '');

require WYPHP.'WangYa.php';

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">

  <title><?php  echo $CONN['apkhbtitle'];?></title>

  <!--http://www.html5rocks.com/en/mobile/mobifying/-->
  <meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1"/>

  <!--https://developer.apple.com/library/safari/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html-->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="format-detection" content="telephone=no">

  <!-- force webkit on 360 -->
  <meta name="renderer" content="webkit"/>
  <meta name="force-rendering" content="webkit"/>
  <!-- force edge on IE -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta name="msapplication-tap-highlight" content="no">
  <!-- force full screen on some browser -->
  <meta name="full-screen" content="yes"/>
  <meta name="x5-fullscreen" content="true"/>
  <meta name="360-fullscreen" content="true"/>
  
  <!-- force screen orientation on some browser -->
  <meta name="screen-orientation" content="landscape"/>
  <meta name="x5-orientation" content="landscape">

  <!--fix fireball/issues/3568 -->
  <!--<meta name="browsermode" content="application">-->
  <meta name="x5-page-mode" content="app">

  <!--<link rel="apple-touch-icon" href=".png" />-->
  <!--<link rel="apple-touch-icon-precomposed" href=".png" />-->

  <link rel="stylesheet" type="text/css" href="style-mobile.css"/>
  <link rel="stylesheet" href="/layui/css/layui.css">
  <link rel="stylesheet" href="/layui/lay/modules/webuploader.css">
  <style>
    input:-webkit-autofill,select:-webkit-autofill {  
        -webkit-box-shadow: 0 0 0px 1000px white  inset !important;  
    } 
    input{
        outline-color: invert ;
        outline-style: none ;
        outline-width: 0px ;
        border: none ;
        border-style: none ;
        text-shadow: none ;
        -webkit-appearance: none ;
        -webkit-user-select: text ;
        outline-color: transparent ;
        box-shadow: none;
    }
    input{
        color:#8e8e8e;
    /* 字体大小直接写样式即可 */
        font-size:3vw;
        padding-left:5%;
    }
    button{
        float: right;
        margin: 0;
        padding: 0;
        background-color: transparent;
        border: 0px solid transparent;
        outline: none;
    }


  </style>
</head>
<body style="background-color:white">
    <img src="<?php echo $CONN['logo'];?>" style="position:absolute;top:6%;left:38%;z-index:999999999;width:25%;height:15%">
    <div>
        <button id='download' type="button" style="background:url(<?php echo '/btn_ljxz_nor.png';?>) no-repeat;position:absolute;width:80%;height:6%;top:27%;left:10%;"><font size="3vw" color="white">立即下载</font></button>

        <button id='jump' type="button" style="background:url(<?php echo '/btn_ljxz_nor.png';?>) no-repeat;position:absolute;width:80%;height:6%;top:35%;left:10%;"><font size="3vw" color="white">跳转到游戏</font></button>
        
        <form id="forms">
            <input type="text" name="tel" id="tel" value="输入手机号"  style="background:url(<?php echo '/btn_sr_nor.png';?>) no-repeat;position:absolute;width:75%;height:6%;top:43%;left:10%;"/>

            <input type="text" name="vcode" id="vcode" value="图形验证码"  style="background:url(<?php echo '/btn_sr_nor.png';?>) no-repeat;position:absolute;width:75%;height:6%;top:51%;left:10%;"/>
            <img id='codeimg' src="<?php echo pichttp('/vcode.php?apptoken=&timess=');?>" style="width:20%;height:5%;position:absolute;top:51.5%;left:70%;">
            <button id='changecode' type="button" style="position:absolute;width:20%;height:5%;top:51.5%;left:70%;"></button>

            <input type="text" name="pass" id="pass" value="请输入密码(6~36字符)"  style="background:url(<?php echo '/btn_sr_nor.png';?>) no-repeat;position:absolute;width:75%;height:6%;top:59%;left:10%;"/>

            <input type="text" name="pass" id="pass1" value="请确认密码" style="background:url(<?php echo '/btn_sr_nor.png';?>) no-repeat;position:absolute;width:75%;height:6%;top:67%;left:10%;"/>

            <input type="text" name="code" id="code" value="请输入验证码"  style="background:url(<?php echo '/btn_sr_nor.png';?>) no-repeat;position:absolute;width:75%;height:6%;top:75%;left:10%;"/>
            <img src="<?php echo '/pic_line.png';?>" style="width:0.5%;height:4%;position:absolute;top:76%;left:70%;">
            <font size="2.5vw" color="red" style="position:absolute;top:76.5%;left:72%;">获取验证码</font>
            <button id='getcode' type="button" style="position:absolute;width:20%;height:5%;top:76.5%;left:72%;"></button>

        </form>

        <button id='register' type="button" style="background:url(<?php echo '/btn_ljxz_nor.png';?>) no-repeat;position:absolute;width:80%;height:6%;top:83%;left:10%;"/><font size="3vw" color="white">注册</font>

    </div>
</body>
</html>
<script src="/layui/layui.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript">

$(function(){
    
    $('#getcode').on('click',function(){

        var zhanghao = $("#tel").val();
        var pass = $("#pass").val();
        var pass1 = $("#pass1").val();
        var vcode = $("#vcode").val();

        if(pass != pass1){
            alert('两次输入的密码不一致，请重新输入');
            return;
        }

        $.ajax({

            url:USIN,
            type: "POST",
            data:{y:"login",d:"delete",zhanghao:zhanghao,pass:pass,vcode:vcode,falx:1,type:1,ttoken:TOKEN,apptoken:getCookie("apptoken")},
            dataType: "json",
            timeout:"6000",
            success: function(data){

                if(data.token && data.token != ""){
                
                    TOKEN = data.token;
                }

                if(data.code == 1){

                    alert(data.msg);
                }

            },error:function(XMLHttpRequest){

                alert(XMLHttpRequest.responseJSON.msg);
            }
        });
    })
})

var value = '';

$("input").focus(function(){
    
    value = $(this).val();

    $(this).val("");

    // if($(this).attr("id") == 'pass' || $(this).attr("id") == 'pass1'){
    //     $(this).setAttribute("type", "password");
    // }
    
})
$("input").blur(function(){

    if($(this).val()==""){
        $(this).val(value);
        // if($(this).attr("id") == 'pass' || $(this).attr("id") == 'pass1'){
        //     $(this).setAttribute("type", "text");
        // }
    }
})

$(function(){
    $('#changecode').on('click',function(){

        var path = "<?php echo pichttp('/vcode.php?apptoken=&timess=');?>";
        $("#codeimg").attr('src',path);

    })
})

$(function(){
    $('#jump').on('click',function(){

        window.location.href="/";
            
    })
})

$(function(){
    $('#download').on('click',function(){

        window.location.href="/down.php";
            
    })
})

$(function(){
    
    $('#register').on('click',function(){

        var zhanghao = $("#tel").val();
        var pass = $("#pass").val();
        var pass1 = $("#pass1").val();
        var vcode = $("#vcode").val();
        var code = $("#code").val();

        if(pass != pass1){
            alert('两次输入的密码不一致，请重新输入');
            return;
        }

        $.ajax({

            url:USIN,
            type: "POST",
            data:{y:"login",d:"post",zhanghao:zhanghao,pass:pass,vcode:vcode,code:code,ttoken:TOKEN,apptoken:getCookie("apptoken")},
            dataType: "json",
            timeout:"6000",
            success: function(data){

                if(data.token && data.token != ""){
                
                    TOKEN = data.token;
                }

                if(data.code == 1){

                    alert(data.msg);
                }

            },error:function(XMLHttpRequest){

                alert(XMLHttpRequest.responseJSON.msg);
            }
        });
    })
})

</script>