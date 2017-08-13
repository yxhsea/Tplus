<?php
/*
* 后台配置控制器
* Author: 初心 [jialin507@foxmail.com]
*/
namespace app\system\controller;

use think\Cache;
use think\Db;
use app\system\model\Million;
class Config extends Admin
{	
	//配置列表
	public function index(){
		$map=['id'=>array('gt',0)];
		$group = 0;
		if($this->request->has("group")){
			$group = $map['group'] = $this->request->param('group');
		}
		$list = Db::name('Config')->where($map)->order('sort ASC')->paginate(10); //分页查询

		$page=$list->render(); //获取分页
		$this->assign('group_id',$group);
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}

	//配置管理
	public function updateConfig(){
		$db = Db::name('Config');

		if($this->request->isAjax()){
			$data=$db->createData(1);
			if(!$data) return $this->ajaxError('数据不能为空');
			$data['update_time'] = time();
			if(empty($data['id'])){
				$data['create_time'] = time();
				$data['status'] = 1; //启用
				$res = $db->insert($data);
			}else{
				$res = $db->update($data);
			}
			if($res) {
				return $this->ajaxSuccess('配置数据更新成功!',url('index'));
			}else{
				return $this->ajaxError('配置数据更新失败!');
			}
		}else{
			if($this->request->has('id')){
				$info=$db->where(['id'=>$this->request->param('id')])->find();
				$this->assign('info',$info);
				return $this->fetch('edit');
			}else{
				return $this->fetch();
			}
		}
	}

	//配置详情
	public function group(){
        $id = $this->request->has('id') ? $this->request->param('id') : 1;
		$map['status'] = ['eq',1];
		$map['group'] = ['eq',$id];
		$list = Db::name("Config")->where($map)->order('sort ASC')->select();

		if($list) {
			$this->assign('list',$list);
		}
		$this->assign('id',$id);
		return $this->fetch();
	}

	//配置批量保存
	public function save(){
		$config = $this->request->post();
		if($config && is_array($config)){
			$Config_db = Db::name('Config');
			foreach ($config as $name => $value){
				$map['name'] = $name;
				$Config_db->where($map)->setField('value', $value);
			}
		}
		Cache::rm('cache_config');//清空缓存。重新读最新配置数据
		return $this->ajaxSuccess('配置数据更新成功!');
	}

    /**
     * 清除缓存
     * author:yxhsea@foxmail.com
     * @return \think\response\Json
     */
    public function clearRuntime(){
        $R = RUNTIME_PATH;
        if($this->_deleteDir($R)) {
            return json(['status'=>1,'msg'=>'清理缓存成功']);
        }else{
            return json(['status'=>0,'msg'=>'清理缓存失败']);
        }
    }

    /**
     * 递归删除缓存文件和目录
     * author:yxhsea@foxmail.com
     * @param $R
     * @return bool
     */
    private function _deleteDir($R){
        $handle = opendir($R);
        while(($item = readdir($handle)) !== false){
            if($item != '.' and $item != '..'){
                if(is_dir($R.'/'.$item)){
                    $this->_deleteDir($R.'/'.$item);
                }else{
                    if(!unlink($R.'/'.$item))
                        die('error!');
                }
            }
        }
        closedir( $handle );
        return rmdir($R);
    }
}
