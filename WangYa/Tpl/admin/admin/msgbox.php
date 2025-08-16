<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('msgbox');

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

            $WHERE['off'] = $_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['uid'] = $_NPOST['tuid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

      
            $WHERE['name LIKE'] = '%'.$_NPOST['soso'].'%';
            $WHERE['neirong OLK'] = '%'.$_NPOST['soso'].'%';
           
        }



        $DATA = $D -> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            
            $Mdata = array();

            $MUID = array();

            foreach($DATA as $shuju){


                $chunam  = '未知';

                if(!isset($MUID[$shuju['uid']])){

                    $uuuu = uid($shuju['uid']);

                    if( $uuuu ){

                        $chunam  =$MUID[$shuju['uid']] = $uuuu['name'];

                    }else{
                    
                        $chunam  =$MUID[$shuju['uid']] = '未知';
                    }
                }

                $shuju['uname'] = $chunam;
                $Mdata[] = $shuju;
            
            }

            $SHUJU['data'] = $Mdata;
            


        }else{

            $CODE = -1;
        }

        $SHUJU['off'] = logac('off');
        $SHUJU['yesno'] = logac('yesno');
        $SHUJU['msgbox'] = logac('msgbox');




    }else{

        /*读取一条数据*/

        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $DATA = $D ->where(array('id' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }


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

    
    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $_NPOST['atime'] = time();
    $_NPOST['ip'] = ip();
    $_NPOST['off'] = 2;

    if($_NPOST['msgbox'] == '3' ){
                            /*除了代理*/

        $all = $D ->setbiao('user')->zhicha('uid,tuid')->where(array('level'=> 0))-> select();
        $D ->setbiao('msgbox');

        foreach($all as $iii){

            $_NPOST['uid'] = $iii['uid'];
            $zuuu[]=$D -> psql($_NPOST);

        }

        $fanhui = $D -> pqsql($zuuu);
        $fanhui = true;

    }else if($_NPOST['msgbox'] == '2' ){

         /*所有会员*/

        $all = $D ->setbiao('user')->zhicha('uid,tuid')-> select();

        $D ->setbiao('msgbox');

        foreach($all as $iii){

            $_NPOST['uid'] = $iii['uid'];
            $zuuu[]=$D -> psql($_NPOST);

        }

        $fanhui = $D -> pqsql($zuuu);
        $fanhui = true;

    }else if($_NPOST['msgbox'] == '1' ){

        /*全部代理*/

        $all = $D ->setbiao('user')->zhicha('uid,tuid')->where(array('level >'=> 0))-> select();

        $D ->setbiao('msgbox');

        foreach($all as $iii){

            $_NPOST['uid'] = $iii['uid'];
            $zuuu[]=$D -> psql($_NPOST);

        }

        $fanhui = $D -> pqsql($zuuu);
        $fanhui = true;
    
    
    }else{

        $fanhui = $D ->setbiao('msgbox')-> insert($_NPOST);
    }
    
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

    $YZTOKEN = token();

    sescc('token',$YZTOKEN,$UHA);

    $DATA = $D ->where(array('id' => $ID ))-> find();

    if(!$DATA){

        return apptongxin($SHUJU,415,-1,'ID不存在',$YZTOKEN,$WY);
    }

    if($DATA['atime'] < 1){

        $_NPOST['atime'] = time();
    }

    $_NPOST['ctime'] = time();

    $fan = $D ->where( array( 'id' => $ID))-> update( $_NPOST);

    if( $fan){ 

        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATA, 
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

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    if(!$DATAS){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }


    $fan = $D -> where( array('id' => $ID ) ) -> delete();


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