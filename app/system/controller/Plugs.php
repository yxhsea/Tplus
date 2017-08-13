<?php
namespace app\system\controller;
use app\common\controller\Tplus;
use think\Db;

//后台插件需继承Plugs
class Plugs extends Tplus
{   
    
    protected $view = null;
    public $info                =   [];
    public $addon_path          =   '';
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin_list          =   [];
    public $custom_adminlist    =   '';
    public $access_url          =   [];

    protected function _initialize(){
        parent::_initialize();
        //判断插件是否勾选了对外访问
        if($this->request->has('plugname')){
            $plug = $this->request->param('plugname');
            $addons = Db::name('addons')->where(['name' => $plug])->find();
            if($addons['webvisit'] == 0){
                if(!is_login()) $this->error('请先登录！');
            }
        }else{
            if(!is_login()) $this->error('请先登录！');
        }

        $this->view         =   \Think\View::instance(\Think\Config::get('template'), \Think\Config::get('view_replace_str'));
        $this->addon_path   =   PLUGS_PATH.DS.$this->request->param('plugname').DS;
        $rootUrl = $this->request->root(true);

        $this->view->assign('plugPath',$rootUrl.'/'.PLUGS_PATH_NAME.'/'.$this->request->param('plugname'));
    }

    

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
     protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }


    //用于显示模板的方法
     protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {   
       $template = $this->addon_path.$template.'.html';
        if(!is_file($template)){
                throw new \Exception("模板不存在:$template");
        }
        $this->view->assign('custom_adminlist',$this->view->fetch($template, $vars, $replace, $config));
        echo $this->view->fetch('Addons/adminlist', $vars, $replace, $config);
    }

    //实例化插件模型
    protected function loadModel($name = ''){
        $class = PLUGS_PATH_NAME.DS.$this->request->param('plugname').DS.'model'.DS.$name;
         if(class_exists($class)){
            return new $class;
         }else{
            throw new \Exception("模型不存在");
            return false;
         }
    }

    //插件控制器实例化类
    public function open(){

       $param = $this->request->param();
       $class = PLUGS_PATH_NAME.DS.$param['plugname'].DS.'controller'.DS.$param['plugaction'];
    
       if(class_exists($class)){
            $class = new $class;
            //return $class->$param['plugfun']();
            return call_user_func([$class,$param['plugfun']]);
       }else{
         $this->error('插件'.$param['plugname'].'不存在');
       }
    }

}
