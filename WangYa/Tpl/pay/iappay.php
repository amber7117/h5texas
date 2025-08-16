<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android'))
{
    $PAYAC = xitongpay( "appweixin" );

    include WYPHP.'Tpl/pay/appweixin.php';
    exit();
}

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$TYID   = $PAYAC['beizhu']; //支付方式

#define SANDBOX @"https://sandbox.itunes.apple.com/verifyReceipt"
//正式环境验证
#define AppStore @"https://buy.itunes.apple.com/verifyReceipt"

$PAYHT  = 'https://sandbox.itunes.apple.com/verifyReceipt'; //支付通信地址
$PAYYB  = WZHOST.'pay/yb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

if( $PLAYFS  == '1'){//充值处理

    $DINGID['payjine'] = $DINGID['payjine'] *100;

    $CANSHU = array( 
        'out_trade_no' => $DINGID['orderid'],
        'total_fee' => $DINGID['payjine'],
        'notify_url' => $PAYYB
    );
     
    htmlhead('application/json;charset=UTF-8',$WY);
    return apptongxin( implode("@",$CANSHU),200,1,'支付串','',$WY);
        
  

}else if($PLAYFS  == '2'){ //异步通信

    $raw_post_data = file_get_contents( 'php://input' , 'r' ); 
    $raw_post_data = $raw_post_data ? $raw_post_data : $GLOBALS['HTTP_RAW_POST_DATA'] ;
    $code = "1";

    if($raw_post_data){

        $data = json_decode($raw_post_data,true);

        $token = md5( $data['dingid'].$data['time'].$data['receipt-data'].$PAYKEY);

        if($token == $data['key']){
            

            $post_data = array('receipt-data'=>$data['receipt-data']);  
            $ch = curl_init($PAYHT);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($ch, CURLOPT_POST, true);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  
            
            $response = curl_exec($ch);  
            $errno    = curl_errno($ch);  
            $errmsg   = curl_error($ch);  
            curl_close($ch);  
            
            if ($errno != 0) {  
                throw new Exception($errmsg, $errno);  
            }  
            
            $datas = json_decode($response, 1);
            
            if($datas['status'] == '0'){

                $dataa = reset($datas['receipt']['in_app']);

            

                $jins = explode(".",$dataa['product_id']);

                

                chongzhifan( $dataa['transaction_id'] , (float)( end($jins) /100) , $data['dingid'] );
                
                $code = "2";
                
            }



            



        }

       


    }


    
    return apptongxin( array() ,200,$code,'app支付','',$WY);

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