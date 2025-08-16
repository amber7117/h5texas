<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$PAYHT  = 'https://pay.zszhifu.com/api/gateway.php?'.time(); //支付通信地址
$TYID   = $PAYAC['beizhu']; //支付方式

$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

$parameter = array(

    "payment_type"      =>  $TYID,  //支付方式 1 支付宝 2 微信
    "partner"           =>  $PAYID , //通信账号
    "return_url"        =>  $PAYTB,//同步地址
    "notify_url"        =>  $PAYYB, //异步地址
    "out_trade_no"      =>   $DINGID['orderid'], //订单id
    "total_fee"         =>  number_format($DINGID['payjine'], 2, '.', ''),  //支付金额 保留2位小数
    "extra_common_param" =>$DINGID['tongyiid'] , //用户备注
    'appid' => $DINGID['uid'],//应用编号 自定义
    'geformt'=>'html'
);


$parameter['sign'] = 

md5($PAYKEY.$parameter['payment_type'].'#'.$parameter['partner'].'#'.$parameter['return_url'].'#'.$parameter['notify_url'].'#'.$parameter['out_trade_no'].'#'.$parameter['total_fee'] .'#'.$parameter['extra_common_param'] .'#'.$parameter['appid'].'#'.$PAYKEY );


$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$PAYHT."' method='post'>";
while (list ($key, $val) = each ($parameter)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
}
$sHtml = $sHtml."<input type='submit' value='loading...'></form>";
$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
echo $sHtml;





    


}else if($PLAYFS  == '2'){ //异步通信


      if( isset( $_NPOST['out_trade_no'] ) &&  isset( $_NPOST['off'] ) ){


        $CNASHU = array(
            'partner' =>$_NPOST['partner'],  //通信账号
            'appid'=>$_NPOST['appid'],   //用户自定义appid
            'out_trade_no'=>$_NPOST['out_trade_no'],  //用户自定用账号
            'trade_no'=>$_NPOST['trade_no'],//系统订单
            'total_fee' =>  $_NPOST['total_fee'], //充值金额
            'off' => $_NPOST['off'],  //充值状态
            'extra_common_param'=>  $_NPOST['extra_common_param'],  //充值备份
            'atime' =>$_NPOST['atime'],
        );

        
        $CNASHU['sign'] = md5(implode("#", $CNASHU).'#'.$PAYKEY);

            if( $CNASHU['sign'] == $_NPOST['sign']){

               if( $_NPOST['off'] =='2'){

                    /**
                     * 掌上支付订单查询
                     */
                    $rel = is_pay( $_NPOST['partner'],$_NPOST['out_trade_no'],$_NPOST['trade_no'],$PAYKEY);
                    //rizhi('zspay',date('Y-m-d H:i:s').' '.ip().' '.json_encode($_NPOST).' sign:'.$CNASHU['sign'].' chaxun:'.$rel);
                    $rel = json_decode($rel,true);
                    if($rel['code'] == 1 && $rel['data']['off'] == 2 && $_NPOST['total_fee'] == $rel['data']['total_fee']){
                        chongzhifan( $_NPOST['trade_no']  , $_NPOST['total_fee']  , $_NPOST['out_trade_no'] );
                    }
               }
            }

      }

    return htmlout('success',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
        
    }else { 

        if(  isset( $_NGET['ordernum']) && strlen( $_NGET['ordernum'] ) > 10  ){
            
            header("Location: /");
            return htmlout('1',$WY,302);

        }else{

            header("Location: /");
            return htmlout('1',$WY,302);
        }
    }

}