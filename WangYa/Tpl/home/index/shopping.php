<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/16
 * Time: 10:22
 */


//if($USERID < 1) $USERID = $_NPOST['uid'];

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


$D = db('marketlog');

if( $MOD == 'get' ){

    /* 市场 */

    $NUM = (int)(isset($_NPOST['limit'])?$_NPOST['limit']:10);
    $PAG = (int)(isset($_NPOST['page'])?$_NPOST['page']:1);

    $type = (int)(isset($_NPOST['type'])?$_NPOST['type']:0);

    if($NUM < 8){

        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

    $WHERE = array();
    $limit = listmit( $NUM , $PAG);


    if(!empty($type)){
        $where['ml_muid'] = $USERID; //购买记录
    }else{
        $where['ml_uid'] = $USERID; //发布的订单记录
    }

    /*交易市场列表*/
    $rel = $D -> where($where) -> limit($limit) -> order('ml_time desc') -> select();

    if($rel){

        foreach ( $rel as $k => $v ){
            $rel[$k]['ml_time'] = date('m-d',$v['ml_time']);
            $rel[$k]['ml_mtime'] = date('m-d',$v['ml_mtime']);
        }
        $CODE = 1;
        $SHUJU['data'] = $rel;

    }else{
        $CODE = -1;
        $SHUJU['data'] = [];
        $MSG = '没有更多数据';
    }



}else if( $MOD == 'post' ){
    /* post */


}else if( $MOD == 'put' ){
    /* put */


}else if( $MOD == 'delete' ){
    /* delete */


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);