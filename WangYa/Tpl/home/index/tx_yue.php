<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

//$USERID = $_GET['uid'];

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('user');

if($MOD == 'get'){
    /*获取数据*/

    $user = uid( $USERID,1 );

    $data = [];
    if( !$user || $user['off'] != 1 ) {

        $data['huobi'] = 0;
        $data['yongjin'] = 0;

    }else{

        $data['huobi'] = (float)$user['huobi'];
        $data['yongjin'] = (float)$user['yongjin'];

    }

    $txsxf = isset($CONN['txsxf'])?(float)$CONN['txsxf'] : 0;

    $data['txsxf'] = ( $txsxf * 100 ).'%';

    $CODE = 1;
    $STAT = 200;

    $SHUJU['data'] = $data;

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);