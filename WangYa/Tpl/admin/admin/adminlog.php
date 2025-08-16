<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('adminlog');

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

        $DATA = $D ->zhicha('id,type,atime,ip,uid')-> where($WHERE) ->limit($limit) ->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;

            $KKK = array();

            $zuhe = array();
            $D -> setbiao('admin');

            foreach($DATA as $sss){

                if($sss['uid'] < 1){

                    $sss['uname'] = '未知';

                }else{

                    if(!isset($zuhe[$sss['uid']])){

                        $add = $D ->Where(array('id' => $sss['uid'] ))-> find();
                        if($add){

                            $sss['uname'] = $zuhe[$sss['uid']] = $add['name'];

                        }else{

                            $sss['uname'] = $zuhe[$sss['uid']] = '未知';
                        } 

                    }else{

                        $sss['uname'] = $zuhe[$sss['uid']];
                    }
                }
                
                $KKK[] = $sss;
            
            }

            $SHUJU['data'] = $KKK ;
            $SHUJU['adminlog'] = logac('adminlog');

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

        $DATA = $D ->where(array('id' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        $add = $D -> setbiao('admin')->Where(array('id' => $DATA['uid'] ))-> find();

        $CODE = 1;

        if(!$add) $add['name'] = '未知';

        $DATA['uname'] = $add['name'];

        $DATA['data'] = $DATA['data'] != '' ?unserialize( $DATA['data'] ) : '';
        $SHUJU = $DATA;

    }



}else if($MOD == 'post'){
    

    return apptongxin($SHUJU,415,-1,"禁止新增",$YZTOKEN,$WY);


}else if($MOD == 'delete'){
    /*删除数据*/

    
    return apptongxin($SHUJU,415,-1,"禁止删除",$YZTOKEN,$WY);


}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);