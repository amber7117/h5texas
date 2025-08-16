<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('pinglun');

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

        if( isset($_NPOST['level']) && $_NPOST['level'] != '' && $_NPOST['level'] > -1 ){

            $WHERE['off'] = $_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['uid'] = $_NPOST['tuid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['qq'] = $_NPOST['soso'];
            $WHERE['xingming OLK'] = '%'.$_NPOST['soso'].'%';
            $WHERE['dizhi OLK'] = '%'.$_NPOST['soso'].'%';
        }




        $DATA = $D ->zhicha('id,uid,xingming,qq,dizhi,email,off')-> where($WHERE) ->limit($limit) -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $UUUU = array();

            foreach($DATA as $k => $ggdd){


                if(!isset($UUUU[$ggdd['uid']])){

                    $uuc = uid($ggdd['uid']);



                    if($uuc){

                        $DATAS['uname'] = $uuc['name'];

                    }else{
                        $DATAS['uname'] = '未知';
                    }


                    $DATA[$k]['uname'] = $UUUU[$ggdd['uid']] = $DATAS['uname'];


                
                }else{
                
                    $DATA[$k]['uname'] = $UUUU[$ggdd['uid']];
                
                }



                

                

                
            
            }


            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        $SHUJU['chulioff'] = logac('chulioff');




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

        $SHUJU = $DATA;

        $uu = uid($DATA['uid']);

        if($uu){

            $SHUJU['uname'] = $uu['name'];
            $SHUJU['shouji'] = $uu['shouji'];
            $SHUJU['jine'] = $uu['jine'];

            $D -> setbiao('jinelog');

            $WHERE = array('uid' =>$DATA['uid'] );
            $where =  $D -> wherezuhe( $WHERE );

            $fanhui = $D -> qurey("select sum(jine) num from `".$D->biao().'` '.$where);

            $SHUJU['yjine'] = ((float) $fanhui['num']);



        
        }else{

            $SHUJU['uname'] = '';
            $SHUJU['shouji'] = '';
            $SHUJU['jine'] = 0;
            $SHUJU['yjine'] =0;
        }

       


    
    
    
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

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);


    $DATA = $D ->where( array( 'id' => $ID))-> find();
    if(!$DATA){

        return apptongxin($SHUJU,415,-1,'数据错误',$YZTOKEN,$WY);
    
    }

    if($DATA['off'] != '0'){

        return apptongxin($SHUJU,415,-1,'已经处理了无法更改',$YZTOKEN,$WY);

    }

    $new = array(

        'huifu' => $_NPOST['huifu'],
        'off' => $_NPOST['off'],
        'xtime' => time()
    );

    $fan = $D ->where( array( 'id' => $ID))-> update( $new);

    if( $fan){ 

        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATA, 
            'data'=> $new )));

        return apptongxin($SHUJU,200,1,$MSG,$YZTOKEN,$WY);

    }else{

        return apptongxin($SHUJU,400,-1,$MSG,$YZTOKEN,$WY);
    
    }



    


}else if($MOD == 'delete'){
    /*删除数据*/



}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);