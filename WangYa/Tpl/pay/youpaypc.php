<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式
$PAYHT  = 'http://sanfang.youpay.cc/dealpay.php'; //支付通信地址

if($TYID == 28){

    /*公众号支付*/
    $PAYHT  ='http://sanfang.youpay.cc/dealpay_wx.php';
}


$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

 /*
    $DINGID['orderid']; //订单id
    $DINGID['payjine']; //订单金额
    $DINGID['tongyiid'] ;  //备注
*/



    $DATA = array(
        'appid' => $PAYID,
        'orderid' =>  $DINGID['orderid'],
        'subject' => 'pay',
        'fee' => $DINGID['payjine'] *100,
        'tongbu_url' => $PAYYB,
        'clientip' => IP(),
        'body' => 'pay',
        'cpparam' => 'pay',
        'back_url' => $PAYTB,
        'sfrom' => $PAYZH,
        'paytype' => $TYID

    );

   




    $DATA['sign'] = Md5($PAYID.$DINGID['orderid'].$DATA['fee'].$DATA['tongbu_url'].$PAYKEY);

    if($DATA['sfrom'] == 'app'){


        if(strpos( $AGENT, "Android")){
        
            $DATA['mode']  = 'AND';

        }else{

             $DATA['mode']  = 'IOS';
        
        }

        $DATA['appname'] = '小曾突围';
        $DATA['appbs'] = 'com.wangya.games';

    
    }


    if($DATA['sfrom'] == 'pc'){

        $sHtml = "<form id='wangyasubmit' name='wangyasubmit' action='".$PAYHT."' method='get'>";

        while ( list ( $key, $val ) = each ( $DATA ) ) {

            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

        $sHtml = $sHtml."<script>document.forms['wangyasubmit'].submit();</script>";

        return htmlout($sHtml,$WY);

    }else{

        $fanhui = post($DATA,$PAYHT);
 
        if($fanhui ){

            if(!$SHOUJI&& 0){

                $fanhui = json_decode($fanhui,true);

                if($fanhui['code'] == 'success'){
                
                    header("Location:". $fanhui['msg']);
                    exit();
                
                }else{
                
                   
                    htmlout($fanhui['msg'],$WY);
                    exit();
                
                }
    
            }else exit($fanhui);

        }else exit( apptongxin(array(),200,-1,'没有数据',''  ));
    
    
    }



}else if($PLAYFS  == '2'){ //异步通信


    if(isset($_NGET['orderid'])){


        if(IP()!= "106.14.236.25"){

            exit("No");
        }

        $key = md5($_NGET['orderid'].$_NGET['result'].$_NGET['fee'].$_NGET['tradetime'].$PAYKEY);

        if($key == $_NGET['sign']){

            if($_NGET['result'] == 1){


                chongzhifan( $_NGET['orderid']  , $_NGET['fee']/100  , $_NGET['orderid'] );
            }
        }
    }

    

    return htmlout('ok',$WY);

}else if($PLAYFS  == '3'){ //同步返回

    if( $ISAPP ) {

        return App_Pay_tb($WY,$_NGET,$_NPOST);
      
    }else { 

        if(  isset( $_NGET['orderid']) && strlen( $_NGET['orderid'] ) > 10  ){
            
            header("Location:".WZHOST.'chading.html?id='.$_NGET['orderid']);
            return htmlout('1',$WY,302);

        }else{

            header("Location:". WZHOST);
            return htmlout('1',$WY,302);
        }
    }

}