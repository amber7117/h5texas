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


$D = db('user');

if( $MOD == 'get' ){

    /* 市场 */

    $data = $D -> zhicha('uid,name,chickentype,atime') -> where(['tuid'=>$USERID]) -> select();
    if($data){

        foreach ($data as $k => $v ){
            $data[$k]['atime'] = date( 'Y-m-d H:i:s',$v['atime'] );
        }

    }else $data = [];

    $SHUJU['data'] = $data;
    $SHUJU['num'] = count($data);

}else if( $MOD == 'post' ){
    /* post */

    $uid = isset( $_NPOST['uid'] ) ? (int)( $_NPOST['uid'] ) : 0; /*要送的好友id*/

    if($uid < 1) return apptongxin($SHUJU,415,-2,"用户id不能为空",$YZTOKEN,$WY);

    $suser = uid( $uid,1 ); /*要送的用户*/
    if(!$suser) return apptongxin($SHUJU,415,-3,"好友不存在",$YZTOKEN,$WY);
    elseif ($suser['off'] != 1) return apptongxin($SHUJU,415,-4,"好友账号被封",$YZTOKEN,$WY);
    elseif ( $suser['tuid'] != $USERID ) return apptongxin($SHUJU,415,-5,"该用户不是你的好友",$YZTOKEN,$WY);
    elseif ( $suser['chickentype'] == 1 ) return apptongxin($SHUJU,415,-1,"已送过，不可再送",$YZTOKEN,$WY);


    //鸡价格 几鸡蛋/鸡
    $price = isset($CONN['cfj_chicken_price'])?(int)$CONN['cfj_chicken_price']:100;
    if($price <= 0) return apptongxin($SHUJU,415,2,"暂停销售",$YZTOKEN,$WY);


    $user = uid( $USERID,1 );
    if(!$user) return apptongxin($SHUJU,415,-5,"已掉线，请重新登陆",$YZTOKEN,$WY);
    elseif($user['dan'] < $price) return apptongxin($SHUJU,415,-6,"鸡蛋不足,无法买鸡送好友",$YZTOKEN,$WY);


    $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['dan -'=>$price]);
    $sql .= $D -> setshiwu(1) ->  where(['uid'=>$uid]) -> update(["ji_num +"=>1,'chickentype'=>1]);
    $sql .= $D -> setbiao('danlog')-> setshiwu(1) -> insert([
        'dan_uid' => $USERID,
        'dan_jine' => $price,
        'dan_ip' => $_SERVER['REMOTE_ADDR'],
        'dan_atime' => time(),
        'dan_type' => 4,
        'dan_data' => '给'.$uid.'送鸡',
        'dan_price' => $price,
        'dan_num' => 1,
    ]);
    $sql .= $D -> setbiao('chickenhouse')-> setshiwu(1) -> insert([
        'ch_uid' => $uid,
        'ch_time' => time(),
        'ch_price' => $price,
    ]);

    $result = $D -> query($sql, 'shiwu');
    if($result){

        $arr['dan'] = (int)($user['dan'] - $price);
        $arr['ji_num'] = (int)$user['ji_num'];
        $arr['fodder'] = (int)$user['fodder'];
        $arr['huobi'] = round( (float)$user['huobi'],2 );

        $SHUJU['data'] = $arr;

        $CODE = 1;
        $MSG = '购买成功';

    }else {
        $CODE = -1;
        $MSG = '购买失败';
        $SHUJU['data'] = $sql;
    }


}else if( $MOD == 'put' ){
    /* put */


}else if( $MOD == 'delete' ){
    /* delete */


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);