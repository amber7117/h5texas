<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式
$PAYHT  = 'https://tmapi.jdpay.com/PayGate'; //支付通信地址
$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理


    $DATA = array( 
        
        'v_mid' => $PAYID,
        'v_oid' => $DINGID['orderid'],
        'v_amount' => $DINGID['payjine'],
        'v_moneytype' => 'CNY',
        'v_url' => $PAYTB,
        'pmode_id' => $TYID,
        'remark2' => $PAYYB 
    );



    $DATA['v_md5info'] = strtoupper ( md5( $DATA['v_amount'] .$DATA['v_moneytype'] . $DATA['v_oid'] . $DATA['v_mid'] . $DATA['v_url']. $PAYKEY  ));


    $sHtml = "<form id='wangyasubmit' name='wangyasubmit' action='".$PAYHT."' method='post'>";

    while ( list ( $key, $val ) = each ( $DATA ) ) {

           $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }

    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

    $sHtml = $sHtml."<script>document.forms['wangyasubmit'].submit();</script>";

    return htmlout($sHtml,$WY);



}else if($PLAYFS  == '2'){ //异步通信



    if( isset( $_NPOST['v_oid'])){

        $key = strtoupper( md5($_NPOST['v_oid'].$_NPOST['v_pstatus'].$_NPOST['v_amount'].$_NPOST['v_moneytype'].$PAYKEY ));

        if( $key == $_NPOST['v_md5str']){

            if( $_NPOST['v_pstatus'] =='20') chongzhifan( $_NPOST['v_oid']  , $_NPOST['v_amount']  , $_NPOST['v_oid'] );

        }

        return htmlout('ok',$WY);
    }
    

    return htmlout('error',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        if(  isset( $_NGET['v_oid']) && strlen( $_NGET['v_oid'] ) > 10  ){
            
            header("Location:". WZHOST.'chading.html?id='.$_NGET['v_oid']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}