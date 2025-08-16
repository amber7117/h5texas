<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/16
 * Time: 14:22
 */

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


$D = db('user');

if( $MOD == 'get' ){

    /* 市场 */

    $user = $D -> zhicha('uid,name,huobi,dan,ji_num,off,tuid,fodder') -> where(['uid'=>$USERID]) -> find();

    if($user){
        $SHUJU['data'] = $user;
    }
    else{
        $SHUJU['data'] = [];
        $CODE = -1;
        $MSG = '数据有误，请稍后重试';
    }

}else if( $MOD == 'post' ){
    /* post */


}else if( $MOD == 'put' ){
    /* put */


}else if( $MOD == 'delete' ){
    /* delete */


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);
