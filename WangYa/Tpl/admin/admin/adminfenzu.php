<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('adminfenzu');

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

        $DATA = $D ->zhicha('id,name,miaoshu')-> where($WHERE) ->limit($limit) ->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;
           

        }

        if($PAG  < 2){

            $i = 0;

            foreach( $ACTION as $kts => $shuju){

                if( ! isset( $YANZQX[$kts])) continue;

                if( isset( $YANZQX[$kts]['0']))
                    $newyan = array_flip($YANZQX[$kts]);
                else $newyan = ($YANZQX[$kts]);


                $SHUJU['cd'][$i]['caidan'] = $LANG['caidan'][$kts];
                $SHUJU['cd'][$i]['ac'] = $kts;


                if( $shuju){ 

                    foreach( $shuju as  $vv){ 

                        if( ! isset( $newyan[$vv])) continue;

                        $SHUJU['cd'][$i]['data'][$vv] = $LANG['adminac'][$vv];
                                            
                    }

                }

                $i++;
            }
        }

         

            

    }else{

        /*读取一条数据*/
        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $DATA = $D ->zhicha('id,name,miaoshu,shezhi')->where(array('id' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        $CODE = 1;
        $DATA['shezhi'] = $DATA['shezhi'] != '' ?unserialize($DATA['shezhi']):array();
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

    $fanhui = $D -> where(array( 'name' => $_NPOST['name']))-> find();
    if( $fanhui ){

        return apptongxin($SHUJU,415,-1,"管理组存在",$YZTOKEN,$WY);
    }

    if( isset( $_NPOST['shezhi'] ) ) $_NPOST['shezhi'] = serialize( $_NPOST['shezhi'] );
    else                            $_NPOST['shezhi'] = '';

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

  

    
    $fanhui = $D -> where(array( 'id' => $_NPOST['id']))-> find();
    if( !$fanhui ){

        return apptongxin($SHUJU,404,-1,"帐号布存在",$YZTOKEN,$WY);
    }



    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    if( isset( $_NPOST['shezhi'] ) ) $_NPOST['shezhi'] = serialize( $_NPOST['shezhi'] );
    else                            $_NPOST['shezhi'] = '';

    $fan = $D ->where( array( 'id' => $ID))-> update( $_NPOST);

    if( $fan){ 

  
        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $fanhui, 
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

    if( $ID == $sescc['aid']){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,"删除自己有意思吗?",$YZTOKEN,$WY);
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