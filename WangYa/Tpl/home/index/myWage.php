<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D = db('huobilog');

if($MOD == 'get'){
    /*获取工资数据*/

    $tticheng = logac('ticheng');
    $dailiticheng = array();
    foreach($tticheng as $v){
        $data = explode('_',$v);

        if($data[2] == 1){
            $dailiticheng[] = $data;
        }
    }


    $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = 18 AND jine > 0 AND `uid` = ".$AUSER['u'];
    $jine = $D -> query($sql,'other');
    $num = isset($jine['num'])?$jine['num']:0; /*所有佣金*/
    $rel['all'] = $num;

    $Ztime = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));
    $Jtime = mktime(0,0,0,date('m'),date('d'),date('Y'));
    $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = 18  AND jine > 0 and `atime` > $Jtime  AND `uid` = ".$AUSER['u'];
    $dayjine = $D -> query($sql,'other');
    $daynum = isset($dayjine['num'])?$dayjine['num']:0; /*今日佣金*/
    $rel['today'] = $daynum;

    /*昨日佣金*/
    $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = 18  AND jine > 0 AND `atime` >= $Ztime AND `atime` < $Jtime AND `uid` = ".$AUSER['u'];
    $jine = $D -> query($sql,'other');
    $zr_num = isset($jine['num'])?$jine['num']:0; /*昨日佣金*/

    $jine = CanGet( $zr_num,$AUSER['u'] ); /*可领工资*/

    if($jine > 0){
        $rel['yesterday'] = $zr_num;
        $rel['gz'] = $jine;
        $rel['code'] = 1;
    }elseif($jine == -1){
        $rel['yesterday'] = $zr_num;
        $rel['gz'] = 0;
        $rel['code'] = 3;
        $rel['msg'] = '已领取工资';
    }elseif ($jine == -2){
        $rel['yesterday'] = $zr_num;
        $rel['gz'] = 0;
        $rel['code'] = 2;
        $rel['msg'] = '过期';
    }else{
        $rel['yesterday'] = $zr_num;
        $rel['gz'] = 0;
        $rel['code'] = 4;
        $rel['msg'] = '未达到最低工资标准';
    }

    $CODE = 1;
    $STAT = 200;
    $SHUJU['data'] = $rel;
    $SHUJU['jinejl'] = $dailiticheng;


}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*领取工资*/


    $hash = 'gz/'.$USERID;
    if($Mem -> g($hash)){
        return apptongxin($SHUJU,415,-9,"请勿重复提交",$YZTOKEN,$WY);
    }
    $Mem -> s($hash,1,20);

    $Ztime = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));
    $Jtime = mktime(0,0,0,date('m'),date('d'),date('Y'));
    /*昨日佣金*/
    $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = 18  AND jine > 0 AND `atime` >= $Ztime AND `atime` < $Jtime AND `uid` = ".$USERID;
    $jine = $D -> query($sql,'other');
    $zr_num = isset($jine['num'])?$jine['num']:0; /*昨日佣金*/

    $jine = CanGet( $zr_num,$USERID ); /*可领工资*/

    if($jine > 0){

        $D = db('user');

        $sql = $D -> setbiao('user') -> setshiwu(1) -> where(['uid'=>$USERID]) -> update(['huobi +'=>$jine]);
        $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
            'uid' => $USERID,
            'jine' => $jine,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'atime' => time(),
            'type' => 4,
            'data' => '领取工资',
        ]);
        $sql .= $D -> setbiao('gzlog') -> setshiwu(1) -> insert([
            'gz_uid' => $USERID,
            'gz_jine' => $jine,
            'gz_time' => time(),
        ]);

        $result = $D -> query($sql,'shiwu');

        $Mem -> d($hash);

        if($result){

            $rel['gz'] = 0;
            $rel['yesterday'] = $zr_num;
            $rel['code'] = 1;

            $SHUJU['data'] = $rel;
            $MSG = '领取成功';
            $STAT = 200;
            $CODE = 1;
            return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);
        }else{
            return apptongxin($SHUJU,415,-8,"领取失败，请稍后重试",$YZTOKEN,$WY);
        }


    }else{

        $Mem -> d($hash);

        if($jine == -1){
            $rel['yesterday'] = $zr_num;
            $rel['gz'] = 0;
            $rel['code'] = 3;
            $rel['msg'] = '已领取工资';
        }elseif ($jine == -2){
            $rel['yesterday'] = $zr_num;
            $rel['gz'] = 0;
            $rel['code'] = 2;
            $rel['msg'] = '过期';
        }else{
            $rel['yesterday'] = $zr_num;
            $rel['gz'] = 0;
            $rel['code'] = 4;
            $rel['msg'] = '未达到最低工资标准';
        }

        return apptongxin($SHUJU,415,$rel['code'],$rel['msg'],$YZTOKEN,$WY);

    }

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);