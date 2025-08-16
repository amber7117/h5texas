<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('vbjl_kjlog');

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

        if( isset($_NPOST['vk_qishu']) && $_NPOST['vk_qishu'] != '' ){

            $WHERE['vk_qishu'] = $_NPOST['vk_qishu'];
        }


        $DATA = $D -> where($WHERE) ->limit($limit)->order('vk_time desc') -> select();


        if($DATA){


            foreach ($DATA as $k => $v){


                $bet_dealer = json_decode($v['vk_dealer'],true);

                if($bet_dealer){

                    /*'1'=>'黑桃','2'=>'红桃','3'=>'梅花','4'=>'方块'*/
                    for($i=0;$i<count($bet_dealer);$i++){
                        $hua = substr($bet_dealer[$i],0,1);
                        if($hua == 1){
                            $hua = '黑桃';
                        }elseif($hua == 2){
                            $hua = '红桃';
                        }elseif($hua == 3){
                            $hua = '梅花';
                        }else{
                            $hua = '方块';
                        }
                        $zhi = substr($bet_dealer[$i],1);
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
                        $bet_dealer[$i] = $pai;
                    }

                    $dealer = implode('；',$bet_dealer);
                }else{
                    $dealer = '';
                }

                $DATA[$k]['vk_dealer'] = $dealer;

                $bet_player = json_decode($v['vk_player'],true);

                if($bet_player){

                    /*'1'=>'黑桃','2'=>'红桃','3'=>'梅花','4'=>'方块'*/
                    for($i=0;$i<count($bet_player);$i++){
                        $hua = substr($bet_player[$i],0,1);
                        if($hua == 1){
                            $hua = '黑桃';
                        }elseif($hua == 2){
                            $hua = '红桃';
                        }elseif($hua == 3){
                            $hua = '梅花';
                        }else{
                            $hua = '方块';
                        }
                        $zhi = substr($bet_player[$i],1);
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
                        $bet_player[$i] = $pai;
                    }

                    $player = implode('；',$bet_player);
                }else{
                    $player = '';
                }

                $DATA[$k]['vk_player'] = $player;
                $DATA[$k]['vk_time'] = date('Y-m-d H:i:s',$v['vk_time']);
            }


            $CODE = 1;
            $STAT = 200;

            $Mdata = array();

            $MUID = array();

            foreach($DATA as $shuju){


                $chunam  = '未知';

                if(!isset($MUID[$shuju['bet_uid']])){

                    $uuuu = uid($shuju['bet_uid']);

                    if( $uuuu ){

                        $chunam  =$MUID[$shuju['bet_uid']] = $uuuu['name'];

                    }else{

                        $chunam  =$MUID[$shuju['bet_uid']] = '未知';
                    }
                }else{

                    $chunam  =$MUID[$shuju['bet_uid']];
                }

                $shuju['uname'] = $chunam;
                $Mdata[] = $shuju;

            }

            $SHUJU['data'] = $Mdata;
            $SHUJU['type'] = logac('betlog');

            if($WHERE){

                $where =  $D -> wherezuhe( $WHERE );
                $fanhui = $D -> qurey("select sum(bet_bonus) num from `".$D->biao().'` '.$where);
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