<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('dezhoubetlog');

if($MOD == 'get'){
    /*获取数据*/

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


    if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

        $WHERE['bet_uid'] = (int)$_NPOST['tuid'];
    }
    if( isset($_NPOST['room_id']) && $_NPOST['room_id'] != '' ){

        $WHERE['bet_roomid'] = (int)$_NPOST['room_id'];
    }

    if( isset($_NPOST['ktime']) && !empty($_NPOST['ktime']) &&  isset($_NPOST['jtime']) && !empty($_NPOST['jtime']) ){
        $WHERE['bet_time >='] = strtotime($_NPOST['ktime']);
        $WHERE[' bet_time <='] = strtotime($_NPOST['jtime']);
    }else if( isset($_NPOST['ktime']) && $_NPOST['ktime'] != '' ){
        $WHERE['bet_time >='] = strtotime($_NPOST['ktime']);
    }else if( isset($_NPOST['jtime']) && $_NPOST['jtime'] != '' ){
        $WHERE['bet_time <='] = strtotime($_NPOST['jtime']);
    }

    $DATA = $D -> where($WHERE) ->limit($limit)->order('bet_id desc') -> select();

    if($DATA){

        $hua = array('1'=>'黑桃','2'=>'红桃','3'=>'梅花','4'=>'方块');
        foreach ($DATA as $k => $v){

            $DATA[$k]['bet_time'] = date( 'Y-m-d H:i:s',$v['bet_time'] );

            $chi = json_decode( $v['bet_chi'],true ); //牌池牌
            $chi_str = '';
            if( is_array($chi) ){

                $arr = [];
                for($i=0;$i<count($chi);$i++){
                    $hua = substr($chi[$i],0,1);
                    if($hua == 1){
                        $hua = '黑桃';
                    }elseif($hua == 2){
                        $hua = '红桃';
                    }elseif($hua == 3){
                        $hua = '梅花';
                    }else{
                        $hua = '方块';
                    }
                    $zhi = substr($chi[$i],1);
                    if($zhi == 1){
                        $zhi = 'A';
                    }elseif($zhi == 11){
                        $zhi = 'J';
                    }elseif($zhi == 12){
                        $zhi = 'Q';
                    }elseif($zhi == 13){
                        $zhi = 'K';
                    }
                    $pai = $hua.$zhi;
                    $arr[$i] = $pai;
                }

                $chi_str = implode('；',$arr);

            }
            $DATA[$k]['bet_chi'] = $chi_str;

            $pai = json_decode( $v['bet_pai'],true ); //玩家牌
            $pai_str = '';
            if( is_array($pai) ){

                $arr = [];
                for($i=0;$i<count($pai);$i++){
                    $hua = substr($pai[$i],0,1);
                    if($hua == 1){
                        $hua = '黑桃';
                    }elseif($hua == 2){
                        $hua = '红桃';
                    }elseif($hua == 3){
                        $hua = '梅花';
                    }else{
                        $hua = '方块';
                    }
                    $zhi = substr($pai[$i],1);
                    if($zhi == 1){
                        $zhi = 'A';
                    }elseif($zhi == 11){
                        $zhi = 'J';
                    }elseif($zhi == 12){
                        $zhi = 'Q';
                    }elseif($zhi == 13){
                        $zhi = 'K';
                    }
                    $str = $hua.$zhi;
                    $arr[$i] = $str;
                }

                $pai_str = implode('；',$arr);

            }
            $DATA[$k]['bet_pai'] = $pai_str;

            $DATA[$k]['bet_state'] = $v['bet_state'] == -1?'弃牌':($v['bet_state'] == 5?'ALL IN':'跟注');
            $DATA[$k]['mang'] = $v['xiaomang'].'/'.$v['damang'];

        }


        $CODE = 1;
        $STAT = 200;

        $SHUJU['data'] = $DATA;
        $SHUJU['type'] = ['','小盲','大盲'];

        $number = $D -> where($WHERE) -> total();

        $SHUJU['page'] = ($number/$NUM) > (int)($number/$NUM)?(($number/$NUM) + 1):($number/$NUM);

        if($WHERE){

            $where =  $D -> wherezuhe( $WHERE );
            $fanhui = $D -> qurey("select sum(bet_win) num from `".$D->biao().'` '.$where);
            $SHUJU['jine'] =(float)$fanhui['num'];

        }


    }else{

        $CODE = -1;
    }


}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}


return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);