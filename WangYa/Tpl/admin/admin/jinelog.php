<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('jinelog');

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

        if( isset($_NPOST['level']) && $_NPOST['level'] != '' && $_NPOST['level'] > -1 ){

            $WHERE['type'] = $_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['uid'] = $_NPOST['tuid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){


            $WHERE['data LIKE'] = '%'.$_NPOST['soso'].'%';
           
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
                }else{
                
                    $chunam  =$MUID[$shuju['uid']];
                } 

                $shuju['uname'] = $chunam;
                $Mdata[] = $shuju;
            
            }

            $SHUJU['data'] = $Mdata;
            $SHUJU['type'] = logac('jinelog');

            if($WHERE){

                $where =  $D -> wherezuhe( $WHERE );
                $fanhui = $D -> qurey("select sum(jine) num from `".$D->biao().'` '.$where);
                $SHUJU['jine'] =(float)$fanhui['num']; 

            }
            


        }else{

            $CODE = -1;
        }




    }else{

        /*读取一条数据*/
    
    
    
    }



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);