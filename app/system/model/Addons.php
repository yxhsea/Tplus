<?php
namespace app\system\model;
use think\Model;

/**
 * 插件模型
 */

class Addons extends Model {

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
    /**
     * 文件模型自动完成
     * @var array
     */
   /* protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );
*/
    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = ''){
        if(!$addon_dir)
            $addon_dir = PLUGS_PATH.'/';
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return false;
        }

		$addons			=	[];
        if(empty($dirs))
            $where  =   [];
        else
		    $where['name']	=	['in',$dirs];
    
		$lists			=	$this::where($where)->paginate(10);
        $page = $lists->render();

        $list=[];
        //暂时转为数组使用
        foreach ($lists as $key => $value) {
           $list[$key]=$value->data;
        }

		foreach($list as $addon){
			$addon['uninstall']		=	0;
			$addons[$addon['name']]	=	$addon;
		}

        foreach ($dirs as $value) {
            if(!isset($addons[$value])){
				$class = get_plugs_class($value);
				if(!class_exists($class)){ // 实例化插件失败忽略执行

					\Think\Log::record('插件'.$value.'的入口文件不存在！');
					continue;
				}
                $obj    =   new $class;
				$addons[$value]	= $obj->info;
				if($addons[$value]){
					$addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
				}
			}
        }
     
        int_to_string($addons, ['status'=>[-1=>'损坏', 0=>'禁用', 1=>'启用', null=>'未安装']]);
        

        $addons = list_sort_by($addons,'uninstall','desc');
        $list = [];
        $list['addons'] = $addons;
        $list['page'] = $page;
        return $list;
    }

    /**
     * 获取插件的后台列表
     */
    public function getAdminList(){
        $admin = [];
        $db_addons = $this->where("status=1 AND has_adminlist=1")->select();
        //dump($db_addons); exit;
        if($db_addons){
            foreach ($db_addons as $value) {
                
                $admin[] = ['title'=>$value->data['title'],'url'=>"Addons/adminList?name={$value->data['name']}"];
            }
        }
        
        return $admin;
    }
}
