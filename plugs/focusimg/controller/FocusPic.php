<?php
namespace plugs\FocusImg\controller;

use app\system\controller\Plugs; //继承后台插件地址类
use think\Db;
class FocusPic extends Plugs
{	
	public function index(){
		$map = [];
		if(!$this->request->has('catid')){
			$this->error('参数错误!');
		}
		$map['catid'] = $this->request->param('catid');
	 	$list = Db::name('FocusImg')->where($map)->paginate(10);

	    $page=$list->render(); //获取分页
        $this->assign('catid',$map['catid']);
	    $this->assign('list',$list);
	    $this->assign('page',$page);
		return $this->fetch('view/index');
	}
	public function addPic(){
		if($this->request->isAjax()){
			$db = Db::name('FocusImg');
			$data = $db->createData(1);
			if(!$data) return $this->ajaxError('轮播数据分析失败');
			$data['create_time'] = time();
			if($db->insert($data)){
				return $this->ajaxSuccess('轮播创建成功',plugUrl('FocusImg/FocusPic/index',['catid'=>$data['catid']]));
			}else{
				return $this->ajaxError('轮播创建失败');
			}
		}else{
		    $this->assign('catid',$this->request->param('catid'));
			return $this->fetch('view/addpic');
		}
	}

	public function edit(){
		$db = Db::name('FocusImg');
		if($this->request->isAjax()){
			$data = $db->createData(1);
			if(!$data) return $this->ajaxError('轮播数据分析失败');
			if($db->update($data)){
				return $this->ajaxSuccess('轮播更新成功',plugUrl('FocusImg/FocusPic/index',['catid'=>$data['catid']]));
			}else{
				return $this->ajaxError('轮播更新失败');
			}
		}else{
			$id = $this->request->param('id');
			$this->assign('info',$db->where(['id'=>$id])->find());
			return $this->fetch('view/editpic');
		}
	}

	public function del(){
		$db = Db::name('FocusImg');
		$id = $this->request->param('id') ?: 0;
		if($db->where(['id'=>$id])->delete()){
			return $this->ajaxSuccess('图片成功删除');
		}else{
			return $this->ajaxError('图片删除失败');
		}
	}

	public function setStatus(){
		if($this->request->isAjax()){
			$db = Db::name('FocusImg');
			if($this->request->param('status') == 1){
				$data = ['status'=>0];
			}else{
				$data = ['status'=>1];
			}

			if($db->where(['id'=>$this->request->param('id')])->update($data)){
				return $this->ajaxSuccess('状态更新成功');
			}else{
				return $this->ajaxError('状态更新失败');
			}
		}
	}
} 
