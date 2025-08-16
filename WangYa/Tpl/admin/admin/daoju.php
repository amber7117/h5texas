<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('daoju');

if($MOD == 'get'){
    /*获取数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        /*小于1 多条数据*/

        $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
        $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);
        $UID = (int)(isset($_NPOST['uid'])?$_NPOST['uid']:0);

        if($NUM < 8){
            
            $NUM = 8;
        }

        if($NUM > 100){

            $NUM = 100;
        }

        
        $WHERE = array();

        $limit = listmit( $NUM , $PAG);

        $YZTOKEN = token();
        if($UID > 0){

            $WHERE['uid'] = $UID;
        }

        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D -> where($WHERE) ->limit($limit) -> select();
        

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['daoju'] = array();
            $daoju = logac('daoju');
            foreach($daoju as $v){

                
                list( $name , $biaoshi , $miaosu , $suiji , $off  , $jine ,   $time ,$shiijan) = explode( "_", $v);
                $off =(int)$off;

                if($off > 0){

                    $SHUJU['daoju'][$biaoshi] = $name;
                }
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

        $DATA = $D ->where(array('uid' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        $SHUJU =$DATA;
    
    
    
    }



}else if($MOD == 'post'){
    /*新增数据*/

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
    unset($_NPOST['uid']);
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

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);