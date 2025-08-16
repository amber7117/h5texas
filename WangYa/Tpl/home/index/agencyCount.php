<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


if($MOD == 'get'){
    /*每级代理的个数 代理佣金 产生佣金方式*/

    $class_type = trim(isset($_NPOST['class_type'])?$_NPOST['class_type']:'dez_jishu'); //游戏代理级数标识
    $type = trim(isset($_NPOST['type'])?$_NPOST['type']:'dez_ji'); //游戏代理佣金标识

    $jishu = isset($CONN[$class_type])?$CONN[$class_type]:10;
    if($jishu > 10) $jishu = 10;
    else if($jishu < 0) $jishu = 0;
    $data = daili_count($USERID,$jishu);

    $CODE = 1;
    $STAT = 200;

    if($data){

        for($i=0;$i<$jishu;$i++){
            if(empty($data[$i])){
                $data[$i] = 0;
            }
        }

        $arr = [];
        for ($i=1;$i<=$jishu;$i++){

            $k = $type.$i;
            $v = isset($CONN[$k])?(float)$CONN[$k]:0;
            if( $v < 0 ) $v = 0;
            else $v = ($v * 100)."%";

            $arr[$i] = $v;
        }

        if(count($arr) == 2) $str = '30*10*10*'.$arr[1].'+30^2*10*10*'.$arr[2].'='.(30*10*10*(float)$arr[1]/100+30*30*10*10*(float)$arr[2]/100);
        else if(count($arr) == 1) $str = '30*10*10*'.$arr[1].'='.(30*10*10*(float)$arr[1]/100);
        else if(count($arr) == 0) $str = 0;
        else $str = '30*10*10*'.$arr[1].'+30^2*10*10*'.$arr[2].'+30^3*10*10*'.$arr[3].'='.(30*10*10*(float)$arr[1]/100+30*30*10*10*(float)$arr[2]/100+30*30*30*10*10*(float)$arr[3]/100);

        $SHUJU['data'] = $data;
        $SHUJU['brokerage'] = $arr;
        $SHUJU['formula'] = $str; //公式

    }else{
        $SHUJU['data'] = [];
        $SHUJU['brokerage'] = [];
        $SHUJU['formula'] = ''; //公式
    }

    return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);