<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('user');

//rizhi('user',$MOD.'  '.json_encode($_NPOST));

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
        

        $YZTOKEN = token();

        sescc('token',$YZTOKEN,$UHA);

        
        $WHERE = array();

        $limit = listmit( $NUM , $PAG);

        if( isset($_NPOST['level']) && $_NPOST['level'] != '' && $_NPOST['level'] > -1 ){

            $WHERE['level'] = (int)$_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['tuid'] = (int)$_NPOST['tuid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['uid'] = (int)$_NPOST['soso'];

        }

        //时间
        if( isset($_NPOST['ktime']) && !empty($_NPOST['ktime']) &&  isset($_NPOST['jtime']) && !empty($_NPOST['jtime']) ){
            $WHERE['atime >='] = strtotime($_NPOST['ktime']);
            $WHERE[' atime <='] = strtotime($_NPOST['jtime']);
        }else if( isset($_NPOST['ktime']) && $_NPOST['ktime'] != '' ){
            $WHERE['atime >='] = strtotime($_NPOST['ktime']);
        }else if( isset($_NPOST['jtime']) && $_NPOST['jtime'] != '' ){
            $WHERE['atime <='] = strtotime($_NPOST['jtime']);
        }

        if( isset($_NPOST['name']) && $_NPOST['name'] != '' ){

            $WHERE['name OLK'] = '%'.$_NPOST['name'].'%';
            $WHERE['zhanghao OLK'] = '%'.$_NPOST['name'].'%';
        }



        $DATA = $D ->zhicha('uid,name,zhanghao,integral,shouji,off,atime,ip,tuid,huobi,yongjin,jifen,tuid1,tuid2,tuid3,tuid4,tuid5,tuid6,tuid7,tuid8,tuid9')-> where($WHERE) ->limit($limit) ->order('uid desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            foreach($DATA as $k=>$v){
                if($v['tuid']){
                    $tuser = uid($v['tuid']);
                    $v['tname'] = $tuser['name'];
                }else{
                    $v['tname'] = '';
                }
                
                $DATA[$k] = $v;
            }

            $number = $D -> where($WHERE) -> total();
            $SHUJU['page'] = ($number/$NUM) > (int)($number/$NUM)?(($number/$NUM) + 1):($number/$NUM);

            $SHUJU['data'] = $DATA;
            $SHUJU['level'] = logac('level');
            $SHUJU['xingbie'] = logac('xingbie');
            $SHUJU['off'] = logac('off2');
            $SHUJU['yip'] = logac('yesno');
            $SHUJU['jine'] = array('jine'=>$CONN['jine'],'jifen' => $CONN['jifen'],'huobi' => $CONN['huobi'],'yongjin' => $CONN['yongjin'],'integral' => $CONN['integral']);

        }else{

            $CODE = -1;

        }

    }else{

        /*读取一条数据*/
        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $DATA = $D ->where(array('uid' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        unset($DATA['mima']);
        unset($DATA['ermima']);

        $DATA['touxiang'] = touxiang($DATA['touxiang'] );

        $CODE = 1;
        $SHUJU = $DATA;

    }



}else if($MOD == 'post'){
    /*新增数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    if( $_NPOST['mima'] == '') unset( $_NPOST['mima']);
    else $_NPOST['mima'] = mima( $_NPOST['mima']);

    if( $_NPOST['ermima'] == '') unset( $_NPOST['ermima']);
    else $_NPOST['ermima'] = mima( $_NPOST['ermima']);

    $unset = array( 'jine', 'jifen', 'huobi', 'yongjin' , 'tuid1', 'tuid2', 'adminid','atime','appid','qdtime');

    foreach( $unset as $unsets){

        if( isset( $_NPOST[ $unsets] ))   unset( $_NPOST[ $unsets]);

    }

    $jiance = array('zhanghao','email','shouji','weixin','weixinui','qqopen','weibo','zhifubaoopen','openid','openidd');

    foreach($jiance as $xuns){

        if( $_NPOST[$xuns] != ''){

            $fanhui = $D  ->where(array( $xuns => $_NPOST[$xuns] ))-> find();

            if( $fanhui) {

                return apptongxin($SHUJU,415,-1,$_NPOST[$xuns]."已经存在",$YZTOKEN,$WY);
            }

        }
    }

    $_NPOST['atime'] = time();
    $_NPOST['ip'] = ip();

    $_NPOST['tuid'] = (int) $_NPOST['tuid'];

    $tuigyhu = uid($_NPOST['tuid']);

    if($tuigyhu){

        for( $i=1 ;$i<  $CONN['tujishu'];$i++){

            $wds = $i-1;
            if($wds < 1) $wds='';
            $_NPOST['tuid'.$i] = $tuigyhu['tuid'.$wds];
        }

    }else  $_NPOST['tuid'] = '0';

    $_NPOST['admin'] = $sescc['aid'];


    $_NPOST['touxiang'] =  TOU_ti($_NPOST['touxiang']);




    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $fanhui = $D -> insert($_NPOST);
    
    if( $fanhui){ 

        adminlog($sescc['aid'], 5 , serialize( array( 'ac' => $AC , 'mo' => $MOD ,'id'=> $fanhui ,'data'=> $_NPOST  )));
        return apptongxin($SHUJU,200,1,"新增成功",$YZTOKEN,$WY);

    }else return apptongxin($SHUJU,406,-1,"添加失败",$YZTOKEN,$WY);


}else if($MOD == 'put'){
    /*修改数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,400,-1,$MSG,$YZTOKEN,$WY);
    
    }

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);
    }

    $UUUU = uid($ID);


    if( $_NPOST['mima'] == '') unset( $_NPOST['mima']);
    else $_NPOST['mima'] = mima( $_NPOST['mima']);

    if( $_NPOST['ermima'] == '') unset( $_NPOST['ermima']);
    else $_NPOST['ermima'] = mima( $_NPOST['ermima']);

    $unset = array( 'jine', 'jifen', 'huobi', 'yongjin' , 'tuid1', 'tuid2', 'adminid','atime','appid','qdtime');

    foreach( $unset as $unsets){

        if( isset( $_NPOST[ $unsets] ))   unset( $_NPOST[ $unsets]);

    }

    $jiance = array('zhanghao','email','shouji','weixin','weixinui','qqopen','weibo','zhifubaoopen','openid','openidd');

    foreach($jiance as $xuns){

        if( $UUUU[$xuns] !=  $_NPOST[$xuns] && $_NPOST[$xuns] != ''){

            $fanhui = $D  ->where(array( $xuns => $_NPOST[$xuns] ))-> find();

            if( $fanhui) {

                return apptongxin($SHUJU,415,-1,$_NPOST[$xuns]."已经存在",$YZTOKEN,$WY);
            }

        }
    }

    $_NPOST['atime'] = time();
    $_NPOST['ip'] = ip();

    $_NPOST['tuid'] = (int) $_NPOST['tuid'];

    $tuigyhu = uid($_NPOST['tuid']);

    if($tuigyhu){

        for( $i=1 ;$i<  $CONN['tujishu'];$i++){

            $wds = $i-1;
            if($wds < 1) $wds='';
            $_NPOST['tuid'.$i] = $tuigyhu['tuid'.$wds];
        }

    }else  $_NPOST['tuid'] = '0';

   

    $_NPOST['touxiang'] =  TOU_ti($_NPOST['touxiang']);


    $YZTOKEN = token();

    sescc('token',$YZTOKEN,$UHA);

    $fan = $D ->where( array( 'uid' => $ID))-> update( $_NPOST);

    if( $fan){ 

        uid( $ID , 1);

  
        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $UUUU, 
            'data'=> $_NPOST )));

        return apptongxin($SHUJU,200,1,$MSG,$YZTOKEN,$WY);

    }else{

        return apptongxin($SHUJU,400,-1,$MSG,$YZTOKEN,$WY);
    
    }





}else if($MOD == 'delete'){
    /*删除数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $DATAS = $D -> where( array('uid' => $ID ) ) -> find();

    if(!$DATAS){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }


    $fan = $D -> where( array('uid' => $ID ) ) -> delete();


    if( $fan ){


        uid( $ID , 2);

        adminlog($sescc['aid'], 4 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS  )));

        

    }else{

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);

    }


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);