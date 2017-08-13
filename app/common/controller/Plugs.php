<?php
namespace app\common\controller;
use think\Db;
use think\Request;
/**
 * 插件类
 */
abstract class Plugs{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;

    /**
     * $info = array(
     *  'name'=>'Editor',
     *  'title'=>'编辑器',
     *  'description'=>'用于增强整站长文本的输入和显示',
     *  'status'=>1,
     *  'author'=>'Onion',
     *  'version'=>'0.1'
     *  )
     */
    public $info                =   [];
    public $addon_path          =   '';
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin_list          =   [];
    public $custom_adminlist    =   '';
    public $access_url          =   [];

    public function __construct(){
        //$this->view         =   \Think\Think::instance('Think\View');
        $this->view         =   \Think\View::instance(\Think\Config::get('template'), \Think\Config::get('view_replace_str'));
        $this->addon_path   =   PLUGS_PATH.DS.$this->getName().DS;
        $TMPL_PARSE_STRING = config('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['PLUGS_ROOT'] = ROOT_PATH.'/'.PLUGS_PATH_NAME.'/'.$this->getName();
        config('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);

        if(is_file($TMPL_PARSE_STRING['PLUGS_ROOT'].DS.'config.php')){
            $this->config_file = $TMPL_PARSE_STRING['PLUGS_ROOT'].DS.'config.php';
        }
        $request = Request::instance();
        $this->request = $request;
        $rootUrl = $this->request->root(true);
        $this->view->assign('plugPath',$rootUrl.'/'.PLUGS_PATH_NAME.'/'.$this->getName());
    }

    /**
     * 模板主题设置
     * @access protected
     * @param string $theme 模版主题
     * @return Action
     */
    final protected function theme($theme){
        $this->view->theme($theme);
        return $this;
    }

    //显示方法
    final protected function display($template=''){
        echo ($this->fetch($template));
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
    final protected function assign($name,$value='') {
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
        return $this->view->fetch($template, $vars, $replace, $config);
    }

   
    final public function getName(){
        $class = get_class($this);
        return substr($class,strrpos($class, '\\')+1, -5);
    }

    final public function checkInfo(){
        $info_check_keys = array('name','title','description','status','author','version');
        foreach ($info_check_keys as $value) {
            if(!array_key_exists($value, $this->info))
                return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取插件的配置数组
     */
    final public function getConfig($name=''){
        static $_config = array();
        if(empty($name)){
            $name = $this->getName();
        }
        if(isset($_config[$name])){
            return $_config[$name];
        }
        $config =   array();
        $map['name']    =   $name;
        $map['status']  =   1;
        $config  =   Db::name('Addons')->where($map)->find();
        $config=$config['config'];
        if(!empty($config)){
            $config   =   json_decode($config, true);
        }else{
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {

                if($value['type'] == 'group'){
                    foreach ($value['options'] as $gkey => $gvalue) {
                        foreach ($gvalue['options'] as $ikey => $ivalue) {
                            $config[$ikey] = $ivalue['value'];
                        }
                    }
                }else{

                    $config['config'][$key] = $temp_arr[$key]['value'];
                }
            }
            

        }
        $_config[$name]     =   $config;

        return $config;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
