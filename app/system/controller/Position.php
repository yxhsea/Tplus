<?php
/*
* 后台用户控制器
* Author: 初心 [jialin507@foxmail.com]
*/
namespace app\system\controller;
use think\Db;
use app\system\model\Million;
use app\system\model\UserModel;
use think\Loader;

class Position extends Admin
{
	//1-部门列表
	public function index(){
		
		$list = Db::name('Position')->order('id ASC')->paginate(10); //分页查询
		$page=$list->render(); //获取分页
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}

	public function add(){
		if($this->request->isAjax()){
			$db = Db::name('Position');
			$data = $db->createData(1);
			if(!$data) return $this->ajaxError('暂无需要创建的部门数据!');
			$data['create_time'] = time();
			if(Db::name('Position')->insert($data)){
				return $this->ajaxSuccess('部门创建成功!',url('index'));
			}else{
				return $this->ajaxError('部门数据创建失败!');
			}
			
		}else{
			return $this->fetch();
		}
	}

	public function edit($id = 0){
		if($this->request->isAjax()){
			$data = Db::name('Position')->createData(1);
			if(!$data) return $this->ajaxError('暂无要修改的数据!');
			if($data['id'] > 0){
				if(Db::name('Position')->where(['id'=>$data['id']])->update($data)){
					return $this->ajaxSuccess('部门修改成功!',url('index'));
				}else{
					return $this->ajaxError('部门修改失败!');
				}
			}
		}else{
			$this->assign('info',Db::name('Position')->where(['id'=>$id])->find());
			return $this->fetch();
		}
	}
	
}
