<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER  = uid( $USERID );

$QQIDANDAO = $Mem  -> g("qiandao/".$USERID);
if(!$QQIDANDAO ){

    $QQIDANDAO  = array(
        'qday' => 0,
        'qdtime' => 0,
        'zatime' => 0,
        'zcishu' => 0,
    );

    $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);

}

$jinday =  mktime(0,0,0,date("m"),date("d"),date("Y")); 


if($MOD == 'get'){


    /*大转盘*/

    if($CONN['zhuanpancishu'] < 1){

        return apptongxin(array(),415,-1,'大转盘关闭',$YZTOKEN,$WY);
    }
  
    $YZHost = 'weiyi/'.md5('post'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);


    $yinday =  mktime(0,0,0,date("m",$QQIDANDAO['zatime']),date("d",$QQIDANDAO['zatime']),date("Y",$QQIDANDAO['zatime'])); 

    if($yinday == $jinday){


        if($USER['turnnum'] <= 0){

            return apptongxin(array(),415,-2,"转盘次数已用完",$YZTOKEN,$WY);
            
        }

        $jins = array('jine','jifen','huobi');
        $bsshi = isset( $jins[$CONN['zhuanpanbi']])?$jins[$CONN['zhuanpanbi']]:'huobi';

        if( $USER[$bsshi] < $CONN['zhuanpanxiao'] ){

           return apptongxin($SHUJU,415,-1,$CONN[$bsshi]."不足1",$YZTOKEN,$WY);
        }



        if($CONN['zhuanpanxiao'] > 0){

            $jine = 0;
            $jifen = 0;
            $huobi = 0;

            if($CONN['zhuanpanbi'] == '0'){
                 $jine = -$CONN['zhuanpanxiao'];

            }else if($CONN['zhuanpanbi'] == '1'){
                $jifen = -$CONN['zhuanpanxiao'];

            }else if($CONN['zhuanpanbi'] == '2'){
                $huobi = -$CONN['zhuanpanxiao'];

            }else{
                $jifen = -$CONN['zhuanpanxiao'];
            }


            $USER  = jiaqian ($USERID,10,$jine,$jifen,$huobi );

            if(!$USER ){

                return apptongxin($SHUJU,415,-1,"扣除".$CONN[$bsshi]."失败",$YZTOKEN,$WY);
            }
        }
        
    }else{

        $QQIDANDAO['zcishu']  = 0;
    }

    $QQIDANDAO['zatime'] = time();
    $QQIDANDAO['zcishu'] += 1;
    $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);


    $zhuanpan = logac("zhuanpan",1);
    $jiangli = array();

    if($zhuanpan){

        $i = 0;
        foreach($zhuanpan as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $jiangli[$i] = array( $YANS[0], $YANS[1],$YANS[2]);
                if(isset($YANS[3])){
                    $jiangli[$i][] = $YANS[3];
                }
                
                $i++;
            }
        }   
    }
    
 

    $randnum = "0";
    $jianglis = "0";
    $names = '谢谢惠顾';

    // if( rand(1,2) != 1 ){
    //     $randnum = "0";
    //     $jianglis = "0";

    // }else{
        $KKK = '';
        $jianglibili = 0;
        $rand = rand(1,10000);
      
        foreach($jiangli as $k=>$v){
            if(isset($v['3'])){

                if($rand > $jianglibili && $rand <= $jianglibili+$v['3']*10000){
                    $KKK = $k;
                    break;
                }
                $jianglibili += $v['3']*10000;
            }
        }
      
        if($KKK == ''){
            $KKK = array_rand($jiangli,1);
        }
        
        $FANHUIS = $jiangli[ $KKK  ];


        if($FANHUIS){
            $randnum = $KKK;
            $jianglis = $FANHUIS[1];
            $names = $FANHUIS[2];

            if($FANHUIS[1] > 0){

                $JINE = 0;
                $jifen = 0;
                $huobi = 0;

                if($FANHUIS[0]  == "0"){
                    $JINE = $FANHUIS[1];
                }else if($FANHUIS[0]  == "1"){
                    $jifen = $FANHUIS[1];
                }else if($FANHUIS[0]  == "2"){
                    $huobi = $FANHUIS[1];
                }
                $back = db('user') -> where(array('uid' => $USERID)) -> update( array('turnnum -' => 1));
                if($back){
                    $USER = jiaqian($USERID,9,$JINE,$jifen,$huobi,'大转盘获取奖励','',0,0);
                    if(!$USER){
    
                        return apptongxin($SHUJU,415,-1,"奖励失败",$YZTOKEN,$WY);
                    }
                }else return apptongxin($SHUJU,415,-1,"奖励失败1",$YZTOKEN,$WY);
            }
            
        }else  return apptongxin($SHUJU,415,-1,"转盘暂无奖励",$YZTOKEN,$WY);
    // }
    





    $SHUJU = array(

        
        'huobi' => $USER['huobi'],
        'jifen' => $USER['jifen'],
        'turnnum' => $USER['turnnum'],
        'randnum' => $randnum,
        'jiangli' => $jianglis,
        'name' => $names,
      
    );



}else if($MOD == 'post'){
    /*签到*/
    
    $YZHost = 'weiyi/'.md5('post'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $yinday =  mktime(0,0,0,date("m",$QQIDANDAO['qdtime']),date("d",$QQIDANDAO['qdtime']),date("Y",$QQIDANDAO['qdtime'])); 

    if($yinday == $jinday){

        return apptongxin($SHUJU,415,-1,"已经签到",$YZTOKEN,$WY);
    }

    $QQIDANDAO['qday'] = $QQIDANDAO['qday']+1;
    $QQIDANDAO['qdtime'] = time();

    if($QQIDANDAO['qday'] > 7) $QQIDANDAO['qday'] = 1;

    $zhuanpan = logac("qianjiangli");
    $JIANGLI = array();


    if($zhuanpan){

        $i = 1;
        foreach($zhuanpan as $shuju){

            if($shuju){

                $YANS = explode("_",$shuju);
                $JIANGLI[$i] = array( $YANS[0],$YANS[1]  );
                $i++;
            }

           
        }
    }

    if( !isset( $JIANGLI[$QQIDANDAO['qday']])){

        return apptongxin($SHUJU,415,-1,"签到奖励不存在",$YZTOKEN,$WY);

    }

     $jing = $JIANGLI[$QQIDANDAO['qday']];

     $JINE = 0;
     $jifen = 0;
     $huobi = 0;

     if($jing[0]  == "0"){
        $JINE = $jing[1];
     }else if($jing[0]  == "1"){
        $jifen = $jing[1];
     }else if($jing[0]  == "2"){
        $huobi = $jing[1];
     }

     $USER  = jiaqian ($USERID,8,$JINE,$jifen, $huobi );

     if(!$USER){

        return apptongxin($SHUJU,415,-1,"签到奖励失败",$YZTOKEN,$WY);
     }

     $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);

     $SHUJU = array(

        'huobi' => $USER['huobi'],
        'jifen' => $USER['jifen'],
        'qday' => $QQIDANDAO['qday']
     );



}else if($MOD == 'put'){
    /*修改数据
        用户兑换数据
    */

    if($CONN['fangkajin']  == "0"){

        return apptongxin($SHUJU,415,-1,"兑换系统关闭",$YZTOKEN,$WY);
    }

    $SHULIANG = (int)( isset($_NPOST['jine'])?$_NPOST['jine']:0);
    /*兑换的数量*/
    if($SHULIANG  < 1){
        return apptongxin($SHUJU,415,-1,"兑换数据错误",$YZTOKEN,$WY);
    }

    if($USER['jifen'] < $SHULIANG){

        return apptongxin($SHUJU,415,-1,$CONN['jifen']."不足无法兑换",$YZTOKEN,$WY);
    }


    $huobi = $SHULIANG * $CONN['fangkajin'];

    $USER = jiaqian($USERID,13,0, -$SHULIANG , $huobi);

    if(!$USER){

        return apptongxin($SHUJU,415,-1,"兑换失败",$YZTOKEN,$WY);
    }

    $SHUJU = array(
        'jifen'=>$USER['jifen'],
        'huobi' =>$USER['huobi'],
    );



}else if($MOD == 'delete'){
    /*删除数据*/
  
    $HAHSS =  "fenxiang/".$USERID;
    $QQIDANDAOs = $Mem  -> g($HAHSS);
    if( !$QQIDANDAOs ){

        $QQIDANDAOs = 0;
    }

    if($QQIDANDAOs < 2){


        $Mem  -> s($HAHSS,$QQIDANDAOs+1,3600*24);

        $USER = jiaqian($USERID,16,0, 0, $CONN['shangjljb']);

        $SHUJU = array(
            'jifen'=>$USER['jifen'],
            'huobi' =>$USER['huobi'],
        );

    }else{

        return apptongxin($SHUJU,415,-1,"24小时内分享次数到达2次",$YZTOKEN,$WY);
    }


 

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);