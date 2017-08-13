<?php
/*
* 微信登录控制器
* Author: 初心 [jialin507@foxmail.com]
*/
namespace plugs\WechatLogin\controller;
use app\system\controller\Addons;
use think\Db;
use think\Session;

class WechatLogin extends Addons{
    /**
     * @var string 微信配置信息
     */
    private $wehcat_config = '';
    /**
     * @var string 获取code
     */
    private $authorize = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * @var string 获取access_token
     */
    private $access_token = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    /**
     * @var string 获取用户信息
     */
    private $userinfo = "https://api.weixin.qq.com/sns/userinfo";

    //初始化获取微信配置信息
    protected function _initialize()
    {
        $this->wehcat_config = Db::name('addons')->where(['name' => 'WechatLogin'])->find();
        $this->wehcat_config = json_decode($this->wehcat_config['config'],true);
    }

    public function index(){
        $redirect_uri = $this->wehcat_config['wechat_host'] . url('system/plugs/open',['plugname'=>'WechatLogin','plugaction'=>'WechatLogin','plugfun'=>'redirects']);
        $param = [
            'appid'         =>  $this->wehcat_config['wechat_appid'],
            'redirect_uri'  =>  $redirect_uri,
            'response_type' =>  'code',
            'scope'         =>  $this->wehcat_config['wechat_scope'],
            'state'         =>  ''
        ];
        $param = http_build_query($param);
        $url = $this->authorize . '?' . $param . '#wechat_redirect';
        header("Location:".$url);exit;
    }

    //微信回调路径
    public function redirects(){
        if($this->request->has('code')){
            $code = $this->request->param('code');
            $param = [
                'appid'     => $this->wehcat_config['wechat_appid'],
                'secret'    => $this->wehcat_config['wechat_appsecret'],
                'code'      => $code,
                'grant_type'=> 'authorization_code'
            ];
            $param = http_build_query($param);
            $url = $this->access_token . '?' . $param;
            $output = https_request($url);
            $arr = json_decode($output,true);
            if(isset($arr['openid'])){
                Session::set('wechat_openid',$arr['openid']);
                Session::set('wechat_access_token',$arr['access_token']);
                if($this->wehcat_config['wechat_scope'] == 'snsapi_userinfo'){
                    $this->getUserInfo();
                }else{
                    $this->redirect($this->wehcat_config['wechat_redirects']);
                }
            }else{
                echo "微信验证失败，请退出重试 : 1001";
            }
        }
    }

    //获取用户信息存储
    public function getUserInfo(){
        $access_token = Session::get('wechat_access_token');
        $openid = Session::get('wechat_openid');
        $param = [
            'access_token'  =>  $access_token,
            'openid'        =>  $openid,
            'lang'          =>  'zh_CN'
        ];
        $param = http_build_query($param);
        $url = $this->userinfo . '?'.$param;
        $output = https_request($url);
        $arr = json_decode($output,true);
        if(isset($arr['openid'])){
            Session::set('wechat_nickname',$arr['nickname']);
            Session::set('wechat_img_url',$arr['headimgurl']);
            $this->redirect($this->wehcat_config['wechat_redirects']);
        }else{
            echo "微信验证失败，请退出重试 : 1002";
        }
    }
}
