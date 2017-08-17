<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"D:\phpStudy\WWW\Tplus/app/system\view\addons\hooks.html";i:1484533854;s:54:"D:\phpStudy\WWW\Tplus/app/system\view\base\common.html";i:1487660130;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T+ 最清爽的后台框架</title>
    <meta name="keywords" content="T+ 最清爽的后台框架">
    <meta name="description" content="T+ 最清爽的后台框架">
    <link rel="shortcut icon" href="./favicon.ico">
    <link href="<?php echo $_css; ?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $_css; ?>/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="<?php echo $_css; ?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/main.css" rel="stylesheet" />
    <link href="<?php echo $_css; ?>/style.min862f.css?v=4.1.0" rel="stylesheet" />
    <link href="<?php echo $_css; ?>/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <script src="<?php echo $_js; ?>/jquery.min.js?v=2.1.4"></script>
     
    <script src="<?php echo $_js; ?>/bootstrap.min.js?v=3.3.6"></script>
    <script src="<?php echo $_js; ?>/plugins/iCheck/icheck.min.js"></script>
    <script src="<?php echo $_js; ?>/plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo $_js; ?>/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo $_js; ?>/common.js"></script>
    <script src="<?php echo $_js; ?>/main.js"></script>
    <script src="<?php echo $_js; ?>/modernizr-2.8.0.min.js"></script>
    <script src="<?php echo $_js; ?>/video.js"></script>
    <script src="<?php echo $_js; ?>/jquery.autocompleter.js"></script>
   <script src="<?php echo $_js; ?>/plugins/layer/layer.min.js"></script>
</head>

<body class="gray-bg">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>钩子列表</h5>
                        
                    </div>
                    <div class="ibox-content">
                        <div class="row">

                            <div class="col-sm-9 m-b-xs">
                                 <a class="btn btn-primary" href="<?php echo url('addhook'); ?>"><i class="fa fa-plus"></i> &nbsp;创建钩子</a>
                               <!--  <div data-toggle="buttons" class="btn-group"> -->

                                    <!-- <button class="btn btn-primary ajax-post" target-form="ids" url="<?php echo url('setStatus',['module'=>'Menu','status'=>1]); ?>" type="button">
                                        <i class="fa fa-check"></i>
                                        &nbsp;启用
                                    </button> -->
                                   <!-- <button class="btn btn-primary list_sort" url="<?php echo url('sort'); ?>" type="button">
                                        <i class="fa fa-sort"></i>
                                        &nbsp;排 序
                                    </button> -->
                                    
                                    <!-- <button class="btn btn-danger ajax-post" target-form="ids" url="<?php echo url('setStatus',['module'=>'Menu','status'=>0]); ?>" type="button">
                                        <i class="fa fa-ban"></i>
                                        &nbsp;禁用
                                    </button> -->
                                    <!-- <button class="btn btn-danger ajax-post confirm" target-form="ids" url="<?php echo url('deleteData',['module'=>'Menu']); ?>" type="button">
                                        <i class="fa fa-trash"></i>
                                        &nbsp;删 除
                                    </button> -->
                                <!-- </div> -->
                            </div>
                           <!--  <div class="col-sm-3">
                               <div class="input-group search-form">
                                   <input type="text" placeholder="请输入关键词" name='title' value="<?php echo input('get.title'); ?>" class="search-input input-sm form-control">
                                   <span class="input-group-btn">
                                       <button type="button" id="search" url="<?php echo url(); ?>" class="btn btn-sm btn-primary">搜索</button>
                                   </span>
                               </div>
                           </div> -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>名 称</th>
                                        <th>描述</th>
                                        <th>类型</th>
                                        <th>操 作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <tr>
                                     
                                        <td>
                                           <?php echo $vo['name']; ?>
                                        </td>
                                        <td><?php echo $vo['description']; ?></td>
                                        
                                        <td><?php if($vo['type'] == 1): ?>视图<?php elseif($vo['type'] == 2): ?>控制器<?php else: ?>无类型<?php endif; ?></td>
                                        
                                        <td>
                                             <a href="<?php echo url('edithook',['id'=>$vo['id']]); ?>">
                                                <i class="fa fa-wrench text-navy"></i>
                                                修改
                                            </a>
                                                &nbsp;&nbsp;
                                            <a class="confirm ajax-get" href="<?php echo url('delhook',['id'=>$vo['id']]); ?>">
                                                <i class="fa fa-trash text-danger"></i>
                                                删除
                                            </a>
                                        </td>
                                    </tr>
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
   
</body>

</html>