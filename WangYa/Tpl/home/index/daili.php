<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);

// if($USER['level'] < 1){

//     return apptongxin($SHUJU,200,-3,"不是代理",$YZTOKEN,$WY);
// }


if($MOD == 'get'){
    /*获取数据*/

    $SHUJU['level'] = logac('level');
    $SHUJU['bizhong'] = array($CONN['jine'],$CONN['jifen'],$CONN['huobi']);
    $SHUJU['pay'] = xitongpay(-2);
    $SHUJU['jine'] = $USER['jine'];
    $SHUJU['dengji'] = $USER['level'];
    $SHUJU['key'] = TuiGuang_sckey($USERID);





}else if($MOD == 'post'){
    /*下线查询*/

    $SHUJU = array();

    $USER = uid($USERID,1);

    $SHUJU['shangji'] = (int)$USER['tuid'] > 0?uid($USER['tuid'],1):'';

    $where = ['tuid'=>$USERID];
    $xiaji = db('user') -> zhicha('uid,tuid,name,touxiang,shouji') -> where($where)-> select();

    $SHUJU['xiaji'] = $xiaji?$xiaji:array();

}else if($MOD == 'put'){
    /*我的收益 佣金列表*/

    $SHUJU = array();
    $SHUJU['jilu'] = array();
    $data = db('huobilog') -> where(array('uid' => $USERID,'type' => 18))->limit($limit)->order('atime desc') -> select();
    if($data){

        foreach($data as $k=>$v){

            $data[$k]['atime'] = date('Y-m-d | H:i:s',$v['atime']);

        }

        $SHUJU['jilu'] = $data;
    }

    //今日收益
    $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

    $where = db('huobilog') -> wherezuhe(array('atime >' => $todayStart,'atime <=' => $todayEnd,'type'=>18,'uid'=>$USERID));

    $sql = 'SELECT SUM(jine) as "todayGet" FROM ay_huobilog '.$where;

    $todayTotal = db('huobilog') -> qurey($sql);

    $SHUJU['todayGet'] = $todayTotal['todayGet']?$todayTotal['todayGet']:0;

    //本月总收益
    $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
    $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));

    $where = db('huobilog') -> wherezuhe(array('atime >' => $beginThismonth,'atime <=' => $endThismonth,'type'=>18,'uid'=>$USERID));

    $sql = 'SELECT SUM(jine) as "mouthGet" FROM ay_huobilog '.$where;

    $mouthTotal = db('huobilog') -> qurey($sql);

    $SHUJU['mouthGet'] = $mouthTotal['mouthGet']?$mouthTotal['mouthGet']:0;

}else if($MOD == 'delete'){
    /*我的团队*/
    $SHUJU = daili_count($USERID,$CONN['tuiji'],'apkhongbao');
}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);