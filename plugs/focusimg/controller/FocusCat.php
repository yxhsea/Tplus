<?php
namespace plugs\FocusImg\controller;

use app\system\controller\Plugs; //继承后台插件地址类
use think\Db;
class FocusCat extends Plugs
{
	public function addCat(){
		if($this->request->isAjax()){
			$db = Db::name('FocusCat');
			$data = $db->createData(1);
			if(!$data) return $this->ajaxError('分类数据分析失败');
			$data['create_time'] = time();
			if($db->insert($data)){
				return $this->ajaxSuccess('分类创建成功',url('Addons/adminlist',['name'=>'FocusImg']));
			}else{
				return $this->ajaxError('分类创建失败');
			}
			
		}else{
			
			return $this->fetch('view/add');
		}
	}

	public function edit(){
		$db = Db::name('FocusCat');
		if($this->request->isAjax()){
			$data = $db->createData(1);
			if(!$data) return $this->ajaxError('分类数据分析失败');
			if($db->update($data)){
				return $this->ajaxSuccess('分类更新成功',url('Addons/adminlist',['name'=>'FocusImg']));
			}else{
				return $this->ajaxError('分类更新失败');
			}
		}else{
			$id = $this->request->param('id');
			$this->assign('info',$db->where(['id'=>$id])->find());
			return $this->fetch('view/edit');
		}
	}

	public function del(){
		$db = Db::name('FocusCat');
        $id = $this->request->param('id') ?: 0;
		if($db->where(['id'=>$id])->delete()){
			return $this->ajaxSuccess('分类成功删除');
		}else{
			return $this->ajaxError('分类删除失败');
		}
	}
} 
