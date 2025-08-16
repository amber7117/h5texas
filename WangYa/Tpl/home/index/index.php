<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
 /*index start*/


$SHUJU['azver'] = $CONN['azver'];
$SHUJU['pgver'] = $CONN['pgver'];
$SHUJU['azdown'] = $CONN['azgengxin'];
$SHUJU['pgdown'] = $CONN['pggengxin'];

$USERID = $USERID < 1?$_GET['uid']:$USERID;

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


/*读取游戏创建设置

Game_List($flie = 1, $qx = '' )

游戏列表 1 只要标识名字  2 游戏INFO

Game_Info( $flie , 1);
Game_Set($flie , 1);
Game_Server( $flie ,1);
读取数据其他信息*/


/*根据游戏id读取游戏服务器ip*/

$HASH = 'gameuid/'.$USERID;
global $Mem;


if( $MOD == 'get' ){
    /*get start*/

    $USER = uid( $USERID,1);

    if($USER['name'] == ''){
        db("user") ->where(array(  'uid' =>  $USERID )) ->update( array( 'name' => '游客'.$USERID ));
        $USER = uid( $USERID,1);

    }

    if($USER && empty($USER['tuid']) && isset( $_SESSION['tuid'] )){

        tj_daili($USER['uid'],$_SESSION['tuid']);
     }

    $SHUJU = array( 'name' => $USER['name'],
                     'uid' => $USER['uid'],
                'touxiang' => pichttp( $USER['touxiang']),
                 'xingbie' => $USER['xingbie'],
                   'level' => $USER['level'],
                    'jine' => $USER['jine'],
                   'jifen' => $USER['jifen'],
                   'huobi' => $USER['huobi'],
                   'yongjin' => $USER['yongjin'],
                   'allhuobi' => $USER['huobi'] + $USER['yongjin'],
        'dan' => $USER['dan'],
        'ji_num' => $USER['ji_num'],
        'fodder' => $USER['fodder'],
        'zhanghao' => $USER['zhanghao'],
        'realname' => $USER['realname'],
    );


    //德州
    $SHUJU['room_id'] = isset($sescc['roomid'])?$sescc['roomid']:0;
    sescc('roomid',0);


    //apk红包

    // $SHUJU['apkhbniurule'] = pichttp($CONN['apkhbniurule']); 
    // $SHUJU['apkhbxtrule'] = pichttp($CONN['apkhbxtrule']); 
    // $SHUJU['apkhbroomrule'] = pichttp($CONN['apkhbroomrule']);

    // $SHUJU['apkhblogo'] = pichttp($CONN['apkhblogo']); 

    // $SHUJU['apkhbrule'] = pichttp($CONN['apkhbrule']); 
    // $SHUJU['apkhbgonggao'] = pichttp($CONN['apkhbgonggao']); 
    // $SHUJU['apkhbgundong'] = $CONN['apkhbdailims'];
    // $SHUJU['apkgivesxf'] = round($CONN['apkgivesxf']*100,2);

    // $SHUJU['apkhkefutype'] = $CONN['apkhkefutype'];
    // $SHUJU['apkhbKeFuurl'] = $CONN['apkhbKeFuurl'];
    // $SHUJU['apkhbCZKeFuurl'] = $CONN['apkhbCZKeFuurl'];
    // $SHUJU['apkhbHDKeFuurl'] = $CONN['apkhbHDKeFuurl'];
    // $SHUJU['apkhbKeFu'] = pichttp($CONN['apkhbKeFu']);
    // $SHUJU['apkhbCZKeFu'] = pichttp($CONN['apkhbCZKeFu']);
    // $SHUJU['apkhbHDKeFu'] = pichttp($CONN['apkhbHDKeFu']);

    // $SHUJU['apkhbtgrule'] =  pichttp($CONN['apkhbtgrule']);
    // $SHUJU['apkhberweima'] = pichttp("/myerweima.php?gametype=apkhongbao&type=app&apptoken=".$SESSIONID);
    // $SHUJU['apkhberweima_dx'] = $CONN['apkhberweima_dx'];
    // $SHUJU['apkhberweima_zy'] = $CONN['apkhberweima_zy'];
    // $SHUJU['apkhberweima_sx'] = $CONN['apkhberweima_sx'];

    // $SHUJU['apkhbminmailei'] = $CONN['apkhbminmailei']; 
    // $SHUJU['apkhbmaxmailei'] = $CONN['apkhbmaxmailei']; 

    // $SHUJU['apkflmaxmailei'] = $CONN['apkflmaxmailei']; 
    // $SHUJU['apkflminmailei'] = $CONN['apkflminmailei'];

    // $hbfbbili = explode('_',$CONN['apkhbfbyongjinbili']);
    // $SHUJU['apkhbfbbili'] = array();
    // foreach($hbfbbili as $v){
        
    //     $SHUJU['apkhbfbbili'][] = ($v*100).'%';
        
    // }
    // $SHUJU['apkhbfbgongshi'] = '30*10*10*'.($hbfbbili[0]*100).'%+30^2*10*10*'.($hbfbbili[1]*100).'%+30^3*10*10*'.($hbfbbili[2]*100).'%='.((30*10*10*$hbfbbili[0])+(30*30*10*10*$hbfbbili[1])+(30*30*30*10*10*$hbfbbili[2]));

    // $hbqbbili = explode('_',$CONN['apkhbqbyongjinbili']);
    // $SHUJU['apkhbqbbili'] = array();
    // foreach($hbqbbili as $v){
        
    //     $SHUJU['apkhbqbbili'][] = ($v*100).'%';
        
    // }
    // $SHUJU['apkhbqbgongshi'] = '30*10*10*'.($hbqbbili[0]*100).'%+30^2*10*10*'.($hbqbbili[1]*100).'%+30^3*10*10*'.($hbqbbili[2]*100).'%='.((30*10*10*$hbqbbili[0])+(30*30*10*10*$hbqbbili[1])+(30*30*30*10*10*$hbqbbili[2]));

    // $gamedetail = db('gameserver') -> where(array('biaoshi' => 'apkhongbao')) -> find();
    // $kuozan = json_decode($gamedetail['stkuozan'],true);
    // $SHUJU['apkgame'] = array();
    // if($kuozan && isset($kuozan['fen'])){
    //     foreach($kuozan['fen'] as $k=>$v){
    //         $arr = explode('_',$k);
    //         if(!$SHUJU['apkgame'][$arr[1]]){
    //             $SHUJU['apkgame'][$arr[1]] = array();
    //         }
    //         $SHUJU['apkgame'][$arr[1]][] = array(
    //             'time' => $arr[0],
    //             'jinbi' => $v
    //         );
    //     }
    // }


    //计算转盘剩余次数
    $SHUJU['remaincishu'] = $USER['turnnum'];
        
    $SHUJU['apkhbfbgetnum'] = $CONN['apkhbfbgetnum'];
    $SHUJU['apkhbqbgetnum'] = $CONN['apkhbqbgetnum'];
    $SHUJU['apkhbyjgetnum'] = $CONN['apkhbyjgetnum'];

    $zhuanpan = logac("zhuanpan");
    $SHUJU['zhuanpan'] =  array();
    if($zhuanpan){

        foreach($zhuanpan as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $SHUJU['zhuanpan'][] = array(
                    'name' => isset($YANS[2])?$YANS[2]:'',
                    'bizhong'=> $YANS[0],
                    'jine' => $YANS[1]
                );
            }
        }
    }
    $SHUJU['zhuanpanxiao'] = array(
        'bizhong'=>$CONN['zhuanpanbi'],
        'xuyao'=>$CONN['zhuanpanxiao']
    );

    //视频龙虎斗
    // $SHUJU['lhgundong'] = $CONN['lhdailims'];
    // $SHUJU['lhgonggaotu'] = pichttp( $CONN['lhgonggaotu']);
    // $SHUJU['lhzaixian'] = $CONN['lhzaixian'];

    // $tbili = explode('_',$CONN['lhyongjinbili']);

    // $dailibili = array();
    // foreach($tbili as $v){
    //     $dailibili[] = round($v*100,2);
    // }
    // $SHUJU['lhyongjinget'] = $dailibili;
   
    // $SHUJU['lhgongshi'] = '30*10*10*'.($tbili[0]*100).'%+30^2*10*10*'.($tbili[1]*100).'%+30^3*10*10*'.($tbili[2]*100).'%='.((30*10*10*$tbili[0])+(30*30*10*10*$tbili[1])+(30*30*30*10*10*$tbili[2]));


    /*牛牛充值金额（index.index）*/
    // $chogzhi = isset($CONN['niu_chongzhi'])?$CONN['niu_chongzhi']:'10,20,50,100,200,500,1000,2000,5000';

    // $c_arr = explode(',',$chogzhi);
    // if(!is_array($c_arr)){
    //     $c_arr = [10,20,50,100,200,500,1000,2000,5000];
    // }elseif(count($c_arr) < 1){
    //     $c_arr = [10,20,50,100,200,500,1000,2000,5000];
    // }
    // $SHUJU['chongzhi'] = $c_arr;


    // $SHUJU['paybilijb'] = $CONN['paybilijb'];

    // $SHUJU['tuid'] = $USER['tuid'];


    // $SHUJU['yjtxlimit'] = $CONN['yjtxlimit']; 
    // $SHUJU['jbtxlimit'] = $CONN['jbtxlimit'];
    // $SHUJU['maxtxjine'] = $CONN['maxtxjine']; 

    // $SHUJU['txsxf'] = ((float)$CONN['txsxf'])*100;

    // $SHUJU['mrtx'] = $CONN['mrtx'];

    // $hassss = 'tixian/'.$USERID;
    // $data = $Mem ->g($hassss);
    // $SHUJU['havetxnum'] = $data['num']?$data['num']:0;

    // $SHUJU['tuiji'] = $CONN['tuiji'];

    // $SHUJU['QQ'] = $CONN['QQ'];

    // $SHUJU['regsongjb'] = $CONN['regsongjb'];
    // $SHUJU['isgetnew'] = $USER['isgetnew'];
  
    // $SHUJU['hallbili'] = array();
    // $SHUJU['hallqiansanbili'] = array();
    // for($i = 0;$i < $CONN['tuiji'];$i++){

    //     $SHUJU['hallbili'][] = $CONN['niu_ji'.($i + 1)].'%';
    //     $SHUJU['hallqiansanbili'][] = '可得佣金：10(下注金额)*'.$CONN['niu_ji'.($i + 1)].'% = '.(10*$CONN['niu_ji'.($i + 1)]/100);

    // }

    // $SHUJU['hallgongshi'] = '30*10*10*'.$CONN['niu_ji1'].'%+30^2*10*10*'.$CONN['niu_ji2'].'%+30^3*10*10*'.$CONN['niu_ji3'].'%='.((30*$CONN['niu_ji1'])+(30*30*$CONN['niu_ji2'])+(30*30*30*$CONN['niu_ji3']));
    
    // $GongGaoData = db('gonggao') -> order('zuixian desc') -> where(array('gametype' => 'dating')) -> select();
    // $SHUJU['xtgonggao'] = array();
    // $SHUJU['xtmessage'] = array();
    // foreach($GongGaoData as $k=>$v){
    //     $GongGaoData[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
    //     $GongGaoData[$k]['img'] = pichttp($v['img']);
    //     $GongGaoData[$k]['iszan'] = 0;
    //     if((int)$v['type'] == 0){

    //         $UIDDATA = explode(',',$v['zanuid']);

    //         if(in_array($USERID,$UIDDATA)){
    //             $GongGaoData[$k]['iszan'] = 1;
    //         }

    //         $SHUJU['xtgonggao'][] = $GongGaoData[$k];
    //     }elseif((int)$v['type'] == 1){
    //         $SHUJU['xtmessage'][] = $GongGaoData[$k];
    //     }
    // }
    

    $QQIDANDAO = $Mem  -> g("qiandao/".$USERID);
    if(!$QQIDANDAO ){

        $QQIDANDAO  = array(
            'qday' => 0,
            'qdtime' => 0,
            'zatime' => 0,
            'zcishu' => 0,
            

        );

        $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);


    }
    $jinday =  mktime(0,0,0,date("m"),date("d"),date("Y"));
    $yinday =  mktime(0,0,0,date("m",$QQIDANDAO['zatime']),date("d",$QQIDANDAO['zatime']),date("Y",$QQIDANDAO['zatime'])); 
    if($yinday != $jinday){
        $QQIDANDAO['zcishu']  = 0;
        $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);
    }

    if($QQIDANDAO['qday'] >= 7){

        if( date( 'Y-m-d', $QQIDANDAO['qdtime'] )  != date( 'Y-m-d') ) {

            $QQIDANDAO['qday'] = 0;
        }


    }

    $SHUJU['qday'] = $QQIDANDAO['qday'];

    $zhuanpan = logac("qianjiangli");
    $SHUJU['qianjiangli'] =  array();

    if($zhuanpan){

        foreach($zhuanpan as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $SHUJU['qianjiangli'][] = array(
                    'name' => isset($YANS[2])?$YANS[2]:'',
                    'bizhong'=> $YANS[0],
                    'jine' => $YANS[1]
                );
            }
        }
    }

    $SHUJU['paytype'] =  array();

    $data = db('pay') -> where(array('xianshi' => 1,'off' => 1)) -> select();

    if($data){
        foreach($data as $k=>$v){
            $SHUJU['paytype'][] = array('id'=>$v['id'],'type'=>$v['paixu'],'beizhu' => $v['name']);
        }

    }

    $tticheng = logac('ticheng');
    $dailiticheng = array();
    foreach($tticheng as $v){
        $dailiticheng[] = explode('_',$v);
    }
    $SHUJU['ticheng'] = $dailiticheng;

    


    if($USER['off'] < 1){

        $fan = $SECC -> d('session/'.$UHA);

          return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
    
    
    }


    $SHUJU['apptoken'] = $SESSIONID;

    if($USER['tuid'] > 0){

        $DDLI = uid( $USER['tuid']);

        if($DDLI){
            $SHUJU['KeFu'] = $DDLI['zhiye'];
            if($DDLI['zhiye']){
                $str = $DDLI['zhiye'];
                $str = str_replace('<color=#7d0600>','',$str);
                $str = str_replace('<color=#0d8002>','',$str);
                $str = str_replace('</color>','',$str);
                $str = str_replace(array("\r\n", "\r", "\n"), "  ", $str);
               
                $SHUJU['gundong'] = $str;
            }else{
                $SHUJU['gundong'] = $CONN['dailims'];
            }

        }else{
        
            $SHUJU['gundong'] = $CONN['dailims'];

            $SHUJU['KeFu'] = $CONN['KeFu'];
            
        }

    }else{

        $SHUJU['KeFu'] = $CONN['KeFu'];

        $SHUJU['gundong'] = $CONN['dailims'];
    }

    $SHUJU['KeFu'] = pichttp($CONN['KeFu']);

    
    //录入代理 自己获得
    $SHUJU['lurutuifk'] = $CONN['lurutuifk'];
    $SHUJU['lurutuijb'] =  $CONN['lurutuijb'];
    //录入代理 上级获得
    $SHUJU['shangjlfk'] = $CONN['shangjlfk'];
    $SHUJU['shangjljb'] = $CONN['shangjljb'];

    $SHUJU['fenxiang'] = $CONN['afenxiang'];
    $SHUJU['dating'] =  $CONN['agame'];
    $SHUJU['azver'] = $CONN['azver'];
    $SHUJU['pgver'] = $CONN['pgver'];
    $SHUJU['azdown'] = $CONN['azgengxin'];
    $SHUJU['pgdown'] = $CONN['pggengxin'];
    
    

    /*财富鸡*/
    $market_list = db('market') -> where(['ma_time <='=>time()]) -> limit("0,7") -> order('ma_time desc') -> select();
    if(!$market_list) $market_list = [];
    foreach ($market_list as $k => $v ){
        $market_list[$k]['ma_time'] = date('m-d',$v['ma_time']);
    }
    $SHUJU['market_list'] = array_reverse($market_list); /*市场价格*/

    $SHUJU['cfj_rule'] = pichttp($CONN['cfj_rule']); /*财富鸡规则图*/





    $SHUJU['gonggao'] = $CONN['gonggao'];
    
    $SHUJU['shopstore'] = array();
    $shopstore = logac("shopstore");
    if($shopstore){

        foreach($shopstore as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);
                $SHUJU['shopstore'][$YANS['0']][] = array(
                    'name' => $YANS['3'],
                    'jine' =>$YANS['1'],
                    'suode' =>$YANS['2']
                );
            }
        }
    }

    $SHUJU['gameinfo'] = array();
    $gameinfo = logac('gameinfo');
    if($gameinfo){
        $i = 1;
        foreach($gameinfo as $shuju){

            if($shuju){
                $YANS = explode("_",$shuju);

                $SHUJU['gameinfo'][] = array(
                    'changid' => $i,
                    'moneylabel' =>$YANS['1'],
                    'peoplenum' =>$YANS['2'],
                    'bizhong' =>$YANS['0'],
                );

                $i++;
            }

        }

    }


    




    if(isset($_NPOST['duzil'])){

        $D = db('msgbox');

        $SHUJU['msgbox'] =  $D ->where( array( 'uid' => $USERID ,'yuedu' => 0,'off'=>2) )-> total();

        $SHUJU['jinfenx'] = $CONN['jifen'];

        $SHUJU['gengduo'] = array(
            
            'cz' => $CONN['jifenbili'],
            'rgs' => $CONN['resgong'],
            'tgs' => $CONN['tuiregs'],
            'mrs' => $CONN['meirisong'],
            'trs' => $CONN['tuisong'],
            'tgp' => $CONN['tuipay']
        );

        $SHUJU['tuid'] = $USER['tuid'];
        $SHUJU['level'] = $USER['level'];
		$SHUJU['zhanghao'] = $USER['zhanghao'];



        $SHUJU['pay'] = xitongpay(-2);



    
        return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);
    }

    

    $SHUJU['gamet']   = ''; //当前用户token
    $SHUJU['gamegid'] = ''; //用户正在的游戏
    $SHUJU['gamefid'] = ''; //游戏房间id
    $SHUJU['isfguan'] = 0 ; // 是不是房管
    $SHUJU['fangmm']  = ''; // 房间密码

    $USERONLINE = $Mem -> g( $HASH );

    if( $USERONLINE )
    {

        $SHUJU['gamet']   = isset($USERONLINE['t'])? $USERONLINE['t'] : ''; //当前用户token
        $SHUJU['gamegid'] = isset($USERONLINE['gid'])? $USERONLINE['gid'] :''; //用户正在的游戏
        $SHUJU['gamefid'] = isset($USERONLINE['fangid'])? $USERONLINE['fangid'] :''; //游戏房间id
        $SHUJU['isfguan'] = isset($USERONLINE['isfguan'])? $USERONLINE['isfguan'] :0 ; // 是不是房管
        $SHUJU['fangmm']  = isset($USERONLINE['fangmm'])? $USERONLINE['fangmm'] :''; // 房间密码

        $yuanFANGID = $USERONLINE['fangid'];
      
        $GAMEID = $Mem -> g( 'fangid/'.((int)$yuanFANGID));

        if( $GAMEID ){

        
            $IP = fenpeiip($yuanFANGID,Game_Server($GAMEID));

            if(!$IP && $SHUJU['gamegid'] != "pipei"){
                
                return apptongxin($SHUJU,200,-1,"没有服务器通信地址,请联系管理",$YZTOKEN,$WY);

            }
        }
      

        $ganeid = true;


        $usesuju = array('y'=>'fangcha','d' => $yuanFANGID );


        $fan = httpudp( $usesuju,$IP['ip'],  $IP['port'] );

                
        if(!$fan || $fan['code'] == '-1')  $ganeid =  false;



        if( !$ganeid )
        {  
            //$Mem -> s( $HASH , array(  ) );
            $SHUJU['gamet']   = '';
            $SHUJU['gamegid'] = '';
            $SHUJU['gamefid'] = '';
            $SHUJU['isfguan'] = 0;
            $SHUJU['fangmm']  = '';

        }

    }

    $YZTOKEN = token();

    sescc('token',$YZTOKEN,$UHA);


    /*get end*/

}else if($MOD == 'post'){

    /*post start*/

    $YZHost = 'weiyi/'.md5('post'.$USERID);

    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

//        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $USER = uid( $USERID,1 );

    $USERONLINE = $Mem -> g( $HASH );

    $GAMEID = isset( $_NPOST['gid'] ) ?  $_NPOST['gid'] : '';
    $PASSMM = isset( $_NPOST['fangmm'] ) ?  $_NPOST['fangmm'] : '';
    $TOKIEN = isset( $_NPOST['ttoken'] ) ?  $_NPOST['ttoken'] : '';

    if( date('Y-m-d',$USER['qdtime']) != date('Y-m-d') && $CONN['meirisong'] > 0 ){
        
        $J = db('user');

        $fan = $J -> where(array('uid' =>$USERID )) -> update( array('qdtime' => time() ));

        if( $fan ){

            //uid($USERID,1);

            //jiaqian($USERID,6,0,$CONN['meirisong']);

            /*
                return apptongxin($SHUJU,415,-1,'获得每日赠送房卡 +'.$CONN['meirisong'],$YZTOKEN,$WY);
            */
        }
    }

    if( $GAMEID == '' )return apptongxin($SHUJU,415,-1,'没有游戏型号',$YZTOKEN,$WY);


    if(strpos( $GAMEID, "J" ) || strpos( $GAMEID, "B" ) ){
    
    
        return apptongxin($SHUJU,415,-1,'不能创建的游戏',$YZTOKEN,$WY);
    }

    $GAMEIDIP = Game_Server( $GAMEID );
  

    if( !$GAMEIDIP){

        return apptongxin($SHUJU,415,-1,'还没有这个游戏',$YZTOKEN,$WY);
    }

    if( $USERONLINE ){

        $yuanGAMEID = isset($USERONLINE['gid'])? $USERONLINE['gid'] :''; //用户正在的游戏
        $yuanFANGID = isset($USERONLINE['fangid'])? $USERONLINE['fangid'] :''; //游戏房间id

        if( $yuanGAMEID != '' &&  $yuanFANGID != '' ){


            $GAMEIDIP = Game_Server( $yuanGAMEID );


            $IP = fenpeiip($yuanFANGID,$GAMEIDIP);

            if(!$IP){

                return apptongxin($SHUJU,415,-1,'没有服务器通信地址,请联系管理',$YZTOKEN,$WY);
            }

            $ganeid = true;

            $usesuju = array('y'=>'fangcha','d' => $yuanFANGID );


            $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

            
            if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

            if( $ganeid )
            {

                $Mem ->s($YZHost,1,1);

                return apptongxin(ingame( $Mem ,$HASH, $yuanGAMEID,$yuanFANGID,$USERID ,$GAMEIDIP ) ,200,1,'强行进入游戏',$YZTOKEN,$WY);  

            }
        }
    }


    if(! $USERONLINE || ! is_array( $USERONLINE ) ) $USERONLINE = array();

     $Mem ->s($YZHost,1,1);


    $GAMESET = Game_Set($GAMEID);
    $GAMEIDIP = Game_Server( $GAMEID );


    if(!$GAMESET ){

        return apptongxin($SHUJU,415,-1,'游戏配置读取失败',$YZTOKEN,$WY);

    }

    $RENSHU = $GAMESET['renshu'];

    $JISUANJIN = $GAMESET['fangka'];

    $GAEM_KUO = json_decode($GAMESET['stkuozan'],true);

    /*需要传递给游戏服务器的信息*/

    $GAME  = array( 

        'fbizhong' => $GAMESET['huobi'], /*扣除币种*/
        'fpayfs' =>  $GAMESET['koufang'], /*扣除方式 0房管 1 AA只读*/
        'fpay' => $JISUANJIN,
        'xren' => $RENSHU,
        'fangmm' => $PASSMM,
    );

    if($GAMEID == 'apkhongbao'){
        $fabaojine = explode('_',$_NPOST['fabaojine']);

        if(count($fabaojine) != 2){
            return apptongxin($SHUJU,415,-1,'发包金额设置错误',$YZTOKEN,$WY);
        }
        $fabaojine1 = array();
        foreach($fabaojine as $v){
            $fabaojine1[] = (int)$v;
        }
        sort($fabaojine1);
        $GAME['Afabaojine'] = $fabaojine1;

        $_NPOST['fuli'] = array();
        if($_NPOST['shunzi']){
            $_NPOST['fuli']['shunzi'] = $_NPOST['shunzi'];
        }
        if($_NPOST['baozi']){
            $_NPOST['fuli']['baozi'] = $_NPOST['baozi'];
        }

        if((int)$_NPOST['roomtype'] == 2){

            if(!isset($_NPOST['fuli']) || !$_NPOST['fuli']) return apptongxin($SHUJU,415,-1,'未设置福利',$YZTOKEN,$WY);

        }

        $fulijine = 0;
        foreach($_NPOST['fuli'] as $key=>$value){
            $fulijine += $value;
            if((float)$value > 0){
                $GAME['Afuli'][$key] = number_format($value,2);
            }
        }

        if((int)$_NPOST['roomtype'] == 2 || $_NPOST['fuli']){
            if($fulijine <= 0){
                return apptongxin($SHUJU,415,-1,'福利金额设置错误',$YZTOKEN,$WY);
            }
        }
    }

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

//            return apptongxin([$POST,$_NPOST,$GAMESET],$STAT,$CODE,$MSG,$YZTOKEN,$WY);

            if(!isset( $_NPOST[$k])){

                /*判断POST接收完整度*/
                return apptongxin($SHUJU,415,-1,'客户端POST参数不完整'.$k,$YZTOKEN,$WY);

            }else{

//                return apptongxin([$POST,$GAMESET],$STAT,$CODE,$MSG,$YZTOKEN,$WY);

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

//                    return apptongxin([$_NPOST,$GAMESET,$_NPOST[$k],$GAMESET[$v]],$STAT,$CODE,$MSG,$YZTOKEN,$WY);

                    if(!in_array($_NPOST[$k],$GAMESET[$v])){

//                        return apptongxin([$_NPOST,$GAMESET],415,-2,$v.' '.$k.'客户端POST参数错误'.$_NPOST[$k],$YZTOKEN,$WY);
                        // return apptongxin($SHUJU,415,-1,$v.'客户端POST参数错误'.$_NPOST[$k],$YZTOKEN,$WY);

                    }
                }
            }
        }



        if(!isset($GAEM_KUO['panrenshu'])){

            return apptongxin($SHUJU,415,-1,'服务端没有配置接收人数',$YZTOKEN,$WY);

        }


        //前端人数
        $RENSHU = (int)$_NPOST[$GAEM_KUO['panrenshu']];

        
        if($RENSHU < 1){
            
            return apptongxin($SHUJU,415,-1,'客户端POST参数错误'.$GAEM_KUO['panrenshu'],$YZTOKEN,$WY);

        }

        $GAME['xren'] = $RENSHU;


        if( isset($GAEM_KUO['panfen'])){

            $zuhe = array();

            if(!isset($GAEM_KUO['fen'])){

                // return apptongxin($SHUJU,415,-1,'服务端配置扣分规则',$YZTOKEN,$WY);
   
            }

            foreach($GAEM_KUO['panfen'] as $vv){

                $zuhe[] =$_NPOST[$vv];

            }

            $fenzhao = implode('_',$zuhe);

            if(!isset($GAEM_KUO['fen'][$fenzhao])){


                // return apptongxin($SHUJU,415,-1,'服务端错误的扣分规则',$YZTOKEN,$WY);

            }


            $JISUANJIN = $GAEM_KUO['fen'][$fenzhao];

            if( $GAMESET['koufang'] == 1 ){

                
                $JISUANJIN = $JISUANJIN;// / $RENSHU;

                if($GAMESET['huobi'] != 0){
                  
                    $JISUANJIN = ceil($JISUANJIN);
                
                }else{
       
                    $JISUANJIN = sprintf("%.2f",$JISUANJIN);

                    if($JISUANJIN < 0.01){

                        $JISUANJIN = 0.01;
                    }
                }

            }
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

    if($GAME['gameid'] == 'animalJ'){

        $mofadata = logac('mofabei_'.$GAME['changci'],1);

        $mofaarr = explode('_',$mofadata[0]);
        $minmofa = $mofaarr[0];
    
        $usermofa = explode('_',$USER['chouma']);
    
        if(!in_array($minmofa,$usermofa)){
    
            return apptongxin($SHUJU,415,-1,'尚未解锁'.$minmofa.'倍魔法',$YZTOKEN,$WY);
    
        }
    }
    
    if($GAMESET['huobi'] == '2' && $GAMEID == 'apkhongbao'){


        if( $USER['huobi'] < $JISUANJIN ){
    
            return apptongxin($SHUJU,415,-1,$CONN['huobi'].'不足无法创建房间',$YZTOKEN,$WY);
        } 
    
    
    }
    //else if($GAMESET['huobi'] == '1'){

    //     if( $USER['jifen'] < $JISUANJIN ){
    
    //         return apptongxin($SHUJU,415,-1,$CONN['jifen'].'不足无法创建房间',$YZTOKEN,$WY);
    //     } 
    
    // }else{

    //     if( $USER['jine'] < $JISUANJIN ){
    
    //         return apptongxin($SHUJU,415,-1,$CONN['jine'].'不足无法创建房间',$YZTOKEN,$WY);
    //     }
    
    
    // }

 
    $D = db('fanglist');




    $LOIN = array();

    if( $GAMEIDIP )
    {	

		

        $FANGID = Game_Chuang( $Mem , $GAMEID ,$USERID ,$PASSMM ,$USER,$GAMEIDIP,$GAME);

        if($FANGID){

            $LOIN = ingame( $Mem ,$HASH, $GAMEID,$FANGID,$USERID ,$GAMEIDIP);

            $FANGID = (int)$FANGID;

            if($GAMEID != "pipei" && ! strpos( $GAMEID, "J" ) && ! strpos( $GAMEID, "B" )  && ! strpos( $GAMEID, "K" )  ){

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
            }


            return apptongxin( $LOIN  ,'200', '1' , '强行进入游戏' ,$YZTOKEN ,$WY);

        }else{

            return apptongxin( $SHUJU ,'415', '-1' , '连接服务器失败' ,$YZTOKEN ,$WY);
        }


    }else{

        return apptongxin($SHUJU  ,'415', '-1' , '其他游戏开发中' ,$YZTOKEN ,$WY);
    
    }



    /*post end*/

}else if($MOD == 'put'){

    /*put start*/

    $YZHost = 'weiyi/'.md5('put'.$USERID);

    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin($SHUJU,415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $GAMEID = isset( $_NPOST['gid'] ) ?  $_NPOST['gid'] : '';

    /*游戏房间号*/
    $FANGID = isset( $_NPOST['fid'] ) ?  $_NPOST['fid'] : '';
    
    /*房间密码*/
    $PASSMM = isset( $_NPOST['fangmm'] ) ?  $_NPOST['fangmm'] : '';

    /*房间类型*/
    $type = isset( $_NPOST['room_type'] ) ?  (int)$_NPOST['room_type'] : 0;


    /*德州*/
    if( $GAMEID == 'dezhouJ' ){

        list( $data,$state,$code,$msg ) = joinGame( $Mem,$HASH,$GAMEID,$FANGID,$USERID,$type );

        return apptongxin($data ,$state,$code,$msg,$YZTOKEN,$WY);

    }elseif ( $GAMEID == 'sghuachuanK' ){

        //三公撑船
        list( $data,$state,$code,$msg ) = joinGame( $Mem,$HASH,$GAMEID,$FANGID,$USERID );

        return apptongxin($data ,$state,$code,$msg,$YZTOKEN,$WY);
    }


    if( $GAMEID != 'dezhouJ' ){
        if( $FANGID == '' ||  $FANGID == '' ){

            return apptongxin($SHUJU ,415,-1,'没有游戏型号或者房间号',$YZTOKEN,$WY);
        }

        $GAMEID = $Mem -> g( 'fangid/'.((int)$FANGID));

        if(!$GAMEID){

            return apptongxin($SHUJU ,415,-1,'房间号不存在',$YZTOKEN,$WY);
        }
    }


    $GAMEIDIP = Game_Server( $GAMEID );

    if( !$GAMEIDIP ){

        return apptongxin($SHUJU ,415,-1,'还没有这个游戏',$YZTOKEN,$WY);

    }
        
 

    $ganeid = true;


    $usesuju = array('y'=>'fangcha','d' => (int)$FANGID );

    $IP = fenpeiip($FANGID,$GAMEIDIP);

    if(!$IP){

        return apptongxin($SHUJU ,415,-1,'没有服务器通信地址',$YZTOKEN,$WY);
    }

    $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

    if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

    if( !$ganeid )
    {  
        $Mem -> s( $HASH , array(  ) );
        return apptongxin($SHUJU ,415,-1,'房间号过期了',$YZTOKEN,$WY);
    }

    $USERONLINE = $Mem -> g( $HASH );

    if( $USERONLINE ){

        $yuanGAMEID = isset($USERONLINE['gid'])? $USERONLINE['gid'] :''; //用户正在的游戏
        $yuanFANGID = isset($USERONLINE['fangid'])? $USERONLINE['fangid'] :''; //游戏房间id

        if( $yuanGAMEID != '' &&  $yuanFANGID != '' ){

            $GAMEIDIPs = Game_Server( $yuanGAMEID );

            $IP = fenpeiip($yuanFANGID, $GAMEIDIPs);

            if(!$IP){

                return apptongxin($SHUJU ,415,-1,'没有服务器通信地址',$YZTOKEN,$WY);
            }
                

            $usesuju = array('y'=>'fangcha','d' => $yuanFANGID );

            $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

            if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

            if( $ganeid )
            {
                return apptongxin(ingame( $Mem ,$HASH, $yuanGAMEID,$yuanFANGID,$USERID, $GAMEIDIP ) ,200,1,'强行进入游戏',$YZTOKEN,$WY);

            }else{
            
                $Mem -> s( $HASH ,array() );

                

                $ganeid =  true;
               
                $usesuju = array('y'=>'fangcha','d' => $FANGID );
                $IP = fenpeiip($FANGID,$GAMEIDIP);
                $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

                if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

                if(!$ganeid ){

                    return apptongxin($SHUJU ,415,-1,'房间不存在',$YZTOKEN,$WY);
                }

            
            }
        }
    }


    $GAMEIDIP = Game_Server( $GAMEID );


    $ganeid = $fan['msg'];

    $RENSHU = 5;

    if(!is_array($ganeid['Auser'])) $ganeid['Auser']= array();


    if( ! in_array( $USERID ,$ganeid['Auser'] ) ){

        $liang = count($ganeid['Auser']);
      

        $RENSHU = (int)$ganeid['xren'];

        if( $liang >= $RENSHU ){
        
            return apptongxin($SHUJU ,415,-1,'超过('.$RENSHU.')人限制',$YZTOKEN,$WY);
        
        }

        if( isset($ganeid['off']) && $ganeid['off'] != 0  &&  !strstr( $GAMEID , "J")  ){
        
            return apptongxin($SHUJU ,415,-1,'请等待这一局结束',$YZTOKEN,$WY);
        
        } 

        if( isset( $ganeid['fangmm'])  && $ganeid['fangmm'] != '' )
        {
            if( $PASSMM != $ganeid['fangmm'] ){

                return apptongxin($SHUJU ,415,-1,'房间密码错误',$YZTOKEN,$WY);
            }
        }
    }


    if($ganeid['fpayfs'] == 1 && $ganeid['isfguan'] > 0 ){

        $USER = uid($USERID);
        $GAMESET['huobi'] = $ganeid['fbizhong'];

        $JISUANJIN = $ganeid['fpay'];

        if($GAMESET['huobi'] == '2'){


            if( $USER['huobi'] < $JISUANJIN ){
        
                // return apptongxin($SHUJU,415,-1,$CONN['huobi'].'不足无法加入房间',$YZTOKEN,$WY);
            } 
        
        
        }else if($GAMESET['huobi'] == '1'){

            if( $USER['jifen'] < $JISUANJIN ){
        
                return apptongxin($SHUJU,415,-1,$CONN['jifen'].'不足无法加入房间',$YZTOKEN,$WY);
            } 
        
        }else{

            if( $USER['jine'] < $JISUANJIN ){
        
                return apptongxin($SHUJU,415,-1,$CONN['jine'].'不足无法加入房间',$YZTOKEN,$WY);
            }
        
        
        }

    }



    $SHUJU = ingame( $Mem ,$HASH, $GAMEID,$FANGID ,$USERID ,$GAMEIDIP);

    $Mem ->s($YZHost,1,1);

    $CODE = 1;

    /*put end*/
}else if( $MOD == 'delete' ){

    $TUIGUAN = isset( $_NPOST['code'] )?$_NPOST['code']:'';

    if($TUIGUAN == ''){

        return apptongxin($SHUJU,415,'-1' ,'新手礼包key错误',$YZTOKEN,$WY);
    }

    $xyan = TuiGuang_yzkey($TUIGUAN);

    if($xyan < 1){

        return apptongxin($SHUJU,415,'-1' ,'新手礼包key错误',$YZTOKEN,$WY);

    }


        $YZHost = 'weiyi/'.md5('put'.$USERID);

    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin($SHUJU,415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $XUIDD = uid( $xyan );

    if(!$XUIDD){

        return apptongxin($SHUJU,415,'-1' ,'代理ID错误',$YZTOKEN,$WY);
    }



    $USER = uid( $USERID );

    if($USER['tuid'] > 0){

        return apptongxin($SHUJU,415,'-1' ,'已经领取了新手礼包',$YZTOKEN,$WY);
    
    }


    if($USER['level'] > 0){


        return apptongxin($SHUJU,415,'-1' ,'代理无法领取新手礼包',$YZTOKEN,$WY);

    }



    if($xyan == $USERID){

        return apptongxin($SHUJU,415,'-1' ,'自己不能推广自己',$YZTOKEN,$WY);

    }

    if($xyan > $USERID){

        return apptongxin($SHUJU,415,'-1' ,'后买的怎么推广先来的',$YZTOKEN,$WY);

    }

    $D = db('user');

    $WHERES = array(
        'atime'=>time(),

    );

    if( $XUIDD ){

        $WHERES['tuid'] = $xyan;

        for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){

            $wds = $i-1;
            if($wds < 1) $wds= '' ;
            $WHERES['tuid'.$i] = $XUIDD['tuid'.$wds];
        }
    }

    $fan = $D ->where(array('uid' => $USERID))->update( $WHERES );

    if( $fan ){

        /*录入奖励用户*/
        if($CONN['lurutuifk'] > 0 || $CONN['lurutuijb'] > 0 ){

            $USER = jiaqian($USERID,7,0,$CONN['lurutuifk'],$CONN['lurutuijb'],$xyan);
        }

        /*录入奖励上级*/
        if($CONN['shangjlfk'] > 0 || $CONN['shangjljb'] > 0 ){

            jiaqian($xyan,7,0,$CONN['shangjlfk'],$CONN['shangjljb'],$USERID);
        }


        return apptongxin(array( 'jifen'=> $USER['jifen'],'huobi'=> $USER['huobi']),200,'1' ,'礼包领取成功',$YZTOKEN,$WY);


    }else{

        return apptongxin($SHUJU,415,'-1' ,'礼包领取失败',$YZTOKEN,$WY);
    }
}


return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);