<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/


$WZHOST  = WZHOST;



$ACTION = array( 
    '1' => 'qq',
    '2' => 'weixin',
    '3' => 'weibo',
    '4' => 'alipay',
    '5' => 'weixinopen',
);

$LX = (int)( isset($_NGET['y']) ? $_NGET['y'] : 1);
// echo $LX;exit;

if( ! isset( $ACTION[$LX] ) ) return htmlout('非法参数' ,$WY);

$IP = ip();

if( isset( $sescc['uid'] ) && $sescc['uid'] > 0 ) $start = 2;
else $start = 1;

if( isset( $_NGET['isapp']) )  $start = 'isapp';




if($LX == 1){

    $URL = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id='.$CONN['kjqqid'].'&state='.$start.'&redirect_uri='.urlencode($WZHOST.'login/'. $ACTION[$LX].'.php');

    header("Location:". $URL);
    return htmlout('1',$WY,302);


}else if($LX == 3){

    $URL = 'https://api.weibo.com/oauth2/authorize?client_id='.$CONN['kjweiboid'].'&forcelogin=false&redirect_uri='.urlencode($WZHOST.'login/'. $ACTION[$LX].'.php');
    header("Location:". $URL);

    return htmlout('1',$WY,302);


}else if($LX == 4){

  

    $ZUHE = array(
        'service' => 'alipay.auth.authorize',
        'partner' => $CONN['kjzfbid'],
        '_input_charset' => 'utf-8',
        'return_url'=> ( $WZHOST.'login/'. $ACTION[$LX].'.php'),
        'target_service' => 'user.auth.quick.login',
    );

    $zuhe = argSort($ZUHE);
    $ZUHE['sign'] = md5(getarray($zuhe).$CONN['kjzfbkey']);
    $ZUHE['sign_type'] = 'MD5';
    $URL = 'https://mapi.alipay.com/gateway.do?'.getarray($ZUHE);

    header("Location:". $URL);
    return htmlout('1',$WY,302);
    

}else if($LX == 5){

    //weixin开放平台

    header("Location:". $WZHOST);
    return htmlout('1',$WY,302);

}else{
    //威信登录



    if( !strstr( $AGENT, "essenger" ) ) $token = token();
    else $token = 0;

    $URL = ('http://htym.home5566.cn/weixin-duijie.html?appid='.$CONN['kjwxid'].'&redirect_uri='.urlencode($WZHOST.'login/'. $ACTION[$LX].'.php').'&response_type=code&scope=snsapi_userinfo&state='. $token);

    if( strstr( $AGENT , "essenger")){
        header("Location:". $URL);
        return htmlout('1',$WY,302);
    }

    $URL = urlencode($URL);
}

$SHAOMA =<<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>微信登录</title>
        <meta charset="utf-8">
        <style>
        .impowerBox,.impowerBox .status_icon,.impowerBox .status_txt{display:inline-block;vertical-align:middle}a{outline:0}h1,h2,h3,h4,h5,h6,p{margin:0;font-weight:400}a img,fieldset{border:0}body{font-family:"Microsoft Yahei";color:#fff;background:0 0}.impowerBox{line-height:1.6;position:relative;width:100%;z-index:1;text-align:center}.impowerBox .title{text-align:center;font-size:20px}.impowerBox .qrcode{width:380px;margin-top:15px;border:1px solid #E2E2E2}.impowerBox .info{width:280px;margin:0 auto}.impowerBox .status{padding:7px 14px;text-align:left}.impowerBox .status.normal{margin-top:15px;background-color:#232323;border-radius:100px;-moz-border-radius:100px;-webkit-border-radius:100px;box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-moz-box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-webkit-box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444}.impowerBox .status.status_browser{text-align:center}.impowerBox .status p{font-size:13px}.impowerBox .status_icon{margin-right:5px}.impowerBox .status_txt p{top:-2px;position:relative;margin:0}.impowerBox .icon38_msg{display:inline-block;width:38px;height:38px}
        </style>
        <script type="text/javascript" src="/js/z.js"></script>
    </head>
    <body style="padding: 50px; background-color: rgb(51, 51, 51);">
        <div class="main impowerBox" >
            <div class="loginPanel normalPanel">
                <div class="title" onclick="gouhome();">微信登录( 点击返回首页)</div>
                <div class="waiting panelContent">
                    <div class="wrp_code"><img class="qrcode lightBorder" src="{$WZHOST}ewm.php?data={$URL}" /></div>
                    <div class="info">
                        <div class="status status_browser js_status normal" id="wx_default_tip">
                            <p>请使用微信扫描二维码登录</p>
                            <p> {$CONN['kjwxname']} </p>
                        </div>
                       
                        
                    </div>
                </div>
            </div>
        </div>
 <script>
var dajishid ;
var token = '{$token}';
var HTTP = '{$WZHOST}';
var qunid = '{$_GET["qunid"]}';
var times =180;

function getCookie(name)
{
 var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

 if(arr=document.cookie.match(reg))
  
  return (arr[2]);
 else
  return null;
}

function gouhome(){

    window.location.href= HTTP;

}

function yanzhen(){

    times--;

    if(times < 1){

        times =180;
        $(".qrcode").attr({ src: $(".qrcode").attr("src")+'&=1'});
    }

     $.ajax({

        url:HTTP+'json.php',
        type: "POST",
        data:{y:"loginsm",d:"get",ttoken:token,apptoken:getCookie("apptoken")},
        dataType: "json",
        timeout:"3000",
        success: function(data){

            if(data.code == 1){

                /*正常通信中*/
            
            
            }else if(data.code == 2){

                window.clearInterval(dajishid);
                alert("登录成功");
                if(qunid != '' && qunid > 0){
                    window.location.href= '../yaoqing.php?qunid=qunid';
                }else{
                    window.location.href= HTTP;
                }
                
            }else{

                window.clearInterval(dajishid);

                alert("通信失败");
                 window.location.href= HTTP;
            }


        },error:function(XMLHttpRequest){
            window.clearInterval(dajishid);

            alert("通信失败");
            window.location.href= HTTP;

        }
    });


}


dajishid = setInterval("yanzhen()",3000);

</script>
    </body>
</html>
EOT;

return htmlout($SHAOMA,$WY);