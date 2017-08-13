<?php
/*
* 后台菜单控制器
* Author: Onion [133433354@qq.com]
*/
namespace app\system\controller;

use think\Db;
use think\Page;

class Module extends Admin
{	

	/**
	 * 模块列表
	 */
	public function index(){
		$list = model('Module')->getList();
		 
		$this->assign('list', $list['modules']);
		$this->assign('page', $list['page']);
		return $this->fetch();
	}
	//创建向导首页
	public function create(){
		if(!is_writable(MODULE_PATH))
			$this->error('您没有创建目录写入权限，无法使用此功能');
	
		$hooks = Db::name('Hooks')->field('name,description')->select();
		$this->assign('Hooks',$hooks);
		return $this->fetch();
	}
	
	public function build(){
		$modular = Db::name('addons')->where(['name'=>'modular','status'=>1])->find();
		if(!$modular){
			return $this->ajaxError('该功能未启用或者未安装！',url('index'));
		}
		$data   =   $this->request->param(); 
		$data['info']['name']   =   trim($data['info']['name']);
		//判断模块是否已经创建
		$module=Db::name('module')->where(['name'=>$data['info']['name']])->find();
		if($module||in_array($data['info']['name'], array('common','home','install','system')))
			return $this->ajaxError('模块已存在，不能重复创建');
		
		$modules_dir             =   MODULE_PATH;
		//创建目录结构
		$files          =   array();
		$module_dir     = "$modules_dir/{$data['info']['name']}/";
		$files[]        = $module_dir;
		$files[]    =   "{$module_dir}controller/";//controller 目录
		$files[]    =   "{$module_dir}model/";//model 目录
		$files[]    =   "{$module_dir}view/";//view 目录
		$files[]    =   "{$module_dir}common.php";//common.php
		$files[]    =   "{$module_dir}config.php";//config.php
		create_dir_or_files($files);
		//写入文件内容
		  //common.php
		$common = <<<str
<?php
		
str;
		file_put_contents("{$module_dir}common.php", $common);
		//config.php
		$config = <<<str
<?php
/* 后台应用私有配置文件
 * 2016-08-04 10:35:00 [Onion]
 */
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
	
	/* 模板相关配置 */
    'TMPL_PARSE_STRING' => [

        'static' =>  '/Public/home',
        'img'    =>  '/Public/home/img',
        'css'    =>  '/Public/home/css',
        'js'     =>  '/Public/home/js',
    ],
	
];
		
str;
		file_put_contents("{$module_dir}config.php", $config);
		
	 	//插入模块记录
		$m_data = [
				'name'   =>  $data['info']['name'],
				'title' =>   $data['info']['title'],
				'description' =>  $data['info']['description'],
				'create_time' =>  time(),
				
		];
		$res = Db::name('module')->insertGetId($m_data);
		if($res){
			return $this->ajaxSuccess('模块创建成功',url('index'));
		}else{
			return $this->ajaxError('模块创建失败');
		}
		
		

		
	}
	    //修改模块
	     public function edit(){
	        if ($this->request->isAjax()) {
	            $data =$this->request->param();
	            $id = $data['id'];
	            $data['update_time'] = time();
	            $info = Db::name("module")->where("id",$id)->update($data);
	            if($info){
	                  return $this->ajaxSuccess('修改成功!',url('index'));
	              }else{
	                   return $this->ajaxError('修改失败');
	              }            
	        } else {
	            $id = $this->request->param("id");
	            $info = Db::name("module")->where("id",$id)->find();
	            $this->assign('info',$info);
	            return $this->fetch();
	        }
	    }   	
	//删除模块
	public function del(){
	        $id = $this->request->param('id');
	        $name = $this->request->param('name');
	        $fire_name = 'application'.'/'.$name;
	        $del_module = $this->del_dir($fire_name);
	        if($del_module != false){
	        	return $this->ajaxError('删除文件目录失败');
	        }
	        if(Db::name("module")->where('id',$id)->delete()){
	            return $this->ajaxSuccess('删除成功',url('index'));
	        }else{
	            return $this->ajaxError('删除失败');
	        }
	}
	//删除文件夹及子目录
	function del_dir($dir){
	    if(is_dir($dir)){
	        foreach(scandir($dir) as $row){
	            if($row == '.' || $row == '..'){
	                continue;
	            }
	            $path = $dir .'/'. $row;
	            if(filetype($path) == 'dir'){
	                $this->del_dir($path);
	            }else{
	                unlink($path);
	            }
	        }
	        rmdir($dir);
	    }else{
	        return false;
	    }
	}
}
