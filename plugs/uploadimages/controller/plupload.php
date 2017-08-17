<?php
namespace plugs\uploadimages\controller;
use app\system\controller\Addons;
use think\Db;
class plupload extends Addons{

    //配置信息
    private $_config = '';

    //初始化,加载配置信息
    public function _initialize() {
        //获取uploadimage插件的配置项
        $this->_config = Db::name('addons')->where(['name'=>'uploadimages'])->value('config');
        $this->_config = json_decode($this->_config,true)['config'];
    }

    //图片上传方法
    public function upload_images(){
        if($this->request->isPost()){
            //接收参数
            $images = $this->request->file('file');

            //计算md5和sha1散列值，TODO::作用避免文件重复上传
            $md5 = $images->hash('md5');
            $sha1= $images->hash('sha1');

            //提取配置信息
            if(preg_match('/\d+/',$this->_config['upload_size_images'],$size)){
                $upload_size = $size[0] * 1024 * 1024;
            }

            //文件上传规则限制
            $rule = [
                'size' => $upload_size,
                'ext'  => explode(',',$this->_config['upload_type_images'])
            ];
            if(!$images->check($rule)){
                return ['status'=>0,'msg'=>'文件大小或类型不合法'];
            }

            //判断图片文件是否已经上传
            $img = Db::name('picture')->where(['md5'=>$md5,'sha1'=>$sha1])->find();
            if(!empty($img)){
                return json(['status'=>1,'msg'=>'上传成功','data'=>['img_id'=>$img['id'],'img_url'=>$this->request->root(true).'/'.$img['path']]]);
            }else{
                // 移动到框架应用根目录/public/uploads/picture/目录下
                $imgPath = 'public' . DS . 'uploads' . DS . 'picture';
                $info = $images->move(ROOT_PATH . $imgPath);
                $path = 'public/uploads/picture/'.date('Ymd',time()).'/'.$info->getFilename();
                if(file_exists($path)){
                    $data = [
                        'path' => $path ,
                        'md5' => $md5 ,
                        'sha1' => $sha1 ,
                        'status' => 1 ,
                        'create_time' => time() ,
                    ];
                    if($img_id=Db::name('picture')->insertGetId($data)){
                        return json(['status'=>1,'msg'=>'上传成功','data'=>['img_id'=>$img_id,'img_url'=>$this->request->root(true).'/'.$path]]);
                    }else{
                        return json(['status'=>0,'msg'=>'写入数据库失败']);
                    }
                }
            }
        }else{
            return ['status'=>0,'msg'=>'非法请求!'];
        }
    }

    //删除图片
    public function del(){
        $id = $this->request->param('id');
        $res = Db::name('picture')->where(['id'=>$id])->delete();
        if($res){
            return $this->ajaxSuccess('删除成功');
        }else{
            return $this->ajaxError('删除失败');
        }
    }
}