<?php
/*
* 后台菜单控制器
* Author: Onion [133433354@qq.com]
*/
namespace app\system\controller;

use think\Db;

class Menu extends Admin
{	
	//菜单列表
	public function index(){

		$map=['pid'=>0];
		if($this->request->has("pid")){
			$map['pid'] = $this->request->param('pid');
		}
		//搜索实现
		if($this->request->has("title")){
			$map['title'] = ['like',"%".$this->request->param('title')."%"];
		}
		$list = Db::name('Menu')->where($map)->order('sort ASC')->paginate(10); //分页查询
		$page=$list->render(); //获取分页
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('pid',$map['pid']);
		return $this->fetch();
	}

	//新增导航
	public function updateMenu(){
		$db = Db::name('Menu');

		if($this->request->isAjax()){
			$data=$db->createData(1);
			if(!$data) return $this->ajaxError('数据不能为空');

			if(empty($data['id'])){
				$res = $db->insert($data);
			}else{
				$res = $db->update($data);
			}
			if($res) {
				return $this->ajaxSuccess('菜单数据更新成功!',url('index'));
			}else{
				return $this->ajaxError('菜单数据更新失败!');
			}

		}else{
			$menus = $db->select();
			$menus = $this->loaderMode('Tree')->toFormatTree($menus);
			$this->assign('meuns',$menus);
			if($this->request->has('id')){
				$info=$db->where(['id'=>$this->request->param('id')])->find();
				$this->assign('info',$info);
				return $this->fetch('edit');
			}else{
			    if($this->request->has('pid')){
                    $this->assign('pid',$this->request->param('pid'));
                }
				return $this->fetch();
			}
			
		}	
	}
	//排序
	public function sort(){
		$db = Db::name('Menu');
		if($this->request->isAjax()){
			$json = $this->request->param();
			if(!$json) return $this->ajaxError('排序数据不存在!');

			$data = json_decode($json['sort'],true); 

			if(is_array($data) && !empty($data)){
				foreach ($data as $key => $value) {
					$db->where(['id'=>$value['id']])->update(['sort'=>$key]);
				}
			}else{
				return $this->ajaxError('排序更新失败');
			}

			return $this->ajaxSuccess('菜单排序更新成功!',url('index'));
		}else{
			$id=$this->request->param('ids');
			$map= ['id'=>['in',$id]];
			$lists=$db->where($map)->order('sort ASC')->select();
			$this->assign('lists',$lists);
			return $this->fetch();
		}
	}
	//字体图标选择
	public function fontSelect(){
		return $this->fetch();
	}

	//隐藏状态AJAX接受方法
	public function ishide(){
		//判断是否为AJAX的请求
		if($this->request->isAjax()){
			//获取param值
			$hide=$this->request->param('hide');
			$id=$this->request->param('id');

			$hideres=Db::name('Menu')->where(['id'=>$id])->update(['hide'=>$hide]);

			if($hideres){ return json(['data'=>'','info'=>'隐藏状态更新成功','status'=>1]); }else{ return json(['data'=>'','info'=>'隐藏状态更新失败','status'=>0]); }
		}
	}
   
}
