<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$PAYHT  = 'http://pay.gamemg.net/pay.php'; //支付通信地址
$TYID   = $PAYAC['beizhu']; //支付方式

$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/


    if($TYID < 1){

        $URL = $PAYHT.'?zh='.$DINGID['orderid'].'&appid='.$PAYID.'&tyid=1&jine='.$DINGID['payjine'];

        header("Location:". $URL);
        return htmlout('1',$WY,302);
    }


    $DATA = array( 'order' => $DINGID['orderid'] ,
                   'appid' => $PAYID,
                    'tyid' => $TYID ,
                   'ztyid' => $DINGID['payjine'],
                  'beizhu' => $DINGID['tongyiid'] ==''?$DINGID['uid']: $DINGID['tongyiid'],
                   'yburl' => $PAYYB,
                   'tburl' => $PAYTB,
    );

    $DATA['key'] = md5( $PAYID .$DATA['tyid'] . $DATA['ztyid'] . $DATA['order'] . $PAYKEY . $DATA['beizhu'] );

    $sHtml = "<form id='wangyasubmit' name='wangyasubmit' action='".$PAYHT."' method='post'>";

    while ( list ( $key, $val ) = each ( $DATA ) ) {

           $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }

    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

    $sHtml = $sHtml."<script>document.forms['wangyasubmit'].submit();</script>";

    return htmlout($sHtml,$WY);



}else if($PLAYFS  == '2'){ //异步通信

    

      if( isset( $_NPOST['ordernum'] ) &&  isset( $_NPOST['off'] ) ){

          $key = md5($PAYID.$_NPOST['ordernum'].$_NPOST['usernum'].$_NPOST['rjine'].$_NPOST['remark'].$_NPOST['off'].$_NPOST['zhen'].$PAYKEY );
         

           if( $key == $_NPOST['akey']){



              if( $_NPOST['off'] =='2') chongzhifan( $_NPOST['ordernum']  , $_NPOST['rjine']  , $_NPOST['usernum'] );

           }

      }

    return htmlout('success',$WY);

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