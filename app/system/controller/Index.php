<?php
/*
* 后台首页控制器
* Author: yxhsea@foxmail.com
*/
namespace app\system\controller;

use think\Db;

class Index extends Admin
{	
	public function index(){
		//分配模板变量
		$nickname = session('user_auth.username');
		$group_title = session('user_auth.group_title');
		if(IS_ROOT){
			$group_title = '超级管理员';
		}
		$this->assign('group_title',$group_title);
		$this->assign('nickname',$nickname);
		//渲染模板
		return $this->fetch();
	}

	public function logout(){
		logout();
		$this->redirect('Base/login');
	}

	public function main(){
		//获取mysql版本
		$system_info_mysql = Db::query("select version() as v;");
		$this->assign('mysql_v',$system_info_mysql[0]['v']);
		return $this->fetch();
	}




}
