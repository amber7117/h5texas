<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/12
 * Time: 10:04
 */

//if($USERID < 1) $USERID = $_NPOST['uid'];

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D = db('market');

if( $MOD == 'get' ){

    /*商城商品列表*/

    $ma_price = 0; /*鸡蛋市场价*/
    $rel = $D -> where(['ma_time <'=> time()]) -> order('ma_time desc') -> find();
    if($rel) $ma_price = isset($rel['ma_price'])?(float)$rel['ma_price']:0;

    $data = [
        [
            'id'=> 1,
            'touxiang'=> 'http://'.$_SERVER['HTTP_HOST'].'/attachment/store/ji.png',
            'name'=>'富贵鸡',
            'price'=> isset($CONN['cfj_chicken_price'])?(int)$CONN['cfj_chicken_price']:100,
            'unit'=> '只',
        ],[
            'id'=> 2,
            'touxiang'=> 'http://'.$_SERVER['HTTP_HOST'].'/attachment/store/fodder.png',
            'name'=>'饲料',
            'price'=> isset($CONN['cfj_fodder_price'])?(int)$CONN['cfj_fodder_price']:10,
            'unit'=> '袋',
        ],[
            'id'=> 3,
            'touxiang'=> 'http://'.$_SERVER['HTTP_HOST'].'/attachment/store/dan.png',
            'name'=>'鸡蛋',
            'price'=> $ma_price,
            'unit'=> '个',
        ]
    ];

    $SHUJU['data'] = $data;

}else if( $MOD == 'post' ){
    /* post 购买商品*/

    $ID = isset( $_NPOST['id'] ) ? (int)( $_NPOST['id'] ) : 0; /*商品id*/
    $num = isset( $_NPOST['num'] ) ? (int)( $_NPOST['num'] ) : 1; /*商品个数*/

    if($ID > 3 || $ID < 1) return apptongxin($SHUJU,415,-4,"商品不存在",$YZTOKEN,$WY);
    if( $num < 1 ) return apptongxin($SHUJU,415,0,"商品数量有误",$YZTOKEN,$WY);

    $user = uid( $USERID,1 );
    if($ID == 3){

        $ma_price = 0; /*鸡蛋市场价*/
        $rel = $D -> where(['ma_time <'=> time()]) -> order('ma_time desc') -> find();
        if($rel) $ma_price = isset($rel['ma_price'])?(float)$rel['ma_price']:0;

        if($ma_price <= 0) return apptongxin($SHUJU,415,-2,"暂时没有鸡蛋出售，请联系客服",$YZTOKEN,$WY);
        $total_price = round( (float)($ma_price * $num),2 );
        if( $user['huobi'] < $total_price ) return apptongxin($SHUJU,415,-3,"余额不足",$YZTOKEN,$WY);

        /*购买鸡蛋*/
        $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['huobi -'=>$total_price,'dan +'=>$num]);
        $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
            'uid' => $USERID,
            'jine' => $total_price,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'atime' => time(),
            'type' => 22,
            'data' => '单价：'.$ma_price.' 数量：'.$num,
        ]);

        $result = $D -> query($sql, 'shiwu');
        if($result){

            $arr['dan'] = (int)($user['dan'] + $num);
            $arr['ji_num'] = (int)$user['ji_num'];
            $arr['fodder'] = (int)$user['fodder'];
            $arr['huobi'] = round( (float)($user['huobi'] - $total_price),2 );

            $SHUJU['data'] = $arr;

            $CODE = 1;
            $MSG = '购买成功';
        }else {
            $CODE = -1;
            $MSG = '购买失败';
        }

    }else{

        if($ID == 1){
            /*购买鸡*/

            $price = isset($CONN['cfj_chicken_price'])?(int)$CONN['cfj_chicken_price']:100;

        }else{
            /*购买饲料*/

            $price = isset($CONN['cfj_fodder_price'])?(int)$CONN['cfj_fodder_price']:10;

        }

        if($price <= 0) return apptongxin($SHUJU,415,2,"暂停销售",$YZTOKEN,$WY);

        $total_price = (float)($price * $num);
        if($user['dan'] < $total_price) return apptongxin($SHUJU,415,-2,"鸡蛋不足",$YZTOKEN,$WY);

        if($ID == 1 && $user['ji_num'] >= 12) return apptongxin($SHUJU,415,-3,"最多拥有12只鸡",$YZTOKEN,$WY);


        $column = $ID == 1?'ji_num':'fodder';
        $type = $ID == 1?0:1;
        $dan_data = $ID == 1?'购买鸡':'购买饲料';

        $sql = $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['dan -'=>$total_price,"$column +"=>$num]);
        $sql .= $D -> setbiao('danlog')-> setshiwu(1) -> insert([
            'dan_uid' => $USERID,
            'dan_jine' => $total_price,
            'dan_ip' => $_SERVER['REMOTE_ADDR'],
            'dan_atime' => time(),
            'dan_type' => $type,
            'dan_data' => $dan_data,
            'dan_price' => $price,
            'dan_num' => $num,
        ]);
        if($ID == 1){

            for($i=0;$i<$num;$i++){
                $sql .= $D -> setbiao('chickenhouse')-> setshiwu(1) -> insert([
                    'ch_uid' => $USERID,
                    'ch_time' => time(),
                    'ch_price' => $price,
                ]);
            }

        }

        $result = $D -> query($sql, 'shiwu');
        if($result){

            $arr['dan'] = (int)($user['dan'] - $total_price);
            $arr['ji_num'] = (int)$user['ji_num'];
            $arr['fodder'] = (int)$user['fodder'];
            $arr['huobi'] = round( (float)$user['huobi'],2 );

            $arr[$column] += $num;
            $SHUJU['data'] = $arr;

            $CODE = 1;
            $MSG = '购买成功';

        }else {
            $CODE = -1;
            $MSG = '购买失败';
            $SHUJU['data'] = $sql;
        }

    }


}else if( $MOD == 'put' ){
    /* put 仓库*/

    $user = uid( $USERID,1 );

    if(!$user) return apptongxin($SHUJU,415,-1,"已掉线，请登录",$YZTOKEN,$WY);

    $data = [
        'ji_num'=>$user['ji_num'],
        'dan'=>$user['dan'],
        'fodder'=>$user['fodder'],
    ];

    $SHUJU['data'] = $data;

}else if( $MOD == 'delete' ){
    /* delete 其他操作*/


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);