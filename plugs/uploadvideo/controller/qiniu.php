<?php
/**
 * author      : Yxhsea.
 * email       : Yxhsea@foxmail.com
 * createTime  : 2017/8/13 21:54
 * description :
 */
namespace plugs\uploadvideo\controller;
use app\system\controller\Addons;
use think\Db;
class qiniu extends Addons{
    private $_config = '';

    public function _initialize()
    {
        //初始化配置信息
        $this->_config = Db::name('addons')->where(['name'=>'uploadvideo'])->value('config');
        $this->_config = json_decode($this->_config,true);
    }

    public function getToken(){
        vendor('Qiniu/autoload');
        $auth = new \Qiniu\Auth($this->_config['accessKey'], $this->_config['secretKey']);

        //视频水印
        $wmImg = \Qiniu\base64_urlSafeEncode('http://rwxf.qiniudn.com/logo-s.png');
        $pfopOps = "avthumb/m3u8/noDomain/1/wmImage/".$wmImg;

        //视频截图
        $policy = array(
            'persistentOps' => 'vframe/jpg/offset/1',
        );

        // 生成上传Token
        $upToken = $auth->uploadToken($this->_config['bucket'], null, $this->_config['upload_timeout'], $policy);

        return json(['uptoken'=>$upToken]);
    }
}