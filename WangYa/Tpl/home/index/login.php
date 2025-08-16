<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

/*login start */


$ZHANGHAO = isset( $_NPOST['zhanghao'] ) ? ( $_NPOST['zhanghao'] ) : '';  /* 登录帐号  */
$PASS     = isset( $_NPOST['pass'] )     ? ( $_NPOST['pass'] )  : '';     /* 登录密码  */
$OriginalPass     = isset( $_NPOST['OriginalPass'] )     ? ( $_NPOST['OriginalPass'] )  : '';     /* 原登录密码  */
$CCODE = isset( $_NPOST['code'] )     ? ( $_NPOST['code'] )  : ''; /*发送code*/
$VCODE = isset( $_NPOST['vcode'] )     ? ( $_NPOST['vcode'] )  : ''; /*图形验证码*/
$FALX = isset( $_NPOST['falx'] )     ? ( $_NPOST['falx'] )  : 1; /*发送类型 1 注册  2着火*/

if( $MOD == 'get' ){

    /*登录韩快捷注册 */

    if( $USERID > 0 ){

        return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);
    }


    $KJLOGIN =  (int)( isset( $CONN['kjlogin']) ? $CONN['kjlogin'] : 0 );

    $canshu = array(
        'zhanghao#len#1-36',
        'pass#len#3-36',
    );

    $FAN = yzpost( $canshu ,$_NPOST);

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

    $D = db('user');



    $DATA = $D -> where( $WHERE )-> find();

    if( $KJLOGIN == '1' && ! $DATA ){

        $JICHENG = $WHERE;

        $WHERE['name']  = "";
        $WHERE['atime'] = time();
        $WHERE['off']   = 1;
        $WHERE['mima']  = mima($PASS) ;
        $WHERE['touxiang'] = touxiang();
        $WHERE['ip']    = $IP;
        $WHERE['level'] = 0;
        $WHERE['yanzhengip'] = 0;
        $WHERE['hbqun'] = '1,2,3,';
        if(($tuid = sescc('tuid','',$UHA)) > 0 ){
        
            $_NPOST['tuid'] = $tuid;
        } 

        if(isset($_NPOST['tuid'])) {

           
            $sescc = sescc('tuid',(int)$_NPOST['tuid'],$UHA);
        }

      
            
        if( $sescc['tuid']  > 0){

            $tuid =  uid( $sescc['tuid'] );
            if( $tuid ){

                $WHERE['tuid'] = $sescc['tuid'] ;

                for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){
                        $wds = $i-1;
                        if($wds < 1) $wds= '' ;
                        $WHERE['tuid'.$i] = $tuid['tuid'.$wds];
               }
            }

        }


        $sql  =  $D -> setshiwu('1') -> insert( $WHERE );

        $FAN  =  $D -> qurey( $sql , 'shiwu' );

       

        if( $FAN ){
        
            $DATA = $D -> where( $JICHENG )-> find();
            

            if( $DATA ){

                regsong( $DATA );

            }
        }

    }

    if( ! $DATA ){

        return apptongxin($SHUJU,415,$CODE,'账号不存在',$YZTOKEN,$WY);
    }

    
    
    if( $DATA['off'] < 1 ){

        return apptongxin($SHUJU,415,$CODE,'帐号关闭',$YZTOKEN,$WY);
    }

    if( $DATA['level'] < 1 ){

        //return apptongxin($SHUJU,415,$CODE,'不是代理',$YZTOKEN,$WY);
    }
   


    if( $DATA['mima'] != mima( $PASS )){
    
        return apptongxin($SHUJU,415,$CODE,'帐号或密码错误',$YZTOKEN,$WY);
    }

    $USERID  =  $DATA['uid'];

    sescc(array('uid' => $USERID,'ip'=>$IP),'',$UHA);
    userlog( $DATA['uid'] , 0 );

    if( $sescc['kjid'] != '' && strlen($sescc['kjid']) > 10){

        $fan =  bangding( $sescc['kjlx'] ,$DATA['uid'],$sescc['kjuid'],  $sescc['kjname'] ,  $sescc['kjtoux'] , $sescc['kjid']);

        if( $fan ){

            $DATA = uid( $DATA['uid'] ,1 );
        }

        sescc(array( 'kjid'=> '', 'kjuid' => 0, 'kjname' => '','kjtoux' => '') , '', $UHA);

       
    }

    

}else if( $MOD == 'post' ){
    /* post 创建*/

	// return apptongxin(array(),415,$CODE,'注册关闭',$YZTOKEN,$WY);

    if( $USERID > 0 ){

        return apptongxin($SHUJU,415,-1,$USERID.'已经登录',$YZTOKEN,$WY);
    }

    $canshu = array(
        'zhanghao#len#1-36',
        'pass#len#3-36',
        'vcode#len#4',
        // 'code#len#6',
    );

    $FAN = yzpost( $canshu ,$_NPOST);

    if( $FAN['code'] == '0'){

        if( $FAN['biao'] == 'zhanghao')

             return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
        else return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
    }

    if($sescc['code'] != $VCODE){

        return apptongxin(array(),415,-9,'验证码错误',$YZTOKEN,$WY);
    }

    $YZHost = 'weiyi/post'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    if(!$_NPOST['tuid'] || $_NPOST['tuid'] == ''){
        return apptongxin(array(),415,-1,'请填写上级uid'.$sescc['tuid'].$_GET['tuid'],$YZTOKEN,$WY);
    }

    $TUSER = uid((int)$_NPOST['tuid']);

    if(!$TUSER){
        return apptongxin(array(),415,-1,'上级不存在',$YZTOKEN,$WY);
    }

    $WHERE = array();

    $lx = 1;

    if( isshouji( $ZHANGHAO )) {

        $lx = 3;
        
        $WHERE['shouji'] = ( (float) $ZHANGHAO < 1 ) ? -1 : $ZHANGHAO ;

    }
    // else if( isemail( $ZHANGHAO )) {

    //     $lx = 2;

    //     $WHERE['email'] =  $ZHANGHAO ;
        
    // }else{

        $WHERE['zhanghao'] =  $ZHANGHAO ;
    // }

    $D = db('user');

    $data = $D ->where($WHERE)->find();

    if($data){
        
        return apptongxin(array(),415,-1,'帐号已经注册',$YZTOKEN,$WY);
    }

    // $DXHASH = 'duanxin/'.$ZHANGHAO;

    // $jiecode = $Mem ->g($DXHASH);

    // if(!$jiecode || strlen($jiecode) != 6 ){

    //     return apptongxin(array(),415,-1,'请重新点点击发送',$YZTOKEN,$WY);
    // }

    // if($jiecode != $CCODE){

    //     return apptongxin(array(),415,-1,'接收的验证码错误',$YZTOKEN,$WY);
    // }
    $JICHENG = $WHERE;

    $WHERE['name']  = "";
    $WHERE['atime'] = time();
    $WHERE['off']   = 1;
    $WHERE['mima']  = mima($PASS) ;
    $WHERE['touxiang'] = touxiang();
    $WHERE['ip']    = IP();
    $WHERE['level'] = 0;
    $WHERE['yanzhengip'] = (float)$CONN['yanzhengip'];
    $WHERE['hbqun'] = '1,2,3,';


    if(($tuid = sescc('tuid','',$UHA)) > 0 ){
        
        $_NPOST['tuid'] = $tuid;
    } 

    if(isset($_NPOST['tuid'])) {

        $sescc = sescc('tuid',(int)$_SESSION['tuid'],$UHA);
    }

    $WHERE['tuid'] = $TUSER['uid'];

    for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){
        $wds = $i-1;
        if($wds < 1) $wds= '' ;
        $WHERE['tuid'.$i] = $TUSER['tuid'.$wds];
    }
    

    $sql  =  $D -> setshiwu('1') -> insert( $WHERE );

    $FAN  =  $D -> qurey( $sql , 'shiwu' );

    if( $FAN ){
    
        $DATA = $D -> where( $JICHENG )-> find();
        

        if( $DATA ){

            regsong( $DATA );

        }

        $Mem ->d($DXHASH);
    }

    if( !$FAN  || !$DATA  ){


        return apptongxin(array(),415,-1,'注册是啊比',$YZTOKEN,$WY);
    
    }


    $USERID  =  $DATA['uid'];

    sescc(array('uid' => $USERID,'ip'=>$IP),'',$UHA);
    userlog( $DATA['uid'] , 0 );

    if( $sescc['kjid'] != '' && strlen($sescc['kjid']) > 10){

        $fan =  bangding( $sescc['kjlx'] ,$DATA['uid'],$sescc['kjuid'],  $sescc['kjname'] ,  $sescc['kjtoux'] , $sescc['kjid']);

        if( $fan ){

            $DATA = uid( $DATA['uid'] ,1 );
        }

        sescc(array( 'kjid'=> '', 'kjuid' => 0, 'kjname' => '','kjtoux' => '') , '', $UHA);

        
    }

    return apptongxin(array(),200,1,'注册成功',$YZTOKEN,$WY);
}else if( $MOD == 'put' ){
    /* put 修改*/

    if( $USERID > 0 ){

        // return apptongxin($SHUJU,415,-1,'已经登录',$YZTOKEN,$WY);
    }
    
    $canshu = array(
        // 'zhanghao#len#1-36',
        'pass#len#3-36',
        // 'vcode#len#4',
        // 'code#len#6',
    );

    $FAN = yzpost( $canshu ,$_NPOST);

    if( $FAN['code'] == '0'){

        if( $FAN['biao'] == 'zhanghao')

             return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
        else return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
    }



    if($sescc['code'] != $VCODE){

        return apptongxin(array(),415,-9,'验证码错误',$YZTOKEN,$WY);
    }

    $YZHost = 'weiyi/post'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $WHERE = array('uid' => $USERID);

    // $lx = 1;

    // if( isshouji( $ZHANGHAO )) {

    //     $lx = 3;
        
    //     $WHERE['shouji'] = ( (float) $ZHANGHAO < 1 ) ? -1 : $ZHANGHAO ;

    // }else if( isemail( $ZHANGHAO )) {

    //     $lx = 2;

    //     $WHERE['email'] =  $ZHANGHAO ;
        
    // }else{

    //     $WHERE['zhanghao'] =  $ZHANGHAO ;
    // }


    // if($lx == 1){

    //     return apptongxin(array(),415,-1,'帐号无法找回',$YZTOKEN,$WY);
    // }

    $D = db('user');

    $data = $D ->where($WHERE)->find();

    if(!$data){
        
        return apptongxin(array(),415,-1,'帐号不存在无法找回',$YZTOKEN,$WY);
    }

    if(mima($OriginalPass) != $data['mima']){
        
        return apptongxin(array(),415,-1,'原密码错误',$YZTOKEN,$WY);
    }

    // $DXHASH = 'duanxin/'.$ZHANGHAO;

    // $jiecode = $Mem ->g($DXHASH);

    // if(!$jiecode || strlen($jiecode) != 6 ){

    //     return apptongxin(array(),415,-1,'请重新点点击发送',$YZTOKEN,$WY);
    // }

    // if($jiecode != $CCODE){

    //     return apptongxin(array(),415,-1,'接收的验证码错误',$YZTOKEN,$WY);
    // }

    $NEW = mima($PASS);

    if($data['mima'] == $NEW){
    
        return apptongxin(array(),415,-1,'和原密码一样无需修改',$YZTOKEN,$WY);
    }


    $sql = $D ->setshiwu(1)-> where( array( 'uid' => $data['uid'])) -> update(array( 'mima' => $NEW ));

    $fan = $D -> qurey( $sql , 'shiwu');
    if( !$fan ) {

        return apptongxin(array(),415,-1,'密码修改失败',$YZTOKEN,$WY);
    }

    uid( $data['uid'] , 1 );

    $Mem ->d($DXHASH);


}else if( $MOD == 'delete' ){
    /* delete 其他操作*/

    if( $USERID > 0 ){

        return apptongxin($SHUJU,415,-1,$USERID.'已经登录',$YZTOKEN,$WY);
    }

    $canshu = array(
        'zhanghao#len#1-36',
        'pass#len#3-36',
        'vcode#len#4'
    );

    $FAN = yzpost( $canshu ,$_NPOST);

    if( $FAN['code'] == '0'){

        if( $FAN['biao'] == 'zhanghao')

             return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
        else return apptongxin($SHUJU,415,1,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
    }



    if($sescc['code'] != $VCODE){

        return apptongxin(array(),415,-9,'验证码错误',$YZTOKEN,$WY);
    }

    $YZHost = 'weiyi/delete'.md5($ZHANGHAO.$PASS);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,-1,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $WHERE = array();

    $lx = 1;

    // if( isshouji( $ZHANGHAO )) {

        $lx = 3;
    //     $WHERE['shouji'] = ( (float) $ZHANGHAO < 1 ) ? -1 : $ZHANGHAO ;

    // }else if( isemail( $ZHANGHAO )) {

    //     $lx = 2;

    //     $WHERE['email'] =  $ZHANGHAO ;
        
    // }else{

        $WHERE['zhanghao'] =  $ZHANGHAO ;
    // }

    $D = db('user');

    $data = $D ->where($WHERE)->find();

    if($FALX == 1){

        /*注册用户*/

        if($data){
        
            return apptongxin(array(),415,-1,'帐号已经注册',$YZTOKEN,$WY);
        }

    
    }else{
        /*找回密码*/


        if(!$data){
        
            return apptongxin(array(),415,-1,'帐号不存在无法找回密码',$YZTOKEN,$WY);
        }

        if($lx == 1){

            return apptongxin(array(),415,-1,'请使用邮箱或手机找回密码',$YZTOKEN,$WY);
        
        }
    }

    // $DXHASH = 'duanxin/'.$ZHANGHAO;

    // if($Mem ->g($DXHASH)){

    //     return apptongxin(array(),415,-1,'短信已经发送,请注意查收',$YZTOKEN,$WY);
    // }


    // $duanxin = rand(100000,999999);

    // if($lx == 3){

    //     /*发送手机*/

    //     $fanc = duanxin( $ZHANGHAO , array('YZM' => $duanxin ,'type' => 1),$CONN);
    //     userlog( 0 , 3 , $ZHANGHAO . ' '.$duanxin . ' '.$fanc  );

    //     if( !strstr( $fanc, 'success')  ){

    //         return apptongxin(array(),415,-1,'发送失败联系客服',$YZTOKEN,$WY);

    //     }

    
    // }else if($lx == 2){

    //     /*发送邮箱*/
    //     $fanc = youxiang( $ZHANGHAO ,array('YZM' => $duanxin ,'type' => 1),$CONN );

    //     userlog( 0 , 2 , $ZHANGHAO . ' '.$duanxin . ' '.$fanc  );

    //     if( !strstr( $fanc, 'success')  ){

    //         return apptongxin(array(),415,-1,'发送失败联系客服',$YZTOKEN,$WY);

    //     }

    
    // }else{
    //     /*自己发送*/

    //     $Mem -> s($DXHASH,$duanxin,80);
    //     return apptongxin($duanxin,200,2,'短信发送成功',$YZTOKEN,$WY);

    // }

    // $Mem -> s($DXHASH,$duanxin,80);

    $NEW = mima($PASS);

    $sql = $D ->setshiwu(1)-> where( array( 'uid' => $data['uid'])) -> update(array( 'mima' => $NEW ));

    $fan = $D -> qurey( $sql , 'shiwu');
    if( !$fan ) {

        return apptongxin(array(),415,-1,'找回密码失败',$YZTOKEN,$WY);
    }

    uid( $data['uid'] , 1 );

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);