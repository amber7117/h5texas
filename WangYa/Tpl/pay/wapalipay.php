<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/



    $parameter = array(
        "service"			=> "alipay.wap.create.direct.pay.by.user",
        "payment_type"		=> "1",
        "partner"			=> $PAYID,
        "_input_charset"	=> 'UTF-8',
        "seller_id"		=> $PAYID,
        "return_url"		=> $PAYTB,
        "notify_url"		=> $PAYYB,
        "out_trade_no"		=> $DINGID['orderid'] ,
        "subject"			=> '购买产品',
        "body"				=> '购买产品',
        "total_fee"			=>  $DINGID['payjine'] ,
        'app_pay' => 'Y',
        "show_url"	=>   WZHOST,
   );



    $parameter = argSort($parameter);

    $parameter['sign'] = md5(getarray($parameter).$PAYKEY);
    $parameter['sign_type'] = 'MD5';

    $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$PAYHT."' method='get'>";
    while (list ($key, $val) = each ($parameter)) {
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }
    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";
    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";

    return htmlout($sHtml,$WY);


}else if($PLAYFS  == '2'){ //异步通信

 

    if(isset($_NPOST['out_trade_no'])){

        foreach($_NPOST as $k => $v){
            if($v == ''){
            
                unset($_NPOST[$k]);
            }

        }


        $YKEY = $_NPOST["sign"];
        unset($_NPOST["sign"]);


        $parameter = argSort($_NPOST);

        $sign = md5(getarray($parameter).$PAYKEY);

        if($YKEY == $sign){

            $veryfy_url = $CHAHT."partner=".$PAYID."&notify_id=" .$_NPOST["notify_id"];

            $responseTxt = qfopen($veryfy_url);

            if (preg_match("/true$/i",$responseTxt)){

                $out_trade_no          = $_NPOST['out_trade_no'];        //获取订单号
                $trade_no              = $_NPOST['trade_no'];            //获取支付宝交易号
                $total_fee             = $_NPOST['total_fee'];            //获取总价格
                $extra_common_param    = $_NPOST['extra_common_param'];            //获取备注

                if( $_POST['trade_status'] == 'TRADE_SUCCESS' || $_POST['trade_status'] == 'TRADE_FINISHED'){
                     
                     chongzhifan( $trade_no , $total_fee  , $out_trade_no);
                }

                return htmlout('success',$WY);
            }

        }else{
        
            return htmlout('fail',$WY);
        }
    }

    return htmlout('fail',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        if(  isset( $_NGET['out_trade_no']) && strlen( $_NGET['out_trade_no'] ) > 10  ){
            
            header("Location:". WZHOST.'chading.html?='.$_NGET['out_trade_no']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}