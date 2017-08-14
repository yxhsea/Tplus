#### 基于七牛云的视频上传插件
> 这是一个基于七牛云的视频上传插件，直接采用js接口上传到七牛云平台,
> 极大的减轻客户服务器的压力，提高了视频上传的效率。
> 插件使用，必须在后台安装并启用后才能生效，并且要求设置相关参数。
```
//插件调用示例
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">标题</label>
    <div class=".col-xs-6 col-sm-10">
        <input type="text" class="form-control" value="" name="title" placeholder="标题" />
    </div>
</div>
 /**
  * 调用
  * name 名称
  * value 包括视频的视频的播放地址和截图地址，之间用逗号分隔。
  *       例如：http://ouiwsig5o.bkt.clouddn.com/o_1bngf8mpa527uh1vvq1qk61f7ha.mp4,http://ouiwsig5o.bkt.clouddn.com/nUyP59nq1hJe-8iSZV9huoGHk8Y=/lqmZXjbIDyCcwITU4Tgj4-5tmZEr
{:plugs('uploadvideo',['name'=>'video','value'=>''])}
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">姓名 </label>
    <div class=".col-xs-6 col-sm-10">
        <input type="text" class="form-control" value="" name="name" placeholder="姓名" />
    </div>
</div>
```