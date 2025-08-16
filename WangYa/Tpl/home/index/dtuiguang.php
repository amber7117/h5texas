<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);



if($MOD == 'get'){
    /*获取数据*/


    $YZHost = 'weiyi/'.md5('get'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    /*小于1 多条数据*/

    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

    if($NUM < 8){
        
        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

    $D =db('user');

  
    $ZONG = array();

    $ren =$jine = 0;


    for($i =1;$i< 8 ; $i++){


        if($i == 1){ 

            $stert = time();
            $end = mktime(0,0,0,date('m'),date('d'),date('y'));

        }else{

            $stert = mktime(0,0,0,date('m'),date('d')-($i-2),date('y'));
            $end = mktime(0,0,0,date('m'),date('d')-($i-1),date('y'));

        }


        $ZONG[$i]['time'] = date( 'Y-m-d', $end ); 

        $where = array('atime >='=>$end, 'atime <'=> $stert,'tuid' => $USERID);

        $ZONG[$i]['reg'] = $D ->setbiao('user')->where( $where ) -> total();

        $where['appid'] = 1;

        $ZONG[$i]['yox'] = $D ->setbiao('user')->where( $where ) -> total();

        unset($where['tuid']);

         $wheres  =  $D ->setbiao('dingdan')-> wherezuhe( array( 'off'=> 2,'shid'=>$USERID,'atime >='=>$end, 'atime <'=> $stert ) );

        $fanhui =  $D ->setbiao('dingdan')-> qurey( "select sum(rejine) num from `".$D->biao().'` '.$wheres );

        $ZONG[$i]['pay'] = (float)$fanhui['num'];

    }
    $i++;

    $ZONG[$i]['time'] = '全部 统计';

    $ZONG[$i]['reg'] = $D ->setbiao('user')->where( array('tuid' => $USERID) ) -> total();
    $ZONG[$i]['yox'] = $D ->setbiao('user')->where( array('tuid' => $USERID,'appid' => 1) ) -> total();
    $wheres  =  $D ->setbiao('dingdan')-> wherezuhe( array( 'off'=> 2,'shid'=>$USERID) );
    $fanhui =  $D ->setbiao('dingdan')-> qurey( "select sum(rejine) num from `".$D->biao().'` '.$wheres );
    $ZONG[$i]['pay'] =(float)$fanhui['num'];

    $SHUJU['data']=  $ZONG;
    $SHUJU['reg'] =$ren;
    $SHUJU['jine'] =$jine;

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);