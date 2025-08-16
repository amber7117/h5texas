<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/16
 * Time: 10:22
 */


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


$D = db('danlog');

if( $MOD == 'get' ){

    /* 鸡蛋、饲料记录 */

    $NUM = (int)(isset($_NPOST['limit'])?$_NPOST['limit']:10);
    $PAG = (int)(isset($_NPOST['page'])?$_NPOST['page']:1);

    if($NUM < 8){

        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }


    $WHERE = array();
    $limit = listmit( $NUM , $PAG);

    $where['dan_uid'] = $USERID;


    $type = (int)(isset($_NPOST['type'])?$_NPOST['type']:0); /*1:购买记录 2:喂小鸡 3：产蛋记录 5：奖励记录*/

    if($type < 0 || $type >5){
        return apptongxin($SHUJU,415,-1,"类型错误",$YZTOKEN,$WY);
    }

    if($type == 1){
        $where['dan_type IN'] = '0,1';
    }else $where['dan_type'] = $type;


    $rel = $D -> where($where) -> limit($limit) -> order('dan_atime desc') -> select();
    if($rel){

        $dan_type = logac('danlog');

        foreach ( $rel as $k => $v ){
            $rel[$k]['dan_atime'] = date('m-d',$v['dan_atime']);
            $rel[$k]['dan_type'] = $dan_type[$v['dan_type']];
        }

        $SHUJU['data'] = $rel;
    }else{
        $SHUJU['data'] = [];
        $CODE = -1;
        $MSG = '没有跟多数据';
    }


}else if( $MOD == 'post' ){
    /* post */


}else if( $MOD == 'put' ){
    /* put */

}else if( $MOD == 'delete' ){
    /* delete */


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);