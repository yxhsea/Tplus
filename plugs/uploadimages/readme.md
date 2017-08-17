#### 单多图片上传插件
> 图片上传插件，基于Plupload插件，支持单多图片上传、实时预览图片。
> 插件使用，必须在后台安装并启用后才能生效，并且要求设置相关参数。  
```
//调用示例，type为1表示单图上传，为2表示多图上传。
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">真实姓名</label>
    <div class=".col-xs-6 col-sm-10">
        <input type="text" class="form-control" value="" name="nickname" placeholder="用户的真实姓名" />
    </div>
</div>
//调用
{:plugs('uploadimages',['name'=>'images','value'=>'','type'=>'2'])}
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">真实姓名</label>
    <div class=".col-xs-6 col-sm-10">
        <input type="text" class="form-control" value="" name="nickname" placeholder="用户的真实姓名" />
    </div>
</div>
```