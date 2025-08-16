<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式
$PAYHT  = 'http://www.8vka.com/pay.php'; //支付通信地址
$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/


   

    $sHtml = "<form id='wangyasubmit' name='wangyasubmit' action='".$PAYHT."' method='post'>";

    while ( list ( $key, $val ) = each ( $DATA ) ) {

           $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }

    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

    $sHtml = $sHtml."<script>document.forms['wangyasubmit'].submit();</script>";

    return htmlout($sHtml,$WY);



}else if($PLAYFS  == '2'){ //异步通信

    /*

    商家订单id   返回金额 用户的dindgan

    chongzhifan( $_NPOST['ordernum']  , $_NPOST['rjine']  , $_NPOST['usernum'] );
    
    
    
    */

    

    return htmlout('success',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        if(  isset( $_NGET['ordernum']) && strlen( $_NGET['ordernum'] ) > 10  ){
            
            header("Location:".WZHOST.'chading.html?id='.$_NGET['ordernum']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}