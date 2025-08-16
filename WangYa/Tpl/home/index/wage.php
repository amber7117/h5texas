<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

//$USERID = 388;

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


$jishu = isset($CONN['jinejishu'])?(int)$CONN['jinejishu']:10;
if( $jishu > 10 ) $jishu = 10;
if($jishu < 0) $jishu = 10;


if($MOD == 'get'){
    /*获取数据*/


    $rel = gongzi($USERID,$jishu,'jinejl','gzlog');

    $tticheng = logac('ticheng');
    $data = array();
    foreach($tticheng as $v){
        $arr = explode('_',$v);

        if($arr[2] == 1){
            $a = [];
            $a[$arr[0]] = $arr[1];
            $data[] = $a;
        }
    }


    $CODE = 1;
    $STAT = 200;

    $SHUJU['data'] = $rel;
    $SHUJU['list'] = $data;

}else if($MOD == 'post'){
    /*领取工资*/

    $rel = gongzi($USERID,$jishu,'jinejl','gzlog');

    if($rel['code'] == 1){

        $D = db('user');

        $sql = $D -> setbiao('user') -> setshiwu(1) -> where(['uid'=>$USERID]) -> update(['yongjin +'=>$rel['gz']]);
        $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
            'uid' => $USERID,
            'jine' => $rel['gz'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'atime' => time(),
            'type' => 20,
            'data' => '领取工资',
        ]);
        $sql .= $D -> setbiao('gzlog') -> setshiwu(1) -> insert([
            'gz_uid' => $USERID,
            'gz_jine' => $rel['gz'],
            'gz_time' => time(),
        ]);

        $result = $D -> query($sql,'shiwu');

        if($result){
            $rel['gz'] = 0;
            $CODE = 1;
            $SHUJU['data'] = $rel;
            $MSG = $rel['msg'];
        }else{

            $CODE = -1;
            $SHUJU['data'] = $rel;
            $MSG = '领取失败，强稍后重试';
        }


    }else{
        $CODE = $rel['code'];
        $SHUJU['data'] = $rel;
        $MSG = $rel['msg'];
    }

    $STAT = 200;

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);