<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D = db('dezhoubetlog');

if($MOD == 'get'){

    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

    if($NUM < 8){

        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }


    $WHERE = array();

    $limit = listmit( $NUM , $PAG);

    $WHERE = array('bet_uid' => $USERID);

    $DATA = $D -> where($WHERE) -> limit($limit) -> order('bet_id desc') -> select();

    if($DATA){

        $user = uid( $USERID );
        foreach ( $DATA as $k => $v ){

            $DATA[$k]['bet_time'] = date('m-d H:i',$v['bet_time']);
            $DATA[$k]['touxiang'] = pichttp($user['touxiang']);
            $DATA[$k]['bet_state'] = $v['bet_state'] == -1?'弃牌':($v['bet_state'] == 5?'ALL IN':'跟注');

        }


        $CODE = 1;
        $STAT = 200;
        $SHUJU['data'] = $DATA;

    }else{
        $CODE = -1;
        $SHUJU['data'] = [];
        $MSG = '没有更多数据';
    }


}else if($MOD == 'post'){

}else if($MOD == 'put'){


}else if($MOD == 'delete'){
    /*删除数据*/


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);