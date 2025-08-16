<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式
$PAYHT  = 'https://mapi.alipay.com/gateway.do?'; //支付通信地址
$CHAHT  = 'http://mapi.alipay.com/trade/notify_query.do?';
$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址


if($SHOUJI || ( isset($_NPOST['wap']) &&  $_NPOST['wap']  == '1' )){

    $PAYHT  = 'http://wappaygw.alipay.com/service/rest.htm?'; //支付通信地址



    $lujin = WYPHP.'Tpl/pay/wap'. anquanqu( $PAYAC ['payfile'] ).'.php';

    if( is_file( $lujin ) ){

        return include $lujin;
    }

    return htmlout('未见不存在' ,$WY);

}


if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/



    $parameter = array(

        "service"           =>  "create_direct_pay_by_user",
        "payment_type"      =>  "1",
        "partner"           =>  $PAYID,
        "_input_charset"    =>  'UTF-8',
        "seller_email"      =>  $PAYZH,
        "return_url"        =>  $PAYTB,
        "notify_url"        =>  $PAYYB,
        "out_trade_no"      =>  $DINGID['orderid'],
        "subject"           => 'PAY充值',
        "body"              => 'PAY充值',
        "total_fee"         =>  $DINGID['payjine'],
        "extra_common_param"  =>  $DINGID['uid'] 
        
    );

    $parameter = argSort($parameter);

    $parameter['sign'] = md5(getarray($parameter).$PAYKEY);
    $parameter['sign_type'] = 'MD5';

    $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$PAYHT."_input_charset=UTF-8' method='get'>";
    while (list ($key, $val) = each ($parameter)) {
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }
    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";
    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
    return htmlout($sHtml,$WY);


}else if($PLAYFS  == '2'){ //异步通信

    /*

    商家订单id   返回金额 用户的dindgan

    chongzhifan( $_NPOST['ordernum']  , $_NPOST['rjine']  , $_NPOST['usernum'] );

    */

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
            
            header("Location:". WZHOST.'chading.html?id='.$_NGET['out_trade_no']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}