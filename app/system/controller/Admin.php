<?php
/*
* 后台公共控制器,主要为权限，公共操作方法的集合
* 需要权限验证，或公共操作方法需继承此控制器
* Author: yxhsea@foxmail.com
*/
namespace app\system\controller;

use app\common\api\UserApi;
use app\common\controller\Tplus;
use think\Cache;
use think\Config;
use think\Db;
use think\Loader;

class Admin extends Tplus
{
    private $delete_auth = false;
	//初始化方法
	protected function _initialize()
    {
        parent::_initialize();
        //如果没有安装跳转到安装文件
        if(!file_exists('./app/install/data/install.lock')){
            $this->redirect('install/index/index');
            exit;
        }
        //登录状态检查
        define('UID',is_login());
        if(!UID){// 还没登录 跳转到登录页面
            $this->redirect('Base/login');
        }

        //动态设置配置
        $config = Cache::get('cache_config');

        if(!$config){
            $config = config_lists();
            Cache::set('cache_config',$config);
        }
        Config::set($config);

        //判断是否为超级管理员
        define('IS_ROOT',is_administrator(UID));

        if(!IS_ROOT && config('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(!in_array(get_ip(),explode(',',config('ADMIN_ALLOW_IP')))){
                $this->error('403:禁止访问');
            }
        }
        if(strtolower($this->request->controller()) == 'index' && strtolower($this->request->module()) == 'system'){
           //首页不判断权限
        }else{
            // 检测访问权限
            $access =   $this->accessControl();
               

            if ( $access === false ) {
                $this->error('403:禁止访问');
            }elseif( $access === null ){
                $dynamic        =   $this->checkDynamic();//检测分类栏目有关的各项动态权限
                if( $dynamic === null ){
                    //检测非动态权限
                    $rule  = strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
                    //$rule = $this->request->path();

                    if ( !$this->checkRule($rule,array('in','1,2')) ){
                        if($this->request->action() == 'deletedata'){
                            $this->delete_auth = true;
                        }else{
                            echo "权限不足，拒绝访问。如需访问请联系管理员提升权限。";
                            exit;
                        }
                    }
                }elseif( $dynamic === false ){
                    echo "权限不足，拒绝访问。如需访问请联系管理员提升权限。2";
                    exit;
                }
            }
        }

    	$menus=$this->getMenus('Menu');

    	$this->assign('tplusmenu',$menus['main']);

        $user = session('user_auth');
        /*if(cache($user['username']) && cache($user['username']) != session_id()){
            logout();
            $this->error('您的账号已在他处登录',url('Base/login'));
        }else{
            cache($user['username'],session_id(),60);
        }*/
        
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type=1, $mode='url'){
       
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new UserApi();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }

    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }

        $allow = config('ALLOW_VISIT');
        $deny  = config('DENY_VISIT');
        $check = strtolower($this->request->controller().'/'.$this->request->action());
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }


    //状态操作
    public function setStatus($module,$ids = '',$status = 0 ,$status_field = 'status'){

        if(!$module) return $this->ajaxError('模型名称不能为空!');
        if(empty($ids)){
            $ids = $this->request->post();
        }

        $id    = is_array($ids) ? implode(',',$ids) : $ids; //构造ID条件
        $map = ['id'=>['in',$id]]; //组合条件
        $res=Db::name($module)->where($map)->update([$status_field=>$status]);

        if($res){
            return $this->ajaxSuccess('更新成功!');
        }else{
            return $this->ajaxError('更新失败!');
        }
    }

    //删除操作  /*2016.8.11 9:23 新增参数 field */
    public function deleteData($module,$ids = '',$field = 'id'){
        if($this->delete_auth){
            return $this->ajaxError('删除失败!权限不足');
        }
        if(!$module) return $this->ajaxError('模型名称不能为空!');
        if(empty($ids)){
            $ids = $this->request->post();
        }
        $id    = is_array($ids) ? implode(',',$ids) : $ids; //构造ID条件
        $map = [$field=>['in',$id]]; //组合条件

        $res=Db::name($module)->where($map)->delete();

        if($res){
            return $this->ajaxSuccess('删除成功!');
        }else{
            return $this->ajaxError('删除失败!');
        }
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     */
    final public function getMenus($controller){
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            /*if(!C('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }*/
            $menus['main']  =   Db::name('Menu')->where($where)->order('sort asc')->select();

            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            $current = Db::name('Menu')->where("url like '%{$controller}/".$this->request->action()."%'")->field('id')->find();
            if($current){
                $nav = $this->getPath($current['id']);

                $nav_first_title = $nav[0]['title'];

                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
                        $this->error('控制器基类$menus属性元素配置有误');
                    }
                    if( stripos($item['url'],$this->request->module())!==0 ){
                        $item['url'] = $this->request->module().'/'.$item['url'];
                    }
                    // 判断主菜单权限
                     
                     $ulrarr = explode('/', $menus['main'][$key]['url']);
                     if(count($ulrarr) == 3){
                        $ruleurl = $menus['main'][$key]['url'];
                     }else{
                        $ruleurl = 'system/'.$menus['main'][$key]['url'];
                     }
                     
                    if ( !IS_ROOT && !$this->checkRule($ruleurl,['in','1,2']) ) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }
                    //dump($menus['main'][$key]);
                    // 获取当前主菜单的子菜单项
                    if($item['title'] == $nav_first_title){
                        $menus['main'][$key]['class']='current';
                        }
                        //生成child树
                        $groups = Db::name('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
                        if($groups){
                            $groups = array_column($groups, 'group');
                        }else{
                            $groups =   [];
                        }
                        //获取二级分类的合法url
                        $where          =   [];
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        /*if(!C('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }*/
//                        $second_urls = Db::name('Menu')->where($where)->find();

                        /*if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,$this->request->module())!==0 ){
                                    $rule = $this->request->module().'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }*/
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] =   $item['id'];
                            $map['hide']    =   0;

                           /* if(!C('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }*/

                            $menuList = Db::name('Menu')->where($map)->field('id,pid,title,url,param,tip,font_class')->order('sort asc')->select();
                            //判断子菜单显示权限
                            $son_menu = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                            foreach ($son_menu as $sonkey => $sonvalue) {
                                $ulrarr = explode('/', $sonvalue['url']);
                                 if(count($ulrarr) == 3){
                                    $ruleurl = $sonvalue['url'];
                                 }else{
                                    $ruleurl = 'system/'.$sonvalue['url'];
                                 }

                                 if ( !IS_ROOT && !$this->checkRule($ruleurl,['in','1,2']) ) {
                                    unset($son_menu[$sonkey]);
                                    continue;//继续循环
                                }
                            }
                            /* $ulrarr = explode('/', $menus['main'][$key]['url']);
                             if(count($ulrarr) == 3){
                                $ruleurl = $menus['main'][$key]['url'];
                             }else{
                                $ruleurl = 'system/'.$menus['main'][$key]['url'];
                             }
                             
                            if ( !IS_ROOT && !$this->checkRule($ruleurl,['in','1,2']) ) {
                                unset($menus['main'][$key]);
                                continue;//继续循环
                            }*/
                            $menus['main'][$key]['child'][$g] = $son_menu;
                            //$menus['main'][$key]['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
//                            var_dump(model('Addons')->getAdminList());exit;
                            //原来是判断扩展目录，2017.1.9改可自由选择父级菜单
                            /*if($item['url'] == $this->request->module().'/Addons/index'){
                                $sonlist=model('Addons')->getAdminList();

                                $menus['main'][$key]['child'][$g]=array_merge($menus['main'][$key]['child'][$g],$sonlist);
                            }*/
                        }
                       /* if($menus[$key]['child'] === array()){
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }*/

                }
            }
            // session('ADMIN_MENU_LIST'.$controller,$menus);
        }

        // model('Addons')->getAdminList();
        //dump($menus['main'][3]['child']);
        return $menus;
    }

    protected function getPath($id){
    	$path = [];
				$nav = Db::name('Menu')->where("id={$id}")->field('id,pid,title')->find();
				$path[] = $nav;
				if($nav['pid'] >1){
					$path = array_merge($this->getPath($nav['pid']),$path);
				}
				return $path;
    }

    //加载模型方法
    protected function loaderMode($Module){
        $Module=Loader::model($Module);
        return $Module;
    }
	
}
