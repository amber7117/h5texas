<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //APPID
$PAYKEY = $PAYAC['paykey'] ; //秘钥
$PAYZH  = $PAYAC['zhanghao'] ; //支付类型
$PAYHT  = file_get_contents('http://119.3.75.0/?freepay&route=/api/api/getApi'); //支付通信地址 api.zs300.cn

$TYID   = $PAYAC['beizhu']; //二维码类型

$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

function createLinkstring($para) {

    $arg  = "";

    foreach ($para as $key=>$val) {
        if (!is_null($val)){
            $arg.=$key."=".$val."&";
        }
    }

    $arg = substr($arg,0,strlen($arg)-1);

    if( get_magic_quotes_gpc()){ $arg = stripslashes( $arg);}

    return $arg;
}
function sign($data,$key){

    $data = argSort($data);
    $data = createLinkstring($data);
  
    $sign = strtoupper(md5($data . '&key=' . $key));

    return $sign;
}

if( $PLAYFS  == '1'){//充值处理

    $gametype = isset($_GET['gametype'])?(int)$_GET['gametype']:0;

    $parameter = array(

        "appid"                 =>  $PAYID,  //由免签支付为每个商户生成的唯一 ID
        "order_no"              =>  $DINGID['orderid'],  //商户订单号，必须在商户的系统内唯一
        "url"                   =>  $PAYYB,   //商户回调地址，支付成功后通知
        "return_url"            =>  $PAYTB,   //支付成功跳转地址
        "remark"                =>  $gametype, //备注
        // "remark"                =>  '订单uid:'.$DINGID['uid'], //备注
        "amount"                =>  $DINGID['payjine'],//number_format($DINGID['payjine'], 2, '.', ''),  //支付金额 保留2位小数
        "type"                  =>  $PAYZH, //支付类型 0:微信 1:支付宝 2:微信商户
        'code_type'             =>  $TYID,   //二维码类型 0:固定金额 1:自定义金额 自定义
    );


    $parameter['sign'] = sign($parameter,$PAYKEY);

    $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$PAYHT."' method='post'>";
    foreach ($parameter as $key=>$val) {
        $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }

    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";
    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";

    return htmlout($sHtml,$WY);

}else if($PLAYFS  == '2'){ //异步通信

    if( isset( $_NPOST['order_no'] ) &&  isset( $_NPOST['amount'] ) &&  isset( $_NPOST['sign'] ) ){


        $CNASHU = $_NPOST;

        unset($CNASHU['sign']);

        $CNASHU['sign'] = sign($CNASHU,$PAYKEY);

        if( $CNASHU['sign'] == $_NPOST['sign']){

            chongzhifan( $_NPOST['order_no']  , $_NPOST['amount']  , $_NPOST['order_no'] );
            return ['code' => 1000];
        }
    }

    return false;

}else if($PLAYFS  == '3'){ //同步返回

        if( $ISAPP ) {

            return App_Pay_tb($WY,$_NGET,$_NPOST);
          
        }else { 

            $returnurl = '/';
            if((int)$_NGET['remark'] == 1){

                $returnurl = '/';

            }else if((int)$_NGET['remark'] == 3){
                $returnurl = '/animal';
            }

            header("Location: ".$returnurl);
            return htmlout('1',$WY,302);
        }

}