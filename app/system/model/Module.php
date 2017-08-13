<?php
namespace app\system\model;
use think\Model;

/**
 * 插件模型
 */

class Module extends Model {
    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = ''){
    	$where  =   [];
    	$modules = $this::where($where)->paginate(10);
    	$page = $modules->render();
    	$list['modules'] = $modules;
    	$list['page'] = $page;
    	return $list;
    }

}
