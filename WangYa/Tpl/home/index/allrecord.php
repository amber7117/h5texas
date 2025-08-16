<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('huobilog');
global $Mem;
if($MOD == 'get'){
    /*游戏记录*/
    
    $SHUJU = array();

    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

    if($NUM < 8){
        
        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

    $limit = listmit( $NUM , $PAG);

    if(isset($_NPOST['gametype'])){

        if($_NPOST['gametype'] == 'apkhongbao'){

            $TYPE = logac('huobilog');
            
            if((int)$_NPOST['type'] == 1){      // 充值记录

                $data = db('huobilog') -> where(array('uid' => $USERID,'type' => 2))->limit($limit)->order('atime desc') -> select();
                if($data){

                    foreach($data as $k=>$v){
            
                        $data[$k]['atime'] = date('Y-m-d | H:i:s',$v['atime']);
                        
                    }
            
                    $SHUJU = $data;
                }

            }else if((int)$_NPOST['type'] == 2){      // 提现记录

                $data = db('huobilog') -> where(array('uid' => $USERID,'type' => 17))->limit($limit)->order('atime desc') -> select();
                if($data){

                    foreach($data as $k=>$v){
            
                        $data[$k]['atime'] = date('Y-m-d | H:i:s',$v['atime']);
                        
                    }
            
                    $SHUJU = $data;
                }
            }else if((int)$_NPOST['type'] == 3){      // 游戏记录

                $WHERE = array();
                $WHERE['uid'] = (int)$USERID;

                $WHERE['gametype LIKE'] = '%apkhongbao%';

                $data = db('jingcairecord') -> where($WHERE)->limit($limit)->order('time desc') -> select();
             
                if($data){

                    foreach($data as $k=>$v){
            
                        $data[$k]['time'] = date('Y-m-d | H:i:s',$v['time']);

                    }
            
                    $SHUJU = $data;
                }
            }
        }
        
    }else{
        if((int)$_NPOST['type'] == 1){      //金币记录

            $data = db('huobilog') -> where(array('uid' => $USERID))->limit($limit)->order('atime desc') -> select();
    
            $TYPE = logac('huobilog');
    
            if($data){
    
                foreach($data as $k=>$v){
    
                    $data[$k]['atime'] = date('m-d | H:i',$v['atime']);
                    $data[$k]['type'] = $TYPE[$v['type']];
                }
    
                $SHUJU = $data;
            }
    
        }else if((int)$_NPOST['type'] == 2){      //游戏记录
    
            $data = db('jingcairecord') -> where(array('uid' => $USERID,'gametype' => 'videolonghuK'))->limit($limit)->order('time desc') -> select();
        
            if($data){
    
                foreach($data as $k=>$v){
    
                    $data[$k]['time'] = date('m-d H:i',$v['time']);
                    $data[$k]['username'] = explode('_',$v['username']);
                }
    
                $SHUJU = $data;
            }
    
        }
    }
    
    
    if(!$SHUJU || $SHUJU == array()){
        return apptongxin($SHUJU,200,-1,"记录中暂无更多数据",$YZTOKEN,$WY);
    }

}else if($MOD == 'post'){
    /* 金币记录 */

  
}else if($MOD == 'put'){
    /* 佣金记录 */

 
}else if($MOD == 'delete'){
    
}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);