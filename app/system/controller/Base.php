<?php
/*
* 后台开发控制器，不经过权限处理
* Author: yxhsea@foxmail.com
*/
namespace app\system\controller;
use app\common\api\UserApi;
use app\common\controller\Tplus;
use think\Controller;
use think\Db;

class Base extends Tplus
{
    public function login(){
		if($this->request->isPost()){
			// if(cache($this->request->post('username'))){
			// 	$this->error('此账号正在使用中......');
			// }

			if($this->request->has('username') && $this->request->has('password','post')){
				if(UserApi::login($this->request->post('username'),$this->request->post('password')) > 0){
					$this->success('登录成功', 'Index/index');
				}else{
					$this->error('登录失败!请检查账号密码！');
				}
			}
		}
		return $this->fetch();
	}
	public function tip_404(){
		return $this->fetch();
	}

}
