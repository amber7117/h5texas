<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);

if($USER['level'] < 1){

    return apptongxin($SHUJU,200,-3,"不是代理",$YZTOKEN,$WY);
}

$SHUJU['bizhong'] = array($CONN['jine'],$CONN['jifen'],$CONN['huobi']);
if($MOD == 'get'){
    /*获取数据*/
    $ID  = (int)(isset($_NPOST['uid'])?$_NPOST['uid']:0);

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    if($ID > 0){


        $tddd = uid($ID);

        if($tddd){

            $LEvel = logac('level');

            $zpay = logac('dlpaylv');

            $SHUJU['uname'] = $tddd['name'];
            $SHUJU['touxiang'] = touxiang($tddd['touxiang']);
            $SHUJU['uid'] = $tddd['uid'];
            $SHUJU['jifen'] =$tddd['jifen'];
            $SHUJU['huobi'] =$tddd['huobi'];
            $SHUJU['uijine'] = $USER['jifen'];
            $SHUJU['uleve'] = $LEvel[$USER['level']];
            $SHUJU['blv'] = $zpay[$USER['level']];
            $SHUJU['czl'] = $CONN['jifenbili'];

        }else{
            
            $CODE = -1;
            $STAT = 404;
        
        }
    
    
    
    }else{
    
        $CODE = -1;
        $STAT = 404;
    
    }




}else if($MOD == 'post'){
    /*新增数据*/


}else if($MOD == 'put'){
    /*修改数据*/

   

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);
    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $UID = (int)(isset($_NPOST['uid']) ? $_NPOST['uid'] : 0);

    if( $UID < 1){
    
         return apptongxin($SHUJU,415,-1,'用户不存在',$YZTOKEN,$WY);
    }

    if($USERID == $UID){


        return apptongxin($SHUJU,415,-1,'自己个给自己有意思吗?',$YZTOKEN,$WY);
    }

    $JINE = (int)(isset($_NPOST['jine']) ? $_NPOST['jine'] : 0);

    if( $JINE < 1){
    
         return apptongxin($SHUJU,415,-1,'充值金额错误',$YZTOKEN,$WY);
    }


    

    $YZHost = 'weiyi/'.md5('put'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $USSS = uid($UID);

    if( !$USSS){
    
        return apptongxin($SHUJU,415,-1,'用户不存在',$YZTOKEN,$WY);
    }

    $jifende = $PLI = $JINE;

    if($JINE > $USER['jifen']){
    
        return apptongxin($SHUJU,415,-1,$CONN['jifen'].'不足!无法给会员充值',$YZTOKEN,$WY);
    }

    $D = db('user');

    $sql  = $D ->setshiwu(1)->where(array('uid' =>$USERID,'jifen >='=>$PLI)) -> update(array('jifen -' => $PLI));
    $sql .= $D ->where(array('uid' => $UID)) -> update(array('jifen +' => $jifende));

    $sql .= $D -> setbiao('jifenlog')-> insert(
                    array(
                        'uid' => $USERID,
                        'type' => 5,
                        'data' => '给'.$UID.'充值',
                        'jine' => -$PLI,
                        'ip' => IP(),
                        'atime' =>time(),
                    )
                );
    $sql .= $D -> setbiao('jifenlog')-> insert(
                    array(
                        'uid' => $UID,
                        'type' => 4,
                        'data' => $USERID.'比例 ',
                        'jine' => $jifende,
                        'ip' => IP(),
                        'atime' =>time(),
                    )
                );
    $fan =  $D -> qurey($sql , 'shiwu');

    if( $fan){

        $ziji = uid($USERID,1);
        $chong = uid($UID,1);

        $SHUJU = array(

            'zji' => $ziji['jifen'],
            'cji' => $chong['jifen']
        );

        $CODE = 1;
        $STAT = 200;


    }else{

        return apptongxin($SHUJU,415,-1,'充值 失败',$YZTOKEN,$WY);
    }

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);