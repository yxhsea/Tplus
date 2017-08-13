<?php
namespace app\system\controller;

use think\Db;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class File extends Admin {


    /**
     * 上传图片
     */
    public function uploadPicture(){

        $config = config('picture');
        $file = $this->request->file('download');
        
        
        $md5 = $file->md5();
        $sha1 = $file->sha1();
        
        //获取上传图片的后缀
        $post = $file->getInfo();
        $suffix = explode('.', $post['name']);
        $suffix = $suffix[1]; 
        //验证是否为可上传的类型文件
        if(strpos($config['type'],$suffix) === false) return $this->ajaxError('不能上传'.$suffix.'格式的文件');
        //验证文件大小
        $size = $file->getSize();
        if($size > $config['size']) return $this->ajaxError('上传的文件大小不能超过'.$config['size'].'KB');

        $db = Db::name('Picture');

        //查询图片是否已经存在
        $result = $db->where(['md5'=>$md5,'sha1'=>$sha1])->find();

        if($result){
            return json(['id'=>$result['id'],'info'=>'文件上传成功','data'=>$this->request->root().'/'.$result['path'],'status'=>1]);
        }else{
             // 移动到框架应用根目录/Public/uploads/ 目录下
            $imgPath = 'Public' . DS . 'uploads' . DS . $config['path'];
            $info = $file->move(ROOT_PATH . $imgPath);
        }
       
        if($info){
            // 成功上传后 获取上传信息
            $imgPath = 'Public' . '/' . 'uploads' . '/' . $config['path'].'/'.date('Ymd',time()).'/'.$info->getFilename();

            if(file_exists($imgPath)){
                
                $data = [
                    'path' => $imgPath ,
                    'md5' => md5_file($imgPath) ,
                    'sha1' => sha1_file($imgPath) ,
                    'status' => 1 ,
                    'create_time' => time() ,
                ];

                if($id=$db->insertGetId($data)){
                    return json(['id'=>$id,'info'=>'文件上传成功','data'=>$this->request->root().'/'.$imgPath,'status'=>1]);
                }else{
                    return $this->ajaxError('数据记录失败');
                }
            }else{
                return $this->ajaxError('没有找到所上传的'.$info->getFilename().'的文件!');
            }
            
        }else{
            // 上传失败获取错误信息
            return $this->ajaxError('上传图片失败('.$file->getError().'),请联系技术员解决!');
        }
    }
}
