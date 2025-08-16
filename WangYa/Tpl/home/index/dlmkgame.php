<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);

if($USER['level'] < 1){

    return apptongxin($SHUJU,200,-3,"不是代理",$YZTOKEN,$WY);
}

$D  = db('fanglist');

if($MOD == 'get'){
    /*获取数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        /*小于1 多条数据*/

        $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
        $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

        if($NUM < 8){
            
            $NUM = 8;
        }

        if($NUM > 100){

            $NUM = 100;
        }

        

        
        $WHERE = array('uid' => $USERID );

        $limit = listmit( $NUM , $PAG);

        $YZTOKEN = token();

        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D ->zhicha('id,gameid,fangid,atime,zqishu,qishu,off,userin')-> where($WHERE) ->limit($limit) ->order('id desc')-> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;
            $SHUJU['game'] = Game_List(1);

        }else{

            $CODE = -1;

        }




    }else{


        $YZHost = 'weiyi/'.md5('get'.$USERID);
        $cuzai = $Mem ->g($YZHost);

        if($cuzai){

            return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
        }

        $Mem ->s($YZHost,1,1);

        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D ->where(array('id' => $ID))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'查询ID错误',$YZTOKEN,$WY);
        }
     

        if(  $DATA['uid'] != $USERID ){
        
            return apptongxin($SHUJU,415,-1,'非法查询,你创建的游戏',$YZTOKEN,$WY);

        }

        $gameid = $DATA['gameid'];

        $fangid = $DATA['fangid'];

        $FJJLU = $D -> setbiao('gamejiu') ->where(array('gameid' => $gameid, 'fangid' => $fangid)) ->select();

        if(!$FJJLU){


            return apptongxin($SHUJU,415,-1,'还没有人在游戏中',$YZTOKEN,$WY);
        
        }

        $SSSS = array();

        foreach($FJJLU as $kk){


            $gggg = unserialize($kk['neirong']);


            $GAMESS = array(
                'userinfo' => $gggg['Auserinfo'],
                'tongji' =>  $gggg['Atongji'],
                'alltongji' =>  $gggg['Aalltongji'],
            );

           
            $SSSS[] = array(

                'qishu' => $kk['qishu'],
           
                'games' => $GAMESS
                
                
            
            );
        
        
        }

        $SHUJU['data'] = $SSSS;
        $SHUJU['uid'] = $USERID;

    }

}else if($MOD == 'post'){
    /*新增数据*/
    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);


    $YZHost = 'weiyi/'.md5('put'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $GAMEID = isset( $_NPOST['gid'] ) ?  $_NPOST['gid'] : '';

    if( $GAMEID == '' )return apptongxin($SHUJU,415,-1,'没有游戏型号',$YZTOKEN,$WY);
    
    $GAMEIDIP = Game_Server( $GAMEID );
  

    if( !$GAMEIDIP){

        return apptongxin($SHUJU,415,-1,'还没有这个游戏',$YZTOKEN,$WY);
    }

    $GAMESET = Game_Set($GAMEID);


    if(!$GAMESET ){

        return apptongxin($SHUJU,415,-1,'游戏配置读取失败',$YZTOKEN,$WY);

    }

    $RENSHU = $GAMESET['renshu'];

    $JISUANJIN = $GAMESET['fangka'];

    $GAEM_KUO = json_decode($GAMESET['stkuozan'],true);

    /*需要传递给游戏服务器的信息*/
     $PASSMM ='';

    $GAME  = array( 

        'fbizhong' => $GAMESET['huobi'], /*扣除币种*/
        'fpayfs' =>  $GAMESET['koufang'], /*扣除方式 0房管 1 AA只读*/
        'fpay' => $JISUANJIN,
        'xren' => $RENSHU,
        'fangmm' => $PASSMM
        
    
    );

    if($GAEM_KUO && isset($GAEM_KUO['post'])){

        $POST = $GAEM_KUO['post'];

        $GAMESET['stjushu'] = explode('#WY#',$GAMESET['stjushu']);
        $GAMESET['strenshu'] = explode('#WY#',$GAMESET['strenshu']);
        $GAMESET['stdifen'] = explode('#WY#',$GAMESET['stdifen']);
        $GAMESET['stzhfen'] = explode('#WY#',$GAMESET['stzhfen']);

        if(isset($GAEM_KUO['postkuo'])){

            $GAMESET = array_merge($GAMESET, $GAEM_KUO['postkuo'] ); 
        }

        foreach($POST as $k=> $v){

            if(!isset( $_NPOST[$k])){

                /*判断POST接收完整度*/
                return apptongxin($SHUJU,415,-1,'客户端POST参数不完整',$YZTOKEN,$WY);

            }else{

                if(!isset($GAMESET[$v])  ){

                    /*判断游戏配置完整度*/

                    return apptongxin($SHUJU,415,-1,$v.'服务端游戏配置错误',$YZTOKEN,$WY);
                   

                }else{

                    if($v == 'wanfa'){


                        $zuhe =array();

                        foreach($GAMESET[$v] as $kdd =>$kss){

                            $zuhe[] =$kdd;
                        }

                        $GAMESET[$v] =$zuhe;
                    }

                    if(!in_array($_NPOST[$k],$GAMESET[$v])){

                        return apptongxin($SHUJU,415,-1,$v.'客户端POST参数错误',$YZTOKEN,$WY);
                    }
                }
            }
        }



        if(!isset($GAEM_KUO['panrenshu'])){

            return apptongxin($SHUJU,415,-1,'服务端没有配置接收人数',$YZTOKEN,$WY);

        }

        $RENSHU = (int)$_NPOST[$GAEM_KUO['panrenshu']];


        if($RENSHU < 1){
            
            return apptongxin($SHUJU,415,-1,'客户端POST参数错误',$YZTOKEN,$WY);

        }

        $GAME['xren'] = $RENSHU;


        if( isset($GAEM_KUO['panfen'])){

            $zuhe = array();

            if(!isset($GAEM_KUO['fen'])){

                return apptongxin($SHUJU,415,-1,'服务端配置扣分规则',$YZTOKEN,$WY);
   
            }

            foreach($GAEM_KUO['panfen'] as $vv){

                $zuhe[] =$_NPOST[$vv];

            }

            $fenzhao = implode('_',$zuhe);

            if(!isset($GAEM_KUO['fen'][$fenzhao])){


                return apptongxin($SHUJU,415,-1,'服务端错误的扣分规则',$YZTOKEN,$WY);

            }

            /*
            $JISUANJIN = $GAEM_KUO['fen'][$fenzhao];

            if( $GAMESET['koufang'] == 1 ){

                
                $JISUANJIN = $JISUANJIN / $RENSHU;

                if($GAMESET['huobi'] != 0){
                  
                    $JISUANJIN = ceil($JISUANJIN);
                
                }else{
       
                    $JISUANJIN = sprintf("%.2f",$JISUANJIN);

                    if($JISUANJIN < 0.01){

                        $JISUANJIN = 0.01;
                    }
                }

            }
            */
        }


        $GAME['fpay'] = $JISUANJIN;

        /*游戏单个值*/

        if(isset($GAEM_KUO['games'])){

            foreach($GAEM_KUO['games'] as $k => $v){

                $xzz = $_NPOST[$v];


                if( substr($k,0,1) == 'A'){


                    $GAME[$k] = $GAEM_KUO[$k][$xzz];
                
                }else{

                    $GAME[$k] = $xzz;

                }
            }
        }
    }


    if($GAMESET['huobi'] == '2'){


        if( $USER['huobi'] < $JISUANJIN ){
    
            return apptongxin($SHUJU,415,-1,$CONN['huobi'].'不足无法创建房间',$YZTOKEN,$WY);
        } 
    
    
    }else if($GAMESET['huobi'] == '1'){

        if( $USER['jifen'] < $JISUANJIN ){
    
            return apptongxin($SHUJU,415,-1,$CONN['jifen'].'不足无法创建房间',$YZTOKEN,$WY);
        } 
    
    }else{

        if( $USER['jine'] < $JISUANJIN ){
    
            return apptongxin($SHUJU,415,-1,$CONN['jine'].'不足无法创建房间',$YZTOKEN,$WY);
        }

    }


    $FANGID = Game_Chuang_Dli( $Mem , $GAMEID ,$USERID ,$PASSMM ,$USER,$GAMEIDIP,$GAME);


    if($FANGID){

        $usesuju = array('y'=>'fangcha','d' => $FANGID );

        $IP = fenpeiip($FANGID,$GAMEIDIP);

        $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

        $ganeid = true;

        
        if(!$fan || $fan['code'] == '-1')  $ganeid =  false;


        if($ganeid ){

            $D = db('fanglist');

            if($GAMESET['huobi'] == '2'){

               $fans = jiaqian($USERID,0, 0, 0,-$JISUANJIN ,$GAMEID.'_'.$FANGID);
        
            }else if($GAMESET['huobi'] == '1'){

                $fans = jiaqian($USERID,0, 0, -$JISUANJIN ,0,$GAMEID.'_'.$FANGID);
            
            }else{

                $fans = jiaqian($USERID, 0 ,-$JISUANJIN , 0,0,$GAMEID.'_'.$FANGID);

            }

            if( !$fans ){
            
                return apptongxin($SHUJU,415,-1,'扣除金额失败',$YZTOKEN,$WY);
            }

            $FANGID = (int)$FANGID;

            $D -> insert( array( 
                'gameid' => $GAMEID,
                'fangid' => $FANGID,
                'uid' => $USERID,
                'atime' => time(),
                'zqishu' => $GAME['allqishu'],
                'qishu' => 1,
                'off' => 0,
                'mhash' =>  md5($GAMEID.'_'.$FANGID.'_'.date('ym')),
            ));


            $FANGID = str_pad($FANGID, 6, "0", STR_PAD_LEFT);


            return  apptongxin($FANGID,200,2,'创建房间成功',$YZTOKEN,$WY);

        }else{
        
        
            return apptongxin($SHUJU,415,-1,'和服务器通信失败',$YZTOKEN,$WY);
        }






    }else{


        return apptongxin($SHUJU,415,-1,'创建房间失败',$YZTOKEN,$WY);
    
    }


   


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


    $YZHost = 'weiyi/'.md5('put'.$USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);


    $D ->setbiao('gameserver');

    $data = $D ->where(array('off' => 1))-> select();

    if($data){

        foreach($data as $GAMESET){

            $post = array();
            $postnem =array();

            if($GAMESET['stkuozan'] != ''){

                $GAEM_KUO = json_decode($GAMESET['stkuozan'],true);
                $GAMESET['stjushu'] = explode('#WY#',$GAMESET['stjushu']);
                $GAMESET['strenshu'] = explode('#WY#',$GAMESET['strenshu']);
                $GAMESET['stdifen'] = explode('#WY#',$GAMESET['stdifen']);
                $GAMESET['stzhfen'] = explode('#WY#',$GAMESET['stzhfen']);

          

                if(isset($GAEM_KUO['postkuo'])){

                    $GAMESET = array_merge($GAMESET, $GAEM_KUO['postkuo'] ); 

                }

                

                if(isset($GAEM_KUO['post'])){

                   

                    foreach($GAEM_KUO['post'] as $k=>$v){

                        $post[$k] = $GAMESET[$v];

                    }
                
                    $postnem = $GAEM_KUO['name'];
                }

            
            
            
            }

            $SHUJU['data'][$GAMESET['biaoshi']] = array(
                
                'name' => $GAMESET['name'],
                'huobi' => $GAMESET['huobi'],
                'fangka' => $GAMESET['fangka'],
                'post' => $post,
                'postname'=> $postnem

            );
        
        
        }

        $SHUJU['bizhong'] = array($CONN['jine'],$CONN['jifen'],$CONN['huobi']);

    }else{

        $STAT = 415;
        $CODE = -1;
    
    
    }



}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);