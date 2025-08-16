<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


/*
1.网银
2.一键支付
3非银行卡支付
4 支付宝扫描
5微信扫码
备注


工行 ICBC农行 ABC中行 BOC建行 CCB
交行 COMM招行 CMB浦发 SPDB兴业 CIB
民生 CMBC广发GDB中信 CNCB光大 CEB
华夏 HXB邮储PSBC平安PAB



*/

$PAYID  = $PAYAC['payid']  ; //支付的id
$PAYKEY = $PAYAC['paykey'] ; //支付的key
$PAYZH  = $PAYAC['zhanghao'] ; //支付的帐号 需要用到的填写
$PAYHT  = 'https://trade.mobaopay.com/cgi-bin/netpayment/pay_gate.cgi'; //支付通信地址
$TYID   = $PAYAC['beizhu']; //支付方式


$PAYYB  = WZHOST.'pay/'.anquanqu( $PAYAC ['payfile'] ).'.php'; //异步连接地址
$PAYTB  = WZHOST.'pay/tb'.anquanqu( $PAYAC ['payfile'] ).'.php'; //同步连接地址

function prepareSign($data) {
		if($data['apiName'] == 'MOBO_TRAN_QUERY') {
			$result = sprintf(
				"apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s",
				$data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt']
			);
			return $result;
		#} else if (($data['apiName'] == 'WEB_PAY_B2C') || ($data['apiName'] == 'WAP_PAY_B2C')) {
		} else if ($data['apiName'] == 'WEB_PAY_B2C') {
			$result = sprintf(
				"apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s&merchUrl=%s&merchParam=%s&tradeSummary=%s&customerIP=%s",
			$data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['merchUrl'], $data['merchParam'], $data['tradeSummary'],$data['customerIP']
			);
			return $result;
		} else if ($data['apiName'] == 'MOBO_USER_WEB_PAY') {
			$result = sprintf(
				"apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&userNo=%s&accNo=%s&orderNo=%s&tradeDate=%s&amt=%s&merchUrl=%s&merchParam=%s&tradeSummary=%s",
			$data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['userNo'], $data['accNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['merchUrl'], $data['merchParam'], $data['tradeSummary']
			);
			return $result;
		} else if ($data['apiName'] == 'MOBO_TRAN_RETURN') {
			$result = sprintf(
				"apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s&tradeSummary=%s",
				$data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['tradeSummary']
			);
			return $result;
		} else if ($data['apiName'] == 'PAY_RESULT_NOTIFY') {
			$result = sprintf(
				"apiName=%s&notifyTime=%s&tradeAmt=%s&merchNo=%s&merchParam=%s&orderNo=%s&tradeDate=%s&accNo=%s&accDate=%s&orderStatus=%s",
				$data['apiName'], $data['notifyTime'], $data['tradeAmt'], $data['merchNo'], $data['merchParam'], $data['orderNo'], $data['tradeDate'], $data['accNo'], $data['accDate'], $data['orderStatus']
			);
			return $result;
		} 
}



if( $PLAYFS  == '1'){  //发送

    if($SHOUJI){

        $shouji = 'WEB_PAY_B2C';
    
    
    }else{

        $shouji = 'WEB_PAY_B2C';
    }

   

  
    $DATA = array( 

        'apiName' => $shouji,
        'apiVersion' => '1.0.0.1',
        'platformID' => $PAYID,
        'merchNo' => $PAYZH,
         'tradeDate' => date('Ymd'),
         'orderNo' => $DINGID['orderid'],
        'amt' => 0.01,//$DINGID['payjine'],
        'merchUrl' => $PAYYB,
        'tradeSummary' => 'pay',
        'merchParam'=>'',
        'customerIP' => IP()

    );

    $ss = prepareSign($DATA);

    if(is_numeric($TYID)){
    
    $DATA['choosePayType'] = $TYID;

    }else{

        $DATA['bankCode'] =$TYID;
        $DATA['choosePayType'] = 1;
    }

    $DATA['signMsg'] = md5($ss.$PAYKEY);
   
    $sHtml = "<form id='wangyasubmit' name='wangyasubmit' action='".$PAYHT."' method='post'>";

    while ( list ( $key, $val ) = each ( $DATA ) ) {

           $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }

    $sHtml = $sHtml."<input type='submit' value='".$CONN['loading']."'></form>";

    $sHtml = $sHtml."<script>document.forms['wangyasubmit'].submit();</script>";

    return htmlout($sHtml,$WY);




}else if($PLAYFS =='2'){  // 异步处理





    if( isset( $_NPOST['notifyType'])){


        if($_NPOST['notifyType'] != '1'){

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

        
        }else{

            $ss = prepareSign($_NPOST);

            $sign = md5($ss.$PAYKEY);


            if( $sign == strtolower($_NPOST['signMsg'])){

                if( $_NPOST['orderStatus'] =='1'){

                    chongzhifan($_NPOST['accNo'],$_NPOST['tradeAmt'],$_NPOST['orderNo']);
                }
            }

        }
        
        


        exit('SUCCESS');

    }else exit('error');


}else if($PLAYFS =='3'){  //查询

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