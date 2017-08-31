<?php
namespace app\common\controller;
/*
* Tplus前后台公用控制器
* Author: yxhsea@foxmail.com
*/
use think\Controller;
class Tplus extends Controller
{
    //执行初始化方法
    protected function _initialize()
    {
        $this->_init();
    }
	//初始化方法
	protected function _init()
    {

        defined('TCMS_VERSION') or define('TCMS_VERSION','2.1.170111'); // Tplus版本
        defined('PLUGS_PATH_NAME') or define('PLUGS_PATH_NAME','plugs'); // 插件目录名称
        defined('PLUGS_PATH') or define('PLUGS_PATH', ROOT_PATH.PLUGS_PATH_NAME); // 插件目录
        
        defined('MODULE_PATH_NAME') or define('MODULE_PATH_NAME','application'); // 模块目录名称
        defined('MODULE_PATH') or define('MODULE_PATH', ROOT_PATH.MODULE_PATH_NAME); // 插件目录
        
        $rootUrl = $this->request->root(true); //ROOT域名

        //模板资源变量分配
        foreach (config('TMPL_PARSE_STRING') as $key => $value) {
            $this->view->assign('_'.$key,$rootUrl.$value);
        }
    }

    protected function ajaxError($msg = '', $url = ''){
        $data = ['data'=>'','info'=>$msg,'status'=>0];
        if(!empty($url)){
            $data['url'] = $url;
        }
        return json($data);
    }

    protected function ajaxSuccess($msg = '',$url = '', $data = []){
        $data = ['info'=>$msg,'status'=>1];
        if(!empty($url)){
            $data['url'] = $url;
        }
        if(!empty($data)){
            $data['data'] = $data;
        }
        return json($data);
    }

}
