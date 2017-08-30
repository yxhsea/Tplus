#### 百度编辑器插件
> 该插件，基于百度编辑器ueditor，组件丰富，可以满足常规排版需求，可灵活应用。  
```
//调用示例
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">内容</label>
    <div class=".col-xs-6 col-sm-10">
        {:plugs('ueditor',['name'=>'content','value'=>''])}
        <span class="help-block m-b-none">
            <i class="fa fa-info-circle"></i>
            内容描述
        </span>
    </div>
</div>
```