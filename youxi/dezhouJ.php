<?php
/* WangYa phpFrame  Application
 * ******************************************
 * home: www.2cms.com   mail: wangya@2cms.com
 * Copyright  WangYa
 * Trademark  WangYa
 * ******************************************
 *2017 New year
 */

if(isset( $_SERVER['HTTP_HOST'] ))exit( 'no cli');
error_reporting(E_ALL);
define('WYHEAD','');
define('WYPHP' , dirname(__FILE__).'/../WangYa/');
define('WYTEMP', 'temp');
define("WYCON" , '');
define("WYDB"  , '');
define('WYNAME'  , 'dezhouJ');

/*分解服务端标识*/

$AC = $_SERVER['argv'];
$_SERVERID = (float)isset($AC['1'])?$AC['1']:0;

/*绑定服务地址*/
$_POST['serverip'] = '0.0.0.0';

/*绑定服务端口*/
$_POST['serverpt'] = 200;

/*运行用户身份*/
$_POST['USER']  = 'root';
$_POST['changci'] = 2;
/*服务器名称*/
$_POST['gameid'] = WYNAME;

/*游戏逻辑处理间隔(毫秒)*/
$_POST['addtime'] = 10000;

/*服务器开启进程*/
$_POST['JCNUM'] = 20;

/*解散房间秒*/
$_POST['jsmiao'] = 60;

/*第二句准备时间*/
$_POST['zbtime'] = 10;

/*抢庄时间*/
$_POST['qztime'] = 6;

/*配牌时间*/
$_POST['pptime'] = 15;

$_POST['toushu'] = 1;

$_POST['fangid'] = 1;/*房间id*/

$_POST['Apais'] = []; /*游戏牌*/

$_POST['jiazhu'] = [1/3,1/2,2/3,1]; //加注倍率

$_POST['room_sxren'] = 9;//房间上限人数
$_POST['spectators_sxren'] = 6;//观战上限人数

$_POST['robotren'] = 3;//机器人初始人数

$SQTOKEN = new swoole_table(1000);
$SQTOKEN -> column('u',swoole_table::TYPE_INT,8);
$SQTOKEN -> create();


/*缓存用户数据行数*/

$USERCC = new swoole_table(1000);
$USERCC -> column('u',swoole_table::TYPE_INT,8);/*用户uid*/
$USERCC -> column('z',swoole_table::TYPE_INT,8);/*用户fd*/
$USERCC -> column('f',swoole_table::TYPE_INT,4);/*游戏房间id*/
$USERCC -> column('t',swoole_table::TYPE_STRING,36);/*用户token*/
$USERCC -> column('exit',swoole_table::TYPE_INT,1);/*用户在线状态*/
$USERCC -> column('time',swoole_table::TYPE_INT,4);/*用户掉线时间*/
$USERCC -> create();



/*FID 反查找*/
$FIDDCC = new swoole_table(5000);
$FIDDCC->column('u', swoole_table::TYPE_INT, 8);    /*用户uid*/
$FIDDCC->column('t', swoole_table::TYPE_STRING, 72);/*用户token*/
$FIDDCC->column('sj', swoole_table::TYPE_INT, 4);    /*用户时间*/
$FIDDCC -> create();



/*游戏服务数据库*/
$GAMECC = new swoole_table(200);
$GAMECC -> column('atime',swoole_table::TYPE_INT, 4);/* 游戏创建时间 */
$GAMECC -> column('fangid',swoole_table::TYPE_INT, 4);/* 房间id */
$GAMECC -> column('off',swoole_table::TYPE_INT, 1); /*游戏状态 */
$GAMECC -> column('gamequ',swoole_table::TYPE_INT, 1); /*游戏区 0：新手区 1：高手区 2：大师区　3：巅峰区 */

$GAMECC -> column('Auser',swoole_table::TYPE_STRING,800); /*用户id array()*/
$GAMECC -> column('Auserinfo',swoole_table::TYPE_STRING,3000); /*用户详情 array()*/
$GAMECC -> column('Adongzuo', swoole_table::TYPE_STRING, 400); /*所有人动作*/
$GAMECC -> column('Aonlie', swoole_table::TYPE_STRING, 400);  /*连接状态 array()*/
$GAMECC -> column('Atongji', swoole_table::TYPE_STRING, 500);  /* 当前局数统计*/

$GAMECC -> column('lastshuohua', swoole_table::TYPE_INT,4);  /* 上一个说话的人 uid*/
$GAMECC -> column('shuohua', swoole_table::TYPE_INT,4);  /* 说话的人 uid*/
$GAMECC -> column('dmren', swoole_table::TYPE_INT,4);  /* 大盲 uid*/
$GAMECC -> column('xmren', swoole_table::TYPE_INT,4);  /* 小盲 uid*/
$GAMECC -> column('xiaomang',swoole_table::TYPE_INT,2); /*小盲注*/
$GAMECC -> column('damang',swoole_table::TYPE_INT,2); /*大盲注*/

$GAMECC -> column('betall', swoole_table::TYPE_INT, 4); /*用户总押分*/
$GAMECC -> column('bet', swoole_table::TYPE_INT, 4); /*用户当前押分*/
$GAMECC -> column('xren', swoole_table::TYPE_INT, 1); /*每桌上限人数*/
$GAMECC -> column('ren', swoole_table::TYPE_INT, 1); /*人数*/
$GAMECC -> column('robotren', swoole_table::TYPE_INT, 1); /*机器人数*/
$GAMECC -> column('renall', swoole_table::TYPE_INT, 1); /*总人数*/
$GAMECC -> column('spectators', swoole_table::TYPE_INT, 1); /*观战人数*/
$GAMECC -> column('ddtime',swoole_table::TYPE_INT,4); /*等待时间*/
$GAMECC -> column('Apais',swoole_table::TYPE_STRING,1100); /*第一局剩余牌 array()*/

$GAMECC -> column('Auser_pais',swoole_table::TYPE_STRING,200); /*第一轮牌 array()*/
$GAMECC -> column('Achi',swoole_table::TYPE_STRING,200); /*牌池 array()*/


$GAMECC -> column('jtime',swoole_table::TYPE_INT,4); /*结算时间*/
$GAMECC -> column('shuohua_type',swoole_table::TYPE_INT,1); /*说话状态*/
$GAMECC -> column('jiesuan_type',swoole_table::TYPE_INT,1); /*结算状态 1待结算*/


$GAMECC -> create();


//anail


/* 防御CC k  j 防御的数量*/
$FANGYUCC = new swoole_table(1000);
$FANGYUCC -> column('j',swoole_table::TYPE_INT, 4); /*IP的防御量*/
$FANGYUCC -> create();

$sss = 0;

include WYPHP.'function.php';

##################################################################



$game_server = db('gameserver') -> where(['biaoshi'=> WYNAME ]) -> find();

if($game_server){
    $arr = explode(':',$game_server['serverlist']);
    $game_ip = trim($arr[0]);
}else{
    $game_ip = '';
}

$YJID = $game_ip;

$biaoshi = WYNAME;

$SQFILE = WYPHP.'/../youxi/'.md5($YJID.WYNAME).'.key';

$_SERVER["HTTP_HOST"] = $YJID;


/*zskjshouquanhappyonlone*/


/** 返回所有情况（5张复式)
 * @param $pais 牌池+玩家牌
 * @return array
 */
function jieguo_pai( $pais ){

    $data = [];

    if( count($pais) <= 5 ) return [$pais];
    elseif( count($pais) == 6 ){

        foreach ( $pais as $k => $v ){

            $new = $pais;
            unset($new[$k]);
            rsort($new);
            $data[implode('',$new)] = $new;

        }

    }else{
        foreach ( $pais as $k => $v ){

            $new = $pais;
            unset($new[$k]);
            foreach ( $new as $ko => $vo ){

                $arr = $new;
                unset($arr[$ko]);
                rsort($arr);
                $data[implode('',$arr)] = $arr;
            }

        }
    }

    return array_values($data);

}


/** 获取最大数组
 * @param $allpai 所有牌
 * @return array 最大的牌组 |　牌组牌所在位置
 */
function get_big( $allpai ){

    $data = jieguo_pai( $allpai );//复式牌组

    $maxbig = [];//最大数组
    foreach ( $data as $k => $zhi ){
//        echo " ---- $k ---- \r\n";
        $arr = paixing( $zhi );

//        p($arr);echo "\r\n ** ---- ** \r\n\r\n";

        if( count($maxbig) > 0 ){

            if( $maxbig['lv'] < $arr['lv'] ) $maxbig = $arr;
            elseif ( $maxbig['lv'] == $arr['lv'] ){
                if( $maxbig['fen'] < $arr['fen'] ) $maxbig = $arr;
            }

        }elseif( $arr ) {
            $maxbig = $arr;
        }

    }


    //最大牌的位置
    $all = array_flip( $allpai );

    $keys = [];
    foreach ( $maxbig['pai'] as $k => $v ){
        $keys[] = $all[$v];
    }

    $maxbig['location'] = $keys;
    return $maxbig;

}


//游戏服务器
function gameserver($GAME,$FANGid,$GAMECC,$Mem,$server){

    $GAME = gameccget( $GAME,'json' );
    $CONN = include WYPHP."conn.php";

    global $USERCC;

    //array_search("red",$a); 在数组中搜索值 "red"，并返回它的键名
    //reset(); 输出数组中的当前元素和下一个元素的值，然后把数组的内部指针重置到数组中的第一个元素



    /*所有人动作*/
    if(!is_array($GAME['Adongzuo'])) {$GAME['Adongzuo'] =array();}
    if( !isset($GAME['shuohua_type']) ) $GAME['shuohua_type'] = 1;


    if( $GAME['off'] == 0 ){    /*单局结束 初始*/

        $GAME['shuohua_type'] = 1; //说话状态
        $GAME['jiesuan_type'] = 1; //结算状态

        if( count($GAME['Auser']) > 1 && ($GAME['atime'] + 5) < time() ){


//            p($GAME['Auser']);

            //清理退出玩家数据
            foreach ( $GAME['Auserinfo'] as $uid => $v ){

                //e 用户状态
                if( $v['e'] == 2 ) {
//                    echo $uid. '  '.$FANGid."  exit \n";
                    $GAME = userExit( $uid,$GAME );
                }

            }
            //积分不够大盲注 退出
            foreach ( $GAME['Auser'] as $k => $uid ){

                if( $k < 9 ) {
                    $user = uid($uid, 1);
                    if ($user['huobi'] < $GAME['damang']) {
//                    unset( $GAME['Aonlie'][$uid] );
                        $GAME = userExit($uid, $GAME); //用户退出房间
                    }
                }
            }
            $GAME = clearRobot( $GAME ); //位置不够清理机器人
            saveRoom( $FANGid,$GAME ); //更换用户房间

            ksort($GAME['Auser']);
            //初始玩家、动作
            $Adongzuo = $new_Auser = [];
            foreach ( $GAME['Auser'] as $k => $uid ){

                if( $k < 9 ){
                    $new_Auser[$uid] = $uid;
                    $Adongzuo[$uid] = $GAME['off'];
                }

            }
            $GAME['Aonlie'] = $new_Auser;
            $GAME['Adongzuo'] = $Adongzuo;
            if( count($GAME['Aonlie']) <= 1 ) {
                $rel = $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );
                return false;
            }

//            p($GAME['Auser'],$GAME['Aonlie'],$GAME['Adongzuo']);

            //房间真人数小于1关闭放间
            $is_ren = false;
            foreach ( $GAME['Auserinfo'] as $uid => $v ){

                //t 用户类型
                if( $v['t'] == 1 ) {
                    $is_ren = true;
                    break;
                }

            }
            if( !$is_ren ) {
                $GAMECC -> del($FANGid);
                return;
            }

            $is_fapai = false;

            /*初始说话人 大盲注人*/
            $game = userTalk( $FANGid,$GAME );
            if( $game ) {

                $GAME = $game;

                //开局扣钱
                foreach ( $GAME['Aonlie'] as $uid ){

                    $GAME['Auserinfo'][$uid]['e'] = 1;

                    //大盲
                    if( $GAME['dmren'] == $uid ){

                        $user = uid( $uid,1 );
                        if( $user['huobi'] >= $GAME['damang'] ){
                            $GAME['Atongji'][$uid] = $GAME['damang'];
                            $GAME['betall'] += $GAME['damang'];
                            $GAME['bet'] = $GAME['damang'];
                        }

                    }elseif( $GAME['shuohua'] == $uid ){ //小盲

                        $user = uid( $uid,1 );
                        if( $user['huobi'] >= $GAME['xiaomang'] ){
                            $GAME['Atongji'][$uid] = $GAME['xiaomang'];
                            $GAME['betall'] += $GAME['xiaomang'];
                        }

                    }else $GAME['Atongji'][$uid] = 0;

                }

                $GAME['off'] = 1;

                $is_fapai = true;

                list( $GAME,$data ) = one_pai( $GAME['Aonlie'],$GAME );

            }else return;

            //房间人数
            $GAME['ren'] = count($GAME['Auser']);
            $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
            $GAME['ddtime'] = time() + $ddtime;
            $rel = $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );
            if( !$rel ) rizhi( 'de_bug',' off 1 ',__FILE__.' '.__LINE__ );

            if( $is_fapai ){

                $user_list = userList($GAME);

                $game = $GAME;
                $game['ddtime'] = $GAME['ddtime'] - time();

                $bet_pool[0] = 0;getBetChi( $GAME );
                foreach ( $GAME['Atongji'] as $v ){
                    $bet_pool[0] += $v;
                }

                GameOpening('opening',['pai'=>$data,'game'=>$game,'bet_pool'=>$bet_pool,'user_list'=>$user_list],$GAME['Auser'],$USERCC,$server,$server);
            }

            return;

        }else clearUser( $FANGid,$GAME,$server );

        return;

    }else if( $GAME['off'] == 1 ){  /*游戏开局*/

        if( $GAME['ddtime'] >= time() ){

            gameStateSave( $FANGid,$GAME,3 );

            return;
        }

        //超时弃牌
        $rel = check( $FANGid,$GAME,1 );

        if( $rel ){

            $rel['game']['ddtime'] -= time();
            GameGbAll('fold',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$rel['game']],$GAME['Auser'],$USERCC,$server,$server);
        }

        return;

    }else if( $GAME['off'] == 2 ){ /*二轮*/

        if( $GAME['ddtime'] >= time() ){

            gameStateSave( $FANGid,$GAME );
            return;
        }

        //超时弃牌
        $rel = check( $FANGid,$GAME,1 );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('fold',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$rel['game']],$GAME['Auser'],$USERCC,$server,$server);
        }

        return;

    }else if( $GAME['off'] == 3 ){ /*三轮*/


        if( $GAME['ddtime'] >= time() ){

            gameStateSave( $FANGid,$GAME );

            return;
        }

        //超时弃牌
        $rel = check( $FANGid,$GAME,1 );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('fold',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$rel['game']],$GAME['Auser'],$USERCC,$server,$server);
        }

        return;

    }else if( $GAME['off'] == 4 ){ /*三轮*/


        if( $GAME['ddtime'] >= time() ){

            gameStateSave( $FANGid,$GAME );

            return;
        }

        //超时弃牌
        $rel = check( $FANGid,$GAME,1 );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('fold',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$rel['game']],$GAME['Auser'],$USERCC,$server,$server);
        }

        return;

    }else if( $GAME['off'] == 5 ){ /*结算*/

        if( $GAME['jiesuan_type'] == 1 ){

            settlement( $FANGid,$GAME ); //结算

        }

        if( $GAME['ddtime'] >= time() ){

            return;

        }

        game_int( $FANGid,$GAME ); //重置游戏
        clearUser( $FANGid,$GAME,$server );

        /*foreach ( $users as $uid ){
            GameGbAll('join_seat',['code'=>1,'uid'=>$uid,'seat_number'=>'','user_list'=>$user_list],$GAME['Auser'],$USERCC,$server,$server);
        }*/

        return;

    }

}


/** 清退用户（退出用户、金币不足用户）
 * @param $room_id 房间id
 * @param $game 游戏数据
 * @param $server
 * @return bool
 */
function clearUser( $room_id,$game,$server ){

    global $USERCC;

//    list($seat,$spectators) = seatNumber( $game['Auser'] );
//    $users = [];

//    echo "\n\n\n\n\n\n clearUser \n";
//    p($game['Auser']);

    foreach ( $game['Auser'] as $k => $uid ){

        if( $k < 9 ) {
            $user = uid( $uid,1 );
            if( $user['huobi'] < $game['damang'] ){ //金币不足 退出房间

//                echo " ---------- $uid \n";
                $usercc = $USERCC -> get($uid);
                exitMessage( $uid,$game,$room_id,$usercc['z'],$server );
                unset($game['Auserinfo'][$uid]);

            }
        }

    }

    foreach( $game['Auserinfo'] as $uid => $v ){ //单局结束用户退出
        if( $v['e'] == 2 ) {

//            echo "********** $uid \n";
            $usercc = $USERCC -> get($uid);
            exitMessage( $uid,$game,$room_id,$usercc['z'],$server );

        }
    }

    return true;

}


/** 开局发牌 过滤
 * @param $J
 * @param $CHUAN
 * @param $Auser
 * @param $USERCC
 * @param $server
 * @param $connection
 * @param $column 屏蔽键表识
 */
function GameOpening($J,$CHUAN,$Auser,$USERCC,$server,$connection,$column = 'pai')
{
    /*
    $J 通信action [y]

    $CHUAN 通信数据 [d]
    $YUGEM 广播的人
    $USERCC 获得用户的FD 通信id

    $server 网络总线
    $connection ws当前
    */

    if(!is_array($Auser)){

        return ;
    }

    $data = $CHUAN[$column]; //屏蔽数据
    foreach($Auser as $uid){

        $linshi = $USERCC -> get($uid);
        $tongid =  isset($linshi['z'])?$linshi['z']:0;

        if( $tongid > 0){

            if( $server->exist($tongid)) {

                $CHUAN[$column] = isset($data[$uid])?$data[$uid]:[]; //只显示自己
                $fan = $connection -> push($tongid,ydsend($J,$CHUAN));

            }
        }
    }

    return $connection;

}


/** 玩牌阶段的状态
 * @param $FANGid 房间id
 * @param $GAME 游戏数据
 * @param int $num 牌池发牌个数
 * @return mixed
 */
function gameStateSave( $FANGid,$GAME,$num = 1 ){

    global $USERCC,$GAMECC,$server;

//    echo "\n\n\n\n\n\n\n";
//    p($GAME['Adongzuo']);echo "\n";

    $is_true = true;
    $all_in_num = 0;
    foreach ( $GAME['Adongzuo'] as $uid => $v ){

        if( $v == 6 ) $all_in_num += 1; //allin人数

        if( isset($GAME['Aonlie'][$uid]) && ( $v == -1 || $v == 6 ) ){ //弃牌 allin

            unset($GAME['Aonlie'][$uid]);
            $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );

        }elseif ( $v >= 0  && $v < $GAME['off'] ) { //本轮未说话

            if( count($GAME['Aonlie']) > 1 ){ //还剩多家
                $is_true = false;
            }elseif( count($GAME['Aonlie']) == 1 && $GAME['Atongji'][$uid] < $GAME['bet'] ){ //只剩一个(判断allin)
                $is_true = false;
            }

        }

    }

    //在局的人数( 弃牌只剩一个人 )
    if( count($GAME['Aonlie']) <= 1 && $all_in_num == 0){

//        echo " qipai sheng 1 \n";

        $GAME['off'] = 5; //结算

        $ddtime = isset($CONN['dez_jtime'])?($CONN['dez_jtime']):3;
        $GAME['ddtime'] = time() + $ddtime;
        $GAME['jiesuan_type'] = 0;
        $rel = $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );

        if( $rel ){

            settlement( $FANGid,$GAME,1 ); //结算

        }

    }else if( $is_true ){ //说话完毕

//        echo $GAME['off']." talk over \n";
//        p( $GAME['Aonlie'],$GAME['Atongji'] );echo "\n";

        $GAME['off'] += 1;
        if( $GAME['off'] == 5 ){
            $ddtime = isset($CONN['dez_jtime'])?($CONN['dez_jtime']):3;
        }else{
            $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
        }

        $GAME['ddtime'] = time() + $ddtime;

        $rel = $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );

        if( $rel && $GAME['off'] < 5 ){

            list($data,$GAME) = twe_pai( $FANGid,$GAME,$num );
            if( !$data ) rizhi( 'de_bug',' no pai ',__LINE__ );
            $game = $GAME;
            $game['ddtime'] -= time();

            $bet_pool = getBetChi( $game );
            $SuitPatterns = getSuitPatterns( $game ); //牌型

            GameOpening('fapai',['pai'=>$data,'game'=>$game,'bet_pool'=>$bet_pool,'suit_patterns'=>$SuitPatterns],$GAME['Auser'],$USERCC,$server,$server,'suit_patterns');

        }


    }else{

        //玩家自动操作
        if( $GAME['Auserinfo'][$GAME['shuohua']]['zd'] > 0 ){

            if ($GAME['shuohua_type'] == 1) { //通知说话

                $GAMECC->set($FANGid, ['shuohua_type' => 0]);

                $rel = saveCheckFold( $FANGid,$GAME );

                if( $rel ){
                    if( $rel['code'] == 1 ){

                        $game = $rel['data'];
                        $game['ddtime'] -= time();

                        if( $GAME['Adongzuo'][$GAME['shuohua']] == -1 ){
                            GameGbAll('fold',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$game],$game['Auser'],$USERCC,$server,$server);
                        }else{
                            GameGbAll('check',['code'=>1,'uid'=>$GAME['shuohua'],'game'=>$game],$game['Auser'],$USERCC,$server,$server);
                        }

                    }
                }else{ //不满足条件 正常说话

                    $data = raiseData($GAME);
                    $game = $GAME;
                    $game['ddtime'] -= time();

                    $otherOperation = [];
                    //清理已设置操作的用户
                    foreach ( $game['Auserinfo'] as $uid => $value ){

                        if( $value['zd'] < 1 && $uid != $game['shuohua'] && in_array($uid,$game['Aonlie']) ){
                            $otherOperation[$uid] = [
                                'fold'=>1,
                                'check_fold'=>1,
                                'check'=>1,
                            ];
                        }

                    }

                    GameGbAll('talk', ['uid' => $GAME['shuohua'], 'data' => $data, 'game' => $game,'other'=>$otherOperation], $GAME['Auser'], $USERCC, $server, $server);

                }
            }

        }else {

            if ($GAME['shuohua_type'] == 1) { //通知说话

//                echo $GAME['off']." other \n";
//                p( $GAME['Atongji'] );echo "\n";

                $GAMECC->set($FANGid, ['shuohua_type' => 0]);
                $data = raiseData($GAME);
                $game = $GAME;
                $game['ddtime'] -= time();

                $otherOperation = [];
                //清理已设置操作的用户
                foreach ( $game['Auserinfo'] as $uid => $value ){

                    if( $value['zd'] < 1 && $uid != $game['shuohua'] && in_array($uid,$game['Aonlie']) ){
                        $otherOperation[$uid] = [
                            'fold'=>1,
                            'check_fold'=>1,
                            'check'=>1,
                        ];
                    }

                }

                GameGbAll('talk', ['uid' => $GAME['shuohua'], 'data' => $data, 'game' => $game,'other'=>$otherOperation], $GAME['Auser'], $USERCC, $server, $server);
            } else {

                $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
                $end = $ddtime-2;
                $start = $ddtime-10;

                if ($GAME['Auserinfo'][$GAME['shuohua']]['t'] != 1 && ( $GAME['ddtime'] - rand($start,$end) ) < time() ) {

                    robotTalk( $FANGid,$GAME,$USERCC,$server );

                }

            }
        }
    }

    return $GAME;

}


function getSuitPatterns( $game ){

    $usersPai = [];
    foreach ( $game['Adongzuo'] as $uid => $v ){

        $pais = $game['Achi'];

        $pais[5] = $game['Auser_pais'][$uid][0];
        $pais[6] = $game['Auser_pais'][$uid][1];

//        p($pais);echo "\n  --- $uid ---  \n";

        $usersPai[$uid] = get_big( $pais );
    }

    $_POST['suit_patterns'] = $usersPai;

    $type = ['高牌','一对','两对','三条','顺子','同花','葫芦','四条','同化顺','皇家同化顺'];
    foreach ($usersPai as $uid => $v){

        $usersPai[$uid]['lv'] = $type[$v['lv']];

    }

    return $usersPai;

}


/** 获取押注不同成级的总数
 * @param $game 游戏数据
 * @return array
 */
function getBetChi( $game ){

    $tongji = $game['Atongji'];
    uasort($tongji,"mySort");//按玩家牌大小排序

    $i = 0;
    $arr = [];
    $num = 0;
    foreach ( $tongji as $uid => $jine ){

        foreach ( $tongji as $uuid => $v ){

            $number = (float)( $v - $num );
            if( $number > 0 && $jine > $num){

                if( !isset($arr[$i]) ) $arr[$i] = 0;
                $arr[$i] += $jine - $num;

            }

        }

        if( $jine > $num )  $i++;
        $num = $jine;

    }

    return $arr;

}

/** 机器人说话
 * @param $room_id 房间id
 * @param $game 游戏数据
 * @param $USERCC 用户数据库
 * @param $server 服务器
 */
function robotTalk( $room_id,$game,$USERCC,$server ){

    $data = raiseData($game);

    $arr = [];
    foreach ( $data['type'] as $k => $v ){
        if( $v == 1 ) $arr[$k] = $k;
    }

    $uid = $game['shuohua'];
    $type = array_rand($arr,1);

    if( $game['off'] > 0 ){
        $_POST['suit_patterns'] = 0;
    }else{

        $users_pai = $game['Auser_pais'];
        $pai = isset($users_pai[$uid])?$users_pai[$uid]:'';
        if( is_array($pai) ){
            $one = substr($pai[0],1);
            $twe = substr($pai[1],1);
            if( $one > 10 && $twe > 10 ){

                if( in_array('call',$arr) ){
                    $type = 'call';
                    if( in_array('raise',$arr) && rand(1,100) > 80 ) $type = 'raise';
                }

            }elseif( !empty($one) && $one == $twe ){

                $gailv = rand(1,100);
                if( in_array('raise',$arr) && in_array('call',$arr) ){
                    if( $gailv > 55 ){
                        $type = 'raise';
                    }else $type = 'call';
                }elseif ( in_array('all_in',$arr) && $gailv > 58 ){
                    $type = 'all_in';
                }elseif ( count( $arr ) > 1 ){
                    if( in_array( 'fold',$arr ) ) unset($arr['fold']);
                    $type = array_rand($arr,1);
                }

            }
        }

    }

    if( $type == 'call' ){

        $rel = yazhu($room_id, $game);

        if ($rel) {

            $rel['game']['ddtime'] -= time();
            GameGbAll('call', ['uid' => $game['shuohua'], 'bet' => $rel['bet'],'yue'=>round((float)$rel['yue'],2), 'game' => $rel['game']], $game['Auser'], $USERCC, $server, $server);
        }

    }elseif ( $type == 'raise' ){

        if( $data['raise']['big'] > $game['betall'] )  $data['raise']['big'] = $game['betall'];

        $raise = rand( $data['raise']['min'],$data['raise']['big'] );

        $rel = yazhu( $room_id,$game,$raise );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('raise',['code'=>1,'uid'=>$game['shuohua'],'bet'=>$rel['bet'],'yue'=>round((float)$rel['yue'],2),'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
        }

    }elseif ( $type == 'check' ){

        $rel = check( $room_id,$game );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('check',['code'=>1,'uid'=>$game['shuohua'],'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
        }

    }elseif ( $type == 'all_in' ){

        $rel = allIn( $room_id,$game );

        if( $rel['code'] == 1 ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('all_in',['code'=>1,'uid'=>$game['shuohua'],'bet'=>$rel['bet'],'yue'=>round((float)$rel['yue'],2),'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
        }

    }else{

        $rel = check( $room_id,$game,1 );

        if( $rel ){
            $rel['game']['ddtime'] -= time();
            GameGbAll('fold',['code'=>1,'uid'=>$game['shuohua'],'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
        }

    }

    return;

}


/** 押注
 * @param $FANGid 房间id
 * @param $game 游戏数据
 * @param $jiazhu 押注金额
 */
function yazhu( $FANGid,$game,$jiazhu = 0 ){

    global $GAMECC;

    $uid = $game['shuohua'];

//    echo __LINE__."  shuohua   $uid   ---- \n";
//    p($game['Adongzuo'],$game['Atongji']);echo "\n";

    $bet = 0;//押注值

    $user = uid( $uid,1 );

    if( $game['Adongzuo'][$uid] < $game['off'] && $game['Adongzuo'][$uid] != -1 ){

        $yue = (int)($user['huobi'] - $game['Atongji'][$uid]);

        if( $jiazhu < 0 ) $jiazhu = 0;

//        echo $game['Atongji'][$uid].'  '.$user['huobi'].'  '.$yue."   $jiazhu   \n";

        $bet = $game['bet'] - $game['Atongji'][$uid];
        $bet += $jiazhu;
        if( $yue > $bet ){

            $game['betall'] += $bet;
            $game['Atongji'][$uid] += $bet;
            if( $game['Atongji'][$uid] > $game['bet'] ) $game['bet'] = $game['Atongji'][$uid];

            if( $yue > $game['bet'] ) $game['Adongzuo'][$uid] = $game['off'];
            else $game['Adongzuo'][$uid] = 6;

        }elseif ( $yue == $bet || ( $yue < $bet && $yue >= 0 ) ){

            $bet = $yue;//余额不足时

            $game['betall'] += $yue;
            $game['Atongji'][$uid] += $yue;
            if( $game['Atongji'][$uid] > $game['bet'] ) $game['bet'] = $yue;
            $game['Adongzuo'][$uid] = 6;

        }

        //判断是否押注完成
        $game = is_yazhu( $game );

    }

    $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
    $game['ddtime'] = time() + $ddtime;

    $rel = $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );

    if( $rel ){

        $yue = (float)($user['huobi'] - $game['Atongji'][$uid]);

        $game = userTalk( $FANGid,$game );
        return ['game'=>$game,'bet'=>$bet,'yue'=>$yue];
    }else return false;

}


/*** 让牌|弃牌
 * @param $FANGid 房间id
 * @param $game 游戏数据
 * @param $type 0让或弃 1弃
 * @return array|bool
 */
function check( $FANGid,$game,$type = 0 ){

    global $GAMECC;

    $uid = $game['shuohua'];

    $bet = 0;
    if( $type == 0 ){

        $bet = $game['Atongji'][$uid];
        $is_check = true;
        foreach ( $game['Atongji'] as $v ){

            if( $v > $bet ) $is_check = false;

        }

        if( $is_check ){
            $game['Adongzuo'][$uid] = $game['off'];
        }else{
            unset($game['Aonlie'][$uid]);
            $game['Adongzuo'][$uid] = -1;
        }

    }else {
        unset($game['Aonlie'][$uid]);
        $game['Adongzuo'][$uid] = -1;
    }


    //判断是否押注完成
    $game = is_yazhu( $game );

    $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
    $game['ddtime'] = time() + $ddtime;

    $rel = $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );

    if( $rel ){

        $result = userTalk( $FANGid,$game );
        if( $result ) $game = $result;
        return ['game'=>$game,'bet'=>$bet];
    }else return false;

}


/** 判断当轮是否押注完成
 * @param $game 游戏数据
 * @return mixed
 */
function is_yazhu( $game ){

    $is_zhi = false;
    $is_off = true;

    foreach ( $game['Adongzuo'] as $uid => $off ){

        if( $off > -1 && $off < 6 ) {

            if( $off >= 0 && $off < $game['off'] ) {
                $is_off = false;
                break;
            } else {

                if( $game['bet'] != $game['Atongji'][$uid] ) $is_zhi = true;

            }
        }
    }

    if( $is_off && $is_zhi ){

        foreach ( $game['Adongzuo'] as $uid => $off ){

            if( $game['shuohua'] != $uid ) {

                if( $off != -1 && $off != 6 ){
                    $game['Adongzuo'][$uid] = 0;
                }

            }

        }

    }

    return $game;

}


/** 基础牌
 * @param $users 玩家
 * @param $GAME 游戏数据
 * @return array
 */
function one_pai( $users,$GAME ){

    $pais = create_pai();

    $data = [];

    //在线玩家
    foreach ( $users as $uid ){

        $keys = array_rand( $pais,2 );

        $pai = [];
        foreach ( $keys as $k => $ko ){
            $pai[] = $pais[$ko];
            unset( $pais[$ko] );
        }

        $data[$uid] = $pai;

    }

    $GAME['Apais'] = $pais;
    $GAME['Auser_pais'] = $data;

    return [$GAME,$data];

}


/** 二轮牌
 * @param $GAME
 * @param int $num 牌数
 * @return array|bool
 */
function twe_pai( $FANGid,$GAME,$num = 1 ){

    if( !is_array($GAME['Apais']) ) return false;

    global $GAMECC;

    $pai = [];

    for( $i=0;$i<$num;$i++ ){
        $k = array_rand( $GAME['Apais'],1 );
        $pai[] = $GAME['Apais'][$k];
        array_push( $GAME['Achi'],$GAME['Apais'][$k] );
        unset( $GAME['Apais'][$k] );
    }

    $rel = $GAMECC -> set( $FANGid,gameccset( $GAME,'json' ) );

    if( !$rel ) rizhi( 'two_case','set err' );
//    p($GAME['Achi'],$num);

    return [$pai,$GAME];

}


/** 用户退出游戏（数据修改）
 * @param $uid 用户id
 * @param $game 游戏数据
 * @return mixed
 */
function userExit( $uid,$game,$type = 0 ){

    global $USERCC;

    if( $game['Auserinfo'][$uid]['t'] == 1 ) $game['ren'] -= 1;
    else $game['robotren'] -= 1;

    $game['renall'] -= 1;

    unset( $game['Auserinfo'][$uid] );
//    unset( $game['Auser'][$uid] );
    foreach ( $game['Auser'] as $k => $Auid ){
        if( $uid == $Auid ){
            unset( $game['Auser'][$k] );
        }
    }

    if( $type == 0 ) $USERCC -> del($uid);

    return $game;

}


/** 更换说话的人
 * @param $roomid 房间id
 * @param $game 游戏数据
 * @return mixed
 */
function userTalk( $roomid,$game = '' ){

    global $GAMECC;

    if( !is_array( $game ) ) $game = gameccget( $GAMECC -> get( $roomid ),'json' );

    if( count($game['Aonlie']) < 1 ) return false;

//    p($game['Aonlie']);

    $game['lastshuohua'] = isset($game['shuohua'])?(int)$game['shuohua']:0; //上一个说话的id

    //每轮开局更换盲注
    if( $game['off'] == 0  ){

        $Aonlie = $game['Aonlie'];
        $Aonlie = array_values($Aonlie);
        if( !isset($game['dmren']) || empty($game['dmren']) ){

            $game['xmren'] = $game['shuohua'] = $Aonlie[0]; //小盲

            $newAonlie = array_reverse( $Aonlie );
            $game['dmren'] = $newAonlie[0]; //大盲

        }else{

            //更换盲注 说话人
            foreach ( $Aonlie as $k => $uid ){

                if( $uid == $game['dmren'] ){
                    next($Aonlie);
                    $game['dmren'] = current($Aonlie);

                    if( empty($game['dmren']) ){

                        $game['dmren'] = $Aonlie[0]; //大盲
                        $game['xmren'] = $game['shuohua'] = $Aonlie[1]; //小盲

                    }else{

                        next($Aonlie);
                        $game['xmren'] = $game['shuohua'] = current($Aonlie);

                        if( empty($game['xmren']) ){

                            $game['xmren'] = $game['shuohua'] = $Aonlie[0];

                        }

                    }

                    break;
                }else next($Aonlie);

            }


            if( empty($game['dmren']) ){

                $Aonlie = array_reverse( $Aonlie );
                $i = 0;
                foreach ( $Aonlie as $k => $uid ){
                    if( $i == 0 ){
                        $game['dmren'] = $uid;
                        break;
                    }
                    $i++;
                }

            }

        }

//        p($game['Aonlie']);
//        echo "\n".$game['shuohua'].' - '.$game['xmren'].' - '.$game['dmren']." ----mangzhu--- \n";

    }else{

        /*判断说话的人*/
        $key = array_search( $game['shuohua'],$game['Aonlie'] );

        //更换说话人
        if( !$key ){

            if( $game['shuohua'] > 0 ){

                ksort($game['Auser']);

                $Auser = $game['Auser'];
                foreach ( $game['Auser'] as $k => $uid ){
                    if( $k < 9 ){
                        $Auser[$k] = $uid;
                    }
                }

                $shuohua = 0;
                foreach ( $Auser as $k => $uid ){

                    if( $game['shuohua'] == $uid ){

                        next($Auser);
                        $game['shuohua'] = $shuohua = current($Auser);
                        if( empty($game['shuohua']) ){
                            break;
                        }elseif( in_array($shuohua,$game['Aonlie']) ){
                            break;
                        }else {
                            $shuohua = 0;
                        }

                    }else next($Auser);

                }

                if( empty($shuohua) ) $game['shuohua'] = reset($game['Aonlie']);

            }else $game['shuohua'] = reset($game['Aonlie']);

        }else{

            foreach ( $game['Aonlie'] as $k => $uid ){

                if( $uid == $game['shuohua'] ){
                    next($game['Aonlie']);
                    $game['shuohua'] = current($game['Aonlie']);
                    break;
                }else next($game['Aonlie']);

            }

            if( !$game['shuohua'] ){
                $game['shuohua'] = reset($game['Aonlie']);
            }

        }

    }

//    echo "\n".$game['shuohua']."  shuohua \n\n\n";

    $game['shuohua_type'] = 1;//说话广播标识 1发广播
    $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
    $game['ddtime'] = time() + $ddtime + 1;

    $rel = $GAMECC -> set( $roomid,gameccset( $game,'json' ) );

    if( $rel ){
        return $game;
    }else return false;

}


function allIn( $room_id,$game = '' ){

    global $GAMECC;

    if( !is_array( $game ) ) $game = gameccget( $GAMECC -> get( $room_id ),'json' );

    $uid = $game['shuohua']; //说话人

    if( !isset($game['Aonlie'][$uid]) ) return ['code'=>-9,'msg'=>'你不在本局游戏中'];

    if( $game['Adongzuo'][$uid] >= 6 ) return ['code'=>-1,'msg'=>'已All IN，请勿重复操作'];
    elseif( $game['Adongzuo'][$uid] < 0 ) return ['code'=>-1,'msg'=>'已弃牌，不能操作'];
    else{

        $user = uid( $uid,1 );

        if( $user && $user['off'] == 1 ){
            $yue = (int)$user['huobi'];
            $bet = $yue - (int)$game['Atongji'][$uid];

            if( $bet >= 0 && $yue > 0){
                $game['betall'] += $bet;
                $game['Atongji'][$uid] = $yue;
                $game['Adongzuo'][$uid] = 6;
                if( $user['huobi'] > $game['bet'] ) $game['bet'] = $yue;

            }else {
                if( $yue <= 0 ){
                    $game['Atongji'][$uid] = 0;
                    $game['Adongzuo'][$uid] = -1;
                }
            }

            //判断是否押注完成
            $game = is_yazhu( $game );

            $rel_game = userTalk( $room_id,$game );

            $yue = (float)$user['huobi'] - $game['Atongji'][$uid];

            if( $rel_game ) return ['code'=>1,'bet'=>$bet,'game'=>$rel_game,'yue'=>$yue];
            else return ['code'=>-1,'msg'=>'操作失败，请重试'];

        }else return ['code'=>-1,'msg'=>'账号不存在或被封'];

    }

}



/** 创建房间
 * @param $type 盲注类型 （0：新手区 1：高手区 2：大师区　3：巅峰区）
 * @return bool|int|string
 */
function createRoom( $type = 0 ){

    global $GAMECC;
    $CONN = include WYPHP."conn.php";

    if( $type < 0 || $type > 3 ) $type = 0;

    $mangzhu = fangFen(); //盲注列表

    $data = [];
    foreach ( $GAMECC as $roomid => $v ){
        $data[] = $roomid;
    }


    if( count($data) > 0 ){

        $i=1;
        while ( 1 ){

            $roomid = rand( 100000,999999 );

            if( !in_array( $roomid,$data ) ) break;

            $i++;
        }

    }else $roomid = 100000;

    $robotren = $_POST['robotren'];//机器人初始人数

    $data = getRobot( $robotren ); //获取机器人

    $xren = isset($CONN['dez_xren'])?(int)$CONN['dez_xren']:9; //动作等待时间

    $mang = $mangzhu[$type]; //大小盲
    $game = [
        'gamequ'=>$type,
        'xiaomang'=>$mang['xm'],
        'damang'=>$mang['dm'],
        'atime'=>time(),
        'fangid'=>$roomid,
        'robotren'=>0,  //机器人人数
        'ren'=>0,  //人数
        'renall'=>0,  //总人数
        'spectators'=>0,  //观战人数
        'ddtime'=>time(),      //等待时间
        'xren'=>$xren,          //房间上限人数
        'Apais'=>[],            //游戏牌
    ];

    $rel = $GAMECC -> set( $roomid,gameccset( $game,'json' ) ); /*创建房间*/

    foreach ( $data as $k => $user ){

        //添加机器人积分
        if( $user['huobi'] < $mang['huobi'] && $user['off'] == 0 ){
            $rel = db('user') -> where(['uid'=>$user['uid']]) -> update(['huobi +'=>$mang['huobi']]);
            if( !$rel ) rizhi( 'robot',' save huobi ' );
        }
        joinRoom( $roomid,$user ); /*加入房间*/

    }

    if( $rel ) return $roomid;
    else return false;

}


/** 加入房间
 * @param $roomid 房间id
 * @param $user 用户数据
 * @param $type 用户类型 0：机器人 1：人
 * @param $jine 用户带人金额
 * @return mixed
 */
function joinRoom( $roomid,$user,$type = 0,$jine = 0 ){

//    if( $type == 0 ) return ['code'=>-9,'msg'=>'禁止加入'];

    global $GAMECC,$USERCC,$server;

    $game = $GAMECC -> get($roomid);
    if( !$game ) return ['code'=>-1,'msg'=>'房间错误'];
    $game = gameccget( $game,'json' );


    list( $seat,$spectators ) = seatNumber( $game['Auser'] );

    $seat_number = -1; //座位编号

    //人数上限判断
    if( ($game['ren'] + $game['robotren']) <= ( $_POST['room_sxren'] - 1 ) ){

        //机器人直接入座
        if( $type == 0 ){
            $game['robotren'] += 1;
            $game['renall'] += 1;
            $state = 1;//游戏

            if( $game['off'] == 0 ){
                $game['Adongzuo'][$user['uid']] = 0; //动作
                $game['Atongji'][$user['uid']] = 0; //用户数据
            }

            if ( count( $seat ) > 0 ) {

                $key = array_rand($seat,1);
                $seat_number = $seat[$key];
                $game['Auser'][$seat_number] = $user['uid'];

            }else return ['code'=>-1,'msg'=>'状态错误'];

        }else{

            $state = 0;

            if( !in_array( $user['uid'],$game['Auser'] ) ){
                if ( count( $spectators ) > 0 ) {

                    $game['spectators'] += 1;
                    $key = array_rand($spectators,1);
                    $seat_number = $spectators[$key];
                    $game['Auser'][$seat_number] = $user['uid']; //用户数据

                }else return ['code'=>-2,'msg'=>'观战位置已满'];
            }
        }

        $game['Auserinfo'][$user['uid']] = [
            'u'=>$user['uid'],//uid
            'off'=>$game['off'],//游戏状态
            'e'=>$state,//0观战 1游戏 2退出
            't'=>$user['off'],//用户类型 0机器人 1人
            'hb'=>$jine,//用户积分 huobi
            'add'=>1,//添加时间
            'zd'=>0,//预存操作 0无 1让 2让或弃 3弃
        ];


        $rel = $GAMECC -> set( $roomid,gameccset( $game,'json' ) );

        if( $rel && $state == 1 ){ //机器人直接加入游戏、入座

            $otherOperation = [];
            //清理已设置操作的用户
            foreach ( $game['Auserinfo'] as $uid => $value ){

                if(  $value['e'] == 1 && $value['zd'] < 1 && $uid != $game['shuohua'] ){
                    $otherOperation[$uid] = [
                        'fold'=>1,
                        'check_fold'=>1,
                    ];

                    if( isset($game['Atongji'][$uid]) && $game['Atongji'][$uid] >= $game['bet'] ){
                        $otherOperation[$uid]['check'] = 1;
                    }else $otherOperation[$uid]['check'] = 0;
                }

            }


            $user_list = userList($game);
            $game['ddtime'] -= time();
            GameGbAll('join_seat',['code'=>1,'uid'=>$user['uid'],'seat_number'=>$seat_number,'user_list'=>$user_list,'game'=>$game,'other'=>$otherOperation ],$game['Auser'],$USERCC,$server,$server);

        }

        return ['code'=>1,'data'=>$game];

    }else return ['code'=>-1,'msg'=>'房间人数达到上限'];

}


/** 获取游戏、观战空余位置
 * @param $Auser 房间用户uid数组
 * @return array|bool
 */
function seatNumber( $Auser ){

    if( !is_array($Auser) ) return [[0,1,2,3,4,5,6,7,8,],[9,10,11,12,13,14]];

    $keys = array_keys($Auser);

    $seat = []; //空余座位
    $spectators = []; //观战位置数
    $i = 0;
    while ( $i < 15 ){
        if( $i < 9 ){ //游戏人数
            if( !in_array( $i,$keys ) ) $seat[] = $i;
        }else{
            if( !in_array( $i,$keys ) ) $spectators[] = $i;
        }
        $i++;
    }

    return [$seat,$spectators];

}



/** 获取机器人
 * @param $num 机器人人数
 * @return array
 */
function getRobot( $num ){

    global $GAMECC;

    if( $num < 1 ) $num = 1;
    elseif ( $num > 5 ) $num = 5;

    $uids = [];
    foreach ( $GAMECC as $room_id => $game ){
        $Auser = json_decode($game['Auser'],true);
        if( !is_array($Auser) ) $Auser = [];
        $uids += array_flip($Auser);
    }

    $uids = array_keys($uids);

    if( count($uids) > 0 ){
        $data = db('user') -> zhicha('uid,name,off,touxiang,huobi,jine,huobi') -> where(['off'=>0,'uid NOTIN'=>$uids])  -> select();
    }else{
        $data = db('user') -> zhicha('uid,name,off,touxiang,huobi,jine,huobi') -> where(['off'=>0])  -> select();
    }

    if( !$data ) return [];

    $robot = [];
    if( $num == 1 ){
        $key = array_rand( $data,$num );
        $robot[] = $data[$key];
    }else{
        $keys = array_rand( $data,$num );
        foreach ( $keys as $k => $v ){
            $robot[] = $data[$v];
        }
    }

    return $robot;

}



/**
 * @return array 牌
 */
function create_pai(){

    /*牌生成器 */
//    $hua = array('1'=>'黑桃','2'=>'红桃','3'=>'梅花','4'=>'方块');
    $hua = array('1'=>'hei','2'=>'hong','3'=>'mei','4'=>'fang');
    $PAII = array();

    for( $i = 1 ;$i <= 4; $i++){

        for($j = 1; $j < 14 ; $j++){

            if( $j == 11)      $z  = $hua[$i].'J';
            else if( $j == 12) $z  = $hua[$i].'Q';
            else if( $j == 13) $z  = $hua[$i].'K';
            else if( $j == 1)  $z  = $hua[$i].'A';
            else $z = $hua[$i].$j;
            $PAII[$z] = $i.''.$j ;
        }
    }


    $shuju = array_flip( $PAII );
    shuffle($PAII);

    for($i=0;$i<rand(0,6);$i++){
        shuffle($PAII);
    }

    $NPAII = array();
    foreach($PAII as $zhi){
        $NPAII[ $shuju[$zhi]] =$zhi;
    }

    return $NPAII;
}


/**
 * @param $pai 系统牌
 * @param array $arr 锁牌数组（返回值$data的键）
 * @param array $Aone 一轮牌数组
 * @return array|bool 开牌数组，要删除的键
 */
function get_pai( $pai,$arr = [],$Aone = [] ){

    if( !is_array($pai) || count($pai) < 10 ) return false;
    if( !is_array($arr) ) return false;

    $data = $Aone;
    $keys = [];
    for($i=0;$i<5;$i++){

        if( !in_array($i,$arr) ){

            $key = array_rand( $pai,1 );
            $data[$i] = $pai[$key];
            unset($pai[$key]);
            $keys[$i] = $key;

        }

    }

    return [$data,$keys];

}




/** 结算
 * @param $room_id 房间id
 * @param $game 游戏数据
 * @param $type 0：正常游戏结束 1：弃牌结束
 * @return bool
 */
function settlement( $room_id,$game,$type = 0 ){

    global $GAMECC;

    $CONN = include WYPHP."conn.php";

    $game['jiesuan_type'] = 0;
    $GAMECC -> set( $room_id,['jiesuan_type'=>0] );


    list($usersPai,$robotWin) = calculation($game,$type);

//    echo " settlement  ----- \n";
//    p( $usersPai,$robotWin );

    saveData( $usersPai,$room_id,$game );

    return;
}



//计算
function calculation( $game,$type ){

    $usersPai = [];

    foreach ( $game['Adongzuo'] as $uid => $v ){

        $pais = $game['Achi'];

        $pais[5] = $game['Auser_pais'][$uid][0];
        $pais[6] = $game['Auser_pais'][$uid][1];

        if( count($pais) < 5 ){
            if( $v < 0 ){
                $usersPai[$uid]['lv'] = 0;
                $usersPai[$uid]['fen'] = 0;
            }else{
                $usersPai[$uid]['lv'] = 1;
                $usersPai[$uid]['fen'] = 1;
            }
            $usersPai[$uid]['location'] = [];
            $usersPai[$uid]['pai'] = [];
        }else $usersPai[$uid] = get_big( $pais );

    }

    uasort($usersPai,"my_sort");//按玩家牌大小排序

    $robotWin = 0; //机器人输赢
    $cha = 0; //彩池已计算金额
    $renNum = count($usersPai); //玩家人数

    $paiFen = array_column( $usersPai,'fen' );//牌数数组
    $rens = array_count_values( $paiFen ); //不同牌分的人数

    //结算前扣除押注金额
    foreach ( $usersPai as $uid => $v ){
        $usersPai[$uid]['win'] = -$game['Atongji'][$uid];
    }

    //计算用户输赢
    foreach ( $usersPai as $uid => $v ){

        $bet = $game['Atongji'][$uid];//押注
        $win = $usersPai[$uid]['win'];
        $bet -= $cha;
        $ren = $rens[$v['fen']];//牌相同的人数

        if( $bet > 0 && $game['Adongzuo'][$uid] >= 0){

            $he = 0;
            $t_users = [];
            if( $ren > 1 ){

                foreach ( $game['Atongji'] as $kouid => $vo ){
                    if( $v['fen'] == $usersPai[$kouid]['fen'] ){
                        $he += $vo;
                        if( $uid != $kouid ) $sheng = $vo;
                        $t_users[$kouid] = $vo;
                    }
                }

                foreach ( $game['Atongji'] as $tjuid => $zhi ){

                    if( $uid == $tjuid ){ //返还自己
                        $win += $bet;
                        $game['Atongji'][$tjuid] -= $bet;
                    }else{

                        if( $usersPai[$tjuid]['fen'] != $v['fen'] && $zhi > 0 ){

                            //玩家剩余金额小于需赔付的金额
                            if( $zhi < $he ){

                                //剩余相同牌玩家
                                foreach ( $t_users as $suid => $szhi ){

                                    if( $suid == $uid ) {
                                        $num = count($t_users);
                                        //减去剩余没有赔付的
                                        foreach ($t_users as $suid1 => $szhi1) {

                                            if ($szhi1 < $zhi/count($t_users)) {
                                                $zhi -= $szhi1;
                                                $num -= 1;
                                            }

                                        }

                                        //需赔付的金额大于平均金额
                                        if ($szhi > ($zhi/$num)) {
                                            $win += $zhi/$num;
                                            $game['Atongji'][$tjuid] -= $zhi/$num;
                                        } else {
                                            $win += $szhi;
                                            $game['Atongji'][$tjuid] -= $szhi;
                                        }

                                    }
                                }

                            }else{

                                $win += $bet;
                                $game['Atongji'][$tjuid] -= $bet;
                            }

                        }
                    }
                }

            }else{ //没有相同牌分的人

                foreach ( $game['Atongji'] as $tjuid => $zhi ){
                    if( $bet < $zhi ){
                        $win += $bet;
                        $game['Atongji'][$tjuid] -= $bet;
                    }else{
                        $win += $zhi;
                        $game['Atongji'][$tjuid] = 0;
                    }
                }
            }
        }

        $renNum -= 1;
        $usersPai[$uid]['win'] = $win;

        if( $game['Auserinfo'][$uid]['t'] != 1 ) $robotWin += $win;

    }

    return [$usersPai,$robotWin];

}

/** 更新用户数据（开奖）
 * @param $usersPai 用户游戏详情
 * @param $room_id 房间id
 * @param $game 游戏数据
 */
function saveData( $usersPai,$room_id,$game ){

    global $USERCC,$GAMECC,$server,$Mem;
    $conn = include WYPHP.'conn.php';

    $D = db('dezhoubetlog');
    $time = time();
    $sql = '';

    $jilu = [];

    foreach ( $usersPai as $uid => $v ){

        $win = (float)$v['win']; //输赢
        $rake = 0; //抽成
        $dez_rake = isset($conn['dez_rake'])?(float)$conn['dez_rake']:0.01;
        if( $dez_rake > 1 ) $dez_rake /=100;
        if( $win > 0 ) $rake = $win * $dez_rake;
        $win -= $rake;
        $usersPai[$uid]['win'] = (float)$win;

        /*用户投注记录*/
        $sql .= $D -> setbiao('dezhoubetlog') ->  setshiwu(1) -> insert([
            'bet_sum' => $game['Atongji'][$uid],
            'bet_uid' => $uid,
            'bet_time' => $time,
            'bet_roomid' => $room_id,
            'bet_win' => $win,
            'bet_rake' => $rake,
            'bet_chi' => json_encode($game['Achi']),
            'bet_pai' => json_encode($game['Auser_pais'][$uid]),
            'bet_state' => $game['Adongzuo'][$uid],
            'bet_mang' => $game['xmren'] == $uid?1:($game['dmren'] == $uid?2:0),
            'bet_data' => json_encode($usersPai),
            'xiaomang' => $game['xiaomang'],
            'damang' => $game['damang'],
        ]);

        $user = uid( $uid,1 );
        if( $user ) {
            $user_data = [
                'uid'=>$uid,
                'huobi'=>(float)$user['huobi'],
                'name'=>$user['name'],
                'touxiang'=>pichttp($user['touxiang']),
            ];

            /*更改用户余额*/
            if( !empty($win) ){

                $user_data['huobi'] = round( ((float)$user_data['huobi'] + $win),2 );

                if( $user['off'] != 1 ){

                    if( $win < 0 && abs($win) > (float)$user['huobi'] && (int)$user['huobi'] ){
                        $sql .= $D -> setbiao('user') -> setshiwu(1) -> where(['uid'=>$uid]) -> update(['huobi +'=>(int)$user['huobi']]);
                    }

                }else{
                    $sql .= $D -> setbiao('user') -> setshiwu(1) -> where(['uid'=>$uid]) -> update(['huobi +'=>$win]);
                    $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
                        'uid'=>$uid,
                        'type'=>1,
                        'jine'=>$win,
                        'data'=>$uid.'德州游戏',
                        'ip'=>ip(),
                        'atime'=> $time,
                    ]);
                }

            }

        }else{
            $user_data = [
                'uid'=>$uid,
                'huobi'=>0,
                'name'=>'',
                'touxiang'=>pichttp(''),
            ];
        }
        $usersPai[$uid]['user'] = $user_data;

        $pai = [];
        if( $game['Adongzuo'][$uid] > -1 ) $pai = $game['Auser_pais'][$uid];

        $jilu[$uid] = [
            'uid'=>$uid,
            'touxiang'=>$user_data['touxiang'],
            'name'=>$user_data['name'],
            'win'=>(float)$win,
            'paichi'=>$game['Achi'],
            'pai'=>$pai,
            'bet'=>$game['Atongji'][$uid],
        ];

    }

    $result = $D -> query($sql,'shiwu');

    $hash = 'dezhou/jilu/'.$room_id;
    $Mem -> s( $hash,$jilu );

    foreach ( $usersPai as $uid => $v ){
        $usersPai[$uid]['dongzuo'] = $game['Adongzuo'][$uid];
        $usersPai[$uid]['Auser_pais'] = $game['Auser_pais'][$uid];
    }

    if( $result ){
        GameGbAll('kaijiang',['code'=>1,'data'=>$usersPai],$game['Auser'],$USERCC,$server,$server);
    }else{
        GameGbAll('kaijiang',['code'=>-1,'msg'=>'系统错误'],$game['Auser'],$USERCC,$server,$server);
    }

    return;

}


//排序
function my_sort($a,$b)
{
    if ($a['lv']==$b['lv']){

        if( $a['fen'] == $b['fen'] ) return 0;
        return ($a['fen']<$b['fen'])?1:-1;

    }
    return ($a['lv']<$b['lv'])?1:-1;
}


function mySort($a,$b)
{
    if( $a == $b ) return 0;
    return ( $a < $b )?-1:1;
}


/** 游戏初始化
 * @param $room_id 房间id
 * @param array $game 不初始的数据
 * @return array
 */
function game_int( $room_id,$game = [] ){

    global $GAMECC,$Mem;

    $CONN = include WYPHP."conn.php";

    $game['off'] = 0;
    $game['Apais'] = [];
    $game['Achi'] = [];
    $game['Auser_pais'] = [];
    $game['bet'] = 0;
    $game['betall'] = 0;
    $game['jtime'] = time();
    $game['robotren'] = isset($game['robotren'])?$game['robotren']:0;
    $game['ren'] = isset($game['ren'])?$game['ren']:0;
    $game['allren'] = count($game['Auser']);

    $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
    $game['ddtime'] = time() + $ddtime;

    foreach ( $game['Auserinfo'] as $uid => $vo ){
        if( $game['Auserinfo'][$uid]['e'] == 2 ){
            unset( $game['Auserinfo'][$uid] );
            foreach ( $game['Auser'] as $k => $user_id ){
                if( $uid == $user_id )
                    unset( $game['Auser'][$k] );
            }
        }else {
            $game['Auserinfo'][$uid]['e'] = 1;
            $game['Auserinfo'][$uid]['add'] = 1;
        }
    }

    $game['Atongji'] = [];
    /*foreach ( $game['Aonlie'] as $uid ){
        $game['Atongji'][$uid] = 0;
    }*/

    /*赔率*/
    $game['Apeilv'] = peilv( $CONN );

    /*筹码的值*/
    $game['Achouma'] = chouma();

    $result = $GAMECC -> set( $room_id,gameccset( $game,'json' ) );

    if( $result ){

        return ['code'=>1,'data'=>$game];
    }else{

        return [ 'code'=> -1,'msg'=>'初始失败' ];
    }

}


/** 转译牌（去掉花色）
 * @param $pai 牌
 * @return array
 */
function zhuanyi( $pai ){

    //转译牌
    $vel = [];
    $key = [];
    foreach ( $pai as $k => $v ){

        $key[$k] = substr($v,0,1);
        $vel[$k] = substr($v,1);

    }

    return [$key,$vel];

}


/** 牌型判断
 * @param $pai 初始牌
 * @return array|bool|void
 */
function paixing( $pai ){


    list( $key,$new_pai ) = zhuanyi( $pai );


    //同花顺
    $rel = TongHuaShun( $key,$new_pai,$pai );
    if($rel) return $rel;

    //4条
    $rel = fourStrip( $new_pai,$pai );
    if($rel) return $rel;

    //葫芦
    $rel = gourd( $new_pai,$pai );
    if($rel) return $rel;

    //同花
    $rel = tongHua( $key,$new_pai,$pai );
    if($rel) return $rel;


    //顺子
    $rel = straight( $new_pai,$pai );
    if($rel) return $rel;


    //3条
    $rel = threeStrip( $new_pai,$pai );
    if($rel) return $rel;

    //两队
    $rel = twePair( $new_pai,$pai );
    if($rel) return $rel;

    //对子
    $rel = pair( $new_pai,$pai );
    if($rel) return $rel;

    $rel = highCard( $new_pai,$pai );
    if($rel) return $rel;

    return false;

}




/** 判断同花顺
 * @param $key 花色数组
 * @param $pai 牌值数组
 * @param $data 原牌数组(5牌)
 * @return array|bool
 */
function TongHuaShun( $key,$pai,$data ){

    if(!is_array($key) || !is_array($pai)) return false;

    $is_flush = tongHua( $key,$pai,$data );
    $is_straight = straight( $pai,$data );

    if($is_flush && $is_straight){

        if( $is_straight['type'] == 'big' ) {

            $fen = 90000000000;
            $fen += $is_straight['f'];

            return ['lv'=>9,'fen'=>$fen,'pai'=>$data];
        } else{
            $fen = 80000000000;
            $fen += $is_straight['f'];

            return ['lv'=>8,'fen'=>$fen,'pai'=>$data];
        }

    }

    return false;

}


/** 判断同花
 * @param $key 牌花色数组
 * @param $pai 牌值数组
 * @param $data 牌值数组
 * @return array|bool
 */
function tongHua( $key,$pai,$data ){

    if( !is_array($key) ) return false;

    $fen = 50000000000;

    /*判断同花*/
    $huase = 0;
    for( $i=1;$i<=4;$i++ ){
        if( in_array($i,$key) ) $huase += 1;
    }

    if($huase == 1){

        rsort( $pai );
        $fen += 100000000 * $pai[0];
        $fen += 1000000 * $pai[1];
        $fen += 10000 * $pai[2];
        $fen += 100 * $pai[3];
        $fen += 1 * $pai[4];

        return ['lv'=>5,'fen'=>$fen,'pai'=>$data];
    }else return false;

}


/** 判断顺子
 * @param $pai 牌
 * @param $data 牌值数组
 * @return array
 */
function straight( $pai,$data ){

    if( !is_array($pai) ) return false;

    $new_data = straightAll(); //所有连子

    $fen = 40000000000;

    sort( $pai,1 );
    $str1 = $str = implode('_',$pai);
    if( in_array(1,$pai) ){

        $pai1 = $pai;
        foreach ( $pai1 as $k => $v ){
            if( $v == 1 ) $pai1[$k] = 14;
        }
        sort( $pai1,1 );
        $str1 = implode('_',$pai1);
    }

    if( in_array($str,$new_data) ){

        if( $pai[0] >= 10 ){
            $f = 10;
            $type = 'big';
        }else{
            $f = $pai[0];
            $type = 'small';
        }

        $fen += $f;

        return ['lv'=>4,'type'=>$type,'fen'=>$fen,'f'=>$f,'pai'=>$data];
    }elseif( in_array($str1,$new_data) ){
        $fen += 10;
        return ['lv'=>4,'type'=>'big','fen'=>$fen,'f'=>10,'pai'=>$data];
    }else return false;

}


/** 所有连子
 * @param int $num 连子个数
 * @return array
 */
function straightAll( $num = 5 ){

    $new_data = []; /*所有连子数据 （值升序）*/
    for( $i=0;$i<=(14 - $num);$i++ ){
        $arr = [];
        for($j=1;$j<=$num;$j++){
            array_push($arr,($i+$j));
        }
        array_push($new_data,implode('_',$arr));
    }

    return $new_data;

}


/** 4条
 * @param $pai 牌值数组
 * @param $data 原牌数组(5牌)
 * @return bool|void
 */
function fourStrip( $pai,$data ){

    if( !is_array($pai) ) return false;

    $fen = 70000000000;

    $arr = [];
    foreach ( $pai as $k => $v ){

        if( isset($arr[$v]) ) {
            $arr[$v]['num'] += 1;
            $arr[$v]['v'] = $v;
        }else {
            $arr[$v]['num'] = 1;
            $arr[$v]['v'] = $v;
        }

    }

    rsort($arr);
//    p($arr);echo " shabi ** \r\n";sleep(1000);
    if( $arr[0]['num'] == 4 ){

        if( $arr[1]['v'] == 1 ) $arr[1]['v'] = 14;
        $fen += $arr[1]['v'];

        return ['lv'=>7,'fen'=>$fen,'pai'=>$data];

    }

    return false;

}


/** 判断葫芦
 * @param $pai 牌值数组
 * @param $data 原牌数组
 * @return array|bool
 */
function gourd( $pai,$data ){

    if( !is_array($pai) ) return false;

    $fen = 60000000000;

    $arr = [];
    foreach ( $pai as $k => $v ){

        if( isset($arr[$v]) ) {
            $arr[$v]['num'] += 1;
            $arr[$v]['v'] = $v;
        }else {
            $arr[$v]['num'] = 1;
            $arr[$v]['v'] = $v;
        }

    }

    rsort($arr);
    if( count($arr) == 2 ){

        if( $arr[0]['num'] == 3 ){

            if( $arr[0]['v'] == 1 ) $arr[0]['v'] = 14;
            $fen +=  100 * $arr[0]['v'];
            if( $arr[1]['v'] == 1 ) $arr[1]['v'] = 14;
            $fen +=  1 * $arr[1]['v'];

            return ['lv'=>6,'fen'=>$fen,'pai'=>$data];
        }

    }

    return false;

}


/** 判断3条
 * @param $pai 牌值数组
 * @param $data 原牌数组
 * @return array|bool
 */
function threeStrip( $pai,$data){

    if( !is_array($pai) ) return false;

    $arr = [];
    foreach ( $pai as $k => $v ){

        if( isset($arr[$v]) ) {
            $arr[$v]['num'] += 1;
            $arr[$v]['v'] = $v;
        }else {
            $arr[$v]['num'] = 1;
            $arr[$v]['v'] = $v;
        }

    }

    $fen = 30000000000;

    rsort($arr);
    if( count($arr) == 3 && $arr[0]['num'] == 3 ){

        if( $arr[0]['v'] == 1 ) $arr[0]['v'] = 14;//A
        $fen +=  10000 * $arr[0]['v'];

        if( $arr[1]['v'] == 1 ) $arr[1]['v'] = 14;
        if( $arr[2]['v'] == 1 ) $arr[2]['v'] = 14;

        if( $arr[1]['v'] > $arr[2]['v'] ){
            $big = $arr[1];
            $min = $arr[2];
        }else{
            $big = $arr[2];
            $min = $arr[1];
        }

        $fen +=  100 * $big['v'];
        $fen +=  1 * $min['v'];

        return ['lv'=>3,'fen'=>$fen,'pai'=>$data];

    }

    return false;

}


/** 两对
 * @param $pai 牌值数组
 * @param $data 原牌数组
 * @return array|bool
 */
function twePair( $pai,$data ){

    if( !is_array($pai) ) return false;

    $arr = [];
    foreach ( $pai as $k => $v ){

        if( isset($arr[$v]) ) {
            $arr[$v]['num'] += 1;
            $arr[$v]['v'] = ($v == 1 ?14:$v);
        }else {
            $arr[$v]['num'] = 1;
            $arr[$v]['v'] = ($v == 1 ?14:$v);
        }

    }

    $fen = 20000000000;

    rsort($arr);
    if( count($arr) == 3 && ( $arr[0]['num'] && $arr[1]['num'] ) == 2 ){

        if( $arr[0]['v'] > $arr[1]['v'] ){
            $big = $arr[0];
            $min = $arr[1];
        }else{
            $big = $arr[1];
            $min = $arr[0];
        }

        $fen += 10000 * $big['v'];
        $fen += 100 * $min['v'];
        $fen += 1 * $arr[2]['v'];

        return ['lv'=>2,'fen'=>$fen,'pai'=>$data];

    }

    return false;

}


/** 对子
 * @param $pai 牌值
 * @param $data 原牌数组
 * @return array|bool
 */
function pair( $pai,$data ){

    if( !is_array($pai) ) return false;

    $arr = [];
    foreach ( $pai as $k => $v ){

        if( isset($arr[$v]) ) {
            $arr[$v]['num'] += 1;
            $arr[$v]['v'] = ($v == 1 ?14:$v);
        }else {
            $arr[$v]['num'] = 1;
            $arr[$v]['v'] = ($v == 1 ?14:$v);
        }

    }

    $fen = 10000000000;

    rsort($arr);

    if( count($arr) == 4 && $arr[0]['num'] == 2 ){

        $fen += 100000000 * $arr[0]['v'];
        $new_arr = $arr;
        unset($new_arr[0]);
        rsort($new_arr);
        $fen += 10000 * $new_arr[0]['v'];
        $fen += 100 * $new_arr[1]['v'];
        $fen += 1 * $new_arr[2]['v'];

        return ['lv'=>1,'fen'=>$fen,'pai'=>$data];

    }

    return false;

}

/** 高牌
 * @param $pai 牌值牌组
 * @param $data 原牌数组
 * @return array|bool
 */
function highCard( $pai,$data ){

    if( !is_array($pai) ) return false;

    foreach ( $pai as $k => $v ){
        if( $v == 1 ) $pai[$k] = 14;
    }

    rsort($pai);

    $fen = 100000000 * $pai[0];
    $fen += 1000000 * $pai[1];
    $fen += 10000 * $pai[2];
    $fen += 100 * $pai[3];
    $fen += 1 * $pai[4];

    return ['lv'=>0,'fen'=>$fen,'pai'=>$data];

}


/**** 赔率
 * @param $CONN
 * @return mixed
 */
function peilv($CONN){

    /*赔率*/
    $Apeilv[0] = isset($CONN['lhp_wutiao'])?(int)$CONN['lhp_wutiao']:350; //5条
    $Apeilv[1] = isset($CONN['lhp_tonghuashunda'])?(int)$CONN['lhp_tonghuashunda']:250; //大同花顺
    $Apeilv[2] = isset($CONN['lhp_tonghuashunxiao'])?(int)$CONN['lhp_tonghuashunxiao']:150; //小同花顺
    $Apeilv[3] = isset($CONN['lhp_sitiao'])?(int)$CONN['lhp_sitiao']:65; //4条
    $Apeilv[4] = isset($CONN['lhp_hulu'])?(int)$CONN['lhp_hulu']:15; //葫芦
    $Apeilv[5] = isset($CONN['lhp_tonghua'])?(int)$CONN['lhp_tonghua']:7; //同花
    $Apeilv[6] = isset($CONN['lhp_shunzi'])?(int)$CONN['lhp_shunzi']:5; //顺子
    $Apeilv[7] = isset($CONN['lhp_santiao'])?(int)$CONN['lhp_santiao']:3; //3条
    $Apeilv[8] = isset($CONN['lhp_liangdui'])?(int)$CONN['lhp_liangdui']:2; //两对
    $Apeilv[9] = isset($CONN['lhp_duizi'])?(int)$CONN['lhp_duizi']:1; // 对10之上

    return $Apeilv;

}


/**
 * @return array 还回游戏筹码
 */
function chouma(){

    $CONN = include WYPHP.'conn.php';

    $chouma1 = isset($CONN['lhp_chouma1'])?(int)$CONN['lhp_chouma1']:1;
    $chouma2 = isset($CONN['lhp_chouma2'])?(int)$CONN['lhp_chouma2']:5;
    $chouma3 = isset($CONN['lhp_chouma3'])?(int)$CONN['lhp_chouma3']:10;
    $chouma4 = isset($CONN['lhp_chouma4'])?(int)$CONN['lhp_chouma4']:20;
    $chouma5 = isset($CONN['lhp_chouma5'])?(int)$CONN['lhp_chouma5']:50;
    $fama = [$chouma1,$chouma2,$chouma3,$chouma4,$chouma5];

    sort($fama);

    return $fama;

}


/** 大牌型额外奖励
 * @return array
 */
function jiangli( $bet = 1 ){

    $CONN = include WYPHP.'conn.php';

    $jiang1 = isset($CONN['lhp_jiangwt'])?(int)$CONN['lhp_jiangwt']:5;
    $jiang2 = isset($CONN['lhp_jiangthsd'])?(int)$CONN['lhp_jiangthsd']:3;
    $jiang3 = isset($CONN['lhp_jiangthsx'])?(int)$CONN['lhp_jiangthsx']:2;
    $jiang4 = isset($CONN['lhp_jiangst'])?(int)$CONN['lhp_jiangst']:1;
    $arr = [$jiang1 * (int)$bet,$jiang2 * (int)$bet,$jiang3 * (int)$bet,$jiang4 * (int)$bet];

    return $arr;

}


/*** 牌型统计
 * @param $uid
 * @param int $paixing
 * @return array
 */
function paixing_count( $uid,$paixing = 0 ){

    $betcount = db('betcount') -> where(['betc_uid'=>$uid]) -> find();
    if( !$betcount ) $betcount = ['betc_uid'=>$uid,'betc_wutiao'=>0,'betc_thsda'=>0,'betc_thsxioa'=>0,'betc_sitiao'=>0];

    if( $paixing == 1 ) $betcount['betc_wutiao'] += 1;
    elseif ( $paixing == 2 ) $betcount['betc_thsda'] += 1;
    elseif ( $paixing == 3 ) $betcount['betc_thsxioa'] += 1;
    elseif ( $paixing == 4 ) $betcount['betc_sitiao'] += 1;

    return $betcount;

}

/** 分配房间号
 * @param int $gamequ 游戏所在区
 * @param int $room_id 房间id
 * @param int $type 0：加入游戏 1：跟换房间
 * @return bool|int|string
 */
function allot_room( $gamequ = 0,$room_id = 0,$type = 0 ){

    global $GAMECC;

    $sxren = $_POST['room_sxren'];//房间上限人数

    $room_id = 0;
    foreach ( $GAMECC as $roomId => $game ){

        if( $gamequ == $game['gamequ'] && $roomId != $room_id ){
            if( $game['ren'] < $sxren - 3 && $game['spectators'] < 5 ){
                $room_id = $roomId;
                break;
            }
        }

    }

    //没有合适的房间创建
    if( empty($room_id) ){

        if( $type == 1 ) return false;//更换房间
        return createRoom( $gamequ );

    }else return $room_id;

}


/** 在座游戏玩家列表
 * @param $game
 * @return array|bool
 */
function userList( $game ){

    if( !is_array($game['Auser']) ) return false;

    ksort($game['Auser']);

    $list = [];
    foreach ( $game['Auser'] as $k => $uid ){

        if( $k < 9 ){
            $user = uid( $uid,1 );
            $yazhu = isset($game['tongji'][$uid])?$game['tongji'][$uid]:0;
            $arr = [
              'uid'=>$uid,
              'name'=>$user['name'],
              'touxiang'=>pichttp($user['touxiang']),
              'huobi'=>(float)$user['huobi'] - $yazhu,
            ];
            $list[$k] = $arr;
        }

    }

    return $list;

}

/** 清退机器人 给玩家让位
 * @param $game 游戏数据
 * @return array
 */
function clearRobot( $game ){

    if( $game['spectators'] > 0 && $game['renall'] > 6 && $game['robotren'] > 0 ){

//        $yuren = $game['renall'] - 6;
        $yuren = rand(0,2);
        $clear_num = $yuren > $game['robotren']?$game['robotren']:$yuren; //清退人数
        $i = 0;
        foreach ( $game['Auserinfo'] as $uid => $info ){

            if( $i < $clear_num ){

                if( $info['t'] != 1 ){
                    $game['Auserinfo'][$uid]['e'] = 2;
                    $i++;
                }

            }else break;

        }

    }

    return $game;

}

/** 人数变动从新匹配房间
 * @param $room_id 目前所在房间号
 * @param $game 游戏数据
 * @return bool
 */
function saveRoom( $room_id,$game ){

    if( $game['ren'] > 1 && $game['renall'] < 5 ){

        $robotren = rand(1,($_POST['room_sxren'] - $game['renall']));
        $data = getRobot( $robotren ); //获取机器人

        $mangzhu = fangFen(); //盲注列表
        $mang = $mangzhu[$game['gamequ']]; //大小盲

        foreach ( $data as $k => $user ){

            $rel = true;

            //添加机器人积分
            if( $user['huobi'] < $mang['huobi'] && $user['off'] == 0 ){
                $rel = db('user') -> where(['uid'=>$user['uid']]) -> update(['huobi +'=>$mang['huobi']]);
                if( !$rel ) rizhi( 'robot',' save huobi ' );
            }


            if( $rel ) {

                $result = joinRoom( $room_id,$user ); /*加入房间*/
                if( $result['code'] == 1 ) $game = $result['data'];
            }
        }

    }elseif( $game['ren'] == 1 && $game['spectators'] == 0 ){

        /*$save_uid = 0;
        foreach ( $game['Auserinfo'] as $uid => $info ){

            if( $info['t'] == 1 ){
                $save_uid = $uid;
                break;
            }

        }

        //更换房间用户
        if( $save_uid > 0 ){

            $user = uid( $save_uid,1 );

            if( $user && $user['off'] == 1 && $user['huobi'] > 0 ){

                //重新分配房间号 没有合适的房间不更换房间
                $new_room_id = allot_room( $game['gamequ '],$room_id,1 );

                if( $new_room_id ){

                    $rel = joinRoom( $new_room_id,$user,1 );
                    if( $rel['code'] == 1 ){ //加入房间成功

                        global $GAMECC;
                        $game = $GAMECC -> get( $new_room_id );
                        if( !$game ) return ['code'=>-1,'msg'=>'房间错误'];
                        $game = gameccget( $game,'json' );

                        //加入
                        list( $seat,$spectators ) = seatNumber( $game['Auser'] );

                        if( count($seat) > 0 && count($seat) < ($_POST['room_sxren'] - 3) ){

                            $seat_number = $seat[array_rand($seat)];
                            $game['spectators'] -= 1;
                            $game['ren'] += 1;
                            $game['renall'] += 1;

                            $game['Auser'][$seat_number] = $save_uid;

                            $rel = $GAMECC -> set( $room_id,gameccset( $game,'json' ) );

                            if( $rel ) return $game;

                        }
                    }
                }else{

                    //没有房间可更换 检查房间人数 不足4个添加机器人
                    return room_add_robot( $room_id );

                }

            }else{

                //强制退出游戏
                return exit_game( $save_uid );

            }
        }*/
    }

    return $game;

}

/** 添加机器人如房间
 * @param $room_id 房间id
 * @return bool
 */
function room_add_robot( $room_id ){

    global $GAMECC;

    $game = $GAMECC -> get( $room_id );
    if( !$game ) return false;
    $game = gameccget( $game,'json' );

    $add_num = 5 - $game['renall'];
    if( $add_num < 1 ) return false;

    $mangzhu = fangFen(); //盲注列表
    $data = getRobot( $add_num ); //获取机器人

    $type = isset($game['gamequ'])?$game['gamequ']:3;
    $mang = $mangzhu[$type]; //大小盲
    foreach ( $data as $k => $user ){

        //不在房间
        if( !in_array($user['uid'],$game['Auser']) ){
            //添加机器人积分
            if( $user['huobi'] < $mang['huobi'] && $user['off'] == 0 ){
                $rel = db('user') -> where(['uid'=>$user['uid']]) -> update(['huobi +'=>$mang['huobi']]);
                if( !$rel ) rizhi( 'robot',' save huobi ' );
            }
            joinRoom( $room_id,$user ); /*加入房间*/
        }

    }

    return true;

}


/** 退出房间断开线程
 * @param $uid 用户id
 * @return bool
 */
function exit_game( $uid ){

    global $USERCC,$server;

    $usercc = $USERCC -> get($uid);

    if( $usercc['z'] ){
        return $server -> close( $usercc['z'] ,true );
    }

    return false;

}


/** 追加等待时间
 * @param $uid 用户id
 * @param $room_id 房间id
 * @param $game 游戏数据
 * @return array
 */
function addTime( $uid,$room_id,$game ){

    global $GAMECC;

    $conn = include WYPHP.'conn.php';

    if( $uid != $game['shuohua'] ) return ['code'=>-1,'msg'=>'不该你说话'];

    $add_jine = isset($conn['dez_add_time'])?(int)$conn['dez_add_time']:5; //添加一倍时间所用金币
    $beilv = isset($game['Auserinfo'][$uid]['add'])?(int)$game['Auserinfo'][$uid]['add']:1; //每局添加时间次数（没多一次金币翻倍）
    $bet = isset($game['Atongji'][$uid])?(int)$game['Atongji'][$uid]:0; //用户当局押注金币

    $user = uid( $uid,1 );
    if( !$user ) return ['code'=>-2,'msg'=>'用户不存在'];
    if ( $user['huobi'] < ($add_jine * $beilv + $bet) ) return ['code'=>-3,'msg'=>'余额不足'];

    $save_jine = $add_jine * $beilv;
    if( $save_jine > 0 ){
        $D = db('user');
        $sql = $D -> setshiwu(1) -> where(['uid'=>$uid]) -> update(['huobi -'=>$save_jine]);
        $sql .= $D -> setshiwu(1) -> setbiao('huobilog') -> insert([
            'uid' => $uid,
            'type' => 28,
            'jine' => -$save_jine,
            'data' => '追加等待时间',
            'ip' => ip(),
            'atime' => time()
        ]);

        $rel = $D -> qurey($sql ,'shiwu');

    }else{
        $rel = true;
    }

    if( $rel ){

        $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间

        $game['Auserinfo'][$uid]['add'] += 1;
        $game['ddtime'] += $ddtime;

        $result = $GAMECC -> set( $room_id,gameccset( $game,'json' ) );
        if( $result ) return ['code'=>1,'game'=>$game,'add_time'=>$ddtime];
        else return ['code'=>-5,'msg'=>'加时失败'];

    }else return ['code'=>-4,'msg'=>'扣除金币失败'];

}


/** 可操作数据
 * @param $game 游戏数据
 * @return array
 */
function raiseData( $game ){

    $uid = $game['shuohua'];

    $chi1 = (int)($game['betall']/3);
    $chi2 = (int)($game['betall']/2);
    $chi3 = (int)($game['betall'] * 2/3);
    $chi4 = (int)$game['betall'];
    $chi = [$chi1,$chi2,$chi3,$chi4];

    $state1 = $state2 = $state3 = $state4 = 0;
    $call = 0;//跟注
    $raise = 0;//加注
    $check = 0;//让牌
    $fold = 1;//弃牌
    $all_in = 0;
    $min = 0;
    $big = 0;
    $call_num = 0;

    $user = uid( $uid,1 );
    if( $user ){

        $bet = $game['Atongji'][$uid]; //押注金额

        if( $bet >= $game['bet'] ) {
            $check = 1;
        }

        if( (int)$user['huobi'] >= $game['bet'] ){

            if( (int)$user['huobi'] == $game['bet'] ) {
                $call = 1;
                $call_num = (float)($game['bet'] - $bet);
            }
            else{

                if( (int)$user['huobi'] < ( $bet + $game['xiaomang'] ) ){
                    $all_in = 1;
                }else{

                    $raise = 1;
                    if( ($user['huobi'] - $bet) > $chi4 ) $state1 = $state2 = $state3 = $state4 = 1;
                    elseif( ($user['huobi'] - $bet) > $chi3 ) $state1 = $state2 = $state3 = 1;
                    elseif( ($user['huobi'] - $bet) > $chi2 ) $state1 = $state2 = 1;
                    elseif( ($user['huobi'] - $bet) > $chi1 ) $state1 = 1;

                    $min = $game['xiaomang'];
                    $big = $user['huobi'] - $bet;

                    if( $check != 1 ) {
                        $call = 1;
                        $call_num = (float)($game['bet'] - $bet);
                    }

                }

            }

        }elseif( (int)$user['huobi'] > $bet ){
            $all_in = 1;
        }


    }

    $state = [$state1,$state2,$state3,$state3];
    $data = [
        'type'=>[
            'call' => $call,
            'raise' => $raise,
            'check' => $check,
            'fold' => $fold,
            'all_in' => $all_in,
        ],
        'raise'=>[
            'min'=> $min,//最小加注金额
            'big'=> $big,//最大加注金额
            'chi'=>$chi,//加注奖池金额的比例
            'state'=>$state,//是否够加注加注状态
        ],
        'call_num'=>$call_num
    ];

    return $data;

}


/** 设置自动让或弃操作
 * @param $FANGid 房间id
 * @param $game 游戏数据
 * @param $uid 操作用户
 * @param $state 1：让 2让或弃
 * @return array
 */
function setCheckFold( $FANGid,$game,$uid,$state = 1 ){

    global $GAMECC;

    if( $game['Auserinfo'][$uid]['zd'] > 0 ) return ['code'=>-1,'msg'=>'不可操作'];
    $game['Auserinfo'][$uid]['zd'] = $state;

    $rel = $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );

    if( $rel ){
        return ['code'=>1,'data'=>$game,'msg'=>'陈功','uid'=>$uid];
    }else return ['code'=>-1,'msg'=>'设置失败'];

}

/** 跟新‘让或弃’状态
 * @param $FANGid 房间id
 * @param $game 游戏数据
 * @return array|bool
 */
function saveCheckFold( $FANGid,$game ){

    global $GAMECC;

    $uid = $game['shuohua'];

    $bet = $game['Atongji'][$uid];

    $state = $game['Auserinfo'][$uid]['zd'];
    $game['Auserinfo'][$uid]['zd'] = 0;//恢复

    if( $state != ( 1 && 2 ) ) {

        $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );
        return ['code'=>-2,'msg'=>'不可操作'];
    }

    if( $state == 2 ){

        //让
        if( $bet >= $game['bet'] ) {

            $game['Adongzuo'][$uid] = $game['off'];

        }else{
            //弃
            unset($game['Aonlie'][$uid]);
            $game['Adongzuo'][$uid] = -1;
        }
    }else{

        if( $bet >= $game['bet'] ) {

            $game['Adongzuo'][$uid] = $game['off'];

        }else{
             $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );
            return ['code'=>-2,'msg'=>'未达到让牌条件'];
        }
    }



    $ddtime = isset($CONN['dez_ddtime'])?(int)$CONN['dez_ddtime']:30; //动作等待时间
    $game['ddtime'] = time() + $ddtime;

    $rel = $GAMECC -> set( $FANGid,gameccset( $game,'json' ) );

    if( $rel ){
        $game = userTalk( $FANGid,$game );
        return ['code'=>1,'data'=>$game,'msg'=>'陈功'];
    }else return ['code'=>-1,'msg'=>'不可操作'];

}


/** 退出消息发送
 * @param $uid 用户id
 * @param $game 游戏数据
 * @param $room_id 房间id
 * @param $fd 线程号
 * @param $connection 服务器
 * @return mixed
 */
function exitMessage( $uid,$game,$room_id,$fd,$server ){

    global $GAMECC,$USERCC;

    $users = $game['Auser'];

    $game = userExit( $uid,$game,1 );

    $GAMECC -> set( $room_id,gameccset( $game,'json' ) );

    GameGbAll('exit',['code'=>1,'uid'=>$uid,'renall'=>$game['renall']],$users,$USERCC,$server,$server);
//    $connection->push($fd, ydsend('exit',['code'=>1,'uid'=>$uid]));
    $USERCC -> del($uid);
    return $server->close( $fd ,true );

}


global $CONN,$Mem;

$server = new swoole_websocket_server( $_POST['serverip'] , $_POST['serverpt'] );


$SETDATA  = array( 'worker_num' => $_POST['JCNUM'] );

if(isset($CONN['shouhu']) && $CONN['shouhu'] == '1')
{
    $SETDATA ['daemonize'] = 1;

}


$SETDATA ['user'] = $_POST['USER'];
$SETDATA ['log_file'] = WYPHP.'Game/'.WYNAME.'_bug.log';
$SETDATA ['log_level'] =  0;

jianli($SETDATA ['log_file']);


$server -> set( $SETDATA );

/*添加服务端通信*/
$server->addlistener( $_POST['serverip'] , $_POST['serverpt'] , SWOOLE_SOCK_UDP);

$ooo = 1;

$process = new swoole_process(function($process) use ($server,$FIDDCC,$CONN){

    global $GAMECC,$Mem,$USERCC,$BETCC;

//    $GAMECC -> set( 0,['off'=>0] );


    createRoom(  );


    $ooo = 1;

    while(1){

        usleep($_POST['addtime']);

        global $ooo;

        $ooo++;

        if($ooo%200 == 0){

            foreach($FIDDCC as $key => $vvvv){

                if(!isset($server->atime[$key])){

                    $server->atime[$key] = 1;
                }

                if(time() - $vvvv['sj'] > 50){

                    if($server->atime[$key] == 1){

                        $vvvv['sj'] = time();

                        $FIDDCC ->set($key,array('sj'=>time() ));

                        $server->atime[$key]+=1;
                        $server->push($key, ydsend("xintiao"));


                    }else{

                        $server->close($key);
                        $FIDDCC ->del($key);
                        unset($server->atime[$key]);
                    }

                }else{

                    $server->push($key, ydsend("xintiao"));
                }

            }

        }


        if($ooo%100 == 0){

            if($GAMECC){

                foreach($GAMECC as $fangid => $GAME){

                    gameserver($GAME,$fangid,$GAMECC,$Mem,$server);

                }

            }
        }

    }

});

$server->addProcess($process);

$server->on('WorkerStart', function ($server, $worker_id)use($CONN)
{

});

$server->on('open', function (swoole_websocket_server $server, $request)
{

    /*连接防御 CC防止攻击*/
    $fdinfo = $server->connection_info($request->fd);

    if(isset($fdinfo) && isset( $fdinfo['remote_ip'] )){

        global $FANGYUCC;
        $hash = md5($fdinfo['remote_ip']);

        $zhid = $FANGYUCC ->get($hash);
        if(!$zhid) $zhid['j'] = 0;

        $zhi = $zhid['j'];

        if($zhi > 88){

            $server->close($request->fd,true);

        }else{

            $FANGYUCC ->incr($hash, 'j',  1);
            $server->push($request->fd , json_encode(array('y'=>'lianjieok','fd'=>$request->fd)));
        }

    }else  $server->close($request->fd,true);

});



$server->on('Packet', function (swoole_server $serv, $data, $addr)use($server,$FIDDCC)
{
    /*UDP 接收数据*/

    global $GAMECC,$USERCC;

    $fanhui = unserialize($data);

    if($fanhui){


        if( isset($fanhui['y']) && $fanhui['y'] == 'fangcha' ){

            $user = uid( $fanhui['uid'],1 );
            if( !$user || $user['off'] != 1 ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '账号不存在或被封' )) , $addr['server_socket']);

            $game = $GAMECC->get((int)$fanhui['d']);

            if( $game ){

                /*未达到房间人数上限*/
                if( $game['spectators'] < $_POST['spectators_sxren'] ){

                    $fangfengs = fangFen();

                    $fangfen = $fangfengs[$game['gamequ']]['huobi'];
                    if( !$fangfen ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '房间类型错误' )) , $addr['server_socket']);

                    if( $user['huobi'] < $fangfen ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '积分不足,请充值' )) , $addr['server_socket']);

                    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1','msg' => gameccget($game,'json') )) , $addr['server_socket']);
                }else return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '已达人数上限' )) , $addr['server_socket']);

            }else return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '房间不存在' )) , $addr['server_socket']);

        }if( isset($fanhui['y']) && $fanhui['y'] == 'joingame' ){

            $user = uid( $fanhui['uid'],1 );
            if( !$user || $user['off'] != 1 ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '账号不存在或被封' )) , $addr['server_socket']);

            $usercc = $USERCC -> get($fanhui['uid']);
            if( !$usercc ){

                $type = isset($fanhui['room_type'])?(int)$fanhui['room_type']:0;
                $fangfengs = fangFen();

                $fangfen = $fangfengs[$type]['huobi'];
                if( !$fangfen ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '房间类型错误' )) , $addr['server_socket']);

                if( $user['huobi'] < $fangfen ) return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1','msg' => '积分不足,请充值' )) , $addr['server_socket']);

            }

            return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1','msg' => 'ok' )) , $addr['server_socket']);

        }else updserver($serv, $data, $addr,$server);

    }else{

        if($addr['address']== '127.0.0.1'){

            $date = base64_encode($data);
            $chuandi = json_encode(array("y"=>"jpeg","d"=>$date));
            foreach($FIDDCC as $key => $vvvv){
                usleep(3);
                $server->push($key, $chuandi);
            }

        }

    }



});




$server->on('message', function (swoole_websocket_server $connection, $frame)use($server)
{
    /*websocket 通信*/

    global $GAMECC,$BETCC,$Mem;

    $CONN = include WYPHP."conn.php";

    $DATA = json_decode($frame-> data,true);

    /*$diyici = false;*/

    if(isset($DATA['y'])){

        global $USERCC, $FIDDCC;

        if($DATA['y'] == 'xintiao'){

            $FIDDCC -> set($frame-> fd ,array('sj' => time() ));

            return;

        }else if($DATA['y'] == 'tx' ){

            global $SQTOKEN;

            /**
             * $DATA['u'] 用户token
             * $DATA['uid'] 用户id
             * $DATA['fid'] 房间id
             */


            $USER = $SQTOKEN ->get($DATA['u']);

            $room_type = isset($DATA['room_type'])?(int)$DATA['room_type']:0;

            if($USER && $USER['u'] > 0){

                $usercc = $USERCC -> get($USER['u']);
                if( $usercc && $usercc['f'] >= 10000 ){

                    $room_id = $usercc['f'];
                }else {

                    if( isset($DATA['f']) && $DATA['f'] >= 10000 ){

                        $room_id = $DATA['f'];
                        $game = $GAMECC -> get($room_id);
                        if( !$game ) {
                            $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-9,'msg'=>'房间不存在']));
                            return ;
                        }

                        //有观战位置进入房间
                        if( $game['spectators'] > 6 ){
                            $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-9,'msg'=>'房间人数已达上限']));
                            return ;
                        }

                    }else{

                        //分配房间
                        $room_id = allot_room( $room_type );
                    }
                }

                if( !$room_id || empty($room_id) ) return $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-9,'msg'=>'加入游戏失败']));

                $USERCC -> set($USER['u'],array('z' => $frame-> fd ,'u' =>$USER['u'] ,'f'=>$room_id,'exit'=>0));
                $SQTOKEN ->del($DATA['u']);
                $fan = $FIDDCC -> set($frame-> fd ,array( 'u' =>  $USER['u'],'t'=>$DATA['u'] ));

                $user = uid( $USER['u'],1 );
                if(!$user || $user['off'] != 1) return $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>2,'msg'=>'用户不存在或账号被封']));

                $game = gameccget( ($GAMECC -> get( $room_id )),'json' );

                if($game){

                    if( !in_array( $DATA['u'],$game['Auser'] ) ){
                        $rel = joinRoom( $room_id,$user,1 );
                        if( $rel['code'] == 1 ) $game = $rel['data'];
                        else return $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-8,'msg'=>'加入游戏失败']));
                    }else{

                        $game['renall'] += 1;
                        $game['ren'] += 1;
                        $game['Auserinfo'][$DATA['u']]['e'] = 1;

                        $GAMECC -> set( $room_id,gameccset( $game,'json' ) );
                    }

                    $seat_number = '';
                    $user_list = userList($game);
                    foreach ( $user_list as $k => $value ){
                        if( $value['uid'] == $USER['u'] ){
                            $seat_number = $k;
                            break;
                        }
                    }

                    $game['ddtime'] -= time();

                    if( $game['off'] == 0 ){
                        $data = [];
                        $otherOperation = [];
                    }else{
                        $data = raiseData($game);

                        $otherOperation = [];
                        //清理已设置操作的用户
                        foreach ( $game['Auserinfo'] as $uid => $value ){

                            if(  $value['e'] == 1 && $value['zd'] < 1 && $uid != $game['shuohua'] ){
                                $otherOperation[$uid] = [
                                    'fold'=>1,
                                    'check_fold'=>1,
                                ];

                                if( isset($game['Atongji'][$uid]) && $game['Atongji'][$uid] >= $game['bet'] ){
                                    $otherOperation[$uid]['check'] = 1;
                                }else $otherOperation[$uid]['check'] = 0;
                            }

                        }
                    }

                    $bet_pool = getBetChi( $game );

                    $data['uid'] = $game['shuohua'];
                    $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>1,'game'=>$game,'user'=>$user,'seat_number'=>$seat_number,'user_list'=>$user_list,'other'=>$otherOperation,'state'=>$data,'bet_pool'=>$bet_pool]));

                    if( in_array($USER['u'],$game['Aonlie']) ){
                        $user_list = userList($game);
                        $game['ddtime'] -= time();
                        GameGbAll('join_seat',['code'=>1,'uid'=>$USER['u'],'seat_number'=>$seat_number,'user_list'=>$user_list,'game'=>$game,'other'=>$otherOperation ],$game['Auser'],$USERCC,$server,$server);
                    }

                }else{
                    return$connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-2,'msg'=>'进入游戏失败']));
                }

            }else return $connection -> push($frame->fd,json_encode(['y'=>'tx','code'=>-1,'msg'=>'非法联机！']));

        }

        $FDUUID =  $FIDDCC -> get($frame-> fd);
        $AUSER = $USERCC -> get($FDUUID['u']);  /*用户数据*/

        $D = db('user');

        if($AUSER){

            $room_id = $AUSER['f']; //房间id

            $game = gameccget( ($GAMECC -> get( $room_id )),'json' );


            if ($DATA['y'] == 'huobilog'){ /*金币记录*/

                if($AUSER['u']){

                    $page = (int)(isset($DATA['page'])?$DATA['page']:1);
                    if($page < 1) $page = 1;

                    $limit = (int)(isset($DATA['limit'])?$DATA['limit']:50);
                    if($limit < 10) $limit =  10;
                    elseif ($limit > 50) $limit = 50;

                    $start = $page * $limit - $limit;

                    $D = db('huobilog');
                    $rel = $D -> where(['uid'=>$AUSER['u']]) -> limit("$start,$limit") -> order('atime desc')-> select();

                    if($rel){

                        $type = logac('huobilog');

                        foreach ($rel as $k=>$v){
                            $rel[$k]['type'] = $type[$v['type']];
                            $rel[$k]['atime'] = date("m-d H:i", $v['atime']);
                        }

                        return $connection -> push($frame->fd,json_encode(['y'=>'huobilog','code' => 1 ,'data'=>$rel]));
                    }else{

                        return $connection -> push($frame->fd,ydsend( 'msg', "没有更多数据" ));
                    }

                }else{

                    return $connection -> push($frame->fd,ydsend( 'msg', "数据错误" ));
                }

            }elseif ($DATA['y'] == 'huobi_yue'){ /*金币、佣金余额*/

                if($AUSER['u']){

                    $user = uid($AUSER['u'],1);

                    if($user){

                        $num = $user['huobi'] - $game['bet'];
                        $txsxf = (int)(isset($CONN['txsxf'])?$CONN['txsxf']:0) * 100;

                        $connection -> push($frame->fd,json_encode(['y'=>'huobi_yue','code' => 1 ,'huobi'=>round( (float)$num,2 ),'jine'=>round( (float)$user['yongjin'],2 ),'txsxf'=>$txsxf ]));

                    }else{
                        return $connection->push($frame->fd, ydsend('msg', "用户数据错误"));
                    }

                }else{
                    return $connection->push($frame->fd, ydsend('msg', "数据出错，请重新连接"));
                }

            }elseif ($DATA['y'] == 'join_seat'){ /*入座*/

                if($AUSER['u']){

                    $result = array_flip($game['Auser']);
                    if( $result[$AUSER['u']] && $result[$AUSER['u']] < 9 ) {
//                        $seat_number = $result[$AUSER['u']];
//                        $user_list = userList($game);
//                        $game['ddtime'] -= time();
//                        GameGbAll('join_seat',['code'=>1,'uid'=>$AUSER['u'],'seat_number'=>$seat_number,'user_list'=>$user_list,'game'=>$game ],$game['Auser'],$USERCC,$server,$server);
//                        return;
                        return $connection->push($frame->fd, ydsend('msg', "已坐下"));
                    }

                    if( !isset( $DATA['seat_number'] ) || (int)$DATA['seat_number'] < 0 || (int)$DATA['seat_number'] > 8 ){
                        return $connection->push($frame->fd, ydsend('msg', "座位号错误"));
                    }
                    $seat_number = (int)$DATA['seat_number'];

                    list( $seat,$spectators ) = seatNumber( $game['Auser'] );

                    if( !in_array($seat_number,$seat) )  return $connection->push($frame->fd, ydsend('msg', "座位有人"));

                    $userData = uid( $AUSER['u'],1 );
                    if( !$userData || $userData['off'] != 1 ) return $connection->push($frame->fd, ydsend('msg', "用户不存在或账号被封"));

                    $game['spectators'] -= 1;
                    $game['ren'] += 1;
                    $game['renall'] += 1;

                    foreach ( $game['Auser'] as $k => $uid ){
                        if( $uid == $AUSER['u'] ) {
                            unset($game['Auser'][$k]);
                            break;
                        }
                    }

                    $game['Auser'][$seat_number] = $AUSER['u'];

                    $rel = $GAMECC -> set( $room_id,gameccset( $game,'json' ) );

                    if( $rel ){

                        $otherOperation = [];
                        //清理已设置操作的用户
                        foreach ( $game['Auserinfo'] as $uid => $value ){

                            if(  $value['e'] == 1 && $value['zd'] < 1 && $uid != $game['shuohua'] ){
                                $otherOperation[$uid] = [
                                    'fold'=>1,
                                    'check_fold'=>1,
                                ];

                                if( isset($game['Atongji'][$uid]) && $game['Atongji'][$uid] >= $game['bet'] ){
                                    $otherOperation[$uid]['check'] = 1;
                                }else $otherOperation[$uid]['check'] = 0;
                            }

                        }

                        $user_list = userList($game);

                        $game['ddtime'] -= time();
                        GameGbAll('join_seat',['code'=>1,'uid'=>$AUSER['u'],'seat_number'=>$seat_number,'user_list'=>$user_list,'game'=>$game,'other'=>$otherOperation ],$game['Auser'],$USERCC,$server,$server);

                    }else{
                        return $connection->push($frame->fd, ydsend('msg', "坐下失败"));
                    }

                }else{
                    return $connection->push($frame->fd, ydsend('msg', "数据出错，请重新连接"));
                }

            }elseif ($DATA['y'] == 'WangYatxt'){ /*消息*/

                $number = isset($DATA['d'])?$DATA['d']:'';
                if( empty($number) ) return $connection->push($frame->fd, ydsend('msg', "编号错误"));

                $is_true = false;
                foreach ( $game['Auser'] as $k => $uid ){
                    if( $k < 9 && $uid == $AUSER['u'] ){
                        $is_true = true;
                    }
                }

                if( $is_true ){
                    GameGbAll('WangYatxt',['code'=>1,'uid'=>$AUSER['u'],'key'=>$number ],$game['Auser'],$USERCC,$server,$server);
                }else return $connection->push($frame->fd, ydsend('msg', "观战人员，不能发送消息"));

            }elseif ($DATA['y'] == 'jilu'){ /*消息*/

                $hash = 'dezhou/jilu/'.$room_id;
                $jilu = $Mem -> g($hash);
                if( !$jilu || !is_array($jilu) ) $jilu = [];

                return $connection->push($frame->fd, ydsend('jilu', $jilu));

            }elseif ($DATA['y'] == 'exit'){ /*退出*/

                if( $game['off'] == 0 || ( !in_array($AUSER['u'],$game['Aonlie']) && $game['Atongji'][$AUSER['u']] <= 0 ) ){
                    exitMessage( $AUSER['u'],$game,$AUSER['f'],$frame->fd,$server );
                }else{

                    if( $game['Auserinfo'][$AUSER['u']]['e'] != 2 ){

                        $game['Auserinfo'][$AUSER['u']]['e'] = 2;
                        $GAMECC -> set( $room_id,gameccset( $game,'json' ) );
                        return $connection->push($frame->fd, ydsend('msg', '本局结束自动退出'));
                    }

                }

            }elseif ($DATA['y'] == 'share'){ /*分享*/

                $user = uid($AUSER['u']);

                $qu_type = ['新手区','高手区','大师区','巅峰区'];

                $rel = $CONN['HTTP']."ewm.php?data=".urlencode($CONN['maHTTP'].'?gametype=dezhou&tuid'.$AUSER['u'].'&room_id='.$room_id);

                $data = [
                    'gamename'=>$CONN['dez_gamename'],
                    'uid'=>$user['uid'],
                    'touxiang'=>pichttp($user['touxiang']),
                    'qu'=>$qu_type[$game['gamequ']],
                    'xiaomang'=>$game['xiaomang'],
                    'damang'=>$game['damang'],
                    'renall'=>$game['renall'],
                    'xren'=>$game['xren'],
                    'room_id'=>$room_id,
                    'url'=>$rel,
                ];

                return $connection->push($frame->fd, ydsend('share',$data));

            }


            $game_action = ['call','raise','check','fold','all_in','check_fold','other_check','add_time']; /*游戏动作*/

            /*游戏*/
            if( in_array($DATA['y'],$game_action) && in_array($AUSER['u'],$game['Aonlie']) ){

                $yes_operate = ['check_fold','other_check'];//其他人可操作类型
                if( $game['shuohua'] == $AUSER['u'] ){ //说话人

                    //说话人不可操作：让或弃
//                    if( in_array($DATA['y'],$yes_operate) ) return $connection -> push($frame->fd,ydsend( 'msg', "非法操作" ));
                    if( in_array($DATA['y'],$yes_operate) ) return;

                }else{ //不是说话人

                    if( !in_array($DATA['y'],$yes_operate) ) return $connection -> push($frame->fd,ydsend( 'msg', "不可操作" ));
                    if( $game['Auserinfo'][$AUSER['u']]['zd'] > 0 ) return $connection -> push($frame->fd,ydsend( 'msg', "不可重复操操作" ));

                }

                if( $DATA['y'] == 'check_fold' ){ //让或弃

                    if( $game['Auserinfo'][$AUSER['u']]['zd'] > 0 ) return ['code'=>-1,'msg'=>'不可操作'];

                    $rel = setCheckFold( $room_id,$game,$AUSER['u'],2 );

                    if( $rel['code'] == 1 ){
                        return $connection->push($frame->fd, ydsend('check_fold', $rel ));
                    }else $connection->push($frame->fd, ydsend( 'msg',$rel['msg'] ));

                    return;

                }elseif( $DATA['y'] == 'other_check' ){ //让

                    if( $game['Auserinfo'][$AUSER['u']]['zd'] > 0 ) return ['code'=>-1,'msg'=>'不可操作'];

                    $rel = setCheckFold( $room_id,$game,$AUSER['u'],1 );

                    if( $rel['code'] == 1 ){

                        return $connection->push($frame->fd, ydsend('other_check', $rel ));
                    }else $connection->push($frame->fd, ydsend( 'msg',$rel['msg'] ));

                    return;

                }elseif( $DATA['y'] == 'call' ){ //跟注

                    $rel = yazhu( $room_id,$game );

                    if( $rel ){

                        $rel['game']['ddtime'] -= time();

                        GameGbAll('call',['code'=>1,'uid'=>$game['shuohua'],'bet'=>$rel['bet'],'yue'=>round((float)$rel['yue'],2),'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', "跟注失败"));

                    return;

                }elseif( $DATA['y'] == 'raise' ){ //加注

                    $raise = isset($DATA['raise'])?(int)$DATA['raise']:0;
                    if( $raise <= 0 ) return $connection->push($frame->fd, ydsend('msg', "加注金额有误"));

                    $rel = yazhu( $room_id,$game,$raise );

                    if( $rel ){
                        $rel['game']['ddtime'] -= time();
                        GameGbAll('raise',['code'=>1,'uid'=>$game['shuohua'],'bet'=>$rel['bet'],'yue'=>round((float)$rel['yue'],2),'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', "加注失败"));

                    return;

                }elseif( $DATA['y'] == 'check' ){ //让牌

                    $rel = check( $room_id,$game );

                    if( $rel ){
                        $rel['game']['ddtime'] -= time();
                        GameGbAll('check',['code'=>1,'uid'=>$game['shuohua'],'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', "让牌失败"));

                    return;

                }elseif( $DATA['y'] == 'fold' ){ //弃牌

                    $rel = check( $room_id,$game,1 );

                    if( $rel ){
                        $rel['game']['ddtime'] -= time();
                        GameGbAll('fold',['code'=>1,'uid'=>$game['shuohua'],'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', "让牌失败"));

                    return;

                }elseif( $DATA['y'] == 'all_in' ){

                    $user = uid( $AUSER['u'],1 );
                    if( !$user || $user['off'] != 1 ){
                        return $connection->push($frame->fd, ydsend('msg', "账号不存在或被封"));
                    }

                    $rel = allIn( $room_id,$game );

                    if( $rel['code'] == 1 ){
                        $rel['game']['ddtime'] -= time();
                        GameGbAll('all_in',['code'=>1,'uid'=>$game['shuohua'],'bet'=>$rel['bet'],'yue'=>round((float)$rel['yue'],2),'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', $rel['msg']));

                    return;

                }elseif( $DATA['y'] == 'add_time' ){ //追加等待时间

                    $rel = addTime( $AUSER['u'],$room_id,$game );

                    if( $rel['code'] == 1 ){
                        $rel['game']['ddtime'] -= time();
                        GameGbAll('add_time',['code'=>1,'uid'=>$game['shuohua'],'time'=>$rel['add_time'],'game'=>$rel['game']],$game['Auser'],$USERCC,$server,$server);
                    }else $connection->push($frame->fd, ydsend('msg', $rel['msg']));

                    return;

                }

            }

        }else return $connection->close( $frame->fd ,true );


    }else return $connection->close( $frame->fd ,true );


});



$server->on('close', function ($server, $fd)use($USERCC)
{


    if($fd){

        global $USERCC,$FIDDCC,$BETCC,$GAMECC;
        $FDUUID =  $FIDDCC -> get($fd);

        if($FDUUID ){

//            $fan = $USERCC -> del($FDUUID['u']);

            global $GAMECC;

            $uid = $FDUUID['u'];
            $usercc = $USERCC -> get($uid);
            if( $usercc['f'] ){
                $game = gameccget( ($GAMECC -> get( $usercc['f'] )),'json' );

                if( $game ){

                    $game['Auserinfo'][$uid]['e'] = 2;
                    /*foreach ( $game['Auser'] as $k => $Auid ){
                        if( $uid == $Auid ){
                            unset( $game['Auser'][$k] );
                        }
                    }*/
                    $GAMECC -> set( $usercc['f'],gameccset( $game,'json' ) );
                }
            }

            $USERCC -> del( $uid );
            $FIDDCC -> del($fd);
        }
    }

    $fdinfo = $server->connection_info($fd);
    if(isset($fdinfo) && isset( $fdinfo['remote_ip'] )){

        global $FANGYUCC;

        $hash = md5($fdinfo['remote_ip']);
        $zong = $FANGYUCC ->decr($hash, 'j',  1);

        if($zong<=1){

            $FANGYUCC ->del($hash);

        }
    }
});

$server->start();