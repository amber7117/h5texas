<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D = db('dezhoubetlog');

if($MOD == 'get'){
    /*每级代理的个数 代理佣金 产生佣金方式*/

    $type = fangFen();
    $SHUJU['type'] = $type;
    $date = [];
    $rel = $D -> where(['bet_uid'=>$USERID]) -> order('bet_id desc') -> find();

    if( $rel ){

        $GAMEIDIP = Game_Server('dezhouJ'); //获取游戏ip、端口
        if( $GAMEIDIP ){
            $IP = explode(':',$GAMEIDIP);
            $IP['ip'] = trim($IP[0]);
            $IP['port'] = trim($IP[1]);

            $fan = httpudp(['y'=>'fangcha','d'=>$rel['bet_roomid'],'uid'=>$USERID],$IP['ip'],  $IP['port'] );
            if( $fan['code'] == 1 ) {

                $data['roomid'] = $rel['bet_roomid'];
                $data['qu'] = $fan['msg']['gamequ'];
                $data['xm'] = $fan['msg']['xiaomang'];
                $data['dm'] = $fan['msg']['damang'];
                $data['xren'] = $fan['msg']['xren'];
                $data['ren'] = $fan['msg']['renall'];
            }
        }
    }

    $SHUJU['data'] = $data;
    $STAT = 200;
    $CODE = 1;

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);