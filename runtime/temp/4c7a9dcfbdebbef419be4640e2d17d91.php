<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:50:"D:\phpStudy\WWW\Tplus\plugs/uploadimages/list.html";i:1502959399;}*/ ?>
<style>
    .preview{
        width: 100px;
        height: 100px;
        display: flex;
        border: 1px solid #ccc;
        padding: 5px 5px 5px 5px;
        align-items: center;
        justify-content: center;
    }
    .previewr{
        width: 100%;
        height: 500px;
        display: flex;
        border: 1px solid #ccc;
        padding: 5px 5px 5px 5px;
        align-items: center;
        justify-content: center;
    }
    .preview img{
        max-width:100%;
        max-height: 100%;
    }
    .previewr img{
        max-width:100%;
        max-height: 100%;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group search-form">
                                <input type="text" placeholder="图片ID" name="id" value="" class="search-input input-sm form-control">
                                <span class="input-group-btn">
                                    <button type="button" id="search" url="<?php echo url('Addons/adminlist',['name'=>'uploadimages']); ?>" class="btn btn-sm btn-primary">搜索</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>图片ID</th>
                                    <th>图片预览</th>
                                    <th>文件md5</th>
                                    <th>文件sha1编码</th>
                                    <th>上传时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <tr>
                                   <td><?php echo $vo['id']; ?></td>
                                   <td>
                                       <a href="#" class="preview"   data-toggle="modal" data-target="#exampleModal" data-whatever="<?php echo $rooturl; ?>/<?php echo $vo['path']; ?>">
                                            <img src="<?php echo $rooturl; ?>/<?php echo $vo['path']; ?>">
                                       </a>
                                   </td>
                                   <td><?php echo $vo['md5']; ?></td>
                                   <td><?php echo $vo['sha1']; ?></td>
                                   <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                                   <td>
                                        <!--<a href="<?php echo plugUrl('focusimg/FocusCat/edit',['id'=>$vo['id']]); ?>">-->
                                            <!--<i class="fa fa-wrench text-navy"></i>-->
                                            <!--编辑-->
                                        <!--</a>-->

                                        <a class="confirm ajax-get" href="<?php echo plugUrl('uploadimages/plupload/del',['id'=>$vo['id']]); ?>">
                                            <i class="fa fa-trash text-danger"></i>
                                            删除
                                        </a>
                                   </td>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">

                        <div class="col-sm-12 m-b-xs">
                            <div class="btn-group center-block">
                                <?php echo $page; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="previewr">
                <img src="">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var recipient = button.data('whatever');
        var modal = $(this);
        modal.find('.modal-body img').attr('src',recipient);
    })
</script>
