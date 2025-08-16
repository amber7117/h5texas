<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式

$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址
if($SHOUJI )
    $PAYHT  ='https://h5pay.jd.com/jdpay/saveOrder';
else 
    $PAYHT  = 'https://wepay.jd.com/jdpay/saveOrder';


if( $PLAYFS  == '1'){//充值处理


    $DATA = array(  
        'version' => 'V2.0',
        'merchant' => $PAYID,
        'tradeNum' => $DINGID['orderid'],
        'tradeName' => 'PAY',
        'tradeTime' => date('YmdHis'),
        'amount' => ($DINGID['payjine']*100),
        'orderType' => 1,
        'userId' => $DINGID['uid'],
        'userType'=> 'BIZ',
        'currency' => 'CNY',
        'callbackUrl' => $PAYTB,
        'notifyUrl' => $PAYYB ,
        'ip'=>ip(),
        'note' => $DINGID['orderid'],
    );


    $CANSHU = argSort($DATA);
    $CANSH = getarray($CANSHU);

    foreach($DATA as $k => $v){

        if($k == 'merchant'|| $k == 'version') continue;
        $DATA[$k] =encrypt2HexStr(  base64_decode($PAYKEY), $v);
    }

    $DATA['sign'] = encryptByPrivateKey(hash ( "sha256", $CANSH));


    $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$PAYHT."' method='post'>";

    while(list( $key, $val ) = each ( $DATA ) ) {

        $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";

    }
    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";

    return htmlout($sHtml,$WY);

}else if($PLAYFS  == '2'){ //异步通信


    $raw_post_data =$request->rawContent();
    $shuju = xmljx( $raw_post_data );

    if( $shuju ){

        $reqBody = decrypt4HexStr(base64_decode($shuju['encrypt']),base64_decode($PAYKEY));
        $cans = xmljx( $reqBody);

        if($cans){

            unset($cans['jdpay']);
            $startIndex = strpos($reqBody,"<sign>");
            $endIndex = strpos($reqBody,"</sign>");
            $xml='';
            if($startIndex!=false && $endIndex!=false){

                $xmls = substr($reqBody, 0,$startIndex);
                $xmle = substr($reqBody,$endIndex+7,strlen($reqBody));
                $xml=$xmls.$xmle;
            }

            $YZKEY =  hash("sha256", $xml);
            $sign = decryptByPublicKey( $cans['sign'] );
             
            if($sign == $YZKEY ){

                if( $cans['status'] == '2') 
                chongzhifan( $cans['tradenum']  , $cans['amount']/100  , $cans['tradenum'] ); 
            }
        }

        return htmlout('ok',$WY);
    }

    return htmlout('error',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        $_NPOST['tradeNum'] = decrypt4HexStr(   $_NPOST["tradeNum"] ,base64_decode($PAYKEY)  );

        if(  isset( $_NPOST['tradeNum']) && strlen( $_NPOST['tradeNum'] ) > 10  ){
            
            header("Location:".WZHOST.'chading.html?id='.$_NPOST['tradeNum']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}