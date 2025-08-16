<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = 1; //支付方式
$ERIMG   = $PAYAC['beizhu']; //二维码图片
$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址
$WZHOST = WZHOST;
$BSHI = '';

if($PLAYFS =='1'){  //发送

    $ALIPAY =  array(
        'zhanghao' => $PAYZH,
    );

    $DD = db('dingdan');

    $fans =  $DD ->zhicha('orderid,id')->where(array( 'orderid' => $DINGID['orderid'] ))-> find();

    if( $fans ){

        $DINGID['id']  = $fans['id'];
    }

    $ERWIMA = '';

    if(!$SHOUJI){

        if($ERIMG != ''){
        
            $ERWIMA = '扫描二维码转账<br /><img src="'.pichttp($ERIMG).'" style="width:200px;height:200px;">';
        
        }
    }

$SSS = <<<EOD
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="format-detection" content="telephone=no" />
        <title> 支付宝付款  </title>
        <script type="text/javascript" src="/js/z.js"></script>
        <style>
            body{font-size:14px;padding-bottom:50px;}
            ul{list-style-type:none;}
            .chonzhi{margin:0 auto;}
            .w-200 {width:250px;}
            .w-50{width:50px;}
            .center{}
            .center ul li{margin-bottom:5px;}
            .pd-30{ padding: 8px; }
            .moni{ padding: 8px;  background:#EDEDED;}
            .xjige{background:#F3F3F3;height:20px;border-top:1px solid #fff; }
            .moren div{float:left;}
            .fangshi{background:#F8F8F8;}
            .tijiao{background:#F3F3F3; position:fixed;bottom:0px;width:100%;}
            .footer{border:0px;}
            .moren div{border:1px solid #F8F8F8;height:58px; cursor:pointer;padding-top:8px;}
            .moren div img{width:38px;height:38px;float:left;}
            .moren div.hover{border:1px solid #0096EA;border-radius: 8px;}
            .moren div p{float:left; padding:0px 0px 0px 20px;font-size:20px;color:#000;}
            .head{background:#2CBAE7;text-align:center;color:#fff;}
            h1{font-size:18px;height:50px;line-height:50px;padding:0px;}

            .moni b{color:Red;}
            .btn-pay{background:#08A1E9;color:#fff;width:96%;}
            #jines{display:block;}
            #jines label{width:88px;display:inline-block;height:30px;overflow:hidden;line-height:30px;}
            #jines label input{margin-right:5px;}
            .dingoff0{color:#ccc;}
            .dingoff1{color:#6666FF;}
            .dingoff2{color:#00CC33;}
            .dingoff3{color:Red;}
            .dingoff4{color:Red;}
            .btn{  display: inline-block;
                height: 38px;
                line-height: 38px;
                padding: 0 18px;
                background-color: #009688;
                color: #fff;
                white-space: nowrap;
                text-align: center;
                font-size: 14px;
                border: none;
                border-radius: 2px;
                cursor: pointer;
                opacity: .9;
                filter: alpha(opacity=90);
            }}

        </style>
    </head>
    <body>

        <div class="chonzhi">

            <div class="head">
                <h1>收银台</h1>
            </div>

            <div class="center pd-30">
               <ul>

                    <li>收款帐号: {$ALIPAY['zhanghao']} </li>
                   
                    <li>转账金额: {$DINGID['payjine']}</li>
                    <li>转账备注: <span style="color:red;">{$BSHI}{$DINGID['id']} </span> </li>

                </ul>
                 {$ERWIMA}
            </div>

            <div class="moni">
            转账步骤<b> 请正确填写 <span style="color:red;">转账备注</span> ,否则无法自动到账.. </b>

            </div>
            <div class="neirong" style="padding:20px;">

                1.首先打开手机支付宝钱包.<br />
                2.选择<span style="color:red;">转账功能</span> 并且选择转到支付宝账户<br />
                3.输入对方账户：<span style="color:green;font-size:18px;font-weight:bold;">{$ALIPAY['zhanghao']}</span> <br />
                4.转账金额填写：<span style="color:green;font-size:18px;font-weight:bold;">{$DINGID['payjine']}</span>  <br />
                5.转账备注填写：<span style="color:red;font-size:18px;font-weight:bold;">{$BSHI}{$DINGID['id']}</span> <br />
                温馨提示：<span style="color:#FF9933;">请勿修改转账金额,转账备注否则不返数据</span><br />
                到账时间：付款成功后,耐心等待 10 秒钟.<br />
                注意事项：<br />
                1.请正确填写 <span style="color:red;">转账备注</span> ,否则无法自动到账..<br />
                2.本站支付宝账户会不定期更换,每次充值前请务必核对支付宝账号..<br />

            </div>

            <div class="tijiao pd-10">

                <input class="btn btn-pay radius " type="submit" onclick="diaozhifubao()" value="请自己打开手机支付宝">
                <div class="cl"></div>

            </div>


        </div>
        <iframe name="left" id="rightMain"  frameborder="false" scrolling="auto" style="width:1px;height:1px;border:none;" allowtransparency="true"></iframe>
    </body>
</html>
<script>
 
var dajishid ;
var token = '{$DINGID['orderid']}';
var HTTP = '{$WZHOST}';
var times =180;

function diaozhifubao(){

    with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('iframe')).src='alipays://platformapi/startapp?appId=20000001&_t='+~(-new Date()/36e5)];
}

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
EOD;
    return htmlout($SSS,$WY);


}else if($PLAYFS =='2'){  // 异步处理

    global $Mem;

    if( isset( $_NPOST['woqu'] ) ){
        
        if($BSHI  == '')
              $fanhuis = explode( '@#@',str_replace(array('>',"'",'"',"  "," " ,"\r","\n",'付款-','    ','style=visibility:visible;','转账'),'', $_NPOST['woqu'] ) );

        else  $fanhuis = explode( '@#@',str_replace(array('>',"'",'"',"  "," " ,"\r","\n",'付款-','    ','style=visibility:visible;',$BSHI.'-',$BSHI,'转账'),'', $_NPOST['woqu'] ) );

        $m = '';
        $SHUJU = array();

        foreach( $fanhuis as $wocao ){

                if( $wocao == '') continue;
                $ding = explode( '@@',$wocao);

                $ding['2'] =preg_replace ("@seed(.*?)=on@is", '' ,$ding['2']) ;
                $ding['1'] = time();
                $m .= $ding['4'];
                if( $ding['4'] > 0) $SHUJU[] = $ding;
        }

        $keyss =  md5($PAYKEY.$m);



        if( $keyss != $_NPOST['mixiao']){
        
        
            return htmlout('no',$WY);
        }

        if( $SHUJU ){

        

            $D = db('dingdan');

            $xs = "\r\n";


            foreach( $SHUJU as $tadege){

                $fan = $D ->where( array( 'id' => $tadege['2'] ) )-> find();

                if( $fan ){

                    chongzhifan( $tadege['3']  , $tadege['4']  , $fan['orderid'] );

                    $xs .= 'OK '.$fan['ordernum'] .' ' . $tadege['4'] .' ' . $tadege['3']."\r\n";

                }else{

                    $xs .= 'NO '.$tadege['2'] .' ' . $tadege['4'] .' ' . $tadege['3']."\r\n";

                    $Mem -> s( 'wzpay/'.$tadege['3'], $tadege );

                }

            }

            return htmlout($xs,$WY);
        }

    }else return htmlout('no',$WY);


}else if($PLAYFS =='3'){  //查询


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