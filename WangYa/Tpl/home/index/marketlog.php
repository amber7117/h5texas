<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/14
 * Time: 17:39
 */

//if($USERID < 1) $USERID = $_NPOST['uid'];

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

if($MOD != 'get'){
    $hash = 'market/'.$USERID;
    $user_market = $Mem -> g($hash);
    if($user_market && $user_market > time()){
        return apptongxin($SHUJU,415,-7,"请勿重复提交",$YZTOKEN,$WY);
    }else{
        $Mem -> s($hash,time() + 1);
    }
    usleep(10);
}


$D = db('marketlog');

if( $MOD == 'get' ){

    /* 市场 */

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


    /*交易市场列表*/
    $rel = $D -> where(['ml_type'=>0]) -> limit($limit) -> select();

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
    /* post 够买*/

    $ID = isset( $_NPOST['id'] ) ? (int)( $_NPOST['id'] ) : 0; /*交易流水号*/
    if($ID < 1) return apptongxin($SHUJU,415,-2,"交易订单号有误",$YZTOKEN,$WY);

    $rel = $D -> where(['ml_id'=>$ID]) -> find();
    if(!$rel || $rel['ml_type'] != 0) return apptongxin($SHUJU,415,-3,"交易订单不存在",$YZTOKEN,$WY);

    $ml_totalprice = round( (float)($rel['ml_price'] * $rel['ml_num']),2 );
    $user = uid( $USERID,1 );
    if($user['huobi'] < $ml_totalprice) return apptongxin($SHUJU,415,-4,"余额不足",$YZTOKEN,$WY);

    $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['huobi -'=>$ml_totalprice,'dan +'=>$rel['ml_num']]); /*买家*/
    $sql .= $D -> setshiwu(1) ->  where(['uid'=>$rel['ml_uid']]) -> update(['huobi +'=>$ml_totalprice]); /*卖家*/
    $sql .= $D -> setbiao('marketlog') -> setshiwu(1) -> where(['ml_id'=>$ID,'ml_type'=>0]) -> update([
        'ml_muid' => $USERID,
        'ml_mtime' => time(),
        'ml_type' => 1,
    ]);

    $result = $D -> query($sql, 'shiwu');
    if($result){

        $arr['dan'] = (int)($user['dan'] + $rel['ml_num']);
        $arr['ji_num'] = (int)$user['ji_num'];
        $arr['fodder'] = (int)$user['fodder'];
        $arr['huobi'] = round( (float)($user['huobi'] - $ml_totalprice),2 );

        $SHUJU['data'] = $arr;

        $CODE = 1;
        $MSG = '购买成功';
    }else {
        $SHUJU['data'] = [];
        $CODE = -1;
        $MSG = '购买失败';
    }

}else if( $MOD == 'put' ){
    /* put 卖出*/

    $num = isset( $_NPOST['num'] ) ? abs((int)( $_NPOST['num'] )) : 0; /*鸡蛋个数*/
    $type = isset( $_NPOST['type'] ) ? (int)( $_NPOST['type'] ) : 0; /*类型 3：平台 0：市场*/
    if( $type != 3 ) $type = 0;

    if($num < 1) return apptongxin($SHUJU,415,2,'请输入出售的鸡蛋数',$YZTOKEN,$WY);

    $user = uid( $USERID,1 );
    if(!$user) return apptongxin($SHUJU,415,-2,'已掉线，请登陆',$YZTOKEN,$WY);
    elseif($user['off'] != 1) return apptongxin($SHUJU,415,-3,'账号被封，请联系客服',$YZTOKEN,$WY);

    if($user['dan'] < $num) return apptongxin($SHUJU,415,-4,'鸡蛋数不足',$YZTOKEN,$WY);

    $ma_price = 0; /*鸡蛋市场价*/
    $rel = $D -> setbiao('market') -> where(['ma_time <'=>time()]) -> order('ma_time desc') -> find();
    if($rel) $ma_price = isset($rel['ma_price'])?(float)$rel['ma_price']:0;
    if($ma_price <= 0) return apptongxin($SHUJU,415,-5,'还未开市，请稍后...',$YZTOKEN,$WY);



    if( $type == 3 ){

        $totalprice = round( (float)($ma_price * $num * 0.9),2 );

        $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['huobi +'=>$totalprice,'dan -'=>$num]);
        $sql .= $D -> setbiao('marketlog') -> setshiwu(1) -> insert([
            'ml_uid' => $USERID,
            'ml_time' => time(),
            'ml_price' => $ma_price,
            'ml_num' => $num,
            'ml_totalprice' => $totalprice,
            'ml_type' => $type,
        ]);
        $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
            'uid' => $USERID,
            'jine' => $total_price,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'atime' => time(),
            'type' => 23,
            'data' => '单价：'.$ma_price.'* 0.9 数量：'.$num,
        ]);

    }else{

        $totalprice = round( (float)($ma_price * $num),2 );

        $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['dan -'=>$num]);
        $sql .= $D -> setbiao('marketlog') -> setshiwu(1) -> insert([
            'ml_uid' => $USERID,
            'ml_time' => time(),
            'ml_price' => $ma_price,
            'ml_num' => $num,
            'ml_totalprice' => $totalprice,
            'ml_type' => $type,
        ]);

    }

    $result = $D -> query($sql, 'shiwu');
    if($result){

        $SHUJU['data'] = uid( $USERID,1 );

        $CODE = 1;
        $MSG = '提交成功';
    }else {
        $CODE = -1;
        $MSG = '提交失败';
    }



}else if( $MOD == 'delete' ){
    /* delete 取消订单*/

    $ID = isset( $_NPOST['id'] ) ? (int)( $_NPOST['id'] ) : 0; /*交易流水号*/

    if($ID < 1) return apptongxin($SHUJU,415,-2,"订单号有误",$YZTOKEN,$WY);

    $rel = $D -> where(['ml_id'=>$ID]) -> find();
    if(!$rel) return apptongxin($SHUJU,415,-3,"交易订单不存在",$YZTOKEN,$WY);
    elseif ($rel['ml_uid'] != $USERID) return apptongxin($SHUJU,415,-4,"订单不是你的，不可操作",$YZTOKEN,$WY);
    else if($rel['ml_type'] != 0){
        $type = logac('marketlog');
        return apptongxin($SHUJU,415,-5,'订单已被'.$type[$rel['ml_type']],$YZTOKEN,$WY);
    }else{

        $sql = $D -> setshiwu(1) -> where(['ml_id'=>$ID,'ml_uid'=>$USERID]) -> update(['ml_type'=>2,'ml_mtime'=>time()]);
        $sql .= $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['dan +'=>$rel['ml_num']]);
        $result = $D -> query($sql, 'shiwu');

        if($result){

            $user = uid( $USERID,1 );

            $arr['dan'] = (int)$user['dan'];
            $arr['ji_num'] = (int)$user['ji_num'];
            $arr['fodder'] = (int)$user['fodder'];
            $arr['huobi'] = round( (float)$user['huobi'],2 );

            $SHUJU['data'] = $arr;

            $CODE = 1;
            $MSG = '取消成功';

        }else{
            $CODE = -1;
            $MSG = '取消失败';
        }

    }

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);