<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/


if(isset($_GET['apptoken'])){
    header('Location: /?tuid='.(int)$_GET['tuid']);
    exit();
}
if($sescc['uid'] < 1){

    header('Location: /login.php?y=2&apptoken='.$SESSIONID.'&tuid='.(int)$_GET['tuid']);
    exit("1");
}

if((int)$CONN['tiaoshi'] > 0){

    $SHAOMA =<<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>温馨提示</title>
        <meta charset="utf-8">
        <script type="text/javascript"></script>
    </head>
    <body>
        <h5>{$CONN['tscontent']}</h5>
        <img src='{$CONN['tstupian']}'/>
    </body>
</html>
EOT;
htmlout($SHAOMA,$WY);
    exit("1");
}

?>
  <script type="text/javascript">

window.ISLOGIN= 0;

</script>
<?php
    $jsapi_ticket   =   $Mem ->g( 'jsapi_ticket');

    if(! $jsapi_ticket ){

        $access_token =  $Mem ->g('access_token');
                            

        if(!$access_token ){

            $csass = sslget('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$CONN['kjwxid'].'&secret='.$CONN['kjwxkey']);
            $woqi = (array)json_decode($csass);
            $access_token = $woqi['access_token'];
            if( strlen( $access_token ) > 10 )$Mem ->s('access_token',$access_token ,'3600');
        }

        $csafss = sslget('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
        $woqdi = (array)json_decode($csafss);
        $jsapi_ticket =  $woqdi['ticket'];
        if(strlen( $jsapi_ticket ) > 10)$Mem ->s('jsapi_ticket',$jsapi_ticket ,'3600');

    }

    $FHSIGN  =  array(  'timestamp' => time(),
                        'noncestr' => md5(time().rand(1,9999)),
                    'jsapi_ticket' => $jsapi_ticket ,
                            'url' =>'http://'. str_replace('index.php','',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),
                );

    $CANSHU = argSort($FHSIGN); 
    $CANSH  = getarray($CANSHU);
    $CANSHU['sign'] = sha1($CANSH);


    $lailu = 'http://www.dss.com/';//来源【可选参数】

    $userdata = uid($sescc['uid']);

    $yes = false;
    list($jine,$type,$dingdannum,$gametype) = explode('_',$_GET['data']);

    $backlujing = '/';

    if((float)$jine > 0){

        global $Mem;
        $isset = $Mem -> g('tixiansuo/'.$sescc['uid']);

        if($isset){
            msgbox('提现不要太频繁喔',$backlujing);
        }

        $Mem -> s('tixiansuo/'.$sescc['uid'],$_GET,6);
        
        $data = db('tixiandingdan') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> find();

        if(!$data) return;

        if(!isset($data['state']) || (int)$data['state'] != 0) return;
        
        if((float)$jine != (float)$data['txjine']){

            msgbox('金额错误',$backlujing);

        }

        if((int)$type == 1){

            $fan = jiaqian($sescc['uid'],17,0,0,-(float)$jine,'提现至微信'.(float)$jine.'金','',0);
            if(!$fan){
        
                msgbox('扣除金币失败',$backlujing);
            }
        
        }else if((int)$type == 2){
            $fan = jiaqian($sescc['uid'],17,0,0,0,'提现至微信'.(float)$jine.'拥金','',-(float)$jine);
            if(!$fan){
        
                msgbox('扣除佣金失败',$backlujing);
            }
        }else{
            msgbox('提现类型错误',$backlujing);
        }
        
        $userjine = (float)$jine - ((float)$jine*(float)$CONN['txsxf']);

        $realjine = round(((float)$userjine)/$CONN['paybilijb'],1);

        if((int)$CONN['txsh'] == 1 && (float)$jine >= (float)$CONN['shje']){     //要审核

            $fan = db('tixiandingdan') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> update(array('state' => 2));

            if($fan){
                $fan = db('tixianshenhe') -> insert(array(
                    'uid' => $sescc['uid'],
                    'username' => $data['username'],
                    'txjine' => $realjine,
                    'time' => $dingdannum,
                    'type' => (int)$data['type'],
                    'state' => 0,   //待审核
                    'openid' => $_GET['openid'],
                ));

                if($fan){

                    msgbox('请等待管理员审核',$backlujing);

                }else{

                    msgbox('生成订单失败，请稍后再试',$backlujing);

                }
            }else{
                msgbox('修改订单状态为审核失败',$backlujing);
            }
            
        }else if((int)$CONN['txsh'] == 0 || ((int)$CONN['txsh'] == 1 && (float)$jine < (float)$CONN['shje'])){

            $post_data = array (
                'appid' => $CONN['tx_uid'], //在掌上零钱里面获取的uid
                'amount' => $realjine, //要请求发放的金额
                'recipient_openid'=> $_GET['openid'], //用户openid
                'order_no'=> $dingdannum, //本地的提现id【要求唯一】字符串类型的数字，最大长度11位数
                'channel' =>'wx', //支付渠道
                'description' => $sescc['uid'].'提现'.$realjine, //订单描述
            );
            ksort($post_data);
            reset($post_data);
            $md5str = "";
            foreach ($post_data as $key => $val) {
              $md5str = $md5str . $key . "=" . $val . "&";
            }
        
            $post_data['sign'] = strtoupper(md5($md5str . "key=" . $CONN['tx_key']));
    
            $url = file_get_contents('http://119.3.33.140/api/api/host').'/api/api/withdraw';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    
            $output = curl_exec($ch);
            curl_close($ch);
    
            //打印获得的数据
            //print_r($output);
            $back = $output;
            $output = json_decode($output,true);
    
            if((int)$output['code'] == 40011 && ($output['data']['return_code'] == 'success' || $output['data']['return_code'] == 'SUCCESS')){  //成功
    
                $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> update(array('state' => 1,'tixianback' => $back));
    
                db('tixiandingdan') -> qurey( $sql ,'shiwu');
    
                msgbox('提现成功',$backlujing);
    
            }else if((int)$output['code'] == 40010 || (int)$output['code'] == 40007){
    
                $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> update(array('state' => 2,'tixianback' => $back));
    
                db('tixiandingdan') -> qurey( $sql ,'shiwu');
    
                msgbox('请等待管理员审核',$backlujing);
    
            }else{
                $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> update(array('state' => -1,'tixianback' => $back));
    
                db('tixiandingdan') -> qurey( $sql ,'shiwu');
    
                $fan = jiaqian($sescc['uid'],17,0,0,(float)$jine,'提现返回'.(float)$jine.'金','',0);
                if(!$fan){
    
                    msgbox('返回金币失败，请联系客服',$backlujing);
                }
    
                msgbox($output['errmsg'],$backlujing);
            }
        }
    }
?>



<script>
  localStorage.removeItem("NEWuserData");
</script>



<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">



  <title><?php echo  $CONN['dez_gamename'];?></title>



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

  <meta name="screen-orientation" content="portrait"/>

  <meta name="x5-orientation" content="portrait">



  <!--fix fireball/issues/3568 -->

  <!--<meta name="browsermode" content="application">-->

  <meta name="x5-page-mode" content="app">



  <!--<link rel="apple-touch-icon" href=".png" />-->

  <!--<link rel="apple-touch-icon-precomposed" href=".png" />-->



  <link rel="stylesheet" type="text/css" href="style-mobile.css"/>

  

</head>

<body>

<canvas id="GameCanvas" oncontextmenu="event.preventDefault()" tabindex="0"></canvas>
<img  id="shipingde" style="position: absolute;top:0;left:0;z-index:999999999;width:100%;"/>

<div>


  <img src="/myerweima.php?gametype=dezhou" alt="" id="erweima" style="position: absolute;top:0%;left:0%;z-index:999999999;width:100%;height:100%;display:none;"/>

  <img src='/back1.png' onclick='showerweima(1)' id='back' style="position: absolute;top:3%;left:3%;z-index:999999999;display:none;width:30px;"/>
</div>

<img src="<?php echo pichttp( $CONN['dez_kefu']);?>" alt="" id="Kerweima" style="position: absolute;top:0%;left:0%;z-index:999999999;width:100%;height:100%;display:none;"/>

<img src='/back1.png' onclick='showerweima(3)' id='Kback' style="position: absolute;top:4%;left:4%;z-index:999999999;display:none;width:30px;"/>

<div id="splash">

  <div class="progress-bar stripes">

    <span style="width: 0%"></span>

  </div>

</div>



<script src="src/settings.js?v=4" charset="utf-8"></script>

<script src="main.js?v=4" charset="utf-8"></script>
<script>

  window.showerweima = function(value){

    if(value == 1){
      document.getElementById('erweima').style.display="none";
      document.getElementById('back').style.display="none";
    }else if(value == 2){
      document.getElementById('erweima').style.display="block";
      document.getElementById('back').style.display="block";
    }else if(value == 3){
      document.getElementById('Kerweima').style.display="none";
      document.getElementById('Kback').style.display="none";
    }else{
      document.getElementById('Kerweima').style.display="block";
      document.getElementById('Kback').style.display="block";
    }

  };


  function onBridgeReady(){
    WeixinJSBridge.call('hideOptionMenu');
  }


  if (typeof WeixinJSBridge == "undefined"){
    if( document.addEventListener ){
      document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    }else if (document.attachEvent){
      document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
      document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
  }else{
    onBridgeReady();
  }



</script>


</body>

</html>

