<?php
/* 后台应用私有函数文件
 * 2016-08-04 10:23:00 [Onion]
 */

function up_title($pid){
	if(!$pid){
		return '无父分类';
	}
	$result=\think\Db::name("Menu")->where(['id'=>$pid])->find();
	if($result){
		return $result['title'];
	}else{
		return '父分类丢失';
	}
}

function get_group_name($uid){
	$group = \think\Db::name('power_user')->where(['uid'=>$uid])->find();
	if($group){
		$info = \think\Db::name('auth_group')->where(['id'=>$group['group_id']])->find();
		if($info){
			return $info['title'];
		}else{
			return '暂无部门';
		}
	}else{
		return '暂无部门';
	}
}
function get_position_table($postion_id){
	$res = db('Position')->where(['id'=>$postion_id])->find();
	if($res){
		return $res['title'];
	}else{
		return '暂无部门';
	}
}

function get_position_user($pid){
	$res = db('User')->where(['position_id'=>$pid])->select();
	return $res;
}