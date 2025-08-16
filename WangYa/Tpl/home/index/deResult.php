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

    //总输赢
    $where =  $D -> wherezuhe( ['bet_uid'=>$USERID] );
    $result = $D -> qurey("select sum(bet_win) num from `".$D->biao().'` '.$where);
    $jine =(float)$result['num'];
    $SHUJU['jine'] = $jine;


    //30日内总输赢
    $time = mktime( 0,0,0,date('m'),date('d') - 30,date('Y') );
    $where =  $D -> wherezuhe( ['bet_uid'=>$USERID,'bet_time >='=>$time] );
    $result = $D -> qurey("select sum(bet_win) num from `".$D->biao().'` '.$where);
    $jine =(float)$result['num'];
    $SHUJU['jine30'] = $jine;


    //7日内总输赢
    $time = mktime( 0,0,0,date('m'),date('d') - 7,date('Y') );
    $where =  $D -> wherezuhe( ['bet_uid'=>$USERID,'bet_time >='=>$time] );
    $result = $D -> qurey("select sum(bet_win) num from `".$D->biao().'` '.$where);
    $jine =(float)$result['num'];
    $SHUJU['jine7'] = $jine;


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

    /*本局详情*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    $data = $D -> where(['bet_id'=>$ID,'bet_uid'=>$USERID]) -> find();
    if( !$data ) return apptongxin([],415,-1,'没有该数据',$YZTOKEN,$WY);

    $DATA = $D -> where(['bet_roomid'=>$data['bet_roomid'],'bet_time'=>$data['bet_time']]) -> select();

    if( $DATA ){

        $user = uid( $USERID );
        $i = 0;
        foreach ( $DATA as $k => $v ){

            $DATA[$k]['bet_time'] = date('m-d H:i',$v['bet_time']);
            $DATA[$k]['touxiang'] = pichttp($user['touxiang']);
            $DATA[$k]['name'] = $user['name'];
            $DATA[$k]['bet_state'] = $v['bet_state'] == -1?'弃牌':($v['bet_state'] == 5?'ALL IN':'跟注');

        }

        $CODE = 1;
        $STAT = 200;
        $SHUJU['data'] = $DATA;

    }else $CODE = -1;

}else if($MOD == 'put'){


}else if($MOD == 'delete'){
    /*删除数据*/


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);