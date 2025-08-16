<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('jingcairecord');
global $Mem;
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

        
        $WHERE = array('gametype OLK' => '%apkhongbao%');

        $limit = listmit( $NUM , $PAG);

        if( isset($_NPOST['uid']) && $_NPOST['uid'] != '' ){

            $WHERE['uid'] = (int)$_NPOST['uid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['name OLK'] = '%'.$_NPOST['soso'].'%';
        }

        if( isset($_NPOST['leixing']) && $_NPOST['leixing'] != '' ){

            if((int)$_NPOST['leixing'] == 0){

                $WHERE['username'] = '';

            }else if((int)$_NPOST['leixing'] == 1){

                $WHERE['username'] = '1';

            }
        }

        $YZTOKEN = token();

        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D -> where($WHERE) ->limit($limit) -> order('id desc')-> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = array();
            foreach($DATA as $value){

                $value['time'] = date("Y-m-d H:i:s",$value['time']);

                $value['yingkui'] = $value['yingkui'];
                $userdata = uid($value['uid']);
                $value['name'] = $userdata['name']?$userdata['name']:$userdata['zhanghao'];
                $value['huobi'] = $value['remainhuobi'];
                $value['touxiang'] = touxiang($userdata['touxiang']);

                if((int)$value['username'] == 1){

                    $value['leixing'] = '抢红包';

                    if((int)$value['iszhonglei'] == 0){

                        $value['iszhonglei'] = '否';

                    }else if((int)$value['iszhonglei'] == 1){

                        $value['iszhonglei'] = '是';

                    }

                }else{
                    $value['leixing'] = '埋雷';
                }

                $SHUJU['data'][] = $value;
            }

            $SHUJU['gamelist'] = Game_List();

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

        $DATA['time'] = date("Y-m-d H:i:s",$DATA['time']);

        $DATA['yingkui'] = $DATA['yingkui'];

        $userdata = uid($DATA['uid']);
        $DATA['name'] = $userdata['name']?$userdata['name']:$userdata['zhanghao'];
        $DATA['huobi'] = $DATA['remainhuobi'];
        $DATA['touxiang'] = touxiang($userdata['touxiang']);

        $SHUJU = $DATA;

    }

}else if($MOD == 'post'){

    /*新增数据*/

}else if($MOD == 'put'){

    /*修改数据*/
    

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

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    if(!$DATAS){

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