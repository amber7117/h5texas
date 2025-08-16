<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

htmlhead('application/json;charset=UTF-8',$WY);
$SHUJU = array();
$CODE  = 1;
$MSG   = '';
$STAT  = 200;
$YZTOKEN  = '';

$USERID = (float) ( $sescc['uid'] );

if(isset($_NGET['y'])) $_NPOST = $_NGET;

$AC = isset($_NPOST['y'])?$_NPOST['y']:'index';


if($AC){


    $MODE = array(
        'post' => '增加',
        'delete' => '删除',
        'put' => '修改',
        'get' => '获取数据',
        'only' => '只处理自己',
    );

    if($AC== 'quite'){


        if( $sescc['aid'] > 0){

            $fan = $SECC -> d('session/'.$UHA);
            session_destroy();
            adminlog($sescc['aid'],1);
        }


    
        return apptongxin($SHUJU,200,-1,'退出成功',$YZTOKEN,$WY);
    }

    $MOD = isset($_NPOST['d']) && isset($MODE[$_NPOST['d']] ) ? $_NPOST['d'] :'get';

    if($sescc['aid'] < 1){

        $NEWS = $ACTION = array(
            'index' => '默认通信接口',
            'login' => '登录',
        );

    }else{

        $ACTION = array(

            'cd1' => array( 'adminfenzu',
                            'admin',
                            'adminlog'
                           ),
            'cd2' => array( 'user',
                            // 'jinelog',
                            'jifenlog',
                             'huobilog',
                            'userlog',
                             'msgbox',
                             'jinejia',
                             'gzlog',
                             'tongJi',
                          ),
            'cd7' => array(

                        'dingdan',
                        'pay',
                        'tixiandingdan',
                        'tixianshenhe',

                     ),
            'cd9' => array(

                        'deBetlog',

                     ),
            'cd18' => array( 
                        'gameserver',
//                        'gamelist',
//                        'tongji',
//                        'fanglist' ,
                     ),
		
           
            'cd4' => array( 'logac',
//                            'xtset',
                            'dextset',
                            // 'bjhtml'
                           )
            
        );

        $LEVEL  = adminfenzu( $_NSESSION['qx'] );

        if( $LEVEL < 1){
            $LEVEL = array();
            $LEVEL['name'] = $LANG['qxall'];
        }

        $YANZQX =  $sescc['qx'] == '0'? $ACTION : unserialize($LEVEL['shezhi']);

        $NEWS = array('index' => array('get'=>'get'));
                
        foreach( $ACTION as $kx => $vv){

            if( is_array( $vv) ){

                foreach($vv as $woqus){
                    $NEWS[$woqus]=$kx;
                }
            }
        }
    
    
    }


    $WHERE = array();


    if(isset($NEWS[$AC])){


        $lujin = HTPL.$AC.'.php';

        if( is_file( $lujin )){

//            $sescc = sescc('aid','121');

            if( $AC != 'login' && $sescc['aid'] < 1)
            {
                $SHUJU['apptoken'] = $SESSIONID;
                return apptongxin($SHUJU,415,-99,'需要登录',$YZTOKEN,$WY);

            }else{

                $D = db('user');

                if( $sescc['yzip']== 1 ){

                    $ip = $Mem ->g('adminip/'.$sescc['aid']);

                    if( $sescc['ip'] != ip() || $ip != ip() ){

                        $SECC -> d('session/'.$UHA);
                        session_destroy();

                        adminlog($sescc['aid'],2,serialize(ip()));

                        return apptongxin($SHUJU,415,-99,'IP错误,请重新登录',$YZTOKEN,$WY);


                    }
                }


            
            
            }


            if( $sescc['qx'] > 0){

               

                if( $AC != 'index' && 
                    
                ( 

                    !$YANZQX || 
                    !isset($YANZQX[$NEWS[$AC]]) ||  
                    !isset($YANZQX[$NEWS[$AC]][$AC]) || 
                    !isset($YANZQX[$NEWS[$AC]][$AC][$MOD]) 
                    
                )  ){

                    return apptongxin($SHUJU,415,-1,'权限不足'.$MOD,$YZTOKEN,$WY);
                }
            }

            include $lujin;


        }else return apptongxin($SHUJU,500,-1,'no File',$YZTOKEN,$WY);

    }else {
        session_destroy();
        return apptongxin($SHUJU,415,-1,'权限不足',$YZTOKEN,$WY);
    }

    
}else{
    
    return apptongxin($SHUJU,500,-1,'no ac',$YZTOKEN,$WY);
}
