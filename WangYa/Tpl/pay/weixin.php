<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao']; //微信登录id
$PAYMM  = $PAYAC['beizhu']; //微信登录key




$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/

    $DINGID['payjine'] = $DINGID['payjine'] *100;

    $CANSHU = array(  'appid'  => $PAYZH ,
                     'mch_id'  => $PAYID,
                   'nonce_str' => md5( $DINGID['orderid']),
                        'body' => 'PAY',
                      'attach' => $DINGID['uid'],
                'out_trade_no' => $DINGID['orderid'],
                   'total_fee' => $DINGID['payjine'],
            'spbill_create_ip' => IP(),
                  'time_start' => date('YmdHis'),
                  'notify_url' => $PAYYB
    );

    if( strstr( $AGENT, "essenger")  &&  $USER['weixin']  != ''){
    
                $CANSHU['trade_type'] = 'JSAPI';

                $CANSHU['openid'] =  $USER['weixin'];

     }else  $CANSHU['trade_type'] = 'NATIVE';

    $CANSHU = argSort( $CANSHU );
    $CANSH  = getarray( $CANSHU );

    $CANSHU['sign'] = strtoupper( md5( $CANSH . '&key='.$PAYKEY ));

    $xml ='<xml>';
     foreach( $CANSHU as $k =>$v ) $xml .= "<$k>$v</$k>";
    $xml .='</xml>';

    

     $fanhui = post($xml,'https://api.mch.weixin.qq.com/pay/unifiedorder?');

    
     $woqu = str_replace(array('<','>'),'',$fanhui);

     $p = xml_parser_create();
     xml_parse_into_struct($p, $fanhui, $vals, $index);
     xml_parser_free($p);

     if($vals){

        $shuju = array();

        foreach( $vals as $zhis){

            $shuju[ strtolower( $zhis['tag'] ) ] = isset( $zhis['value']) ? $zhis['value'] :'';
        }

        if( $shuju['return_code'] == 'SUCCESS'){

            $WZHOST = WZHOST;

            if( $shuju['trade_type'] == 'JSAPI'){

                /*公众号支付*/

                $FHSIGN  =  array(
                    'appId' => $PAYZH ,
                    'timeStamp' => time(),
                   'nonceStr' => md5(time().rand(1,9999)),
                    'package' => 'prepay_id='.$shuju['prepay_id'],
                   'signType' => 'MD5',
                );

                $CANSHU = argSort($FHSIGN);
          
                $CANSH = getarray($CANSHU);

                $CANSHU['sign'] = strtoupper(md5($CANSH.'&key='.$PAYKEY));
                

$HML = <<<EOT
<html lang="zh-CN">
 <head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="format-detection" content="telephone=no">
 </head>
 <body>

    <a href="#" style="color:REd;padding:10px;display:block;text-align:center;">请完成支付</a>

 </body>
</html>
<script type="text/javascript" >

    function onBridgeReady(){

        WeixinJSBridge.invoke( 'getBrandWCPayRequest', {
                   "appId" :"{$FHSIGN['appId']}",
                   "timeStamp":"{$FHSIGN['timeStamp']}",
                   "nonceStr":"{$FHSIGN['nonceStr']}",
                   "package":"{$FHSIGN['package']}",  
                   "signType":"{$FHSIGN['signType']}",
                   "paySign":"{$CANSHU['sign']}" 
               }, function(res){     
                  if(res.err_msg == "get_brand_wcpay_request:ok" ) { 

                          window.location.href="{$WZHOST}chading.html?id={$DINGID['orderid']}";
                  } else window.location.href="{$WZHOST}";
          }
       ); 
    }

    if ( typeof WeixinJSBridge == "undefined"){

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
EOT;
return htmlout($HML,$WY);

            }else{
            /*扫码支付*/

            $JINE = $DINGID['payjine']/100;
$HML =<<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>微信扫码支付</title>
        <meta charset="utf-8">
     <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
        <style>
        .impowerBox,.impowerBox .status_icon,.impowerBox .status_txt{display:inline-block;vertical-align:middle}a{outline:0}h1,h2,h3,h4,h5,h6,p{margin:0;font-weight:400}a img,fieldset{border:0}body{font-family:"Microsoft Yahei";color:#fff;background:0 0}.impowerBox{line-height:1.6;position:relative;width:100%;z-index:1;text-align:center}.impowerBox .title{text-align:center;font-size:20px}.impowerBox .qrcode{width:200px;margin-top:15px;border:1px solid #E2E2E2}.impowerBox .info{width:280px;margin:0 auto}.impowerBox .status{padding:7px 14px;text-align:left}.impowerBox .status.normal{margin-top:15px;background-color:#232323;border-radius:100px;-moz-border-radius:100px;-webkit-border-radius:100px;box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-moz-box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-webkit-box-shadow:inset 0 5px 10px -5px #191919,0 1px 0 0 #444}.impowerBox .status.status_browser{text-align:center}.impowerBox .status p{font-size:13px}.impowerBox .status_icon{margin-right:5px}.impowerBox .status_txt p{top:-2px;position:relative;margin:0}.impowerBox .icon38_msg{display:inline-block;width:38px;height:38px}
        </style>
        <script type="text/javascript" src="/js/z.js"></script>
    </head>
    <body style="padding: 50px; background-color: rgb(51, 51, 51);">
        <div class="main impowerBox" >
            <div class="loginPanel normalPanel">
               
                <div class="waiting panelContent">
                    <div class="wrp_code"><img class="qrcode lightBorder" src="{$WZHOST}ewm.php?data={$shuju['code_url']}" /></div>
                    <div class="info">
                        <div class="status status_browser js_status normal" id="wx_default_tip">
                            <p>微信扫码支付金额: {$JINE} 元</p>
                            <p> {$CONN['kjwxname']} </p>
                        </div>
                       
                        
                    </div>
                </div>
            </div>
        </div>
 
     
    </body>
</html>
<script>
 
var dajishid ;
var token = '{$DINGID['orderid']}';
var HTTP = '{$WZHOST}';
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
        data:{y:"chading",d:"get",ttoken:token,apptoken:getCookie("apptoken")},
        dataType: "json",
        timeout:"3000",
        success: function(data){

            if(data.code == 1){

                /*正常通信中*/

            }else if(data.code == 2){

                window.clearInterval(dajishid);
                alert("充值成功");
                window.location.href= HTTP+'chading.html?id='+token;
            
            
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
EOT;

            return htmlout($HML,$WY);

            }


        }else{

            return htmlout($shuju['return_msg'],$WY);
        }


     
    }else{

        return htmlout($woqu,$WY);
    }


}else if($PLAYFS  == '2'){ //异步通信

    $raw_post_data = file_get_contents( 'php://input' , 'r' ); 
    $raw_post_data = $raw_post_data ? $raw_post_data : $GLOBALS['HTTP_RAW_POST_DATA'] ;

    if( $raw_post_data ){

        $xml = $raw_post_data;
        $p   = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free( $p );

        if( $vals ){

            $shuju = array();

            foreach( $vals as $zhis) $shuju[ strtolower( $zhis['tag'] ) ] = isset( $zhis['value']) ? $zhis['value'] :'';

            unset( $shuju['xml'] );

            $SIGN = $shuju['sign'];

            unset( $shuju['sign'] );

            $CANSHU = argSort($shuju);

            $CANSH = getarray($CANSHU);

            $xcxiay  = strtoupper(md5($CANSH.'&key='.$PAYKEY));

            if(  $xcxiay == $SIGN){

                chongzhifan( $shuju['transaction_id'] , (float)($shuju['cash_fee']/100) , $shuju['out_trade_no'] );

                  $htmsl = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';

            }else $htmsl = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[NO]]></return_msg></xml>';

        } else    $htmsl =  '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[NO]]></return_msg></xml>';

    }else         $htmsl =  '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[NO]]></return_msg></xml>';

    return htmlout($htmsl,$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        if(  isset( $_NGET['ordernum']) && strlen( $_NGET['ordernum'] ) > 10  ){
            
            header("Location:". WZHOST.'chading.html?id='.$_NGET['ordernum']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}