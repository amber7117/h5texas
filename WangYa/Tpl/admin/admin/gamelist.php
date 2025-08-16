<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('gamelist');

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

        
        $WHERE = array();

        $limit = listmit( $NUM , $PAG);

        $YZTOKEN = token();

        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D -> where($WHERE) ->limit($limit) ->order('gl_id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;

            foreach($DATA as $k => $v){
                $DATA[$k]['gl_imgurl'] = pichttp($v['gl_imgurl']);
                $DATA[$k]['gl_tupian'] = pichttp($v['gl_tupian']);
                if(empty($v['gl_gameurl'])){
                    $DATA[$k]['gl_gameurl'] = '#';
                }
                $DATA[$k]['gl_atime'] = date("Y-m-d H:i:s",$v['gl_atime']);
            }

            $SHUJU['data'] = $DATA;

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

        $DATA = $D ->where(array('gl_id' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        $SHUJU = $DATA;

        if($SHUJU['gl_imgurl'] != ''){
        
            $SHUJU['gl_imgurl'] = pichttp($SHUJU['gl_imgurl']);
        }

        if(empty($SHUJU['gl_gameurl'])){
            $SHUJU['gl_gameurl'] = '#';
        }
        $SHUJU['gl_atime'] = date("Y-m-d H:i:s",$SHUJU['gl_atime']);

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

    $arr['gl_name'] = isset($_NPOST['gl_name'])?$_NPOST['gl_name']:"";
    $arr['biaoshi'] = isset($_NPOST['biaoshi'])?$_NPOST['biaoshi']:"";
    $arr['gl_rank'] = isset($_NPOST['gl_rank'])?(int)$_NPOST['gl_rank']:0;
    $arr['gl_gameurl'] = isset($_NPOST['gl_gameurl'])?$_NPOST['gl_gameurl']:'';
    $arr['gl_tupian'] = isset($_NPOST['gl_tupian'])?TOU_ti($_NPOST['gl_tupian']):'';
    $arr['gl_imgurl'] = isset($_NPOST['gl_imgurl'])?TOU_ti($_NPOST['gl_imgurl']):"";

    $arr['gl_atime'] = time();

    $fanhui = $D -> insert($arr);

    if( $fanhui){ 

        return apptongxin($SHUJU,200,1,"新增成功",$YZTOKEN,$WY);

    }else return apptongxin($SHUJU,406,-1,"添加失败",$YZTOKEN,$WY);



}else if($MOD == 'put'){

    /*修改数据*/
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

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $DATAS = $D -> where( array('gl_id' => $ID ) ) -> find();
    if(!$DATAS){

        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    }



    $arr['gl_name'] = isset($_NPOST['gl_name'])?$_NPOST['gl_name']:"";
    $arr['biaoshi'] = isset($_NPOST['biaoshi'])?$_NPOST['biaoshi']:"";
    $arr['gl_rank'] = isset($_NPOST['gl_rank'])?(int)$_NPOST['gl_rank']:0;
    $arr['gl_gameurl'] = isset($_NPOST['gl_gameurl'])?$_NPOST['gl_gameurl']:'';
    $arr['gl_tupian'] = isset($_NPOST['gl_tupian'])?TOU_ti($_NPOST['gl_tupian']):'';
    $arr['gl_imgurl'] = isset($_NPOST['gl_imgurl'])?TOU_ti($_NPOST['gl_imgurl']):"";

    $arr['gl_atime'] = time();

    $fan = $D ->where( array( 'gl_id' => $ID))-> update( $arr);

    if($fan){

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

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);


    $fan = $D -> where( array('gl_id' => $ID ) ) -> delete();

    if( $fan ){

        return apptongxin($SHUJU,200,1,"删除成功",$YZTOKEN,$WY);

    }else{

        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);
    }

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);