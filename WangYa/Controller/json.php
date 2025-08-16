<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

/*输出head头部*/
htmlhead('application/json;charset=UTF-8',$WY);


$SHUJU = array();
$CODE  = 1;
$MSG   = '';
$STAT  = 200;
$YZTOKEN  = '';

if(isset($_NGET['y'])) $_NPOST = $_NGET;

$SHUJU['apptoken'] = $SESSIONID;

$AC = isset($_NPOST['y'])?$_NPOST['y']:'index';

$USERID = (int) ( $sescc['uid'] );


if( isset( $_NPOST['y'])){

    $MODE   = array(
        'post' => '增加',
        'delete' => '删除',
        'put' => '修改',
        'get' => '获取数据',
    );

    $ACTION = array(
        'index' => '默认游戏通信接口',
//        'login' => '登录注册',
       'user' => '用户中心编辑',
//        'jinelog' => $CONN['jine'].'记录',
//        'jifenlog' => $CONN['jifen'].'记录',
        'huobilog' => $CONN['huobi'].'记录',
       'msgbox' => '消息中心',
       'games' => '游戏记录',
//        'chading' => '支付扫码异步通知',
//        'loginsm' => '登录扫码',
       'daili' => '代理数据',
//        'dlmkgame' => '代理开房',
       'zhanji' => '战绩',
//        'online' => '场次等待或者其他在先等待',
//        'dllevel' => '等级信息',
//        'dlmyuser' => '我的会员',

//        'dlupay' => '代理给玩家充值',
//        'dtpaylist' => '会员的充值订单',
//        'dtuiguang' => '我的推广统计',
//        'error' => '错误记录',
        'quite' => '退出登录',
        'duihuan' => '签到大转盘',
        'daojudui' => '道具积分兑换',
        'qunzhu' => '群主操作',

//        'paihang' => '排行榜',
//        'game_list' => '列表',

        'deBetlog' => '德州投注记录',
        'agencyCount' => '代理统计',
        'deLottery' => '德州转盘',
        'deRoomType' => '德州房间类型',
        'myWage' => '工资',
        'deResult' => '工资',
    );

    if( isset( $ACTION[ $AC ] ) ){

        $MOD = strtolower( isset( $_NPOST['d'] ) ? $_NPOST['d'] :'get');

        /** json.php
         * 过滤xss
         */
        if(isset($_NPOST['y'])){

            foreach($_NPOST as $k=>$v){
                if(! is_array($v) ){
                    $_NPOST[$k] = anquanqub($v);
                }
            }
        }

        $lujin = QTPL.$AC.'.php';

        if( is_file( $lujin )){

            return include $lujin;

        }else{
        
            return apptongxin($SHUJU,500,-1,'no File',$YZTOKEN,$WY);
        }

    }else return apptongxin($SHUJU,500,-1,'no ac',$YZTOKEN,$WY);

}else{
    
    return apptongxin($SHUJU,500,-1,'no ac',$YZTOKEN,$WY);
}
