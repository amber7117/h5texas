<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( isset ( $_NGET['state'] ) &&  strlen( $_NGET['state']) == 32){

    $denglu  =  'kjdenglu/'.mima( $_NGET['state'] ).'G';

    $wodese = $Mem ->g( $denglu );

    if( $wodese ){
        
        setcookie('apptoken' , $wodese , time()*2,'/');
        $UHA = md5($wodese);

        $_NSESSION = $sescc = sescc('','',$UHA);

        $Mem -> d ( $denglu );
    }
}

//$WZHOST = WZHOST.'?apptoken='.$SESSIONID.'&tuid='.$sescc['tuid'].'&qunid='.($_GET['qunid']?$_GET['qunid']:'');
//$TIAOURL = $sescc['back'] != '' ? $sescc['back'] : WZHOST;


$WZHOST = "http://".$_SERVER['HTTP_HOST']."/?apptoken=".$SESSIONID;
$TIAOURL = $WZHOST;


if( $sescc['back'] != '' ) sescc(array('back'=>''),'',$UHA);




if($ISAPP){

    htmlhead('application/json;charset=UTF-8' ,$WY);

}else{

    htmlhead(  'text/html;charset=UTF-8' ,$WY);
}



$ACTION = array(
    '1' => 'qq',
    '2' => 'weixin',
    '3' => 'weibo',
    '4' => 'alipay',
    '5' => 'weixinopen',
);

if( $KJTYPE == 1 ){
    /*qq*/
    
    $opencode = $openid = '';

    $csass=  sslget('https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id='.$CONN['kjqqid'].'&client_secret='.$CONN['kjqqkey'].'&code='.$_NGET['code'].'&redirect_uri='.$WZHOST.'login/qq.php');
    
    if( $csass ){

            preg_match_all('#access_token=(.*)&#iUs', $csass, $nrio);

            if( isset( $nrio['1']['0']) &&  $nrio['1']['0'] != ''){

               $opencode = $nrio['1']['0'];

                $useropen =  sslget('https://graph.qq.com/oauth2.0/me?access_token='.$opencode);

                preg_match_all('#openid":"(.*)"#iUs',$useropen,$OPIND);
            
                if( isset( $OPIND['1']['0']) &&  $OPIND['1']['0'] != ''){

                    $openid = $OPIND['1']['0'];

                    $fan = tongyihan( 1 ,  $openid , $TIAOURL , $IP ,'',$UHA);

                    if( $fan ){

                        if( $fan['lx'] == 1){

                            if($fan['code'] == '-1'){
                            
                                return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                            }else{
                            
                                return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                            }
                        
                        }else{


                            header("Location:".$fan['data']);
                            return htmlout('1',$WY,302);
                        }
                    }




                }else{

                    return htmlout('2:'.$useropen ,$WY);
                }
          
            }else{

                return htmlout('1:'.$csass ,$WY);

            }


    }else{

        $fan = gengduo( '-1' , '通信连接失败' ,'' , $WZHOST );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }


    } 


    $zilia =  sslget('https://graph.qq.com/user/get_user_info?access_token='.$opencode.'&openid='.$openid.'&oauth_consumer_key='.$CONN['kjqqid']);
    
    if( $zilia ){

        $woqi = (array)json_decode($zilia);

      

        if( $woqi ){
            $zuhhou =  anquanqu($woqi['nickname']);
            if($woqi['gender'] =='男')$sex = 1;
            else $sex =0;

            $_SESSION['tourist'] = array( 'lx'  => 1 ,
                                          'uid' => $openid ,
                                         'name' => $zuhhou,
                                           'tx' => $woqi['figureurl_qq_2'],
                                         'sex' =>$sex
            );

        }else {

            return htmlout('3:'.$zilia ,$WY);

        }

  
    }else{
    
    
        $fan = gengduo( '-1' , '通信连接失败2' ,'' , $WZHOST );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }

     }



}else if( $KJTYPE == 2){
    /*weixin*/

    $unopid =  $toke = $openid = '';

    $csass = sslget('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$CONN['kjwxid'].'&secret='.$CONN['kjwxkey']."&code=".$_NGET['code'].'&grant_type=authorization_code');
    global $Mem;


    if( $csass ){

        $woqi = (array)json_decode( $csass,true );
        
        if( $woqi ){

            $toke = $woqi['access_token'];
            $openid = $woqi['openid'];

            if( isset( $woqi['unionid']) &&  $woqi['unionid'] != '' )  $unopid = $woqi['unionid'];

            $fan = tongyihan( 2 ,  $openid , $TIAOURL , $IP , $unopid ,$UHA);
            
            if( $fan ){

                if( $fan['lx'] == 1){
                    $fan['data']['qunid'] = $_GET['qunid'];
                    if($fan['code'] == '-1'){
                    
                        return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                    }else{
                    
                        return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                    }

                }else{


                    if( $fan['lx'] == 2 && !isset($_NGET['gametype'])){
                        $ddd =  sslget('https://api.weixin.qq.com/sns/userinfo?access_token='.$toke.'&openid='.$openid.'&lang=zh_CN');
                        if( $ddd ){
     
                            $ddddd = (array)json_decode( $ddd , true);
                    
                            if( $ddddd ){

                                $udata = db('user') -> where(array('weixin' => $openid)) -> find();

                                if($udata){
                                 
                                    if($ddddd['nickname'] && $udata['name'] != $ddddd['nickname']){

                                        $back = db('user') -> where(array('weixin' => $openid)) -> update(array('name' => $ddddd['nickname']));

                                    }

                                    if($ddddd['headimgurl'] && ($udata['touxiang'] != $ddddd['headimgurl'] || strstr($udata['touxiang'],'http://thirdwx.qlogo.cn'))){

                                        $back = db('user') -> where(array('weixin' => $openid)) -> update(array('touxiang' => xiazaipic(touxiang($ddddd['headimgurl']))));

                                    }

                                }
                                
                            }
                        }
                        
                    }

                    if($_GET['qunid'] != '' && $_GET['qunid'] > 0){
                        header("Location:".$CONN['HTTP']."yaoqing.php?qunid=".$_GET['qunid']);
                    }else{

                        header("Location:".$fan['data'].'&gametype='.$_NGET['gametype']);
                    }
                    
                    return htmlout('1',$WY,302);
                }
            }

        }else{

            return htmlout('1:'.$csass ,$WY);
        
        }

    }else{

        $fan = gengduo( '-1' , '通信连接失败1' ,'' , $WZHOST );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }

            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }
    }

    $ddd =  sslget('https://api.weixin.qq.com/sns/userinfo?access_token='.$toke.'&openid='.$openid.'&lang=zh_CN');

    if( $ddd ){
     
        $ddddd = (array)json_decode( $ddd , true);

        if( $ddddd ){

            if($ddddd['sex'] == 1) $sex = 1;
            else $sex = 0;

            $xinminz =  anquanqu($ddddd['nickname']);
            $tx = $ddddd['headimgurl'];

            if($xinminz =='') $xinminz = 'WX'.mima($openid);


            $sescc = sescc(array(
                'kjlx'=>'2',
                'kjid' => $openid,
                'kjname'=> $xinminz,
                'kjtoux' =>$tx,
                'sex' => $sex,
                'kjuid'=>$unopid
            ),'',$UHA);


        }else{

            return htmlout('2:'.$ddd);
        }




    }else{

        $fan = gengduo( '-1' , '通信连接失败2' ,'' , $WZHOST );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }

    }

}else if( $KJTYPE == 3){

    /*weibo
    
    */

     $fanhui = array(
         
        'client_id' => $CONN['kjweiboid'],
        'client_secret' => $CONN['kjweibokey'],
        'grant_type' => 'authorization_code',
        'code' => $_NGET['code'],
        'redirect_uri' => $WZHOST.'login/weibo.php'
    );

    $huidiao = post(getarray( $fanhui ) ,'https://api.weibo.com/oauth2/access_token');

    if( $huidiao ){

         $ddddd = (array)json_decode( $huidiao ,true);


        if( $ddddd ){

             if( isset( $ddddd['access_token']) &&  isset( $ddddd['uid']) ){

                $fan =  tongyihan( 3 ,  $ddddd['uid'] , $TIAOURL , $IP , '' ,$UHA);

                if( $fan ){

                    if( $fan['lx'] == 1){

                        if($fan['code'] == '-1'){
                        
                            return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                        }else{
                        
                            return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                        }

                    }else{


                        header("Location:".$fan['data']);
                        return htmlout('1',$WY,302);
                    }
                }

                $sescc = sescc(array(

                    'kjlx'=>3,
                    'kjid' =>$ddddd['uid']
                ),'',$UHA);

                 
             }else{

                return htmlout('2:'.$ddddd);

             }

        }else{

            return htmlout('1:'.$huidiao);
        }

     }else{

        $fan = gengduo( '-1' , '通信连接失败1' ,'' , $WZHOST );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }

     }


}else if( $KJTYPE == 4){

    /*alipay*/
    $url = 'http://notify.alipay.com/trade/notify_query.do?';
    $sign = $_NGET['sign'];

    unset($_NGET['sign']);
    unset($_NGET['sign_type']);

    $zuhe = argSort($_NGET);

    $key = md5( getarray( $zuhe ).$CONN['kjzfbkey'] );

    if( $key != $sign){

        $fan = gengduo( '-1' , '签名错误' ,'' , $WZHOST);

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }
    }

    if( $_NGET['is_success'] != 'T'){

        $fan = gengduo( '-1' , '登录失败' ,'' , $TIAOURL );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }
    
    
    } 

     /* 需要保留
        user_id  唯一标识
        real_name 昵称用于
        touxiang(); 用户初始头像
     */

    $fanhui = qfopen($url ."partner=" . $CONN['kjzfbid'] . "&notify_id=".$_NGET['notify_id']);

    if( ! preg_match("/true$/i", $fanhui ) ){


        $fan = gengduo( '-1' , 'token过期重新登录' ,'' , $TIAOURL );

        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }
    
    
    
    }

    $fan = tongyihan( 4 ,  $_NGET['user_id'] , $TIAOURL , $IP ,'',$UHA);

    if( $fan ){

        if( $fan['lx'] == 1){

            if($fan['code'] == '-1'){
            
                return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

            }else{
            
                return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
            }

        }else{


            header("Location:".$fan['data']);
            return htmlout('1',$WY,302);
        }
    }

    $sescc = sescc(array(

        'kjlx'=>'4',
        'kjid' => $_NGET['user_id'],
        'kjname'=> $_NGET['real_name'],
        'kjtoux' =>'',
        'sex' => '',
        'kjuid'=>$unopid
    ),'',$UHA);
 
}else if( $KJTYPE == 5){

    /* weixinopen */




    $unopid =  $toke = $openid = '';

    $csass = sslget('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$CONN['kjkwxid'].'&secret='.$CONN['kjkwxkey']."&code=".$_NGET['code'].'&grant_type=authorization_code');

   

    if( $csass ){

        $woqi = (array)json_decode( $csass,true );

        if( $woqi ){

            $toke = $woqi['access_token'];
            $openid = $woqi['openid'];
            if( isset( $woqi['unionid'] ) &&  $woqi['unionid'] != '' )  $unopid = $woqi['unionid'];


            $fan = tongyihan( 5 ,  $openid , $TIAOURL , $IP , $unopid ,$UHA);

            if( $fan ){

                if( $fan['lx'] == 1){

                    if($fan['code'] == '-1'){
                    
                        return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                    }else{
                    
                        return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                    }

                }else{


                    header("Location:".$fan['data']);
                    return htmlout('1',$WY,302);
                }
            }



            $ddd =  sslget('https://api.weixin.qq.com/sns/userinfo?access_token='.$toke.'&openid='.$openid.'&lang=zh_CN');

            if( $ddd ){
     
                $ddddd = (array)json_decode( $ddd ,true);

                if( $ddddd ){

                    $xinminz =  anquanqu($ddddd['nickname']);
                    $tx = $ddddd['headimgurl'];
                    if($ddddd['sex'] == 1)$sex = 1;
                    else $sex = 0;
                    if($xinminz =='') $xinminz = 'WX'.mima($openid);

                    $sescc = sescc(array(

                        'kjlx'=>'5',
                        'kjid' => $openid,
                        'kjname'=> $xinminz,
                        'kjtoux' =>$tx,
                        'sex' => $sex,
                        'kjuid'=>$unopid
                    ),'',$UHA);


                }else{

                    return htmlout('2:'.$ddd ,$WY);

                }

            }else{

                $fan = gengduo( '-1' , '通信连接失败2' ,'' , $WZHOST );
                if( $fan ){

                    if( $fan['lx'] == 1){

                        if($fan['code'] == '-1'){
                        
                            return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                        }else{

                            return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                        }
                    
                    }else{

                        header("Location:".$fan['data']);
                        return htmlout('1',$WY,302);
                    }
                }
            
            
            }


        }else{

            $fan = gengduo('-1','JSON 格式错误1');
            if( $fan ){

                if( $fan['lx'] == 1){

                    if($fan['code'] == '-1'){
                    
                        return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                    }else{

                        return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                    }
                
                }else{

                    header("Location:".$fan['data']);
                    return htmlout('1',$WY,302);
                }
            }

        }


    }else{

        $fan = gengduo( '-1','JSON 格式错误2');
        if( $fan ){

            if( $fan['lx'] == 1){

                if($fan['code'] == '-1'){
                
                    return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                }else{

                    return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                }
            
            }else{

                header("Location:".$fan['data']);
                return htmlout('1',$WY,302);
            }
        }

    }


}else if( $KJTYPE == 6){        /* 第三方公众号 */

//    p($_GET['accesstoken']);die();

    if($_GET['accesstoken']){

        $sk = $CONN['kjwxkey'];
        $accesstoken = $_GET['accesstoken'];
        //  请求方式 post
        $data = [
            'sk'            => $sk,
            'accesstoken'   => $accesstoken,
        ];
        $url = 'http://www.a0081.xyz/index/wechat/getUser';
        //  返回json
        //  code    0 失败 1 成功
        //  msg     返回信息
        //  time    返回时间
        //  data    返回结果数据
        $result = post($data,$url);

        if($result){
            $woqi = (array)json_decode( $result,true );
            if($woqi){

                sescc('uid','');

                $ddddd = $woqi['data'];

                if($ddddd['sex'] == 1) $sex = 1;
                else $sex = 0;

                $xinminz =  anquanqu($ddddd['nickname']);
                $tx = $ddddd['headimgurl'];

                $openid = $ddddd['openid'];
                if($xinminz =='') $xinminz = 'WX'.mima($openid);

                $sescc = sescc(array(
                    'kjlx'=>'2',
                    'kjid' => $openid,
                    'kjname'=> $xinminz,
                    'kjtoux' =>$tx,
                    'sex' => $sex,
                    'kjuid'=>''
                ),'',$UHA);

                $unopid =  $toke = '';
                $fan = tongyihan( 2 ,  $openid , $TIAOURL , ip() , $unopid ,$UHA);

                if( $fan ){

                    if( $fan['lx'] == 1){

                        if($fan['code'] == '-1'){

                            return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                        }else{

                            return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                        }

                    }else{


                        header("Location:".$fan['data'].'&gametype='.$_GET['gametype']);
                        return htmlout('1',$WY,302);
                    }
                }

            }
        }

    }else{
        header("Location:".$CONN['ewmyuming'].'?tuid='.$_GET['tuid']);
        return htmlout('1',$WY,302);
    }
}



$sescc = sescc('','',$UHA);

$_SESSION = $sescc;

if( $sescc['kjid'] != '' && $sescc['kjlx'] != 0 ){

    

        $uindd = $sescc['kjuid'] != '' ? $sescc['kjuid'] : '' ;

        $kuaijie = kjreg($sescc['kjlx'] , $sescc['kjid']   , $sescc['kjname']  , $sescc['kjtoux'], $uindd , $sescc['sex']);

        if( $kuaijie ){

            $USER = kjcha( $sescc['kjlx']  , $sescc['kjid'] ,$uindd );
            
            if( $USER ){


                $USERID = $USER['uid'];

                regsong($USER);

                sescc(array(
                    'uid' => $USERID,
                     'ip' => $IP,
                    'kjlx'=>'0',
                    'kjid' => '',
                    'kjname'=> '',
                    'kjtoux' =>'',
                    'sex' => '',
                    'kjuid'=>''
                ),'',$UHA);



                if( isset ( $_NGET['state'] ) &&  strlen( $_NGET['state']) == 32){

                   $HASH = 'kjdenglu/'.mima( $_NGET['state']  );
                   $Mem -> s($HASH, $USER['uid'] ,20);

                }

                $USER = uid($USERID ,1);

                $fan = gengduo( 1 ,  '' ,array( 'name' => $USER['name'],
                                          'uid'  =>  $USERID,
                                          'jine' => $USER['jine'],
                                         'jifen' => $USER['jifen'],
                                      'huobi' => $USER['huobi'],
                                      'touxiang' => pichttp( $USER['touxiang'] ),
                                        'shouji' => ( $USER['shouji'] ),
                                ) , $TIAOURL  );

                if( $fan ){

                    if( $fan['lx'] == 1){

                        if($fan['code'] == '-1'){
                        
                            return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                        }else{

                            return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                        }
                    
                    }else{

//                        header("Location:".$fan['data']);
                        header("Location: /?gametype=".$_GET['gametype']);
                        return htmlout('1',$WY,302);
                    }
                }
         

         
            }else{

                $fan = gengduo( '-1' , '失败联系管理2' ,'' , WZHOST );

                if( $fan ){

                    if( $fan['lx'] == 1){

                        if($fan['code'] == '-1'){
                        
                            return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                        }else{

                            return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                        }
                    
                    }else{

                        header("Location:".$fan['data']);
                        return htmlout('1',$WY,302);
                    }
                }

            } 




        }else{

            sescc(array(

                'kjlx'=>'0',
                'kjid' => '',
                'kjname'=> '',
                'kjtoux' =>'',
                'sex' => '',
                'kjuid'=>''
            ),'',$UHA);

            $fan =  gengduo( '-1' , '插入失败联系管理' ,'' , WZHOST );
            if( $fan ){

                if( $fan['lx'] == 1){

                    if($fan['code'] == '-1'){
                    
                        return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

                    }else{

                        return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
                    }
                
                }else{

                    header("Location:".$fan['data']);
                    return htmlout('1',$WY,302);
                }
            }

        }



}else{

    $fan = gengduo( '-1' , '未知快捷登录 或者未知处理函数' ,'' , $TIAOURL );

    if( $fan ){

        if( $fan['lx'] == 1){

            if($fan['code'] == '-1'){
            
                return apptongxin($fan['data'],415,-1,$fan['msg'],$YZTOKEN,$WY);

            }else{

                return apptongxin($fan['data'],200,1,$fan['msg'],$YZTOKEN,$WY);
            }
        
        }else{

            header("Location:".$fan['data']);
            return htmlout('1',$WY,302);
        }
    }

}