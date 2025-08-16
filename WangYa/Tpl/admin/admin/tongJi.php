<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if($MOD == 'get'){
    /*获取数据*/

    

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    $SHUJU['gao'] = 0;

    if( $ID > 0 ){

        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);
        }
    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    if($ID == 1){
       /*今日充值*/

       $D = db('dingdan');

        for($i=0;$i<24;$i++ ){

            $start = mktime($i,0,0,date('m'),date('d'),date('y'));
            $end = mktime($i+1,0,0,date('m'),date('d'),date('y'));
            $where  =  $D -> wherezuhe( array( 'off' => 2 , 'atime >=' => $start, 'atime <' => $end ) );
            $fanhui =  $D -> qurey( "select sum(rejine) num from `".$D->biao().'` '.$where );

            $jin = (float)$fanhui['num'];
            if($SHUJU['gao'] < 1 ) $SHUJU['gao'] = $jin;
            if($SHUJU['gao'] < $jin) $SHUJU['gao'] = $jin;

            $SHUJU['data'][] = array(

                'k' => $i.'-'.($i+1).'点' ,
                'v' => $jin
            );
        }

    }else if($ID == 2){
        /*七天充值*/

        $D = db('dingdan');

        $day = 7;

        for($i =1;$i<= $day; $i++){

            if($i == 1){ 

                $start = mktime(0,0,0,date('m'),date('d'),date('y'));
                $end = time();

            }else{


                $end  = mktime(0,0,0,date('m'),date('d')-($i-2),date('y'));
                $start = mktime(0,0,0,date('m'),date('d')-($i-1),date('y'));
             }

            $shijian = date( 'Y-m-d', $start); 
            $where  =  $D -> wherezuhe( array( 'off'=> 2,'atime >=' => $start, 'atime <' =>$end ) );
            $fanhui =  $D -> qurey( "select sum(rejine) num from `".$D->biao().'` '.$where );

            $jine   =  (float)$fanhui['num'];
            if($SHUJU['gao'] < 1 ) $SHUJU['gao'] = $jine;
            if($SHUJU['gao'] < $jine) $SHUJU['gao'] = $jine;

            $SHUJU['data'][] = array(

                'k' => $shijian,
                'v' => $jine
            );
         }

    }else if($ID == 3){

        /*所有充值*/
        $D = db('dingdan');
        $xtpay = xitongpay(-1);
        if($xtpay){
        foreach($xtpay as $k => $v){

            $where  =  $D -> wherezuhe( array( 'off' => 2, 'paytype' => $k) );
            $fanhui =  $D -> qurey( "select sum(rejine) num from `".$D->biao().'` '.$where );
            $jine   =  (float)$fanhui['num'];
            if($SHUJU['gao'] < 1 ) $SHUJU['gao'] = $jine;
            if($SHUJU['gao'] < $jine) $SHUJU['gao'] = $jine;


            $SHUJU['data'][] = array(
                    'k' => $v,
                    'v' => $jine
                );
        }

        }

    
    }else if($ID == 5){
        /*30天房卡记录*/
        $D = db('fanglist');
        for($i =1;$i< 32 ; $i++){

            if($i == 1){ 
                
                $stert = time();
                $end = mktime(0,0,0,date('m'),date('d'),date('y'));

            }else{

            $stert = mktime(0,0,0,date('m'),date('d')-($i-2),date('y'));
            $end = mktime(0,0,0,date('m'),date('d')-($i-1),date('y'));

            }

            $shijian= date( 'Y-m-d', $end );
            $where = array('atime >='=>$end, 'atime <'=> $stert);
            
            $shuju = $D ->where( $where ) -> total();

            if($SHUJU['gao'] < 1 ) $SHUJU['gao'] = $shuju;
            if($SHUJU['gao'] < $shuju) $SHUJU['gao'] = $shuju;

            $SHUJU['data'][] = array(
                'k' => $shijian,
                'v' => $shuju
            );
        }

    
    }else if($ID == 6){
        /*30天注册统计*/
        $D = db('user');
        $shijian = array();

        $shuju = array();

        for($i =1;$i< 32 ; $i++){

            if($i == 1){ 
        
                $stert = time();
                $end = mktime(0,0,0,date('m'),date('d'),date('y'));
            }else{

                $stert = mktime(0,0,0,date('m'),date('d')-($i-2),date('y'));
                $end = mktime(0,0,0,date('m'),date('d')-($i-1),date('y'));
            }

    

            $shijian= date( 'Y-m-d', $end ); 

            $where = array('atime >='=>$end, 'atime <'=> $stert);
            $shuju = $D ->where( $where ) -> total();
            if($SHUJU['gao'] < 1 ) $SHUJU['gao'] = $shuju;
            if($SHUJU['gao'] < $shuju) $SHUJU['gao'] = $shuju;

            $SHUJU['data'][] = array(
                'k' => $shijian,
                'v' => $shuju
            );

        }

    }

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}

return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);