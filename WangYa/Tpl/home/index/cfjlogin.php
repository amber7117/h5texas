<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/9
 * Time: 10:59
 */


$ZHANGHAO = isset( $_NPOST['zhanghao'] ) ? ( $_NPOST['zhanghao'] ) : '';  /* 登录帐号  */
$PASS     = isset( $_NPOST['pass'] )     ? trim( $_NPOST['pass'] )  : '';     /* 登录密码  */
$PASS1     = isset( $_NPOST['pass1'] )     ? trim( $_NPOST['pass1'] )  : '';     /* 确认密码  */
$NAME     = isset( $_NPOST['name'] )     ? ( $_NPOST['name'] )  : '';     /* 用户名  */
$CCODE = isset( $_NPOST['code'] )     ? trim( $_NPOST['code'] )  : ''; /*发送code*/
$VCODE = isset( $_NPOST['vcode'] )     ? ( $_NPOST['vcode'] )  : ''; /*图形验证码*/
$FALX = isset( $_NPOST['falx'] )     ? ( $_NPOST['falx'] )  : 1; /*发送类型 1 注册  2着火*/

$D = db('user');

if( $MOD == 'get' ){

    /* 登录 */

    if(empty($ZHANGHAO))  return apptongxin(array(),415,-1,'账号不能为空',$YZTOKEN,$WY);
    if(empty($PASS)) return apptongxin(array(),415,-1,'密码不能为空',$YZTOKEN,$WY);

    $rel = $D -> zhicha('uid,name,zhanghao,jine,huobi,off,jingyan,tuid,touxiang,dan,ji_num,mima') -> where(['zhanghao'=>$ZHANGHAO]) -> find();

    if(!$rel) return apptongxin(array(),415,-1,'账号不存在',$YZTOKEN,$WY);
    else {
        if($rel['mima'] != mima($PASS)) return apptongxin(array(),415,-1,'密码有误',$YZTOKEN,$WY);

        unset($rel['$rel']);

        $CODE = 1;
        $SHUJU['data'] = $rel;

        $USERID  =  $DATA['uid'];

        $rel['ip'] = $IP;
        sescc($rel,'',$UHA);
        userlog( $DATA['uid'] , 0 );

    }

}else if( $MOD == 'post' ){
    /* post 创建*/


    if( $USERID > 0 ){
        return apptongxin($SHUJU,415,-1,'已经登录',$YZTOKEN,$WY);
    }

    if( !isshouji( $ZHANGHAO )) return apptongxin(array(),415,-1,'手机号格式有误',$YZTOKEN,$WY);

    $YZHost = 'weiyi/post'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    if(!isset($NAME) || empty($NAME)) return apptongxin(array(),415,-1,'用户名不能为空',$YZTOKEN,$WY);

    if(!isset($PASS) || empty($PASS)) return apptongxin(array(),415,-1,'密码不能为空',$YZTOKEN,$WY);

    if($PASS != $PASS1) return apptongxin(array(),415,-1,'两次密码不一致',$YZTOKEN,$WY);

    if(!isset($CCODE) || empty($CCODE)) return apptongxin(array(),415,-1,'验证码不能为空',$YZTOKEN,$WY);

    $sescc = sescc('code','123456');
    if($sescc['code'] != $CCODE){

        return apptongxin(array(),415,-1,'验证码错误',$YZTOKEN,$WY);
    }


    /*验证账号密码长度*/
    $canshu = array(
        'zhanghao#len#11',
        'pass#len#3-36',
        'name#len#2-16',
        'code#len#6',
    );
    $FAN = yzpost( $canshu ,$_NPOST); /*验证账号、密码*/

    if( $FAN['code'] == '0'){

        if( $FAN['biao'] == 'zhanghao')

            return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
        else return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
    }



    /* 并发控制 限制用户唯一标识不管提交地址 */
    $YZHost = 'weiyi/get'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);


    $WHERE = array();

    $WHERE['zhanghao'] =  $ZHANGHAO;


    $DATA = $D -> where( $WHERE )-> find();

    if($DATA) return apptongxin($SHUJU,415,$CODE,'用户已存在',$YZTOKEN,$WY);

    $WHERE['name']  = $NAME;
    $WHERE['atime'] = time();
    $WHERE['off']   = 1;
    $WHERE['mima']  = mima($PASS) ;
    $WHERE['touxiang'] = touxiang();
    $WHERE['ip']    = $IP;
    $WHERE['level'] = 0;
    $WHERE['yanzhengip'] = 0;
    $WHERE['shouji'] = ( (float) $ZHANGHAO < 1 ) ? -1 : $ZHANGHAO ;

    if(($tuid = sescc('tuid','',$UHA)) > 0 ){

        $_NPOST['tuid'] = $tuid;
    }

    if(isset($_NPOST['tuid'])) {


        $sescc = sescc('tuid',(int)$_NPOST['tuid'],$UHA);
    }



    if( $sescc['tuid']  > 0){

        $tuid =  uid( $sescc['tuid'],1 );
        if( $tuid ){

            $WHERE['tuid'] = $sescc['tuid'] ;

            for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){
                $wds = $i-1;
                if($wds < 1) $wds= '' ;
                $WHERE['tuid'.$i] = $tuid['tuid'.$wds];
            }
        }

    }


    $DATA  =  $D -> insert( $WHERE );

    if( ! $DATA ){

        return apptongxin($SHUJU,415,$CODE,'注册失败',$YZTOKEN,$WY);
    }

    $SHUJU['data'] = $DATA;
    $MSG = '注册成功';

}else if( $MOD == 'put' ){
    /* put 修改*/

    if( $USERID > 0 ){
        return apptongxin($SHUJU,415,-1,'已经登录',$YZTOKEN,$WY);
    }


    $YZHost = 'weiyi/post'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,2);
        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,2);


    $user = uid( $ZHANGHAO,1 );
    if(!$user) return apptongxin(array(),415,-1,'用户不存在',$YZTOKEN,$WY);


    if($PASS != $PASS1) return apptongxin(array(),415,-1,'两次密码不一致',$YZTOKEN,$WY);

    $canshu = array(
        'pass#len#3-36',
        'code#len#6',
    );

    $FAN = yzpost( $canshu ,$_NPOST);

    if( $FAN['code'] == '0'){

        if( $FAN['biao'] == 'zhanghao')

            return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
        else return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
    }


    if($sescc['code'] != $CCODE){

        return apptongxin(array(),415,-9,'验证码错误',$YZTOKEN,$WY);
    }

    $rel = $D -> where(['zhanghao'=>$ZHANGHAO]) -> update([
        'mima'=>mima($PASS),
    ]);

    if(!$rel) return apptongxin(array(),415,-9,'修改失败',$YZTOKEN,$WY);

}else if( $MOD == 'delete' ){
    /* delete 其他操作*/

    session_destroy();

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);