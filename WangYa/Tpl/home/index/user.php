<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}


if($MOD == 'get'){

    $USER  = uid($USERID,1);

    
    
    $STAT = 200;
    $CODE = 1;
    $SHUJU = array( 

            'name' => $USER['name'],
            'uid' => $USER['uid'],
    'touxiang' => pichttp( $USER['touxiang']),
        'xingbie' => $USER['xingbie'],
        'level' => $USER['level'],
        'jine' => $USER['jine'],
        'jifen' => $USER['jifen'],
        'huobi' => $USER['huobi'],
    'jingyan' => $USER['jingyan'],
    // 'maxjin' =>$jinya,
        'zhiye' => $USER['zhiye'], //姓名
        'qqhaoma' => $USER['qqhaoma'],//方式
            'weixinhaoma' => $USER['weixinhaoma'],//账号
            'openidd'=> $USER['openidd'],//开户行
            'daili' =>$USER['daili'],

        'yongjin' => $USER['yongjin'],

    );

    $where = db('huobilog') -> wherezuhe(array('type' => 18,'uid' => $USERID));

    $sql = 'SELECT SUM(jine) as "TotalGet" FROM ay_huobilog '.$where;

    $Total = db('huobilog') -> qurey($sql);

    $SHUJU['allyongjin'] = $Total['TotalGet']?$Total['TotalGet']:0;

    $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
    $where = db('huobilog') -> wherezuhe(array('atime >' => $todayStart,'atime <=' => $todayEnd,'type'=>18,'uid'=>$USERID));

    $sql = 'SELECT SUM(jine) as "todayGet" FROM ay_huobilog '.$where;

    $todayTotal = db('huobilog') -> qurey($sql);

    $todayGet = $todayTotal['todayGet']?$todayTotal['todayGet']:0;

    $SHUJU['todayGet'] = $todayGet;
    
    $tticheng = logac('ticheng');
    $dailiticheng = array();
    foreach($tticheng as $v){
        $data = explode('_',$v);
        $data['state'] = 0;     //0:未达到  1:可领取  2:已过期  3:已领取
        if((time() - strtotime(date('Y-m-d'))) > 43200){
            $data['state'] = 2;
        }else{
            if((float)$todayGet >= (float)$data[0]){
                $data = db('huobilog') -> where(array('type' => 4,'uid' => $USERID,'atime >' => $todayStart,'atime <=' => $todayEnd)) -> find();
                if(!$data){
                    $data['state'] = 1;
                }else $data['state'] = 3;
            }
        }
        
        $dailiticheng[] = $data;
    }
    
    $SHUJU['hbtticheng'] = $dailiticheng;
    
}else if($MOD == 'post'){
    /*新增数据*/

    if((time() - strtotime(date('Y-m-d'))) > 43200){

        return apptongxin($SHUJU,415,-1,'您的工资已过期，请明天准时领取',$YZTOKEN,$WY);
    }

    $USERDATA = db('user') -> where(array('uid' => $USERID)) -> find();

    if((int)$USERDATA['islinqu'] == 1){

        return apptongxin($SHUJU,415,-1,'已经领取了',$YZTOKEN,$WY);

    }

    $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
    $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

    global $Mem;
    $islingqu = $Mem -> g('lingqu/'.$USERID);

    if((int)$islingqu == 1){    //已领取

        return apptongxin($SHUJU,415,-1,'已经领取了1',$YZTOKEN,$WY);

    }
    $tomorrow =mktime(0,0,1,date('m'),date('d')+1,date('Y'));

    $fann = $Mem -> s('lingqu/'.$USERID,1,$tomorrow - time());

    if(!$fann){
        return apptongxin($SHUJU,415,-1,'领取失败1',$YZTOKEN,$WY);
    }
    
    $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>18,'uid'=>$USERID));
    $sql = 'SELECT SUM(jine) as "yesterdayGet" FROM ay_huobilog '.$where;

    $yesterdayTotal = db('huobilog') -> qurey($sql);
    $yesterdayGet = $yesterdayTotal['yesterdayGet']?$yesterdayTotal['yesterdayGet']:0;

    $gongzi = CanGet($yesterdayGet,$USERID);

    if($gongzi <= 0){

        return apptongxin($SHUJU,415,-1,'无可领工资',$YZTOKEN,$WY);

    }

    $fan = db('user') -> where( array( 'uid' => $USERID) ) -> update(array('islinqu' => 1));

    if($fan){

        $SHUJU = jiaqian($USERID,18,0,0,0,'获得'.$gongzi.'工资','',$gongzi);

        return apptongxin($SHUJU,200,1,'成功领取'.$gongzi.'工资',$YZTOKEN,$WY);

    }

    return apptongxin($SHUJU,415,-1,'领取失败',$YZTOKEN,$WY);
}else if($MOD == 'put'){
    /*修改数据*/

    $YZHost = 'weiyi/put'.md5($USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);


    $LX = (int)(isset($_NPOST['lx']) ?$_NPOST['lx']:1);

    if( $LX == 1 ){
        /*修改昵称*/
        $NAME =  anquanqub( isset($_NPOST['name']) ?$_NPOST['name']:'');

        if(strlen($NAME) < 2 || strlen($NAME) > 30){
        
            return apptongxin(array(),415,$CODE,'昵称格式错误',$YZTOKEN,$WY);
        }

        $USER = uid($USERID);

        if($USER['name'] == $NAME){

            return apptongxin(array(),415,$CODE,'和原来一样无需修改',$YZTOKEN,$WY);
        }

        $DB = db('user');

        $fan =$DB ->where( array( 'uid' => $USERID ) )-> update( array( 'name' => $NAME ));

        if( $fan ){

            userlog( $USERID , 4 , $USER['name']. ' '.$NAME);

            uid( $USERID , 1 );
            $CODE = 1;
            $MSG = $NAME ;

        }else{
        
            return apptongxin(array(),415,$CODE,'昵称修改失败',$YZTOKEN,$WY);
        }

    }else if( $LX == 2 ){


        $USER = uid($USERID);

        if($USER['xingbie'] != -1 ){

            return apptongxin(array(),415,$CODE,'性别已经选择',$YZTOKEN,$WY);
        }

        $sex = (int)(isset($_NPOST['sex']) ?$_NPOST['sex']:0);

        if($sex > 1|| $sex < -1)$sex =0;

         $DB = db('user');

        $fan =$DB ->where( array( 'uid' => $USERID ) )-> update( array( 'xingbie' => $sex ));

        if( $fan ){

            userlog( $USERID , 5 , $sex);

            uid( $USERID , 1 );
            $CODE = 1;
      

        }else{
        
            return apptongxin(array(),415,$CODE,'性别设置失败',$YZTOKEN,$WY);
        }

    }else if( $LX == 3){
        /*实名制*/
        $NAME =  anquanqub( isset($_NPOST['zhanghao']) ?$_NPOST['zhanghao']:'');

        if(strlen($NAME) < 2 || strlen($NAME) > 30){
        
            return apptongxin(array(),415,$CODE,'姓名格式错误',$YZTOKEN,$WY);
        }

        $USER = uid($USERID);

        if($USER['realname']){

            return apptongxin(array(),415,$CODE,'已认证',$YZTOKEN,$WY);
        }

        $DB = db('user');

        $fan =$DB ->where( array( 'uid' => $USERID ) )-> update( array( 'realname' => $NAME ));

        if( $fan ){

            userlog( $USERID , 4 , $USER['realname']. ' '.$NAME);

            uid( $USERID , 1 );
            $CODE = 1;
            $MSG = $NAME ;

        }else{
        
            return apptongxin(array(),415,$CODE,'认证失败',$YZTOKEN,$WY);
        }

    }

}else if($MOD == 'delete'){
    
}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);