<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:51:"D:\phpStudy\WWW\Tplus\plugs\uploadimages\index.html";i:1502978439;}*/ ?>
<!-- production -->
<!--<script type="text/javascript" src="<?php echo $plugPath; ?>/plupload.full.min.js"></script>-->

<!-- debug-->
<script type="text/javascript" src="<?php echo $plugPath; ?>/plupload/moxie.js"></script>
<script type="text/javascript" src="<?php echo $plugPath; ?>/plupload/plupload.dev.js"></script>
<script type="text/javascript" src="<?php echo $plugPath; ?>/plupload/jquery.min.js"></script>
<style>
    ul{
        list-style:none;
    }
    #file-list {overflow: hidden;padding-left: initial;}
    #file-list li {
        width:160px;
        float: left;
        height:200px;
        position: relative;
        height: inherit;
        margin-bottom: inherit;
    }
    #file-list li a {
        width:150px;
        height:150px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        margin:0 auto;
        border:1px solid #ccc;
        padding: 5px 5px 5px 5px;
    }
    .close{
        background-image: url("<?php echo $plugPath; ?>/plupload/close.png");
        width: 30px;
        height: 30px;
        background-size: contain;
        position: absolute;
        right: 2%;
        top: 0;
    }
    #file-list li a img {max-width:100%;max-height: 100%;}
    .progress{
        position: absolute;
        background-color: rgba(4, 4, 4, 0.53);
        color: #fff;
        padding: 3px 3px 3px 3px;
        border-radius: 10%;
    }
</style>
<input type="hidden" id="images_upload" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
<div class="form-group">
    <label class=".col-xs-6 col-sm-2 control-label text-right">图片上传</label>
    <div class=".col-xs-6 col-sm-10">
        <div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
        <div id="container">
            <button class="btn btn-primary" type="button" id="pickfiles" style="height: 30px;line-height: 8px;">选择图片</button>
            <button class="btn btn-primary" type="button" id="uploadfiles" style="display: none">开始上传</button>
            <ul id="file-list">
                <?php if(!(empty($value) || ($value instanceof \think\Collection && $value->isEmpty()))): if(is_array($imgs_arr) || $imgs_arr instanceof \think\Collection): $i = 0; $__LIST__ = $imgs_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <li id="file-<?php echo $vo['img_id']; ?>">
                            <span class="close" img_id="<?php echo $vo['img_id']; ?>"></span>
                            <a><img src="<?php echo $vo['img_src']; ?>"></a>
                        </li>
                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
            </ul>
        </div>
        <span class="help-block m-b-none" style="clear:both;">
			<i class="fa fa-info-circle"></i><?php echo $tips; ?>
		</span>
    </div>
</div>
<script type="text/javascript">

    //调用例子
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : 'pickfiles',
        container: document.getElementById('container'),
        url : "<?php echo url('system/plugs/open',['plugname'=>'uploadimages','plugaction'=>'plupload','plugfun'=>'upload_images']); ?>",
        flash_swf_url : "<?php echo $plugPath; ?>/Moxie.swf",
        silverlight_xap_url : "<?php echo $plugPath; ?>/Moxie.xap",
        multi_selection: "<?php echo $upload_type; ?>" == 1 ? false : true,
        filters : {
            max_file_size : '100mb',
            mime_types: [
                {title : "Image files", extensions : "jpg,gif,png"},
            ]
        },
        init: {
            //init事件发生后触发
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';
                document.getElementById('uploadfiles').onclick = function() {
                    uploader.start();
                    return false;
                };
            },
            FilesAdded: function(up, files) {
                var upload_type = "<?php echo $upload_type; ?>";
                if(upload_type == 1){
                    $("#images_upload").val('');
                    $("ul li").each(function(){
                        $(this).remove();
                    });
                }

                var len = len = files.length;
                for(var i = 0; i<len; i++){
                    var file_name = files[i].name; //文件名
                    var file_size = files[i].size;//文件大小
                    //构造html来更新UI
                    //var html = '<li id="file-' + files[i].id +'"><p class="file-name">' + file_name + '(' + plupload.formatSize(file_size) + ')' + '</p><p class="progress"></p></li>';
                    var html = '<li id="file-' + files[i].id +'"><span class="close"></span></li>';
                    $(html).appendTo('#file-list');
                    !function(i){
                        previewImage(files[i],function(imgsrc){
                            $('#file-'+files[i].id).append('<a><img src="'+ imgsrc +'" /><span class="progress">12</span></a>');
                        })
                    }(i);
                    $("#uploadfiles").trigger('click');
                }
                /*plupload.each(files, function(file) {
                    document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });*/
            },

            UploadProgress: function(up, file) {
                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                $('#file-'+file.id +" .progress").html(file.percent + "%");
            },
            FileUploaded : function (up,file,res) {
                var data = JSON.parse(res.response).data;
                $('#file-'+file.id).children('.close').attr('img_id',data.img_id);
                var img = $("#images_upload");
                var str = img.val();
                if(str == ''){
                    str = data.img_id;
                }else{
                    str += ','+data.img_id;
                }
                img.val(str);
            },
            Error: function(up, err) {
                document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            }
        }
    });

    //plupload中为我们提供了mOxie对象
    //有关mOxie的介绍和说明请看：https://github.com/moxiecode/moxie/wiki/API
    //file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数
    function previewImage(file,callback){
        if(!file || !/image\//.test(file.type)) return; //确保文件是图片
        if(file.type=='image/gif'){ //gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
            var gif = new moxie.file.FileReader();
            gif.onload = function(){
                callback(gif.result);
                gif.destroy();
                gif = null;
            };
            gif.readAsDataURL(file.getSource());
        }else{
            var image = new moxie.image.Image();
            image.onload = function() {
                image.downsize( 150, 150 );//先压缩一下要预览的图片,宽300，高300
                var imgsrc = image.type=='image/jpeg' ? image.getAsDataURL('image/jpeg',80) : image.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
                callback && callback(imgsrc); //callback传入的参数为预览图片的url
                image.destroy();
                image = null;
            };
            image.load( file.getSource() );
        }
    }

    uploader.init();

    //移除图片
    $("#file-list").on('click',".close",function(){
        var img_id = $(this).attr("img_id");
        var img = $("#images_upload");
        var items=img.val().split(",");
        var index = items.indexOf(img_id);
        items.splice(index,1);//删除元素
        img.val(items.join(','));
        $(this).parent().remove();
    });
</script>