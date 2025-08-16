<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/12
 * Time: 10:04
 */


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

if( $MOD != 'get' ){
    $hash = 'chickenhouse/'.$USERID;
    $user_market = $Mem -> g($hash);
    if($user_market && $user_market > time()){
        return apptongxin($SHUJU,415,-7,"请勿重复提交",$YZTOKEN,$WY);
    }else{
        $Mem -> s($hash,time() + 1);
    }
    usleep(10);
}

$user = uid($USERID,1);

if(!$user || $user['off'] != 1) return apptongxin($SHUJU,415,-1,"账号不存在或被封",$YZTOKEN,$WY);

$D = db('chickenhouse');


if( $MOD == 'get' ){

    /* 鸡场 */


    //存活的鸡
    $data = $D -> where(['ch_uid'=>$USERID,'ch_off'=>1]) -> select();

    if($data){

        foreach ( $data as $k => $v ){

            $lifetime = isset($CONN['cfj_life_time'])?(int)$CONN['cfj_life_time']:90; //小鸡存活时间（天）
            $layeggstime = isset($CONN['cfj_layeggs_time'])?(int)$CONN['cfj_layeggs_time']:24; //小鸡产蛋时间（小时）
            $layeggs_num =  isset($CONN['cfj_layeggs_num'])?(int)$CONN['cfj_layeggs_num']:100; //小鸡最大产蛋数

            if( ($v['ch_off'] == 1 && ($v['ch_time'] + $lifetime * 3600 * 24) < time()) || ($v['ch_num'] >= $layeggs_num) ){ /*超过存活期 || 产蛋达到上限*/
                $rel = $D -> where(['ch_id'=>$v['ch_id']]) -> update(['ch_off'=>0,'ch_dietime'=>time()]);
                if($rel) unset($data[$k]);
                else rizhi('chickenhouse','guoqu '.$v['ch_id']);
            }else if( $v['ch_type'] == 1 && $v['ch_feedtime'] > 0 && ($v['ch_feedtime'] + ($layeggstime * 3600)) <= time() ){

                $rel = $D -> where(['ch_id'=>$v['ch_id']]) -> update(['ch_type'=>2]);
                if($rel) $data[$k]['ch_type'] = 2;
                else rizhi('chickenhouse','layeggs '.$v['ch_id']);

            }

        }

        $SHUJU['data'] = $data;
        $SHUJU['user'] = $user;

    }else {

        $CODE = -1;
        $MSG = '没有更多数据';

    }





}else if( $MOD == 'post' ){
    /* post 喂食*/

    $ID = isset( $_NPOST['id'] ) ? (int)( $_NPOST['id'] ) : 0; /*小鸡编号*/
    if($ID <= 0) return apptongxin($SHUJU,415,-2,"小鸡编号不能为空",$YZTOKEN,$WY);

    if( $user['fodder'] < 1 ) return apptongxin($SHUJU,415,-1,"饲料不足",$YZTOKEN,$WY);

    $data = xiaoJi( $ID );
    if($data['code'] != 1) return apptongxin($SHUJU,415,$data['code'],$data['msg'],$YZTOKEN,$WY);

    $xiaoji = $data['data'];

    if($xiaoji['ch_off'] != 1) return apptongxin($SHUJU,415,-2,"小鸡已离世",$YZTOKEN,$WY);
    else if($xiaoji['ch_uid'] != $USERID) return apptongxin($SHUJU,415,-3,"小鸡不是你家的",$YZTOKEN,$WY);

    if( $xiaoji['ch_type'] != 0 ) return apptongxin($SHUJU,415,-3,"小鸡不需要喂食",$YZTOKEN,$WY);
    else {

        $sql = $D -> setshiwu(1) -> where(['ch_id'=>$ID]) -> update(['ch_type'=>1,'ch_feedtime'=>time()]);
        $sql .= $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['fodder -'=>1]);
        $sql .= $D -> setbiao('danlog') -> setshiwu(1) ->  insert([
            'dan_uid'=>$USERID,
            'dan_type'=>2,
            'dan_num'=>-1,
            'dan_atime'=>time(),
            'dan_ip'=> ip(),
            'dan_data'=> '小鸡编号'.$ID,
        ]);

        $result = $D -> query($sql, 'shiwu');

        if($result){

            $arr['fodder'] = (int)($user['fodder'] - 1);
            $arr['dan_type'] = 2;

            $SHUJU['data'] = $arr;

            $CODE = 1;
            $MSG = '成功';

        }else{
            $CODE = -1;
            $MSG = '喂养失败';
        }

    }

}else if( $MOD == 'put' ){
    /* put 收蛋*/

    $ID = isset( $_NPOST['id'] ) ? (int)( $_NPOST['id'] ) : 0; /*小鸡编号*/
    if($ID <= 0) return apptongxin($SHUJU,415,-2,"小鸡编号不能为空",$YZTOKEN,$WY);

    $data = xiaoJi( $ID );
    if($data['code'] != 1) return apptongxin($SHUJU,415,$data['code'],$data['msg'],$YZTOKEN,$WY);

    $xiaoji = $data['data'];

    if($xiaoji['ch_off'] != 1) return apptongxin($SHUJU,415,-2,"小鸡已离世",$YZTOKEN,$WY);
    else if($xiaoji['ch_uid'] != $USERID) return apptongxin($SHUJU,415,-3,"小鸡不是你家的",$YZTOKEN,$WY);
    elseif($xiaoji['ch_num'] >= (isset($CONN['cfj_layeggs_num'])?(int)$CONN['cfj_layeggs_num']:500 ) ){
        return apptongxin($SHUJU,415,-1,"小鸡产蛋数达到最大上限",$YZTOKEN,$WY);
    }

    if( $xiaoji['ch_type'] != 2 ) return apptongxin($SHUJU,415,-3,"小鸡还没有产蛋",$YZTOKEN,$WY);
    else {

        $num = 0; //小鸡本次的产蛋数
        if( time() <= ($xiaoji['ch_time'] + 30 * 3600 * 24) ){
            $num = isset($CONN['cfj_one_layeggs'])?(int)$CONN['cfj_one_layeggs']:0;
        }else if(time() <= ($xiaoji['ch_time'] + 60 * 3600 * 24) ){
            $num = isset($CONN['cfj_one_layeggs'])?(int)$CONN['cfj_one_layeggs']:0;
        }else{
            $num = isset($CONN['cfj_one_layeggs'])?(int)$CONN['cfj_one_layeggs']:0;
        }
        if($num < 1) $num = 1;

        $layeggsxkg = isset($CONN['cfj_layeggsxkg'])?(int)$CONN['cfj_layeggsxkg']:0; /*下蛋达到上限小鸡离世开关 0：关闭*/
        $layeggs_num = isset($CONN['cfj_layeggs_num'])?(int)$CONN['cfj_layeggs_num']:500; /*小鸡最大产蛋数*/
        $all = (int)$xiaoji['ch_num'] + $num;
        $is_shangxian = false;
        if( $all > $layeggs_num ){

            /*历史产蛋数超过或等于最大产蛋数*/
            if( $xiaoji['ch_num'] >= $layeggs_num ){

                if($layeggsxkg == 1){
                    $rel = $D -> where(['ch_id'=>$ID]) -> update(['ch_off'=>0,'ch_dietime'=>time()]);
                    if(!$rel) rizhi( 'sqlerr','',__FILE__.' '.__LINE__);
                }

                return apptongxin($SHUJU,415,-3,"已达小鸡产蛋上限",$YZTOKEN,$WY);

            }else{
                $num = $layeggs_num - $xiaoji['ch_num'];
            }

        }elseif ( $all == $layeggs_num && $layeggsxkg == 1 ){

            $is_shangxian = true;

        }


        $sql = $D -> setshiwu(1) -> where(['ch_id'=>$ID]) -> update(['ch_type'=>0]);
        if($is_shangxian) {
            $sql .= $D  -> setshiwu(1) -> where(['ch_id'=>$ID]) -> update(['ch_off'=>0,'ch_dietime'=>time()]);
        }
        $sql .= $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$USERID]) -> update(['dan +'=>$num]);
        $sql .= $D -> setbiao('danlog') -> setshiwu(1) ->  insert([
            'dan_uid'=>$USERID,
            'dan_type'=>3,
            'dan_num'=>$num,
            'dan_atime'=>time(),
            'dan_ip'=> ip(),
            'dan_data'=> $ID,/*小鸡编号*/
        ]);


        if($user['tuid'] > 0){
            /*代理奖励*/
            for($i=0;$i<3;$i++){


                if($i == 0){
                    $tuid = $user['tuid'];
                }else{
                    $tuid = $user['tuid'.$i];
                }

                $o = $i + 1; /*佣金用户等级*/
                $yoingjin = isset($CONN['cfj_ji'.$o])?(int)$CONN['cfj_ji'.$o]:0;

                $t_user = uid( $tuid,1 );
                if($yoingjin > 0 && $t_user && $t_user['off'] == 1){

                    $sql .= $D -> setbiao('user') -> setshiwu(1) ->  where(['uid'=>$tuid]) -> update(['dan +'=>$yoingjin]);
                    $sql .= $D -> setbiao('danlog') -> setshiwu(1) ->  insert([
                        'dan_uid'=>$tuid,
                        'dan_type'=>5,
                        'dan_num'=>$yoingjin,
                        'dan_atime'=>time(),
                        'dan_ip'=> ip(),
                        'dan_data'=> '小鸡编号'.$ID,
                    ]);

                }

            }
        }

        $sql .= $D -> setbiao('chickenhouse') -> setshiwu(1) ->  where(['ch_id'=>$ID]) -> update(['ch_num +'=>$num]);

        $result = $D -> query($sql, 'shiwu');

        $SHUJU['sql'] = $sql;

        if($result){

            $arr['dan'] = (int)($user['dan'] + $num);
            $arr['huobi'] = round( (float)$user['huobi'],2 );
            $arr['ji_num'] = (int)$user['ji_num'];
            $arr['fodder'] = (int)$user['fodder'];
            $arr['dan_type'] = 0;

            $SHUJU['data'] = $arr;

            $CODE = 1;
            $MSG = '成功';

        }else{
            $CODE = -1;
            $MSG = '失败';
        }

    }

}else if( $MOD == 'delete' ){
    /* delete 其他操作*/


}


function xiaoJi( $ID ){

    $D = db('chickenhouse');

    $xiaoji = $D -> where(['ch_id'=>$ID]) -> find();
    if(!$xiaoji) return ['code'=>0,'msg'=>'小鸡不存在'];

    $lifetime = isset($CONN['cfj_life_time'])?(int)$CONN['cfj_life_time']:90; //小鸡存活时间（天）
    $layeggstime = isset($CONN['cfj_layeggs_time'])?(int)$CONN['cfj_layeggs_time']:24; //小鸡产蛋时间（小时）

    if( $xiaoji['ch_off'] == 1 && ($xiaoji['ch_time'] + $lifetime * 3600 * 24) < time() ){ /*超过存活期*/

        $rel = $D -> where(['ch_id'=>$xiaoji['ch_id']]) -> update(['ch_off'=>0,'ch_dietime'=>time()]);
        if($rel) $xiaoji['ch_off'] = 0;
        else {
            rizhi('chickenhouse','xiaoji ch_off 0 ：'.$xiaoji['ch_id']);
            return ['code'=>2,'msg'=>'小鸡离世了'];
        }

    }else if( $xiaoji['ch_type'] == 1 && $xiaoji['ch_feedtime'] > 0 && ($xiaoji['ch_feedtime'] + ($layeggstime * 3600)) <= time() ){

        $rel = $D -> where(['ch_id'=>$xiaoji['ch_id']]) -> update(['ch_type'=>2]);
        if($rel) $xiaoji['ch_type'] = 2;
        else rizhi('chickenhouse','xiaoji ch_type 2 ：'.$xiaoji['ch_id']);

    }

    return ['code'=>1,'data'=>$xiaoji];

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);