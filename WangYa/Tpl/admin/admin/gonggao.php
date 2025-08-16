<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('gonggao');

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

        $SHUJU['gamelist'] = Game_List(1);
        $SHUJU['gamelist']['dating'] = '大厅';

        if( isset($_NPOST['gametype']) && $_NPOST['gametype'] != '' && $_NPOST['gametype'] > -1 ){

            $WHERE['gametype'] = $SHUJU['gamelist'][(int)$_NPOST['gametype']];
        }

        if( isset($_NPOST['ggtype']) && $_NPOST['ggtype'] != '' && $_NPOST['ggtype'] > -1 ){

            $WHERE['ggtype'] = (int)$_NPOST['ggtype'];
        }

        $DATA = $D -> where($WHERE) ->limit($limit) ->order('time desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            foreach($DATA as $k=>$v){
                
                $DATA[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
                $DATA[$k]['img'] = pichttp($v['img']);
            }

            $SHUJU['data'] = $DATA;
            
        }

        $SHUJU['gonggaotype'] = logac('gonggaotype');
        
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

        $DATA['img'] = pichttp($DATA['img']);

        $CODE = 1;
        $SHUJU = $DATA;

    }

    if(!$SHUJU){

        $CODE = -1;

    }

}else if($MOD == 'post'){
    /*新增数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $_NPOST['time'] = time();
   
    $_NPOST['img'] =  TOU_ti($_NPOST['img']);

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

    $_NPOST['time'] = time();
    $_NPOST['img'] =  TOU_ti($_NPOST['img']);

    $YZTOKEN = token();

    sescc('token',$YZTOKEN,$UHA);

    $fan = $D ->where( array( 'id' => $ID))-> update( $_NPOST);

    if( $fan){ 

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

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    if(!$DATAS){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }


    $fan = $D -> where( array('id' => $ID ) ) -> delete();


    if( $fan ){

        adminlog($sescc['aid'], 4 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS  )));

    }else{

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);

    }


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);