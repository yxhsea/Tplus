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
class User extends Admin
{
	//1-用户列表
	public function index(){
	
		$list = Db::name('user')->order('id ASC')->paginate(10); //分页查询
		$page=$list->render(); //获取分页
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch('cardindex');
	}
	//1.1-新增用户
	public function addUser(){
		if($this->request->isAjax()){
			$db = Db::name('user');
			$data = $db->createData(1);
			$param = $this->request->param();
			if(!$data){return $this->ajaxError('数据不能为空');}
			$data['repassword'] = input('post.repassword');
			//加载验证器进行验证
			$validate = Loader::validate('User');
			if(!$validate->check($data)){return $this->ajaxError($validate->getError());}
			unset($data['repassword']);//验证完成后删除重复密码
			$data['password'] = tplus_ucenter_md5($data['password'],config('auth_key'));//加密
			$data['reg_time'] = time();
			$data['reg_ip'] = get_client_ip(1);
			$data['status'] = 1; //启用
			//data数据收集验证完成，开始新增数据。
            $res = $db->insertGetId($data);
            
            if($res){
            	Db::name('power_user')->insert(['uid'=>$res,'group_id'=>$param['group_id']]);
                return $this->ajaxSuccess('用户信息新增成功!',url('Index'));
            }else{
                return $this->ajaxError('用户新增失败!');
            }
        }
        $this->assign('group',Db::name('auth_group')->where(['status'=>1])->select());
        $this->assign('position_res',Db::name('Position')->where(['status'=>1])->select());
		return $this->fetch();
	}
	//1.2-修改用户
	public function updateUser(){
		$db = Db::name('user');

		if($this->request->isAjax()){
			$data = $db->createData(1);
			$param = $this->request->param();
			if(!$data){return $this->ajaxError('数据不能为空');}
			$data['repassword'] = input('post.repassword');
			if(!empty($data['password'])){
				//加载验证器进行验证
				$validate = Loader::validate('User');
				if(!$validate->check($data)){return $this->ajaxError($validate->getError());}
				unset($data['repassword']);//验证完成后删除重复密码
				$data['password'] = tplus_ucenter_md5($data['password'],config('auth_key'));//加密
			}else{
				unset($data['password']);
				unset($data['repassword']);
			}
			$map['id'] = $data['id'];
			//data数据收集验证完成，开始修改数据。
			$res = Db::name('user')->where($map)->update($data);
			//同步修改昵称到用户信息表
			if($res){
				Db::name('power_user')->update(['uid'=>$map['id'],'group_id'=>$param['group_id']]);
                return $this->ajaxSuccess('用户信息修改成功!',url('Index'));
			}else{
				return $this->ajaxError('用户信息修改失败!');
			}
		}else{
			$map['id'] = $this->request->param('id');
			$info = $db->where($map)->find();
			$this->assign('info',$info);
			$group_res = Db::name('power_user')->where(['uid'=>$map['id']])->find();
			$this->assign('group_res',$group_res);
		}
		$this->assign('group',Db::name('auth_group')->where(['status'=>1])->select());
		 $this->assign('position_res',Db::name('Position')->where(['status'=>1])->select());
		return $this->fetch();
	}

	//2-权限列表
	public function AuthManager(){
		
		$list = Db::name('auth_group')->order('id ASC')->paginate(10); //分页查询
		$page=$list->render(); //获取分页
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	//2.1-用户分组增改
	public function addAuthManager(){
		$db = Db::name('auth_group');
		if($this->request->isAjax()){
			$data = $db->createData(1);
			if(!$data){return $this->ajaxError('分组数据不能为空');}
			
			if(empty($data['id'])){
				$data['module'] = 'System';
				$data['type'] = '1';//用户组标识，1为管理员组
				$res = Db::name('auth_group')->insert($data);
			}else{
				$res = Db::name('auth_group')->update($data);
			}
			if($res){
				return $this->ajaxSuccess('分组信息编辑成功!',url('AuthManager'));
			}else {
				return $this->ajaxError('分组信息编辑失败!');
			}
		}else{
			
			$this->assign('is_root',IS_ROOT);
			if($this->request->has('id')){
				//根据传入ID读取指定信息。
				$id = $this->request->param('id');
				$map['id'] = ['eq',$id];
				$info = $db->where($map)->find();
				$this->assign('info',$info);
				return $this->fetch('updateAuthManager');
			}else{
				return $this->fetch();
			}
		}
	}
	//2.1.1-成员授权
	public function power_user(){
		if($this->request->isAjax()){
			$uid = $this->request->param('newuid');
			$group_id = $this->request->param('group_id');
			$power_user = Db::name('power_user');
			$map['uid'] = $uid;
			$a_uid = $power_user->where($map)->find();
			if(0 < $a_uid){
				return $this->ajaxError('不能重复添加用户');
			}else{
				$data['uid'] = $uid;
				$data['group_id'] = $group_id;
				$power_user->insert($data);
				return $this->ajaxSuccess('用户添加成功！',url('power_user',['group_id'=>$group_id]));
			}
		}

		$user = Db::name('user');
		$group_id = $this->request->param('group_id');
		$map['group_id'] = ['eq',$group_id];
		//关联查询
		$join = [
				['power_user p','a.id=p.uid'],
		];
		$list = $user->alias('a')->join($join)->where($map)->paginate(10);
		$page = $list->render();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('group_id',$group_id);

		return $this->fetch();
	}
	//2.1.2-分类授权 TODO:待完善
	public function power_category(){
		$group_id = $this->request->param('group_id');

		$this->assign('group_id',$group_id);
		return $this->fetch();
	}
	//2.1.3-访问授权
	public function power_access(){
		if($this->request->isAjax()){
			$res = input('post.');
			$rules = $res['rules'];
			$id = $res['id'];
			sort($rules);
			$str = implode(',',$rules);
			$map['id'] = $id;
			$data['rules'] = $str;
			$res = db('auth_group')->where($map)->update($data);
			if(!$res){
				return $this->ajaxError('访问授权修改失败！');
			}
			return $this->ajaxSuccess('访问授权修改成功！',url('power_access',['group_id'=>$id]));

		}
		$this->updateRules();
		$group_id = $this->request->param('group_id');
		$access = Db::name('auth_rule');
		//获得菜单集合
		$nodes_list   = $this->returnNodes();

		$map['type'] = 2; //顶级
		$map['status'] = 1;//正常未被禁用
		//$map['module'] = 'system';//管理员模块
		//查询一级菜单
		$main_list = $access->where($map)->column('id','name');
		//查询二级菜单
		$map['type'] = 1;//二级
		$child_list = $access->where($map)->column('id','name');
		//dump($nodes_list);
		//共用一个查询条件
		$auth_group = db('auth_group')->where($map)->column('rules','id');
		//dump($nodes_list);
		$this->assign('nodes_list',$nodes_list);
		$this->assign('this_group', $auth_group[$group_id]);
		$this->assign('main_list',$main_list);
		$this->assign('child_list',$child_list);
		$this->assign('group_id',$group_id);
		return $this->fetch();
	}

	/**
	 * 返回后台节点数据
	 * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
	 * @retrun array
	 * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
	 */
	final protected function returnNodes($tree = true){
		static $tree_nodes = array();
		if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
			return $tree_nodes[$tree];
		}

		if((int)$tree){
			$list = db('Menu')->field('id,pid,title,url,param,tip,hide')->order('sort asc')->select();
			foreach ($list as $key => $value) {
				$ulrarr = explode('/', $value['url']);
				$mod = 'system';
				if(count($ulrarr) == 3){
					$mod = $ulrarr[0];
				}
                $value['param'] = str_replace(['&','='],'/',$value['param']);
				if( stripos($value['url'],$mod)!==0 ){
                    if(strlen($value['param'])>0){
                        $list[$key]['url'] = $mod.'/'.$value['url'].'/'.$value['param'];
                    }else{
                        $list[$key]['url'] = $mod.'/'.$value['url'];
                    }

				}
			}
			$nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);

			foreach ($nodes as $key => $value) {
                if ( !IS_ROOT && !$this->checkRule($value['url'],['in','1,2']) ) {
                    unset($nodes[$key]);
                    continue;//继续循环
                }

				if(!empty($value['operator'])){
					$nodes[$key]['child'] = $value['operator'];
					unset($nodes[$key]['operator']);
					foreach ($nodes[$key]['child'] as $skey => $svalue) {
						if ( !IS_ROOT && !$this->checkRule($svalue['url'],['in','1,2']) ) {
			                    unset($nodes[$key]['child'][$skey]);
			                    continue;//继续循环
					     }
                   

						if(!empty($svalue['operator'])){
							foreach ($svalue['operator'] as $dkey => $dvalue) {
								if ( !IS_ROOT && !$this->checkRule($dvalue['url'],['in','1,2']) ) {
					                    unset($nodes[$key]['child'][$skey]['operator'][$dkey]);
					                    continue;//继续循环
					                }
							}
						}
					}
					//如果不是超级管理员无权限分配 权限管理的功能
					if(strpos($nodes[$key]['title'],'管理员')!== false && !IS_ROOT){
						unset($nodes[$key]);
					    continue;//继续循环
					}
					
				}
			}
			
		}else{
			$nodes = db('Menu')->field('title,url,param,tip,pid')->order('sort asc')->select();
			foreach ($nodes as $key => $value) {
				$ulrarr = explode('/', $value['url']);
				$mod = 'system';
				if(count($ulrarr) == 3){
					$mod = $ulrarr[0];
				}

			    $value['param'] = str_replace(['&','='],'/',$value['param']);
				if( stripos($value['url'],$mod)!==0 ){
				    if(strlen($value['param'])>0){
                        $nodes[$key]['url'] = $mod.'/'.$value['url'].'/'.$value['param'];
                    }else{
                        $nodes[$key]['url'] = $mod.'/'.$value['url'];
                    }
                     if ( !IS_ROOT && !$this->checkRule($nodes[$key]['url'],['in','1,2']) ) {
                        unset($nodes[$key]);
                        continue;//继续循环
                    }
				}
			}
		}

		$tree_nodes[(int)$tree]   = $nodes;
		return $nodes;
	}

	/**
	 * 后台节点配置的url作为规则存入auth_rule
	 * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
	 */
	public function updateRules(){
		//需要新增的节点必然位于$nodes
		$nodes    = $this->returnNodes(false);
		$AuthRule = Db::name('auth_rule');
		//$map['module'] = 'system';//status全部取出,以进行更新
		$map['type'] = array('in','1,2');
		//需要更新和删除的节点必然位于$rules
		$rules    = $AuthRule->where($map)->order('name')->select();
		//构建insert数据
		$data     = array();//保存需要插入和更新的新节点
		foreach ($nodes as $value){
			$temp['name']   = $value['url'];
			$temp['title']  = $value['title'];
			$ulrarr = explode('/', $value['url']);
			$mod = 'system';
			if(count($ulrarr) == 3){
				$mod = $ulrarr[0];
			}
			$temp['module'] = $mod;
			if($value['pid'] >0){
				$temp['type'] = 1;
			}else{
				$temp['type'] = 2;
			}
			$temp['status']   = 1;
			$data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
		}
			

		$update = array();//保存需要更新的节点
		$ids    = array();//保存需要删除的节点的id
		foreach ($rules as $index=>$rule){
			$key = strtolower($rule['name'].$rule['module'].$rule['type']);
			if ( isset($data[$key]) ) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
				$data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
				$update[] = $data[$key];
				unset($data[$key]);
				unset($rules[$index]);
				unset($rule['condition']);
				$diff[$rule['id']]=$rule;
			}elseif($rule['status']==1){
				$ids[] = $rule['id'];
			}
		}

		if ( count($update) ) {
			foreach ($update as $k=>$row){
				if ( $row!=$diff[$row['id']] ) {
					$AuthRule->where('id',$row['id'])->update($row);
				}
			}
		}
		if ( count($ids) ) {

			$mapids['id'] = array('in',implode(',',$ids));

			$AuthRule->where($mapids)->update(['status'=>'-1']);
			//删除规则是否需要从每个用户组的访问授权表中移除该规则?
		}
		if( count($data) ){
			$AuthRule->insertAll(array_values($data));
		}

		return true;

	}

}
