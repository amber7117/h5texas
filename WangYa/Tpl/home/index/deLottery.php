<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$today_time =  mktime(0,0,0,date("m"),date("d"),date("Y"));
$D = db('delottery');

$once = isset($CONN['dez_once'])?(int)$CONN['dez_once']:100;//每次抽奖消耗金币数

if( $once < 1 ) return apptongxin($SHUJU,415,-8,"抽奖金额未设置",$YZTOKEN,$WY);

if($MOD == 'get'){

    /*大转盘数据*/
    $zhuanpan = logac("lottery",1);
    $jiangli = array();

    if($zhuanpan){

        $i = 0;
        foreach($zhuanpan as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $jiangli[$i] = array( $YANS[0], $YANS[1],$YANS[2] );
                $i++;
            }
        }
    }

    /*当日押注金额*/
    $where =  $D -> wherezuhe( ['bet_uid'=>$USERID,'bet_time >='=>$today_time] );
    $result = $D -> qurey("select sum(bet_sum) num from `ay_dezhoubetlog` ".$where);
    $bet_sum =(float)$result['num'];

    /*当日充值金额*/
    $where =  $D -> wherezuhe( ['uid'=>$USERID,'atime >='=>$today_time,'type'=>2] );
    $result = $D -> qurey("select sum(jine) num from `ay_user` ".$where);
    $pay_sum =(float)$result['num'];

    $bet_num = (int)$CONN['dez_betnum']; //押注多少转一次盘
    $pay_num = (int)$CONN['dez_paynum']; //充值多少转一次盘
    $number = (int)( $bet_sum/$bet_num ) + (int)( $pay_sum/$pay_num );

    $extract_num = 0; //已抽次数
    $rel = $D -> setbiao('delottery') -> where(['dlo_time >='=>$today_time,'dlo_uid'=>$USERID]) -> find(); //已抽数据
    if( $rel ){
        $extract_num = $rel['dlo_num'];
    }

    $num = (float)($number - $extract_num);

    $CODE = 1;
    $STAT = 200;
    $SHUJU['data'] = $jiangli;
    $SHUJU['number'] = $num > 0?$num:0;
    $SHUJU['bet_num'] = (float)$bet_num;
    $SHUJU['pay_num'] = (float)$pay_num;
    $SHUJU['once'] = (float)$once;
    $SHUJU['state'] = $CONN['dez_turntable'];


}else if($MOD == 'post'){
    /*抽奖*/

    if($CONN['dez_turntable'] != 1){

        return apptongxin(array(),415,-1,'转盘关闭',$YZTOKEN,$WY);
    }

    $l_type = (int)(isset($_NPOST['type'])?$_NPOST['type']:0); //抽奖类型 0免费 1金币抽

    $YZHost = 'dez_turntable/'.md5('post'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){
        return apptongxin(array(),415,-2,'请不要重复提交',$YZTOKEN,$WY);
    }
    $Mem ->s($YZHost,1,1);


    //余额
    $user = uid( $USERID,1 );

    $extract_num = 0; //已抽奖次数

    //金币抽
    if( $l_type == 1 ){
        if( $user['huobi'] < $once ){
            $Mem -> d($YZHost);
            return apptongxin(array(),415,-9,'余额不足，请充值',$YZTOKEN,$WY);
        }
    }

    $rel = $D -> setbiao('delottery') -> where(['dlo_time >='=>$today_time,'dlo_uid'=>$USERID]) -> find(); //已抽数据
    if( $rel ){
        $extract_num = $rel['dlo_num'];
    }


    /*当日押注金额*/
    $where =  $D -> wherezuhe( ['bet_uid'=>$USERID,'bet_time >='=>$today_time] );
    $result = $D -> qurey("select sum(bet_sum) num from `ay_dezhoubetlog` ".$where);
    $bet_sum =(float)$result['num'];

    /*当日充值金额*/
    $where =  $D -> wherezuhe( ['uid'=>$USERID,'atime >='=>$today_time,'type'=>2] );
    $result = $D -> qurey("select sum(jine) num from `ay_user` ".$where);
    $pay_sum =(float)$result['num'];

    $bet_num = (int)$CONN['dez_betnum']; //押注多少转一次盘
    $pay_num = (int)$CONN['dez_paynum']; //充值多少转一次盘
    $number = (int)( $bet_sum/$bet_num ) + (int)( $pay_sum/$pay_num );

    //免费抽
    if( $l_type != 1 ) {
        if ($extract_num >= $number) {
            $Mem->d($YZHost);
            return apptongxin($SHUJU, 415, -3, "抽奖次数已用完", $YZTOKEN, $WY);
        }
    }

    /*转盘奖励*/
    $zhuanpan = logac("lottery",1);
    $jiangli = array();

    if($zhuanpan){

        $i = 0;
        foreach($zhuanpan as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $jiangli[$i] = array( $YANS[0], $YANS[1],$YANS[2] );
                $i++;
            }
        }
    }


    $randnum = "0";
    $jianglis = "0";
    $names = '谢谢惠顾';

    $KKK = array_rand($jiangli,1);
    $FANHUIS = $jiangli[ $KKK  ];


    if($FANHUIS){

        //金币抽
        if( $l_type == 1 ) {
            //抽奖扣除金额
            $rel = $D -> setbiao('user') -> where(['uid' => $USERID])->update(['huobi -' => $once]);
            if (!$rel) {
                $Mem->d($YZHost);
                return apptongxin($SHUJU, 415, -7, "扣除金币失败", $YZTOKEN, $WY);
            }
        }else{

            if( $rel ){
                $result = $D -> setbiao('delottery') -> where(['dlo_id'=>$rel['dlo_id']]) -> update(['dlo_num +'=>1]);
                if( !$result ) {
                    $Mem->d($YZHost);
                    return apptongxin($SHUJU, 415, -7, "数据更新失败", $YZTOKEN, $WY);
                }
            }else{
                $result = $D -> setbiao('delottery') -> insert([
                    'dlo_uid'=>$USERID,
                    'dlo_time'=>time(),
                    'dlo_num'=>1,
                ]);
                if( !$result ) {
                    $Mem->d($YZHost);
                    return apptongxin($SHUJU, 415, -7, "数据更新失败1", $YZTOKEN, $WY);
                }
            }

        }

        $randnum = $KKK;
        $jianglis = $FANHUIS[1];
        $names = $FANHUIS[2];

        if($FANHUIS[1] >= 0){

            $JINE = 0;
            $jifen = 0;
            $huobi = 0;

            $huobi = $FANHUIS[1];//奖励金币

            if($FANHUIS[1] > 0){
                $USER = jiaqian($USERID,27,$JINE,$jifen,$huobi,'抽奖奖励','',0,0);

                if(!$USER){
                    $Mem -> d($YZHost);
                    return apptongxin($SHUJU,415,-1,"奖励失败",$YZTOKEN,$WY);
                }
            }
        }

    }else  {
        $Mem -> d($YZHost);
        return apptongxin($SHUJU,415,-2,"转盘暂无奖励",$YZTOKEN,$WY);
    }

    $Mem -> d($YZHost);

    if( $l_type == 1 ) {
        $huobi = (float)($user['huobi'] - $once + $jianglis);
    }else{
        $huobi = (float)($user['huobi'] + $jianglis);
    }

    $xiaohao = 0; //单局消耗
    if( $l_type == 0 ) $xiaohao = 1;

    $SHUJU = array(

        'huobi' => round( $huobi,2 ),
        'randnum' => $randnum,
        'jiangli' => $jianglis,
        'name' => $names,
        'once' => $once,
        'number' => $number - $extract_num - $xiaohao,

    );

    $SHUJU['bet_num'] = $bet_num;
    $SHUJU['pay_num'] = $pay_num;

}else if($MOD == 'put'){
    /**/


}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);