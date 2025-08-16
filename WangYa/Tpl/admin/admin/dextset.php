<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PANC = '已填写,录入即为修改';

if($MOD == 'get'){
    /*获取数据*/

    $CODE = 1;
    $STAT = 200;

    $yinc = array(

        'duanxinkey',
        'kjqqkey',
        'kjwxkey',
        'kjkwxkey',
        'kjweibokey',
        'kjzfbkey',
        'tx_key',
    );

    foreach($yinc as $kl){

        if( isset( $CONN[$kl])){

            if($CONN[$kl] != '' ){

                $CONN[$kl] = $PANC;
            }
        }
    }

    $SHUJU = $CONN;
    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);


}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);
    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);


    $unset = array(
        '',
        'dir',
        'token',
        'lang',
        'ttoken',
        'apptoken',
        'lang',
        'qtpl',
        'htpl' ,
    );

    $_NPOST['logo'] =  TOU_ti($_NPOST['logo']);
    $_NPOST['dez_kefu'] =  TOU_ti($_NPOST['dez_kefu']);
    $_NPOST['dez_erweima'] =  TOU_ti($_NPOST['dez_erweima']);

    foreach($unset as $kl){

        if( isset( $_NPOST[$kl]) ) unset( $_NPOST[$kl] );
    }

    foreach($_NPOST as $k => $v){

        if( !is_array( $v ) && $v == $PANC ){

            /*判断包含没修改的值 直接清理k*/
            unset($_NPOST[$k]);


        }else if(!isset($CONN[$k])){

            /*conn不存在的值清理掉*/
            unset($_NPOST[$k]);
        }
    }

    $YUCONN = $CONN;


    $CONN = array_merge($CONN, $_NPOST);
    global $CONLJI;

    x($CONLJI,$CONN);

    $yinc = array(

        'duanxinkey',
        'kjqqkey',
        'kjwxkey',
        'kjkwxkey',
        'kjweibokey',
        'kjzfbkey',
        'tx_key',
    );

    foreach($yinc as $kl){

        if( isset( $YUCONN[$kl])){

            if($YUCONN[$kl] != '' ){

                $YUCONN[$kl] = $PANC;
            }
        }

        if( isset( $_NPOST[$kl])){

            if( $_NPOST[$kl] != '' ){

                $_NPOST[$kl] = $PANC;
            }
        }
    }


    adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD ,'yuan'=> $YUCONN , 'data'=> $_NPOST )));

    $CODE = 1;



}else if($MOD == 'delete'){
    /*删除数据*/

}


return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);