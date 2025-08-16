<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('admin');

if($MOD == 'get'){
    /*获取数据*/

}else if($MOD == 'post'){
    /*新增数据*/

    $FANGID = 1;

    $GAMEIDIP = Game_Server("online" );

    $IP = fenpeiip($FANGID,$GAMEIDIP);

    $TONGXIN = md5( token().'扎_金_'.rand(1,99999).'_花房_卡'.$USERID);

    $usesuju = array('t'=>$TONGXIN,'u' => $USERID,'f' => $FANGID, 'glxin' => $_POST['gid'] );

    $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

    if(!$fan || $fan['code'] == '-1') exit( json_encode( apptongxin( array()  ,'415', '-1' , '服务器通信失败,请联系管理')) );

    $SHUJU = array( 'y' => 'ingame',
                   'd' => array('t' => $TONGXIN ,      //游戏通信 token
                                'ip' => $IP['ip'] ,  //分配的服务器ip
                                  'port' => $IP['port'],
                                   'gid' => "online",
                                   'fid' => "online",
                    )
            );


    return apptongxin($SHUJU ,200,1,'强行进入游戏',$YZTOKEN,$WY); 


}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);