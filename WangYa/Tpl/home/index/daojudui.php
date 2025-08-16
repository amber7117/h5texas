<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


$WIYIHASH = 'weiyi/'.$USERID;


if($MOD == 'get'){
    /*获取数据*/
    $D  = db('jiangpin');
    /*小于1 多条数据*/
    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);
    if($NUM < 8){
        
        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

    $NUM = 100;

    
    $WHERE = array( 'off' => 1  ) ;

    $limit = listmit( $NUM , $PAG);

    $YZTOKEN = token();

    $Mem ->s($WIYIHASH ,$YZTOKEN,360);


    $DATA = $D -> where( $WHERE ) ->limit($limit) ->order('paixu desc,id desc')-> select();

    if($DATA){



        $SHUJU['jine'] = array();
        $SHUJU['biaoshi'] = array();
        $SHUJU['lxqiaojie'] = array();

        $daoju = logac('daoju');
        foreach($daoju as $k => $v){
            list( $name , $biaoshi , $miaosu , $suiji , $off  , $jine ,   $time ,$shiijan) = explode( "_", $v);

            if($off < 2 &&  $biaoshi != 'jine'  ){

                if($off == '0' ){
                    if($k < 3){
                        
                        $SHUJU['jine'][$k] = $name;
                        $SHUJU['biaoshi'] [$biaoshi] = $name;
                        $SHUJU['lxqiaojie'][$k] =$biaoshi;
                    }   
                    

                }else{

                    $SHUJU['jine'][$k] = $name;
                    $SHUJU['biaoshi'] [$biaoshi] = $name;
                    $SHUJU['lxqiaojie'][$k] =$biaoshi;
                }
                

            }
        }

        foreach($DATA as $k => $v){

            $DATA[$k]['tupian'] = pichttp($v['tupian']);

        }
           

        $CODE = 1;
        $STAT = 200;
        $SHUJU['shuju'] = $DATA;

    }else{

        $CODE = -1;

    }




    



}else if($MOD == 'post'){


    /*新增数据*/
    $TYPE     = (int) ( isset( $_NPOST['type']) ? $_NPOST['type']:0);
    $ID       = (int) ( isset( $_NPOST['id']) ? $_NPOST['id']:0);
    $xingming = anquanqu(isset( $_NPOST['xingming']) ? $_NPOST['xingming']:'');
    $dianhua  = (isset( $_NPOST['dianhua']) ? $_NPOST['dianhua']:'');
    $dizhi    = (isset( $_NPOST['dizhi']) ? $_NPOST['dizhi']:'');
    $TOKEN    = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:'';

    $YZTOKEN = $Mem ->g($WIYIHASH );

    if(!$YZTOKEN || $TOKEN == '' || $YZTOKEN!=  $TOKEN){

        $YZTOKEN = token();
        $Mem ->s($WIYIHASH ,$YZTOKEN,360);
        return apptongxin($SHUJU,415,-1,'token错误,请重试',$YZTOKEN,$WY);
    }



    $YZTOKEN = token();

    $Mem ->s($WIYIHASH ,$YZTOKEN,360);

    if($TYPE != 1 && $TYPE != 2){
        return apptongxin($SHUJU,415,-1,'类型错误',$YZTOKEN,$WY);
    }


    if($TYPE == 1){


        if(strlen($xingming) < 2 ){
            return apptongxin($SHUJU,415,-1,'收货人姓名错误',$YZTOKEN,$WY);
        }

        if(strlen($dianhua) < 2 ){
            return apptongxin($SHUJU,415,-1,'收货人电话错误',$YZTOKEN,$WY);
        }
    }

    $D  = db('jiangpin');

    $DATA = $D ->where( array( 'id' => $ID )) ->find();

    if(!$DATA || $DATA['off'] != 1){

        return apptongxin($SHUJU,415,-1,'礼品不存在',$YZTOKEN,$WY);
    }

    if($DATA['num'] < 1){

        return apptongxin($SHUJU,415,-1,'已被兑换完',$YZTOKEN,$WY);
    }
    
    
    //xgyongjiu

    $D  = db('jiangpinjilu');
    if($DATA['xgyongjiu']  > 0 ){

        $yigou = $D ->where(array( 'uid' => $USERID,'jiangid' =>$ID ))-> total();
        if($yigou >= $DATA['xgyongjiu']){

            return apptongxin($SHUJU,415,-1,'此礼品永久限兑 '.$DATA['xgyongjiu'].' 次',$YZTOKEN,$WY);
        }
    }


    if($DATA['xiangou'] > 0 ){


        if($DATA['xgday'] < 1){

            $yigou = $D ->where(array( 'uid' => $USERID,'jiangid' =>$ID ))-> total();

            if($yigou >= $DATA['xiangou']){
    
                return apptongxin($SHUJU,415,-1,'此礼品永久限兑 '.$DATA['xgyongjiu'].' 次',$YZTOKEN,$WY);
            }

        }else{
            $atime = time() - $DATA['xgday'] *3600*24;

            $yigou = $D ->where(array( 'uid' => $USERID,'jiangid' =>$ID,'atime >'=>$atime ))-> total();

            if($yigou >= $DATA['xiangou']){
    
                return apptongxin($SHUJU,415,-1,$DATA['xgday'].'天限兑'.$DATA['xiangou'].'次',$YZTOKEN,$WY);
            }

        }




    }

    

   

    $daoju = logac('daoju');

    $lxqiaojie = $biaoshis = $jine = array();
    foreach($daoju as $k => $v){

        list( $name , $biaoshi , $miaosu , $suiji , $off  , $jine ,   $time ,$shiijan) = explode( "_", $v);

    

        $off = (int)$off;

        $biaoshi = trim($biaoshi);

        if($off < 2 &&  $biaoshi != 'jine'  ){

            if($off == '0' ){
                if($k < 3){
                    
                    $jine[$k] = $name;
                    $biaoshis[$biaoshi] = $name;
                    $lxqiaojie[$k] =$biaoshi;
                }   
                

            }else{

                $jine[$k] = $name;
                $biaoshis[$biaoshi] = $name;
                $lxqiaojie[$k] = $biaoshi;
            }
            

        }
    }

    $names = $lxqiaojie[$DATA['xybizhong']];

    $names2 = $lxqiaojie[$DATA['bizhong']];

  

    $JIFEN  = $DATA['xyjine'];
    $JIFEN2 = $DATA['jine'];

    $TYPEs = 13;
    $ip = ip();

    $DATAs = $xingming." ".$dianhua;

    $sql =  $D ->setbiao('jiangpin')->setshiwu(1) ->where(array('id'=>$ID))->update( array( 'num -'=>1));
    $USER = uid($USERID);

    if($names == 'jine' || $names == 'huobi' || $names == 'jifen'){

       
        
        if(  $JIFEN  > $USER[$names] ){


            return apptongxin($SHUJU,415,-1,$biaoshi[$DATA['xyhuobi']].'不足无法兑换',$YZTOKEN,$WY);

        }

        $sql .= $D ->setbiao('user')->where( array( 'uid' =>$USERID ))->update( array( $names." -" =>  $JIFEN ) );
        $sql .= $D -> setshiwu(1) -> setbiao($names.'log') -> insert(array(   'uid' => $UID,
                'type' => $TYPEs,
                'jine' => -$JIFEN,
                'data' => $DATAs,
                'ip' => $ip ==''?ip(): $ip,
                'atime' => time()
        ));

    }else{

        $DAOJU  = GAMEDAOJU($USERID,$names);

        if($JIFEN > $DAOJU  ){

            return apptongxin($SHUJU,415,-1,$biaoshis[$DATA['xyhuobi']].'不足无法兑换',$YZTOKEN,$WY);
        }

        $sql .= $D -> setshiwu(1) ->setbiao('daoju') ->where( array( 'uid' =>$USERID ))->update( array( $names." -" =>  $JIFEN ));
        $sql .= $D -> setbiao('userlog')->insert(

            array(
                'uid' => $USERID,
                'type' => 6,
                'ip'=>$ip,
                'atime' =>time(),
                'data' => -$JIFEN 

            )

        );

    }

    /*
        上面扣除成功
    */


  
    $off  = 0;
     /*
            1 直接发货
            2 兑换为金币
        */

    

    if( $TYPE == 1  ){

        if($DATA['type'] != 1 && $DATA['type'] != 0){

            return apptongxin($SHUJU,415,-1,'无法直接兑换!',$YZTOKEN,$WY);
        }

        $JIFEN2 = 0;

        if( $USER['zpay'] < 1){

            return apptongxin($SHUJU,415,-1,'至少充值1元可兑换!',$YZTOKEN,$WY);
        }



    }else{

        if($DATA['type'] != 2 && $DATA['type'] != 0){

            return apptongxin($SHUJU,415,-1,'无法直接兑换!!',$YZTOKEN,$WY);
        }

        if($JIFEN2 == '0'){

            return apptongxin($SHUJU,415,-1,'无法直接兑换!!!',$YZTOKEN,$WY);
        }

        $off = 3;
        /*
            直接给奖励
        */

        $DATAs = $biaoshis[$names2]. " +" .$JIFEN2;

        if($names2 == 'jine' || $names2 == 'huobi' || $names2 == 'jifen'){

            $sql .= $D ->setbiao('user')->where( array( 'uid' =>$USERID ))->update( array( $names2." +" =>  $JIFEN2 ) );
            $sql .= $D -> setshiwu(1) -> setbiao($names2.'log') -> insert(array(   'uid' => $USERID,
                'type' => $TYPEs,
                'jine' => $JIFEN2,
                'data' => $DATAs,
                'ip' => $ip ==''?ip(): $ip,
                'atime' => time()
            ));



        }else{

            $sql .= $D ->setbiao('daoju')->where( array( 'uid' =>$USERID ))->update( array( $names2." +" =>  $JIFEN2 ) );

            $sql .= $D ->setbiao('userlog')->insert(

                    array(
                        'uid' => $USERID,
                        'type' => 6,
                        'ip'=>$ip,
                        'atime' =>time(),
                        'data' => $JIFEN2 

                    )

            );


        }

    }




    $SSSSS = array(
        'xingming' => $xingming,
        'dianhua' => $dianhua,
        'dizhi' => $dizhi,
        'ip' => $ip,
        'atime' =>time(),
        'off' =>$off,
        'bizhong' => $DATA['bizhong'],
        'jine' => $JIFEN2,
        'uid' =>$USERID,
        'jiangid' => $DATA['id'],
        'xtbeizhu' =>$DATAs,
        
    );

    $sql .= $D ->setbiao('jiangpinjilu') ->insert($SSSSS);

    $fanhui = $D ->qurey($sql,'shiwu');

    if($fanhui ){

        $USER = uid( $USERID , 1 );
        $DAOJU  = GAMEDAOJU($USERID);

        $DAOJU['jine'] = $USER['jine'];
        $DAOJU['jifen'] = $USER['jifen'];
        $DAOJU['huobi'] = $USER['huobi'];
        $DAOJU['num'] = $DATA['num']-1;

        $SHUJU = $DAOJU;
        $MSG = '兑换成功';

    }else{


        return apptongxin($SHUJU,415,-1,'兑换失败!!',$YZTOKEN,$WY);

    }

}else if($MOD == 'put'){
    /*修改数据*/
    $D  = db('jiangpinjilu');
    /*小于1 多条数据*/
    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);
    if($NUM < 8){
        
        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

    $NUM = 10;

    
    $WHERE = array( 'uid' => $USERID  ) ;

    $limit = listmit( $NUM , $PAG);

    $YZTOKEN = token();

    $Mem ->s($WIYIHASH ,$YZTOKEN,360);

    $chulioff = logac('chulioff');


    $DATA = $D ->zhicha('uid,jiangid,xtbeizhu,atime,kuainame,kuaihaoma,jine,off,dizhi,xingming,dianhua')-> where( $WHERE ) ->limit($limit) ->order('id desc')-> select();
    if( $DATA ){

        $SHUJU = array();

        foreach($DATA as $shuju ){

            $shuju['atime'] = date('Y-m-d H:i:s',$shuju['atime']);
            $shuju['offname'] =   $chulioff[$shuju['off']];
            $SHUJU[] = $shuju;

        }

       

    }





}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);