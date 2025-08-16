<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('user');

$YZHost = 'weiyi/'.md5('post'.$USERID);
$cuzai = $Mem ->g($YZHost);

if($cuzai){

	return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
}

$Mem ->s($YZHost,1,1);
	
if($MOD == 'get'){
    /*获取数据*/

    $SHUJU = array();

	$SHUJU['my'] = array();

	if(isset($_NPOST['yjtype']) && $_NPOST['yjtype'] == 1){		//日榜

		$todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		$yjsql = 'SELECT uid,SUM(jine) as "total" FROM ay_huobilog where type = 18 and jine > 0 and atime > '.$todayStart.' and atime <= '.$todayEnd.' GROUP BY uid order by total desc limit 60';
	
		$YJDATA = db('huobilog') -> qurey($yjsql,'erwei');
		
		if($YJDATA){

			foreach($YJDATA as $k=>$v){

				$thisuser = uid($v['uid']);
				$YJDATA[$k]['touxiang'] = pichttp($thisuser['touxiang']);
				$YJDATA[$k]['name'] = $thisuser['name'];

				if($v['uid'] == $USERID){
					$SHUJU['my']['yj'] = $k + 1;
				}
			}
		}
		$SHUJU['yjdata'] = $YJDATA;

	}else{		//总榜

		$yjsql = 'SELECT uid,SUM(jine) as "total" FROM ay_huobilog where type = 18 and jine > 0 GROUP BY uid order by total desc limit 60';
	
		$YJDATA = db('huobilog') -> qurey($yjsql,'erwei');
		
		if($YJDATA){
	
			foreach($YJDATA as $k=>$v){
	
				$thisuser = uid($v['uid']);
				$YJDATA[$k]['touxiang'] = pichttp($thisuser['touxiang']);
				$YJDATA[$k]['name'] = $thisuser['name'];
	
				if($v['uid'] == $USERID){
					$SHUJU['my']['yj'] = $k + 1;
				}
			}
		}
		$SHUJU['yjdata'] = $YJDATA;

	}
	
	$ylsql = 'SELECT uid,SUM(jine) as "total" FROM ay_huobilog where type = 1 and jine > 0 GROUP BY uid order by total desc limit 60';

	$YLDATA = db('huobilog') -> qurey($ylsql,'erwei');
	
	if($YLDATA){

		foreach($YLDATA as $k=>$v){

			$thisuser = uid($v['uid']);
			$YLDATA[$k]['touxiang'] = pichttp($thisuser['touxiang']);
			$YLDATA[$k]['name'] = $thisuser['name'];
			if($v['uid'] == $USERID){
				$SHUJU['my']['yl'] = $k + 1;
			}
		}
	}
	$SHUJU['yldata'] = $YLDATA;


	$FHDATA = db('user') -> zhicha('uid,name,touxiang,huobi') -> where(array('huobi >' => 0)) -> limit(60) -> order('huobi desc') -> select();
	if($FHDATA){

		foreach($FHDATA as $k=>$v){

			$FHDATA[$k]['touxiang'] = pichttp($v['touxiang']);
			$FHDATA[$k]['name'] = $v['name'];
			if($v['uid'] == $USERID){
				$SHUJU['my']['fh'] = $k + 1;
			}
		}
	}
	$SHUJU['fhdata'] = $FHDATA;


	$ZDDATA = db('user') -> zhicha('uid,name,touxiang,xiajicount') -> where(array('xiajicount >' => 0)) -> limit(60) -> order('xiajicount desc') -> select();
	if($ZDDATA){

		foreach($ZDDATA as $k=>$v){

			$ZDDATA[$k]['touxiang'] = pichttp($v['touxiang']);
			$ZDDATA[$k]['name'] = $v['name'];
			if($v['uid'] == $USERID){
				$SHUJU['my']['zd'] = $k + 1;
			}
		}
	}
	$SHUJU['zddata'] = $ZDDATA;

}else if($MOD == 'post'){
    /*点赞*/
	$ID = (int)$_NPOST['ggid'];
	
	$MSGDATA = db('gonggao') -> where(array('id' => $ID)) -> find();

	$UIDDATA = explode(',',$MSGDATA['zanuid']);

	if(in_array($USERID,$UIDDATA)){		//取消点赞

		foreach($UIDDATA as $k=>$uid){

			if((int)$uid == $USERID){

				unset($UIDDATA[$k]);
			}
		}

		$UIDSTR = implode(',',$UIDDATA);

		$fan = db('gonggao') -> where(array('id' => $ID)) -> update(array('zanuid' => $UIDSTR,'zannum -'=>1));

		if($fan){

			$SHUJU = array(
				'iszan' => 0,
				'zannum' => $MSGDATA['zannum'] - 1
			);
			
		}else return apptongxin(array(),415,$CODE,'取消点赞失败，请稍后重试',$YZTOKEN,$WY);

	}else{		//点赞

		$UIDDATA[] = $USERID;

		$UIDSTR = implode(',',$UIDDATA);

		$fan = db('gonggao') -> where(array('id' => $ID)) -> update(array('zanuid' => $UIDSTR,'zannum +'=>1));

		if($fan){

			$SHUJU = array(
				'iszan' => 1,
				'zannum' => $MSGDATA['zannum'] + 1
			);
			
		}else return apptongxin(array(),415,$CODE,'点赞失败，请稍后重试',$YZTOKEN,$WY);
	}

}else if($MOD == 'put'){
    /**/

	$SHUJU = daili_count($USERID,$CONN['tuiji']);
	
}else if($MOD == 'delete'){
    /*删除数据*/

	$UDATA = uid($USERID,1);
	if((int)$UDATA['isgetnew'] == 0){

		$fan = db('user') -> where(array('uid' => $USERID)) -> update(array('isgetnew'=>1));

		if($fan){

			$SHUJU = array(
				'isgetnew' => 1,
			);
			
		}else return apptongxin(array(),415,$CODE,'领取失败，请稍后重试',$YZTOKEN,$WY);

	}else return apptongxin(array(),415,$CODE,'已领取',$YZTOKEN,$WY);
}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);